@extends('admin.layouts.app')


@section('content')

<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="text-center my-4"> {{ trans('main_trans.contactus') }}</h3>
                <div class="text-right mb-3">
                    @can('create apartments')
                    <a href="{{ route('contactuss.create') }}" class="btn-bid-now" style="color:white; cursor:pointer">
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





            {{-- <button type="button" class=" button x-small  " data-toggle="modal" data-target="#exampleModal">
                {{ trans('Counters_trans.add_Grade') }}
            </button> --}}




                <div class="table-responsive">
                    <table class="table table-bordered data-table" id="data-table">
                        <table id="datatable" class="table  table-hover table-sm table-bordered p-0" data-page-length="50"
                        style="text-align: center">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>{{ trans('Counters_trans.Name') }}</th>
                                <th>{{ trans('Counters_trans.email') }}</th>
                                <th>{{ trans('Counters_trans.message_title') }}</th>
                                <th>{{ trans('Counters_trans.message') }}</th>

                                <th>{{ trans('Counters_trans.Processes') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($contactuss as $contactus)

                                <tr>
                                    <td>{{ $contactus->id }}</td>
                                    <td>{{ $contactus->name }}</td>
                                    <td>{{ $contactus->email }}</td>
                                    <td>{{ $contactus->message_title }}</td>
                                    <td>{{ $contactus->message }}</td>


                                    <td>
                                        {{-- <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#edit{{ $contactus->id }}"
                                            title="{{ trans('Counters_trans.Edit') }}"><i
                                                class="fa fa-edit"></i></button> --}}
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#delete{{ $contactus->id }}"
                                            title="{{ trans('Counters_trans.Delete') }}"><i
                                                class="fa fa-trash"></i></button>
                                    </td>



                                </tr>

                                    <!-- edit_modal_Grade -->
                                    <div class="modal fade" id="edit{{ $contactus->id }}" tabindex="-1" role="dialog"
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
                                                    {{-- <form action="{{ route('contactuss.update',$contactus->id) }}" method="post">
                                                        {{ method_field('patch') }}
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="name"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.name') }}
                                                                    :</label>
                                                                <input id="name" type="text" name="name"
                                                                    class="form-control"
                                                                    value="{{ $contactus->name }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $contactus->id }}">
                                                            </div>
                                                        </div>


                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="email"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.email') }}
                                                                    :</label>
                                                                <input id="email" type="text" name="email"
                                                                    class="form-control"
                                                                    value="{{ $contactus->email }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $contactus->id }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="phone"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.phone') }}
                                                                    :</label>
                                                                <input id="phone" type="text" name="phone"
                                                                    class="form-control"
                                                                    value="{{ $contactus->phone }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $contactus->id }}">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col">
                                                                <label for="message"
                                                                    class="mr-sm-2">{{ trans('Counters_trans.message') }}
                                                                    :</label>
                                                                <input id="message" type="text" name="message"
                                                                    class="form-control"
                                                                    value="{{ $contactus->message }}"
                                                                    required>
                                                                <input id="id" type="hidden" name="id"
                                                                    class="form-control" value="{{ $contactus->id }}">
                                                            </div>
                                                        </div>



                                                        <div class="form-group">
                                                            <label for="">Insurancetype </label>
                                                            <select name="insurancetype_id"
                                                                class="form-control form-select">
                                                                @foreach (App\models\Insurancetype::all() as $category)
                                                                    @if (App::getLocale() == 'ar')
                                                                        <option
                                                                            value="{{ $category->id }}"@selected(old('insurancetype_id') == $category->id)>
                                                                            {{ $category->title_ar }}</option>
                                                                    @else
                                                                        <option
                                                                            value="{{ $category->id }}"@selected(old('insurancetype_id') == $category->id)>
                                                                            {{ $category->title_en }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <br><br>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">{{ trans('Counters_trans.Close') }}</button>
                                                            <button type="submit"
                                                                class="btn btn-success">{{ trans('Counters_trans.Submit') }}</button>
                                                        </div>
                                                    </form> --}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
    {{-- //////////////////////////////////// --}}
                                    <!-- delete_modal_Grade -->
                                    <div class="modal fade" id="delete{{ $contactus->id }}" tabindex="-1" role="dialog"
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
                                                    <form action="{{route('contactuss.destroy',$contactus->id)}}" method="post">
                                                        {{method_field('Delete')}}
                                                        @csrf
                                                        {{ trans('Counters_trans.Warning_Grade') }}
                                                        <input id="id" type="hidden" name="id" class="form-control"
                                                               value="{{ $contactus->id }}">
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
                        {{ $contactuss->links('pagination::bootstrap-5') }}


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


                </div>
            </div>
        </div>

    </div>


@endsection



