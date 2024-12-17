
@php
    // Xác định route và title dựa trên config['method']
    if ($config['method'] == 'create') {
        $url = route('user.store');
        $title = $config['seo']['create']['title'];
    } else {
        $url = route('user.update', $user->id);
        $title = $config['seo']['edit']['title'];
    }
@endphp

@include('dashboard.component.breadcrumb', ['title' => $title])

<form action="{{$url}}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Nhập thông tin chung của người sử dụng</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Email <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="email"
                                        value="{{ old('email', ($user->email) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                    @if($errors->has('email'))
                                            <p class="error-message">* {{$errors->first('email')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Username<span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="username"
                                        value="{{ old('username', ($user->username) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                    @if($errors->has('username'))
                                            <p class="error-message">* {{$errors->first('username')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @php
                            $userCatalogue = [
                                ''=>'[Chọn nhóm thành viên]',
                                'admin'=>'Admin',
                                'customer'=>'Customer',
                            ];          
                        @endphp
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Nhóm Thành viên <span class="text-danger">(*)</span></label>
                                    <select name="role" class="form-control setupSelect2">
                                        @foreach($userCatalogue as $key => $item)
                                        <option {{ 
                                            $key == old('role', (isset($user->role)) ? $user->role : '') ? 'selected' : '' 
                                            }}  value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('role'))
                                            <p class="error-message">* {{$errors->first('role')}}</p>
                                    @endif
                                </div>
                            </div>   
                            @if($config['method'] == 'create')
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mật khẩu <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="password"
                                        name="password"
                                        value=""
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                    @if($errors->has('password'))
                                            <p class="error-message">* {{$errors->first('password')}}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <hr> --}}
        
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>