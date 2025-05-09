<?php

namespace App\Http\Controllers;

use App\Models\storetype;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Resources\StoretypeControllerResource;
use Illuminate\Support\Facades\File;

class StoretypeController extends Controller
{
    use GeneralTrait;
    public function index()
    {
        $storetypes = storetype::all();
              $storetypes_data = StoretypeControllerResource::collection($storetypes);

        return $this->returnData('storetypes',$storetypes_data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

            $storetype = new storetype();

            $storetype->name_ar= $request->name_ar;
            $storetype->name_en= $request->name_en;

               $image=$request->image;
            $imagename=time().'.'.$image->getClientOriginalExtension();
            $request->image->move(public_path('storetypes'), $imagename);

            $storetype->image=$imagename;

            $storetype->save();
            //return $this->returnData('counter',$counter);
            $notification = array(
                'message' =>  trans('messages.success'),
                'alert-type' => 'success'
            );
            return $this->returnData('storetype',$storetype);

        } catch (\Exception $e) {
            return $this->returnError('E001','error');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(storetype $storetype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(storetype $storetype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $request->validate([
                'name_ar'        => 'nullable|string|',
                'name_en'        => 'nullable|string|',

            ],
            [
                'name_ar.nullable'        => 'The Arabic name is required.',
               // 'name_ar.string'          => 'The Arabic name must be a valid string.',

                'name_en.nullable'        => 'The English name is required.',
               // 'name_en.string'          => 'The English name must be a valid string.',

            ]);

$storetypes = storetype::find($request->id);
            $storetypes->update([
                'name_ar'=> $request->name_ar,
                'name_en'=> $request->name_en,

            ]);
 if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $oldImage = public_path('storetypes/' . $storetypes->image);
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }

                // Upload the new image
                $image = $request->image;
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storetypes'), $imageName);

                // Update the image name in the database
                $storetypes->image = $imageName;
            }


            $storetypes->save();

            $notification = array(
                'message' =>  trans('messages.success'),
                'alert-type' => 'success'
            );
           // return $this->returnData('counters',$counters);

           return $this->returnData('storetypes',$storetypes);

        } catch (\Exception $e) {
            return $this->returnError('E001','error');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $storetype = storetype::findOrFail($id);
        $storetype->delete();
        if(!$storetype){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }    }
}

