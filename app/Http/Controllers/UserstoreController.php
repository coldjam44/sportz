<?php

namespace App\Http\Controllers;

use App\Models\section;
use App\Models\userstore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserstoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userstores = userstore::paginate(5);
        $sections=section::all();
        return view('pages.userstores.index', compact('userstores','sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections=section::all();
        return view('pages.userstores.create',compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name_ar'        => 'required|string|',
                'name_en'        => 'required|string|',
                //'image'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'section_id'     => 'required|exists:sections,id',
                'rate' => 'required',

            ],[
                'name_ar.required'        => 'الاسم باللغة العربية مطلوب',
                'name_en.required'        => 'الاسم باللغة الانجليزية مطلوب',
                'image.required'          => 'الصورة مطلوبة',
                'section_id.required'     => 'القسم مطلوب',
                'rate.required' => 'The rate field is required.',

            ]);
            $userstore = new userstore();

            $userstore->name_ar= $request->name_ar;
            $userstore->name_en= $request->name_en;
            $userstore->section_id= $request->section_id;
            $userstore->rate= $request->rate;
            $image = $request->image;
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $request->image->move(public_path('userstore'), $imagename);
            $userstore->image=$imagename;
            $userstore->save();
            return redirect()->route('userstores.index')->with('success', 'تم إضافة المنتج بنجاح');
        }catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(userstore $userstore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(userstore $userstore)
    {
        $sections=section::all();
        return view('pages.userstores.edit',compact('userstore','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'name_ar'        => 'required|string|',
                'name_en'        => 'required|string|',
                //'image'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'section_id'     => 'required|exists:sections,id',
                'rate' => 'required',

            ],[
                'name_ar.required'        => 'الاسم باللغة العربية مطلوب',
                'name_en.required'        => 'الاسم باللغة الانجليزية مطلوب',
                'image.required'          => 'الصورة مطلوبة',
                'section_id.required'     => 'القسم مطلوب',
                'rate.required' => 'The rate field is required.',

            ]);
            $userstore = userstore::findOrFail($request->id);

            $userstore->name_ar= $request->name_ar;
            $userstore->name_en= $request->name_en;
            $userstore->rate= $request->rate;
            $userstore->section_id= $request->section_id;
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $oldImage = public_path('userstore/' . $userstore->image);
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }

                // Upload the new image
                $image = $request->image;
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('userstore'), $imageName);

                // Update the image name in the database
                $userstore->image = $imageName;
            }
            $userstore->save();
            return redirect()->route('userstores.index')->with('success', 'تم إضافة المنتج بنجاح');

        }catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userstore = userstore::findOrFail($id);
        $userstore->delete();
        return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}
