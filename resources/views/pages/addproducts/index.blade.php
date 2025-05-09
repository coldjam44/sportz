@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">إدارة المنتجات</h1>
    <a href="{{ route('addproducts.create') }}" class="btn btn-primary mb-3">إضافة منتج جديد</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>id</th>
                <th>{{ trans('Counters_trans.image') }}</th>
                <th>{{ trans('Counters_trans.name') }}</th>
                <th> {{ trans('Counters_trans.description') }}</th>
                <th>{{ trans('Counters_trans.price') }}</th>
                <th>{{ trans('Counters_trans.discount') }}</th>
                <th>{{ trans('Counters_trans.section') }}</th>
                <th>{{ trans('Counters_trans.Processes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($product->image)
                            @php
                                $images = json_decode($product->image);

                                $totalImages = count($images);
                            @endphp

                            @foreach ($images as $index => $image)
                                @if ($index < 3)
                                    <img src="{{ asset('addproducts/' . $image) }}" width="50" height="50"
                                        style="margin-right: 5px;">
                                @endif
                            @endforeach

                            @if ($totalImages > 3)
                                <span>+{{ $totalImages - 3 }} more</span>
                            @endif
                        @endif
                    </td>
                    <td>{{ App::isLocale('en') ? $product->name_en : $product->name_ar }}</td>
                    <td>{{ App::isLocale('en') ? $product->description_en : $product->description_ar }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->discount ?? 'لا يوجد' }}</td>
                    <td>{{ App::isLocale('en') ? $product->section->name_en : $product->section->name_ar }}</td>
                    <td>
                        <a href="{{ route('addproducts.edit', $product->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <form action="{{ route('addproducts.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
