@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>قائمة المتاجر</h1>
        <a href="{{ route('createstores.create') }}" class="btn btn-primary">إضافة ملعب جديد</a>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif



        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المتجر</th>
                    <th>الموقع</th>
                    <th>القسم </th>
                    <th>وثيقة الضريبة</th>
                    <th>صورة الملعب</th>

                    <th>التعديل</th>
                    <th>الحذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($createstores as $store)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $store->name }}</td>
                        <td>{{ $store->location }}</td>
                        <td>{{ App::getLocale() == 'ar' ? $store->section->name_ar : $store->section->name_en }}</td>
                        <td>
                            @if ($store->tax_record)
                                <a href="{{ asset('storage/' . $store->tax_record) }}" target="_blank">عرض الوثيقة</a>
                            @else
                                لا توجد وثيقة
                            @endif
                        </td>
                        <td>
                            @if ($store->image)
                                <img src="{{ asset('storage/' . $store->image) }}" alt="صورة الملعب" style=" width="50" height="50">
                            @else
                                لا توجد صورة
                            @endif
                        </td>

                   


                        <td>
                            <a href="{{ route('createstores.edit', $store->id) }}" class="btn btn-warning">تعديل</a>
                        </td>
                        <td>
                            <form action="{{ route('createstores.destroy', $store->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذا الملعب؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
