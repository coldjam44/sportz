@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>إضافة منتج جديد</h1>

    <form action="{{ route('addproducts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>اسم المنتج بالعربية</label>
            <input type="text" name="name_ar" class="form-control" required>
        </div>

        <div class="form-group">
            <label>اسم المنتج بالإنجليزية</label>
            <input type="text" name="name_en" class="form-control" required>
        </div>

        <div class="form-group">
            <label>الوصف بالعربية</label>
            <textarea name="description_ar" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label>الوصف بالإنجليزية</label>
            <textarea name="description_en" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label>السعر</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label>التخفيض</label>
            <input type="text" name="discount" class="form-control">
        </div>

        <div class="form-group">
            <label>القسم</label>
            <select name="section_id" class="form-control">
                <option value="">اختر القسم</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="images">الصور</label>
            <input type="file" name="image[]" class="form-control" multiple>
        </div>

        <div class="form-group">
            <label>تاريخ البدء</label>
            <input type="date" name="start_time" class="form-control" required>
        </div>

        <div class="form-group">
            <label>تاريخ الانتهاء</label>
            <input type="date" name="end_time" class="form-control" required>
        </div>


        <button type="submit" class="btn btn-success">إضافة المنتج</button>
    </form>
</div>
@endsection
