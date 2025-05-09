@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>تعديل ملعب</h1>

        <form action="{{ route('createstores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">اسم المتجر</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $store->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="location">الموقع</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $store->location) }}" required>
                @error('location')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="sectionr_id">{{ trans('Counters_trans.sections') }}</label>
                <select name="section_id" class="form-control form-select">
                    <option value="">{{ trans('Counters_trans.section') }}</option>
                    @foreach ($sections as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $store->section_id ? 'selected' : '' }}>
                            {{ App::getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tax_record">وثيقة الضريبة</label>
                <input type="file" name="tax_record" id="tax_record" class="form-control">
                @if ($store->tax_record)
                    <p><a href="{{ asset('storage/' . $store->tax_record) }}" target="_blank">عرض الوثيقة الحالية</a></p>
                @endif
                @error('tax_record')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">صورة الملعب</label>
                <input type="file" name="image" id="image" class="form-control">
                @if ($store->image)
                    <p><img src="{{ asset('storage/' . $store->image) }}" alt="صورة الملعب" style="width: 100px; height: auto;"></p>
                @endif
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>








            <button type="submit" class="btn btn-success">تحديث المتجر</button>
        </form>
    </div>
@endsection
