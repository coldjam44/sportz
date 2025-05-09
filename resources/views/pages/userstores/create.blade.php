@extends('admin.layouts.app')


@section('content')
<h3 class="text-center my-4"> {{ trans('main_trans.userstores') }}</h3>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header text-center" style="background: #fff; color: #333; border-radius: 10px 10px 0 0; border-bottom: 2px solid #ddd; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <h3 style="font-weight: bold; letter-spacing: 1px;">{{ trans('Counters_trans.add_Grade') }}</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('userstores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name_ar" class="form-label">{{ trans('Counters_trans.name_ar') }}</label>
                        <input type="text" name="name_ar" class="form-control" id="name_ar" placeholder="{{ trans('Counters_trans.name_ar') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="name_en" class="form-label">{{ trans('Counters_trans.name_en') }}</label>
                        <input type="text" name="name_en" class="form-control" id="name_en" placeholder="{{ trans('Counters_trans.name_en') }}" required>
                    </div>
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

                <div class="row">
                    <div class="col">
                        <label for="rate" class="mr-sm-2">{{ trans('Counters_trans.rate') }}
                            :</label>
                        <input id="rate" type="number" name="rate" class="form-control">
                    </div>

                </div>


                <div class="row">
                    <div class="div_design">
                        <label for="image">Image:</label>
                        <input type="file" name="image" multiple required>
                    </div>
                </div>






                <div class="d-flex justify-content-end">
                    <a href="{{ route('userstores.index') }}" class="btn btn-secondary me-2">{{ trans('Counters_trans.Close') }}</a>
                    <button type="submit" class="btn btn-success">{{ trans('Counters_trans.Submit') }}</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
