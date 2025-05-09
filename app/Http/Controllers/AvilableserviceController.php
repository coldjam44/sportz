<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\avilableservice;
use Illuminate\Support\Facades\File;

class AvilableserviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avilableservices = avilableservice::paginate(5);
        return view('pages.avilableservices.avilableservices', compact('avilableservices'));

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

            $avilableservice = new avilableservice();

            $avilableservice->name_ar= $request->name_ar;
            $avilableservice->name_en= $request->name_en;

            $image=$request->image;
            $imagename=time().'.'.$image->getClientOriginalExtension();
            $request->image->move(public_path('avilableservices'), $imagename);

            $avilableservice->image=$imagename;
            $avilableservice->save();
            //return $this->returnData('counter',$counter);
            $notification = array(
                'message' =>  trans('messages.success'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);

        } catch (\Exception $e) {
           // return $this->returnError('E001','error');
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


       public function update(Request $request)
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

            $notification = array(
                'message' =>  trans('messages.success'),
                'alert-type' => 'success'
            );
           // return $this->returnData('counters',$counters);

            return redirect()->back()->with($notification);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
           // return $this->returnError('E001','error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $avilableservice = avilableservice::findOrFail($id);

        // Delete the image from the folder
        $imagePath = public_path('avilableservices/' . $avilableservice->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        // Delete the service record from the database
        $avilableservice->delete();

        return redirect()->back()->with('success', 'Service deleted successfully.');
    }
}
