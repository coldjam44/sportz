<?php

namespace App\Http\Controllers;

use App\Models\providerrate;
use Illuminate\Http\Request;
use App\Models\createstadium;

class ProviderrateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providerrates=providerrate::paginate(7);
        $stadiums=createstadium::all();
        return view('pages.providerrates.providerrates',compact('providerrates','stadiums'));
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
        $request->validate([
            'stadium_id' => 'required',
            'name' => 'required',
            'rate' => 'required',
            'description' => 'required',
        ],
        [
            'stadium_id.required' => 'The stadium field is required.',
            'name.required' => 'The name field is required.',
            'rate.required' => 'The rate field is required.',
            'description.required' => 'The description field is required.',
        ]);
        $providerrate=new providerrate();
        $providerrate->stadium_id=$request->stadium_id;
        $providerrate->name=$request->name;
        $providerrate->rate=$request->rate;
        $providerrate->description=$request->description;
        $providerrate->save();
        return redirect()->route('providerrates.index')->with('success', 'تم اضافة التسعير بنجاح');
        } catch (\Throwable $th) {
            return redirect()->route('providerrates.index')->with('error', 'لم يتم اضافة التسعير بنجاح');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(providerrate $providerrate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(providerrate $providerrate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, providerrate $providerrate)
    {
        try {
            $request->validate([
                'stadium_id' => 'required',
                'name' => 'required',
                'rate' => 'required',
                'description' => 'required',
            ],
            [
                'stadium_id.required' => 'The stadium field is required.',
                'name.required' => 'The name field is required.',
                'rate.required' => 'The rate field is required.',
                'description.required' => 'The description field is required.',
            ]);
            $providerrate->update([
                'stadium_id'=>$request->stadium_id,
                'name'=>$request->name,
                'rate'=>$request->rate,
                'description'=>$request->description,
            ]);
            return redirect()->route('providerrates.index')->with('success', 'تم تعديل التسعير بنجاح');
            } catch (\Throwable $th) {
                return redirect()->route('providerrates.index')->with('error', 'لم يتم تعديل التسعير بنجاح');
            }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $providerrate = providerrate::findOrFail($id);
        $providerrate->delete();
        return redirect()->back()->with('success', 'تم حذف التسعير بنجاح');
    }
}
