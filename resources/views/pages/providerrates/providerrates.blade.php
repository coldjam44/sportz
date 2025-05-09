@extends('admin.layouts.app')


@section('content')

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="text-center my-4"> {{ trans('main_trans.providerrate') }}</h3>
                <div class="text-right mb-3">
                    @can('create apartments')
                    <a href="{{ route('providerrates.create') }}" class="btn-bid-now" style="color:white; cursor:pointer">
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
                                <th>{{ trans('Counters_trans.stadium') }}</th>
                                <th>{{ trans('Counters_trans.description') }}</th>
                                <th>{{ trans('Counters_trans.rate') }}</th>
                                <th>{{ trans('Counters_trans.Processes') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($providerrates as $providerrate)

                                <tr>
                                    <td>{{ $providerrate->id }}</td>
                                    <td>{{ $providerrate->name }}</td>
                                    <td>{{ $providerrate->stadium->name }}</td>
                                    <td>{{ $providerrate->description }}</td>
                                    <td>{{ $providerrate->rate }}</td>


                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#edit{{ $providerrate->id }}"
                                            title="{{ trans('Counters_trans.Edit') }}"><i
                                                class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#delete{{ $providerrate->id }}"
                                            title="{{ trans('Counters_trans.Delete') }}"><i
                                                class="fa fa-trash"></i></button>
                                    </td>



                                </tr>

                                    <!-- edit_modal_Grade -->
                                    <div class="modal fade" id="edit{{ $providerrate->id }}" tabindex="-1" role="dialog"
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
                                                    <form action="{{ route('providerrates.update',$providerrate->id) }}" method="post" enctype="multipart/form-data">
                                                        {{ method_field('patch') }}
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="name"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.name') }}
                                                                    :</label>
                                                                <input id="name" type="text" name="name"
                                                                    class="form-control"
                                                                    value="{{ $providerrate->name }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $providerrate->id }}">
                                                            </div>

                                                        </div>

                                                        <div class="form-group">
                                                            <label for="stadium_id">{{ trans('Counters_trans.stadium') }}</label>
                                                            <select name="stadium_id" class="form-control form-select">
                                                                <option value="">{{ trans('Counters_trans.sports') }}</option>
                                                                @foreach ($stadiums as $category)
                                                                    <option value="{{ $category->id }}" {{ $category->id == $providerrate->stadium_id ? 'selected' : '' }}>
                                                                        {{  $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="description"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.description') }}
                                                                    :</label>
                                                                <input id="description" type="text" name="description"
                                                                    class="form-control"
                                                                    value="{{ $providerrate->description }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $providerrate->id }}">
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="rate"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.rate') }}
                                                                    :</label>
                                                                <input id="rate" type="number" name="rate"
                                                                    class="form-control"
                                                                    value="{{ $providerrate->rate }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $providerrate->id }}">
                                                            </div>

                                                        </div>

                                                        <br>



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
                                    <div class="modal fade" id="delete{{ $providerrate->id }}" tabindex="-1" role="dialog"
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
                                                    <form action="{{route('providerrates.destroy',$providerrate->id)}}" method="post">
                                                        {{method_field('Delete')}}
                                                        @csrf
                                                        {{ trans('Counters_trans.Warning_Grade') }}
                                                        <input id="id" type="hidden" name="id" class="form-control"
                                                               value="{{ $providerrate->id }}">
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
                        {{ $providerrates->links('pagination::bootstrap-5') }}


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
                        <form action="{{ route('providerrates.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label for="name" class="mr-sm-2">{{ trans('Counters_trans.name') }}
                                        :</label>
                                    <input id="name" type="text" name="name" class="form-control">
                                </div>

                            </div>


                            <div class="form-group">
                                <label for="stadium_id">{{ trans('Counters_trans.stadium') }}</label>
                                <select name="stadium_id" class="form-control form-select">
                                    <option value="">{{ trans('Counters_trans.stadium') }}</option>
                                    @foreach ($stadiums as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="row">
                                <div class="col">
                                    <label for="description" class="mr-sm-2">{{ trans('Counters_trans.description') }}
                                        :</label>
                                    <input id="description" type="text" name="description" class="form-control">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="rate" class="mr-sm-2">{{ trans('Counters_trans.rate') }}
                                        :</label>
                                    <input id="rate" type="number" name="rate" class="form-control">
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



