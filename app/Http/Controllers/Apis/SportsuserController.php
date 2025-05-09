<?php

namespace App\Http\Controllers\Apis;

use App\Models\sportsuser;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Resources\SportsuserControllerResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\userauth; // أضف هذا السطر في الأعلى

class SportsuserController extends Controller
{
    use GeneralTrait;
  
 public function selectFavoriteSports(Request $request)
{
    $request->validate([
        'sports' => 'required|array',
        'sports.*' => 'exists:sportsusers,id',
    ]);

    // جلب المستخدم المصادق عليه من التوكن
    $user = JWTAuth::parseToken()->authenticate();

    // العثور على المستخدم في جدول userauths
    $userauth = Userauth::where('phone', $user->phone_number)->first();

    if (!$userauth) {
        return response()->json(['message' => 'User not found in userauths'], 404);
    }

    // تحديث أو إضافة الرياضات المفضلة
    $userauth->favoriteSports()->sync($request->sports);

    // جلب الرياضات المفضلة بعد التحديث وإزالة `image`
    $favoriteSports = $userauth->favoriteSports->map(function ($sport) {
        return [
            'id' => $sport->id,
            'name_en' => $sport->name_en,
            'name_ar' => $sport->name_ar,
            'created_at' => $sport->created_at,
            'updated_at' => $sport->updated_at,
        ];
    });

    return response()->json([
        'message' => 'Favorite sports updated successfully',
        'favorite_sports' => $favoriteSports
    ]);
}
  
 public function getFavoriteSports()
{
    try {
        // جلب المستخدم المصادق عليه من التوكن
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'Authentication failed'], 401);
        }

        // العثور على المستخدم في جدول userauths
        $userauth = Userauth::where('phone', $user->phone_number)->first();

        if (!$userauth) {
            return response()->json(['message' => 'User not found in userauths'], 404);
        }

        // جلب الرياضات المفضلة وحذف `image` من كل عنصر
        $favoriteSports = $userauth->favoriteSports->map(function ($sport) {
            return [
                'id' => $sport->id,
                'name_en' => $sport->name_en,
                'name_ar' => $sport->name_ar,
                'created_at' => $sport->created_at,
                'updated_at' => $sport->updated_at,
            ];
        });

        return response()->json([
            'favorite_sports' => $favoriteSports
        ]);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['message' => 'Token has expired'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['message' => 'Token is invalid'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['message' => 'Token is missing or invalid'], 401);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
    }
}


  
  
  
  
    public function index()
    {
        $sportsusers = sportsuser::all();
        $sportsusers_data = SportsuserControllerResource::collection($sportsusers);
        return $this->returnData('sportsusers',$sportsusers_data);

    }

    public function store(Request $request)
    {
        try {
            //$validated = $request->validated();
            $request->validate([
                'name_ar'        => 'required|string|',
                'name_en'        => 'required|string|',

            ],
            [
                'name_ar.required'        => 'The Arabic name is required.',
                'name_ar.string'          => 'The Arabic name must be a valid string.',

                'name_en.required'        => 'The English name is required.',
                'name_en.string'          => 'The English name must be a valid string.',


            ]);

            $sportsuser = new sportsuser();

            $sportsuser->name_ar= $request->name_ar;
            $sportsuser->name_en= $request->name_en;

            $image=$request->image;
            $imagename=time().'.'.$image->getClientOriginalExtension();
            $request->image->move(public_path('sportsusers'), $imagename);

            $sportsuser->image=$imagename;
            $sportsuser->save();
            return $this->returnData('sportsuser',$sportsuser);


        } catch (\Exception $e) {
           return $this->returnError('E001','error');
        }
    }

    public function update(Request $request, sportsuser $sportsuser)
    {
        try {

            $request->validate([
                'name_ar'        => 'required|string|',
                'name_en'        => 'required|string|',

            ],
            [
                'name_ar.required'        => 'The Arabic name is required.',
                'name_ar.string'          => 'The Arabic name must be a valid string.',

                'name_en.required'        => 'The English name is required.',
                'name_en.string'          => 'The English name must be a valid string.',

            ]);

            $sportsusers = sportsuser::findOrFail($request->id);
            $sportsusers->update([
                'name_ar'=> $request->name_ar,
                'name_en'=> $request->name_en,

            ]);

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $oldImage = public_path('sportsusers/' . $sportsusers->image);
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }

                // Upload the new image
                $image = $request->image;
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('sportsusers'), $imageName);

                // Update the image name in the database
                $sportsusers->image = $imageName;
            }

            $sportsusers->save();


            return $this->returnData('sportsusers',$sportsusers);

        } catch (\Exception $e) {
            return $this->returnError('E001','error');
        }
    }

    public function destroy($id)
    {
        $sportsuser = sportsuser::findOrFail($id)->delete();

        if(!$sportsuser){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }

    }
}
