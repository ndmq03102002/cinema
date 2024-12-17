@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('rated.store');
        $title = 'Thêm mới nhãn';
        $rated = null; // Đảm bảo biến $rated không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('rated.update', $rated->id);
        $title = 'Cập nhật nhãn';
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
                    <div class="panel-title">Thông tin nhãn</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của nhãn mới</p>
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
                                    <label for="name" class="control-label text-left">Name <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        id="slug"
                                        value="{{ old('name', $rated->name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên nhãn"
                                        autocomplete="off"
                                        onkeyup="ChangeToSlug()"
                                        autofocus
                                    >
                                    @if($errors->has('name'))
                                        <p class="error-message">* {{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="description" class="control-label text-left">Description</label>
                                    <textarea 
                                        type="text"
                                        name="description"
                                        id="description"
                                        
                                        class="form-control"
                                        placeholder="Nhập mô tả nhãn"
                                        autocomplete="off"
                                        stlye = "resize: none;"
                                        rows = "6"
                                    >{{ old('description', $rated->description ?? '') }}</textarea>
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