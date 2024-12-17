@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('room.store');
        $title = 'Thêm mới phòng chiếu';
        $method = 'POST';
    } else {
        $url = route('room.update', $room->id);
        $title = 'Cập nhật phòng chiếu';
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
                    <div class="panel-title">Thông tin phòng chiếu</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của phòng chiếu</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <!-- Tên phòng -->
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Tên phòng <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        id="name"
                                        value="{{ old('name', $room->name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên phòng"
                                        autocomplete="off"
                                    >
                                    @if($errors->has('name'))
                                        <p class="error-message">* {{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Cinema ID -->
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="cinema_id" class="control-label text-left">Chi nhánh (Cinema ID)  <span class="text-danger">(*)</span></label>
                                    @if ($check == 1)
                                    <select name="cinema_id" id="cinema_id" class="form-control">
                                        @foreach($cinema as $key => $value)
                                            <option value="{{ $key }}" {{ isset($room) && $room->cinema_id == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @else
                                        <input 
                                        type="text"
                                        name="cinema_id"
                                        value="{{ $cinema->id }}"
                                        hidden
                                        >
                                        <input 
                                            type="text"
                                            value="{{ $cinema->name }}"
                                            class="form-control"
                                            placeholder="..."
                                            autocomplete="off"
                                            readonly
                                        >
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Bản đồ ghế -->
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="seat_map" class="control-label text-left">Bản đồ ghế (VD: [[1,1,0],[1,1,1],[1,1,1]]  ) <span class="text-danger">(*)</span></label>
                                    <textarea 
                                        name="seat_map"
                                        id="seat_map"
                                        class="form-control"
                                        placeholder="Nhập bản đồ ghế (VD: [[1,1,0],[1,1,1],[1,1,1]])"
                                        rows="4"
                                    >{{ old('seat_map', $room->seat_map ?? '') }}</textarea>
                                    @if($errors->has('seat_map'))
                                        <p class="error-message">* {{ $errors->first('seat_map') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Danh sách ghế -->
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="seat_list" class="control-label text-left">Danh sách ghế (VD:  [
                                        {
                                          "seat_type_id": 1,
                                          "seat_code": "A1"
                                        },
                                        {
                                          "seat_type_id": 2,
                                          "seat_code": "A2"
                                        },
                                        {
                                          "seat_type_id": 1,
                                          "seat_code": "B1"
                                        },
                                        {
                                          "seat_type_id": 2,
                                          "seat_code": "B2"
                                        },
                                        {
                                          "seat_type_id": 3,
                                          "seat_code": "B3"
                                        },
                                        {
                                          "seat_type_id": 2,
                                          "seat_code": "C1"
                                        },
                                        {
                                          "seat_type_id": 2,
                                          "seat_code": "C2"
                                        },
                                        {
                                          "seat_type_id": 2,
                                          "seat_code": "C3"
                                        }
                                      ]  ) <span class="text-danger">(*)</span></label>
                                    <textarea 
                                        name="seat_list"
                                        id="seat_list"
                                        class="form-control"
                                        rows="6"
                                    >
                                        @if(isset($data))
                                            @php
                                                // Mảng seat_list mà bạn muốn hiển thị
                                                $seatList = [];
                                                foreach($data as $row) {
                                                    foreach ($row as $seat) {
                                                        $seatList[] = [
                                                            'seat_type_id' => $seat['seat_type_id'],
                                                            'seat_code' => $seat['seat_code']
                                                        ];
                                                    }
                                                }
                                                // Chuyển mảng thành JSON và giữ nguyên định dạng
                                                echo json_encode($seatList);
                                            @endphp
                                        @endif
                                    </textarea>
                                    @if($errors->has('seat_list'))
                                        <p class="error-message">* {{ $errors->first('seat_list') }}</p>
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
