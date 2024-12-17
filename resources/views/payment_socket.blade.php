<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.7/bootstrap-notify.min.js"></script>
</head>
<body>
<h1>Thông báo</h1>
<main class="login-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-header text-center">Login</h3>
                    <div class="card-body">
                        {{--                            <form method="POST" action="{{ route('login') }}">--}}
                        {{--                                @csrf--}}
                        {{--                                <div class="form-group mb-3">--}}
                        {{--                                    <input type="text" placeholder="Email" id="email" class="form-control" name="email" required--}}
                        {{--                                           autofocus>--}}
                        {{--                                    @if ($errors->has('email'))--}}
                        {{--                                        <span class="text-danger">{{ $errors->first('email') }}</span>--}}
                        {{--                                    @endif--}}
                        {{--                                </div>--}}
                        {{--                                <div class="form-group mb-3">--}}
                        {{--                                    <input type="password" placeholder="Password" id="password" class="form-control" name="password" required>--}}
                        {{--                                    @if ($errors->has('password'))--}}
                        {{--                                        <span class="text-danger">{{ $errors->first('password') }}</span>--}}
                        {{--                                    @endif--}}
                        {{--                                </div>--}}
                        {{--                                <div class="form-group mb-3">--}}
                        {{--                                    <div class="checkbox">--}}
                        {{--                                        <label>--}}
                        {{--                                            <input type="checkbox" name="remember"> Remember Me--}}
                        {{--                                        </label>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="d-grid mx-auto">--}}
                        {{--                                    <button type="submit" class="btn btn-dark btn-block">Signin</button>--}}
                        {{--                                </div>--}}
                        {{--                            </form>--}}
                    </div>
                    <button onclick="disconnect()">hihi</button>

                    {{--                    <fb:login-button--}}
                    {{--                        scope="public_profile,email"--}}
                    {{--                        onlogin="checkLoginState();">--}}
                    {{--                    </fb:login-button>--}}
                    {{--                    <div class="flex items-center justify-end mt-4">--}}
                    {{--                        <a class="ml-1 btn btn-primary" href="{{ url('redirect') }}" style="margin-top: 0px !important;background: #4c6ef5;color: #ffffff;padding: 5px;border-radius:7px;" id="btn-fblogin">--}}
                    {{--                            <i class="fa fa-facebook-square" aria-hidden="true"></i> Login with Facebook--}}
                    {{--                        </a>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

<script>
    const socket = io('http://127.0.0.1:3000'); // Adjust the port accordingly
    const room = 285620;
    socket.emit('joinRoom', room);
    socket.on('booking-channel:Booking', function (data) {
        console.log(data);
        $.notify({
            message: data.seats,
        }, {
            // settings
            delay: 0,
            type: 'danger',
        });
    });

    function disconnect() {
        socket.emit('leave', room);
        socket.disconnect();
    }
</script>

</body>
</html>
