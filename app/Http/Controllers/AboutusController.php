<?php

namespace App\Http\Controllers;

use App\Models\aboutus;
use Illuminate\Http\Request;

class AboutusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aboutuss = aboutus::paginate(5);
        return view('pages.aboutus.aboutus', compact('aboutuss'));
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
                'title_ar'        => 'required|string|',
                'title_en'        => 'required|string|',
                'description_ar'  => 'required|string',
                'description_en'  => 'required|string',
            ],
            [
                'title_ar.required'        => 'The Arabic title is required.',
                'title_ar.string'          => 'The Arabic title must be a valid string.',

                'title_en.required'        => 'The English title is required.',
                'title_en.string'          => 'The English title must be a valid string.',

                'description_ar.required'  => 'The Arabic description is required.',
                'description_ar.string'    => 'The Arabic description must be a valid string.',

                'description_en.required'  => 'The English description is required.',
                'description_en.string'    => 'The English description must be a valid string.',
            ]);

            $aboutus = new aboutus();

            $aboutus->title_ar= $request->title_ar;
            $aboutus->title_en= $request->title_en;
            $aboutus->description_ar = $request->description_ar;
            $aboutus->description_en = $request->description_en;

            
            $aboutus->save();
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

    /**
     * Display the specified resource.
     */
    public function show(aboutus $aboutus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(aboutus $aboutus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, aboutus $aboutus)
    {
        try {

            $request->validate([
                'title_ar'        => 'required|string|',
                'title_en'        => 'required|string|',
                'description_ar'  => 'required|string',
                'description_en'  => 'required|string',
            ],
            [
                'title_ar.required'        => 'The Arabic title is required.',
                'title_ar.string'          => 'The Arabic title must be a valid string.',

                'title_en.required'        => 'The English title is required.',
                'title_en.string'          => 'The English title must be a valid string.',

                'description_ar.required'  => 'The Arabic description is required.',
                'description_ar.string'    => 'The Arabic description must be a valid string.',

                'description_en.required'  => 'The English description is required.',
                'description_en.string'    => 'The English description must be a valid string.',
            ]);

            $aboutuss = aboutus::findOrFail($request->id);
            $aboutuss->update([
                'title_ar'=> $request->title_ar,
                'title_en'=> $request->title_en,
                'description_ar'=> $request->description_ar,
                'description_en'=> $request->description_en,
            ]);



            $aboutuss->save();


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
        $aboutus = aboutus::findOrFail($id);

        // Delete the service record from the database
        $aboutus->delete();

        return redirect()->back()->with('success', 'Service deleted successfully.');
    }
}
