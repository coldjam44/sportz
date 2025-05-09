@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>قائمة الملاعب</h1>
        <a href="{{ route('createstadiums.create') }}" class="btn btn-primary">إضافة ملعب جديد</a>

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
                    <th>اسم الملعب</th>
                    <th>الموقع</th>
                    <th>مستخدم الرياضة</th>
                    <th>وثيقة الضريبة</th>
                    <th>صورة الملعب</th>
                    <th>وقت بدء الصباح</th>
                    <th>وقت انتهاء الصباح</th>
                    <th>وقت بدء المساء</th>
                    <th>وقت انتهاء المساء</th>
                    <th>سعر الحجز</th>
                    <th>سعر إضافي للمساء</th>
                    <th>عدد الأعضاء في الفريق</th>
                    <th>الخدمات المتوفرة</th>
                    <th>التعديل</th>
                    <th>الحذف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($createstadiums as $stadium)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stadium->name }}</td>
                        <td>{{ $stadium->location }}</td>
                        <td>{{ App::getLocale() == 'ar' ? $stadium->sportsuser->name_ar : $stadium->sportsuser->name_en }}</td>
                        <td>
                            @if ($stadium->tax_record)
                                <a href="{{ asset('storage/' . $stadium->tax_record) }}" target="_blank">عرض الوثيقة</a>
                            @else
                                لا توجد وثيقة
                            @endif
                        </td>
                        <td>
                            @if ($stadium->image)
                                <img src="{{ asset('storage/' . $stadium->image) }}" alt="صورة الملعب" style=" width="50" height="50">
                            @else
                                لا توجد صورة
                            @endif
                        </td>
                        <td>{{ $stadium->morning_start_time }}</td>
                        <td>{{ $stadium->morning_end_time }}</td>
                        <td>{{ $stadium->evening_start_time }}</td>
                        <td>{{ $stadium->evening_end_time }}</td>
                        <td>{{ $stadium->booking_price }}</td>
                        <td>{{ $stadium->evening_extra_price_per_hour }}</td>
                        <td>{{ $stadium->team_members_count }}</td>
                    <td>
                        <div class="dropdown position-relative">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                              كل الخدمات
                            </button>
                            <ul class="dropdown-menu">
                              @foreach ($stadium->avilableservice as $amenity)
                                  <li><a class="dropdown-item">{{ App::getLocale() == 'ar' ? $amenity->name_ar : $amenity->name_en }}</a></li>
                              @endforeach
                            </ul>
                          </div>
                    </td>


                        <td>
                            <a href="{{ route('createstadiums.edit', $stadium->id) }}" class="btn btn-warning">تعديل</a>
                        </td>
                        <td>
                            <form action="{{ route('createstadiums.destroy', $stadium->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذا الملعب؟')">
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
