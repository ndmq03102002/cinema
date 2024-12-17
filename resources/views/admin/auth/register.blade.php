<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cinema Register</title>

    <link href="backend/css/bootstrap.min.css" rel="stylesheet">
    <link href="backend/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="backend/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="backend/css/animate.css" rel="stylesheet">
    {{-- <link href="backend/css/style.css" rel="stylesheet"> --}}
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

        .logo-name12 { /* Đổi tên class từ logo-name thành logo-name12 */
            color: #DAA520;
            font-size: 11vh;
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

<body class="gray-bg">

    <div class="middle-box text-center loginscreen   animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name12">CINEMA</h1>

            </div>
            <h3>Register to CinemaEasy</h3>
            <p>Create account to see it in action.</p>
            <form class="m-t" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="UserName" name="username" value="{{ old('username') }}">
                    @if($errors->has('username'))
                        <p class="error-message">* {{ $errors->first('username') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" name="name" value="{{ old('name') }}">
                    @if($errors->has('name'))
                        <p class="error-message">* {{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone Number" name="phone_number" value="{{ old('phone_number') }}">
                    @if($errors->has('phone_number'))
                        <p class="error-message">* {{ $errors->first('phone_number') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}">
                    @if($errors->has('email'))
                        <p class="error-message">* {{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}">
                    @if($errors->has('password'))
                        <p class="error-message">* {{ $errors->first('password') }}</p>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Register</button>
                <br>
                <br>

                <p class="text-muted text-center"><small>Already have an account?</small></p>
                <a  href="{{route('auth.login')}}">Login</a>
            </form>
            <p class="m-t"> <small>© 2024 Cinema</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="backend/js/jquery-3.1.1.min.js"></script>
    <script src="backend/js/bootstrap.min.js"></script>
    <script src="backend/js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
    
</body>

</html>