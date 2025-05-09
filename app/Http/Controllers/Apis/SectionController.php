<?php

namespace App\Http\Controllers\Apis;

use App\Models\section;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use App\Models\providerauth;

class SectionController extends Controller
{
use GeneralTrait;
  
   public function __construct()
    {
        $this->middleware('auth:api'); // التأكد من أن التوكن صالح
    }
    public function index()
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sections = Section::where('providerauth_id', $provider->id)->get();
        return response()->json($sections);
    }

    public function show($id)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $section = Section::where('id', $id)
            ->where('providerauth_id', $provider->id)
            ->firstOrFail();

        return response()->json($section);
    }

    public function store(Request $request)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
                'name_ar'        => 'required|string|',
                'name_en'        => 'required|string|',
        ]);

        $section = Section::create([
            'name_en' => $validatedData['name_en'],
            'name_ar' => $validatedData['name_ar'] ?? null,
            'providerauth_id' => $provider->id
        ]);

        return response()->json($section, 201);
    }

    public function update(Request $request, $id)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $section = Section::where('id', $id)
            ->where('providerauth_id', $provider->id)
            ->firstOrFail();

        $validatedData = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string',
        ]);

        $section->update($validatedData);

        return response()->json($section);
    }

    public function destroy($id)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $section = Section::where('id', $id)
            ->where('providerauth_id', $provider->id)
            ->firstOrFail();

        $section->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}