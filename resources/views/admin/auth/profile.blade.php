@extends('admin.dashboard.layout')
@section('content')

@include('admin.dashboard.component.breadcrumb', ['title' => 'Cập nhật profile'])

<form action="{{ route('auth.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('POST') {{-- Luôn dùng POST cho cả create và update --}}
    <div class="wrapper wrapper-content animated fadeInRight">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin Profile</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của Profile</p>
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
                                        id="name"
                                        value="{{ old('name', isset($profile) ? $profile->name : '') }}"
                                        class="form-control"
                                        placeholder="Nhập họ và tên"
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
                                    <label for="age" class="control-label text-left">Age<span class="text-danger">(*)</span></label>
                                    <input
                                        type="text"
                                        name="age"
                                        id="age"
                                        value="{{ old('age', isset($profile) ? $profile->age : '') }}"
                                        class="form-control"
                                        placeholder="Nhập tuổi"
                                        autocomplete="off"
                                    >
                                    @if($errors->has('age'))
                                        <p class="error-message">* {{ $errors->first('age') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="phone_number" class="control-label text-left">PhoneNumber <span class="text-danger">(*)</span></label>
                                    <input
                                        type="text"
                                        name="phone_number"
                                        id="phone_number"
                                        value="{{ old('phone_number', isset($profile) ? $profile->phone_number : '') }}"
                                        class="form-control"
                                        placeholder="Nhập số điện thoại"
                                        autocomplete="off"
                                    >
                                    @if($errors->has('phone_number'))
                                        <p class="error-message">* {{ $errors->first('phone_number') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row mt-3" >
                                    <label for="avatar" class="control-label text-left">Avatar</label>
                                    <input type="file" name="avatar" id="avatar" class="form-control">
                                    @if(isset($profile) && $profile->avatar)
                                        <img src="{{ $profile->avatar }}" alt="Avatar" style="max-width: 100px; margin-top: 10px;">
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