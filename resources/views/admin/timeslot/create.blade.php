@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('timeslot.store');
        $title = 'Thêm mới timeslot';
        $year = null; // Đảm bảo biến $year không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('timeslot.update', $timeslot->id);
        $title = 'Cập nhật timeslot';
        $method = 'PUT';
    }
@endphp

@include('admin.dashboard.component.breadcrumb', ['title' => $title])
    <form action="{{$url}}" method="POST">
    @csrf
    @method($method)
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin TimeSlot</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của TimeSlot mới</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="slot_name" class="control-label text-left">Slot_name <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="slot_name"
                                        id="slot_name"
                                        value="{{ old('slot_name', $timeslot->slot_name ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('slot_name'))
                                        <p class="error-message">* {{ $errors->first('slot_name') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="start_time" class="control-label text-left">Start_time <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="start_time"
                                        id="start_time"
                                        value="{{ old('start_time', $timeslot->start_time ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('start_time'))
                                        <p class="error-message">* {{ $errors->first('start_time') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="end_time" class="control-label text-left">End_time <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="end_time"
                                        id="end_time"
                                        value="{{ old('end_time', $timeslot->end_time ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('end_time'))
                                        <p class="error-message">* {{ $errors->first('end_time') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="extra_fee" class="control-label text-left">Extra_fee <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="extra_fee"
                                        id="extra_fee"
                                        value="{{ old('extra_fee', $timeslot->extra_fee ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('extra_fee'))
                                        <p class="error-message">* {{ $errors->first('extra_fee') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>


@endsection