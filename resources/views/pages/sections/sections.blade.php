@extends('admin.layouts.app')


@section('content')

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="text-center my-4"> {{ trans('main_trans.section') }}</h3>
                <div class="text-right mb-3">
                    @can('create apartments')
                    <a href="{{ route('sections.create') }}" class="btn-bid-now" style="color:white; cursor:pointer">
                        <i class="fas fa-plus-circle"></i> {{ trans('web.add') }}
                    </a>
                    @endcan
                </div>

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





            <button type="button" class=" button x-small  " data-toggle="modal" data-target="#exampleModal">
                {{ trans('Counters_trans.add_Grade') }}
            </button>




                <div class="table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <table id="datatable" class="table  table-hover table-sm table-bordered p-0" data-page-length="50"
                        style="text-align: center">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>{{ trans('Counters_trans.name') }}</th>
                                <th>{{ trans('Counters_trans.Processes') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($sections as $section)

                                <tr>
                                    <td>{{ $section->id }}</td>
                                    <td>@if(App::getLocale() == 'ar')
                                        {{ $section->name_ar }}
                                    @else
                                        {{ $section->name_en }}
                                    @endif</td>




                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#edit{{ $section->id }}"
                                            title="{{ trans('Counters_trans.Edit') }}"><i
                                                class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#delete{{ $section->id }}"
                                            title="{{ trans('Counters_trans.Delete') }}"><i
                                                class="fa fa-trash"></i></button>
                                    </td>



                                </tr>

                                    <!-- edit_modal_Grade -->
                                    <div class="modal fade" id="edit{{ $section->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                        id="exampleModalLabel">
                                                        {{ trans('Counters_trans.edit_Grade') }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- add_form -->
                                                    <form action="{{ route('sections.update',$section->id) }}" method="post" enctype="multipart/form-data">
                                                        {{ method_field('patch') }}
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="name_ar"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.name_ar') }}
                                                                    :</label>
                                                                <input id="name_ar" type="text" name="name_ar"
                                                                    class="form-control"
                                                                    value="{{ $section->name_ar }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $section->id }}">
                                                            </div>
                                                            <div class="col">
                                                                <label for="name_en"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.name_en') }}
                                                                    :</label>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $section->name_en }}"
                                                                    name="name_en" required>
                                                            </div>
                                                        </div>



                                                        <br><br>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">{{ trans('Counters_trans.Close') }}</button>
                                                            <button type="submit"
                                                                class="btn btn-success">{{ trans('Counters_trans.Submit') }}</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
    {{-- //////////////////////////////////// --}}
                                    <!-- delete_modal_Grade -->
                                    <div class="modal fade" id="delete{{ $section->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                        id="exampleModalLabel">
                                                        {{ trans('Counters_trans.delete_Grade') }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('sections.destroy',$section->id)}}" method="post">
                                                        {{method_field('Delete')}}
                                                        @csrf
                                                        {{ trans('Counters_trans.Warning_Grade') }}
                                                        <input id="id" type="hidden" name="id" class="form-control"
                                                               value="{{ $section->id }}">
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">{{ trans('Counters_trans.Close') }}</button>
                                                            <button type="submit"
                                                                    class="btn btn-danger">{{ trans('Counters_trans.Submit') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                        </table>
                        {{ $sections->links('pagination::bootstrap-5') }}


                    </div>
                </div>
            </div>
        </div>


        <!-- add_modal_Grade -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                            {{ trans('Counters_trans.add_Grade') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- add_form -->
                        <form action="{{ route('sections.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label for="name_ar" class="mr-sm-2">{{ trans('Counters_trans.name_ar') }}
                                        :</label>
                                    <input id="name_ar" type="text" name="name_ar" class="form-control">
                                </div>
                                <div class="col">
                                    <label for="name_en" class="mr-sm-2">{{ trans('Counters_trans.name_en') }}
                                        :</label>
                                    <input id="name_en" type="text" class="form-control" name="name_en" required>
                                </div>
                            </div>


                           
                            <br><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ trans('Counters_trans.Close') }}</button>
                        <button type="submit" class="btn btn-success">{{ trans('Counters_trans.Submit') }}</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>

    </div>


@endsection



