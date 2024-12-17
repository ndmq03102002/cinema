@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('cinema.create');
        $title = 'Thêm chi nhánh mới';
        $cinema = null; // Đảm bảo biến $category không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('cinema.update', $cinema->id);
        $title = 'Cập nhật chi nhánh';
        $method = 'PUT';
    }
@endphp

@include('admin.dashboard.component.breadcrumb', ['title' => $title])
<form action="{{$url}}" method="POST" id="cinema-form" enctype = "multipart/form-data">
    @csrf
    @method($method)
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin danh mục</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của danh mục mới</p>
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
                                        value="{{ old('name', $cinema->name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên danh mục"
                                        autocomplete="off"
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
                                    <label for="address" class="control-label text-left">Address <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="address"
                                        id="address"
                                        class="form-control"
                                        value="{{ old('address', $cinema->address ?? '')}}"
                                        placeholder="Nhập địa chỉ chi nhánh"
                                        autocomplete="off"
                                        style="resize: none;"
                                       
                                    ></input>
                                    @if($errors->has('address'))
                                        <p class="error-message">* {{ $errors->first('address') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="latitude" class="control-label text-left">Latitude <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="latitude"
                                        id="latitude"
                                        class="form-control"
                                        value="{{ old('address', $cinema->latitude ?? '')}}"
                                        placeholder="Nhập Latitude"
                                        autocomplete="off"
                                        style="resize: none;"
                                       
                                    ></input>
                                    @if($errors->has('latitude'))
                                        <p class="error-message">* {{ $errors->first('latitude') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="longitude" class="control-label text-left">Longitude <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="longitude"
                                        id="longitude"
                                        class="form-control"
                                        value="{{ old('address', $cinema->longitude ?? '')}}"
                                        placeholder="Nhập longitude"
                                        autocomplete="off"
                                        style="resize: none;"
                                       
                                    ></input>
                                    @if($errors->has('longitude'))
                                        <p class="error-message">* {{ $errors->first('longitude') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row mt-3" >
                                    <label for="avatar" class="control-label text-left">Avatar <span class="text-danger">(*)</span></label>
                                    <input type="file" name="avatar" id="avatar" class="form-control">
                                    @if($errors->has('avatar'))
                                        <p class="error-message">* {{ $errors->first('avatar') }}</p>
                                    @endif
                                    @if(isset($cinema))                                   
                                            <img src="{{ $cinema->avatar }}" alt="{{ $cinema->name }}" width="100" />
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

@push('scripts')
<script>
    // Lắng nghe sự kiện submit của form
    document.getElementById('cinema-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Ngừng hành động submit mặc định

        let form = this;
        let formData = new FormData(form);

        // Gửi yêu cầu AJAX
        fetch(form.action, {
            method: form.method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                // Thông báo thành công và chuyển hướng
                alert(data.message); // Hiển thị thông báo
                window.location.href = "{{ route('cinema.store') }}"; // Điều hướng tới URL "cinema/store"
            } else {
                // Xử lý nếu có lỗi
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại!');
        });
    });
</script>
@endpush
