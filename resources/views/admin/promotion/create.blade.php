@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('promotion.store');
        $title = 'Thêm mới khuyến mãi';
        $promotion = null; // Đảm bảo biến $promotion không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('promotion.update', $promotion->id);
        $title = 'Cập nhật khuyến mãi';
        $method = 'PUT';
    }
@endphp

@include('admin.dashboard.component.breadcrumb', ['title' => $title])
    <form action="{{$url}}" method="POST" enctype = "multipart/form-data">
    @csrf
    @method($method)
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin khuyến mãi</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của khuyến mãi mới</p>
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
                                    <label for="name" class="control-label text-left"> Name <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="promo_name"
                                        id="promo_name"
                                        value="{{ old('promo_name', $promotion->promo_name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên khuyến mãi"
                                        autocomplete="off"
                                        autofocus
                                    >
                                    @if($errors->has('promo_name'))
                                        <p class="error-message">* {{ $errors->first('promo_name') }}</p>
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
                                        placeholder="Nhập mô tả khuyến mãi"
                                        autocomplete="off"
                                        stlye = "resize: none;"
                                        rows = "6"
                                    >{{ old('description', $promotion->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="start_date" class="control-label text-left">Start_date <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="date"
                                        name="start_date"
                                        id="start_date"
                                        value="{{ old('start_date', $promotion->start_date ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('start_date'))
                                        <p class="error-message">* {{ $errors->first('start_date') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="end_date" class="control-label text-left">End_date <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="date"
                                        name="end_date"
                                        id="end_date"
                                        value="{{ old('end_date', $promotion->end_date ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('end_date'))
                                        <p class="error-message">* {{ $errors->first('end_date') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="Discount" class="control-label text-left">Discount <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="integer"
                                        name="discount"
                                        id="discount"
                                        value="{{ old('discount', $promotion->discount ?? '') }}"
                                        class="form-control"
                                        placeholder="10000"
                                        autocomplete="off"
                                    >
                                    @if($errors->has('discount'))
                                        <p class="error-message">* {{ $errors->first('discount') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="quantity" class="control-label text-left">Quantity <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="integer"
                                        name="quantity"
                                        id="quantity"
                                        value="{{ old('quantity', $promotion->quantity ?? '') }}"
                                        class="form-control"
                                        placeholder="10"
                                        autocomplete="off"
                                    >
                                    @if($errors->has('quantity'))
                                        <p class="error-message">* {{ $errors->first('quantity') }}</p>
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
                                    @if(isset($promotion))                                   
                                            <img src="{{ $promotion->avatar }}" alt="{{ $promotion->name }}" width="100" />
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="status" class="control-label text-left">Active <span class="text-danger">(*)</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ isset($promotion) && $promotion->status == '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ isset($promotion) && $promotion->status == '0' ? 'selected' : '' }}>Không</option>
                                    </select>                                      
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