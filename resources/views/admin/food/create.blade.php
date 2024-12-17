@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('food.store');
        $title = 'Thêm mới món ăn';
        $food = null; // Đảm bảo biến $food không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('food.update', $food->id);
        $title = 'Cập nhật món ăn';
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
                    <div class="panel-title">Thông tin món ăn</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của món ăn mới</p>
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
                                        name="name"
                                        id="name"
                                        value="{{ old('name', $food->name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên món ăn"
                                        autocomplete="off"
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
                                        placeholder="Nhập mô tả món ăn"
                                        autocomplete="off"
                                        stlye = "resize: none;"
                                        rows = "6"
                                    >{{ old('description', $food->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="price" class="control-label text-left">Price <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="integer"
                                        name="price"
                                        id="price"
                                        value="{{ old('price', $food->price ?? '') }}"
                                        class="form-control"
                                        placeholder="..."
                                        autocomplete="off"
                                    >
                                    @if($errors->has('price'))
                                        <p class="error-message">* {{ $errors->first('price') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row mt-3" >
                                    <label for="image" class="control-label text-left">Image <span class="text-danger">(*)</span></label>
                                    <input type="file" name="image" id="image" class="form-control">
                                    @if($errors->has('image'))
                                        <p class="error-message">* {{ $errors->first('image') }}</p>
                                    @endif
                                    @if(isset($food))                                   
                                            <img src="{{ $food->image }}" alt="{{ $food->name }}" width="100" />
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="status" class="control-label text-left">Active <span class="text-danger">(*)</span></label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ isset($food) && $food->status == '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ isset($food) && $food->status == '0' ? 'selected' : '' }}>Không</option>
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