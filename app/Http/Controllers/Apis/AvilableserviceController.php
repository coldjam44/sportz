<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Models\avilableservice;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Resources\AvilableserviceControllerResource;

class AvilableserviceController extends Controller
{
    use GeneralTrait;
    public function index()
    {
        $avilableservices = avilableservice::all();
        $avilableservices_data = AvilableserviceControllerResource::collection($avilableservices);
        return $this->returnData('avilableservices',$avilableservices_data);

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

            $avilableservice = new avilableservice();

            $avilableservice->name_ar= $request->name_ar;
            $avilableservice->name_en= $request->name_en;

            $image=$request->image;
            $imagename=time().'.'.$image->getClientOriginalExtension();
            $request->image->move(public_path('avilableservices'), $imagename);

            $avilableservice->image=$imagename;
            $avilableservice->save();
            return $this->returnData('avilableservice',$avilableservice);


        } catch (\Exception $e) {
           return $this->returnError('E001','error');
        }
    }

    public function update(Request $request, avilableservice $avilableservice)
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

            $avilableservices = avilableservice::findOrFail($request->id);
            $avilableservices->update([
                'name_ar'=> $request->name_ar,
                'name_en'=> $request->name_en,

            ]);

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $oldImage = public_path('avilableservices/' . $avilableservices->image);
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }

                // Upload the new image
                $image = $request->image;
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('avilableservices'), $imageName);

                // Update the image name in the database
                $avilableservices->image = $imageName;
            }

            $avilableservices->save();


            return $this->returnData('avilableservices',$avilableservices);

        } catch (\Exception $e) {
            return $this->returnError('E001','error');
        }
    }

    public function destroy($id)
    {
        $avilableservice = avilableservice::findOrFail($id)->delete();

        if(!$avilableservice){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }

    }
}

