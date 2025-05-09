@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>إضافة متجر جديد</h1>

        <form action="{{ route('createstores.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">اسم المتجر</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="location">الموقع</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" required>
                @error('location')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>



            <div class="form-group">
                <label for="section_id">{{ trans('Counters_trans.section') }}</label>
                <select name="section_id" class="form-control form-select">
                    <option value="">{{ trans('Counters_trans.sports') }}</option>
                    @foreach ($sections as $category)
                        <option value="{{ $category->id }}">
                            {{ App::getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label for="tax_record">وثيقة الضريبة</label>
                <input type="file" name="tax_record" id="tax_record" class="form-control" required>
                @error('tax_record')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">صورة الملعب</label>
                <input type="file" name="image" id="image" class="form-control">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>










            


            <button type="submit" class="btn btn-success">إضافة متجر</button>
        </form>
    </div>
@endsection
