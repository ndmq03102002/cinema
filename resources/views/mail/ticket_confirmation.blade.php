<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận vé xem phim</title>
</head>
<body>
    <p>Xin chào, {{ $username }} đến với rạp chiếu phim {{$cinema_name}}</p>
    <p>Bạn đã thanh toán thành công số tiền: {{ $total }}</p>
    <p>Đây là mã vé xem phim của bạn: {{ $ticketCode }}</p>
    <p>Tên phim: {{ $movie_name }}</p>
    <p>Phòng: {{ $room }}</p>
    <p>Thời gian bắt đầu chiếu: {{ $show_time }}</p>
    <p>Ghế đã đặt: {{ $seatList }}</p>
</body>
</html>
