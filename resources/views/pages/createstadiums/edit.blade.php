@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>تعديل ملعب</h1>

        <form action="{{ route('createstadiums.update', $stadium->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">اسم الملعب</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $stadium->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="location">الموقع</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $stadium->location) }}" required>
                @error('location')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="sportsuser_id">{{ trans('Counters_trans.sports') }}</label>
                <select name="sportsuser_id" class="form-control form-select">
                    <option value="">{{ trans('Counters_trans.sports') }}</option>
                    @foreach ($sports as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $stadium->sportsuser_id ? 'selected' : '' }}>
                            {{ App::getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tax_record">وثيقة الضريبة</label>
                <input type="file" name="tax_record" id="tax_record" class="form-control">
                @if ($stadium->tax_record)
                    <p><a href="{{ asset('storage/' . $stadium->tax_record) }}" target="_blank">عرض الوثيقة الحالية</a></p>
                @endif
                @error('tax_record')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">صورة الملعب</label>
                <input type="file" name="image" id="image" class="form-control">
                @if ($stadium->image)
                    <p><img src="{{ asset('storage/' . $stadium->image) }}" alt="صورة الملعب" style="width: 100px; height: auto;"></p>
                @endif
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="morning_start_time">وقت بدء الصباح</label>
                <input type="time" name="morning_start_time" id="morning_start_time" class="form-control" value="{{ old('morning_start_time', $stadium->morning_start_time) }}" required>
                @error('morning_start_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="morning_end_time">وقت انتهاء الصباح</label>
                <input type="time" name="morning_end_time" id="morning_end_time" class="form-control" value="{{ old('morning_end_time', $stadium->morning_end_time) }}" required>
                @error('morning_end_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="evening_start_time">وقت بدء المساء</label>
                <input type="time" name="evening_start_time" id="evening_start_time" class="form-control" value="{{ old('evening_start_time', $stadium->evening_start_time) }}" required>
                @error('evening_start_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="evening_end_time">وقت انتهاء المساء</label>
                <input type="time" name="evening_end_time" id="evening_end_time" class="form-control" value="{{ old('evening_end_time', $stadium->evening_end_time) }}" required>
                @error('evening_end_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="booking_price">سعر الحجز</label>
                <input type="number" name="booking_price" id="booking_price" class="form-control" value="{{ old('booking_price', $stadium->booking_price) }}" required>
                @error('booking_price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="evening_extra_price_per_hour">سعر إضافي للمساء لكل ساعة</label>
                <input type="number" name="evening_extra_price_per_hour" id="evening_extra_price_per_hour" class="form-control" value="{{ old('evening_extra_price_per_hour', $stadium->evening_extra_price_per_hour) }}">
                @error('evening_extra_price_per_hour')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="team_members_count">عدد الأعضاء في الفريق</label>
                <input type="number" name="team_members_count" id="team_members_count" class="form-control" value="{{ old('team_members_count', $stadium->team_members_count) }}" required>
                @error('team_members_count')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="avilableservice_ids[]">{{ trans('Counters_trans.avilableservice') }}</label>
                <select name="avilableservice_ids[]" class="form-control form-select" id="amenity-select" multiple>
                    @foreach ($amenities as $category)
                        <option value="{{ $category->id }}"
                                {{ in_array($category->id, old('avilableservice_ids', $stadium->avilableservice->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ App::getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <script>
                $(document).ready(function() {
                    $('#amenity-select').select2({
                        placeholder: '{{ trans('Counters_trans.amenity') }}',
                        allowClear: true,
                        closeOnSelect: false
                    });
                });
            </script>

            <button type="submit" class="btn btn-success">تحديث الملعب</button>
        </form>
    </div>
@endsection
