<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Login</title>
    <link href="backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="backend/css/animate.css" rel="stylesheet">
    {{-- <link href="backend/css/style.css" rel="stylesheet">  --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: url("backend/anh_nen.jpg") no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            /* Thêm các thuộc tính Flexbox vào body */
            display: flex;
            justify-content: center; /* Căn giữa theo chiều ngang */
            align-items: center; /* Căn giữa theo chiều dọc */
            min-height: 100vh; /* Chiều cao tối thiểu bằng chiều cao màn hình */
        }

        .middle-box {
            max-width: 95%;
            width: auto;
            z-index: 100;
            margin: 0 auto; /* Không cần thiết nữa vì đã dùng flexbox */
            padding-top: 40px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
        }

        .logo-name12 {
            color: #DAA520;
            font-size: 11vh; /* Thay đổi từ 80px sang 15vw */
            font-weight: 800;
            margin-bottom: 20px;
            font-family: 'Bebas Neue', cursive;
        }

        h3 {
            color: #fff;
        }

        p {
            color: #ccc;
        }

        .form-control {
            background-color: #444;
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: #555;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #DAA520; /* Màu vàng đậm cho nút */
            border-color: #DAA520;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #B8860B; /* Màu vàng đậm hơn khi hover */
            border-color: #B8860B;
        }

        .error-message, .error1 {
            color: #dc3545; /* Màu đỏ cho thông báo lỗi */
        }

        a {
            color: #DAA520; /* Màu vàng đậm cho link */
        }

        a:hover {
            color: #B8860B;
        }
        /* Thêm font chữ nếu chưa có */
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&display=swap');
    </style>
</head>
<body >
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name12">CINEMA</h1> 
            </div>
            <h3>Welcome to CinemaEasy</h3>
            <p>Login to enjoy the latest movies.</p>
            <form class="m-t" role="form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" value={{old('username')}}>
                    @if($errors->has('username'))
                            <p class="error-message">* {{$errors->first('username')}}</p>
                    @endif
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password" >
                    @if($errors->has('password'))
                            <p class="error-message">* {{$errors->first('password')}}</p>
                        @endif
                </div>
                @if ($errors->has('login'))
                    <p class="error1">{{ $errors->first('login') }}</p>
                @endif
                <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                <br>
                <br>
                <a href="{{route('password.request')}}"><small>Forgot password?</small></a>
            
                <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a href="{{ route('auth.register') }}">Create an account</a> 
            </form>
            <br>
            <p class="m-t"> <small>© 2024 Cinema</small> </p>
        </div>
    </div>
    <script src="backend/js/jquery-3.1.1.min.js"></script>
    <script src="backend/js/bootstrap.min.js"></script>
    
</body>
</html>