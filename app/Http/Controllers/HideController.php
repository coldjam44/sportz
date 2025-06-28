<?php

namespace App\Http\Controllers;

use App\Models\addproduct;
use App\Models\createstore;
use Illuminate\Http\Request;
use App\Models\CreateStadium;

class HideController extends Controller
{
    public function hide(Request $request)
    {
      
      Log::info('Headers:', $request->headers->all());
    Log::info('Auth user:', ['user' => Auth::user()]);
        $request->validate([
            'createstore_id' => 'nullable|exists:createstores,id',
            'createstadium_id' => 'nullable|exists:createstadium,id',
            'product_id' => 'nullable|exists:addproducts,id',
        ]);

        if ($request->has('createstore_id')) {
            CreateStore::where('id', $request->createstore_id)->update(['is_hidden' => true]);
        }

        if ($request->has('createstadium_id')) {
            CreateStadium::where('id', $request->createstadium_id)->update(['is_hidden' => true]);
        }

        if ($request->has('product_id')) {
            AddProduct::where('id', $request->product_id)->update(['is_hidden' => true]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إخفاء العنصر بنجاح',
        ]);
    }

    public function unhide(Request $request)
    {
        $request->validate([
            'createstore_id' => 'nullable|exists:createstores,id',
            'createstadium_id' => 'nullable|exists:createstadium,id',
            'product_id' => 'nullable|exists:addproducts,id',
        ]);

        if ($request->has('createstore_id')) {
            createstore::where('id', $request->createstore_id)->update(['is_hidden' => false]);
        }

        if ($request->has('createstadium_id')) {
            createstadium::where('id', $request->createstadium_id)->update(['is_hidden' => false]);
        }

        if ($request->has('product_id')) {
            addproduct::where('id', $request->product_id)->update(['is_hidden' => false]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إظهار العنصر بنجاح',
        ]);
    }
}
