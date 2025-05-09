@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="text-center my-4">{{ trans('Counters_trans.edit_userstore') }}</h3>
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

                <form action="{{ route('userstores.update', $userstore->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col">
                            <label for="name_ar" class="mr-sm-2">{{ trans('Counters_trans.name_ar') }}:</label>
                            <input id="name_ar" type="text" name="name_ar" class="form-control" value="{{ $userstore->name_ar }}" required>
                        </div>
                        <div class="col">
                            <label for="name_en" class="mr-sm-2">{{ trans('Counters_trans.name_en') }}:</label>
                            <input id="name_en" type="text" name="name_en" class="form-control" value="{{ $userstore->name_en }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>القسم</label>
                        <select name="section_id" class="form-control">
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ $userstore->section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label for="rate"
                                class="mr-sm-2">{{ trans('Counters_trans.rate') }}
                                :</label>
                            <input id="rate" type="number" name="rate"
                                class="form-control"
                                value="{{ $userstore->rate }}"
                                required>
                            <input id="id" type="hidden" name="id"
                                class="form-control" value="{{ $userstore->id }}">
                        </div>

                    </div>


<br>

                    <div class="div_design">
                        <label for="">current image :</label>
                        <img src="{{ asset('userstore/' . $userstore->image) }}" width="50" height="50">
                    </div>
                    <br>
                    <div class="div_design">
                        <label for="">chance image :</label>
                        <input type="file" name="image" >
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">{{ trans('Counters_trans.Update') }}</button>

                                                <a href="{{ route('userstores.index') }}" class="btn btn-secondary">{{ trans('Counters_trans.Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
