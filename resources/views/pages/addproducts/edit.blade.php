@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>تعديل المنتج</h1>

    <form action="{{ route('addproducts.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>اسم المنتج بالعربية</label>
            <input type="text" name="name_ar" class="form-control" value="{{ $product->name_ar }}" required>
        </div>

        <div class="form-group">
            <label>اسم المنتج بالإنجليزية</label>
            <input type="text" name="name_en" class="form-control" value="{{ $product->name_en }}" required>
        </div>

        <div class="form-group">
            <label>الوصف بالعربية</label>
            <textarea name="description_ar" class="form-control" required>{{ $product->description_ar }}</textarea>
        </div>

        <div class="form-group">
            <label>الوصف بالإنجليزية</label>
            <textarea name="description_en" class="form-control" required>{{ $product->description_en }}</textarea>
        </div>

        <div class="form-group">
            <label>السعر</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
        </div>

        <div class="form-group">
            <label>التخفيض</label>
            <input type="text" name="discount" class="form-control" value="{{ $product->discount }}">
        </div>

        <div class="form-group">
            <label>القسم</label>
            <select name="section_id" class="form-control">
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ $product->section_id == $section->id ? 'selected' : '' }}>
                        {{ $section->name_ar }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="div_design">
            <label for="">Current Images:</label>
            <div class="d-flex flex-wrap">
                @foreach (json_decode($product->image) as $image)
                    <div class="m-2">
                        <img src="{{ asset('addproducts/' . $image) }}" width="50" height="50" class="rounded">
                    </div>
                @endforeach
            </div>
        </div>

        <br>

        <div class="div_design">
            <label for="">Change Images:</label>
            <input type="file" name="image[]" multiple>
            <small class="text-muted">You can upload multiple images.</small>
        </div>

        <div class="form-group">
            <label>وقت البدء</label>
            <input type="time" name="start_time" class="form-control" value="{{ $product->start_time }}" required>
        </div>

        <div class="form-group">
            <label>وقت الانتهاء</label>
            <input type="time" name="end_time" class="form-control" value="{{ $product->end_time }}" required>
        </div>

        <button type="submit" class="btn btn-success">تحديث المنتج</button>
    </form>
</div>
@endsection
