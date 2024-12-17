<?php

return [
    'success' => [
        'success' => 'Thành công.',
        'required_login' => 'Chưa đăng nhập',
        'cinema' => [
            'create' => 'Tạo rạp chiếu thành công!',
            'edit' => 'Sửa rạp chiếu thành công!',
            'delete' => 'Xóa rạp chiếu thành công!',
            'get' => 'Lấy rạp chiếu thành công!'
        ],
        'users' => [
            'create' => 'Tạo người dùng thành công!',
            'edit' => 'Sửa người dùng thành công!',
            'delete' => 'Xóa người dùng thành công!',
            'lock' => 'Khóa người dùng thành công!',
            'unlock' => 'Mở khóa người dùng thành công!',
            'change_password' => 'Đổi mật khẩu người dùng thành công!',
            'check_mail' => 'Vui lòng kiểm tra mail để xác thực mã code!',
            'login_success' => 'Đăng nhập thành công!',
            'forgot_password' => 'Quên mật khẩu thành công, vui lòng kiểm tra Email!',
            'resend_code' => 'Gửi lại mã code thành công!',
            'confirm_success' => 'Xác thực thành công!',
        ],
    ],
    'errors' => [
        'ticket' => [
            'time_out' => 'Hết hạn giữ chỗ',
        ],
        'cinema' => [
            'create' => 'Tạo rạp chiếu không thành công!',
            'edit' => 'Sửa rạp chiếu không thành công!',
            'delete' => 'Xóa rạp chiếu không thành công!',
            'get' => 'Lấy rạp chiếu không thành công!',
            'id_found' => 'Không tồn tại id rạp chiếu!',
        ],
        'room' => [
            'exist' => 'Phòng chiếu đã tồn tại!',
        ],
        'seat_type' => [
            'create' => 'Tạo loại ghế không thành công!',
            'edit' => 'Sửa loại ghế không thành công!',
            'delete' => 'Xóa loại ghế không thành công!',
            'get' => 'Lấy loại ghế không thành công!',
            'id_found' => 'Không tồn tại id loại ghế!',
        ],
        'show_time' => [
            'exist' => 'Đã tồn tại suất chiếu trong thời gian này',
        ],
        'errors' => 'Thất bại!',
        'not_found' => 'Bản ghi không tồn tại!',
        'users' => [
            'create' => 'Tạo người dùng không thành công!',
            'edit' => 'Sửa người dùng không thành công!',
            'delete' => 'Xóa người dùng không mục thành công!',
            'not_found' => 'Người dùng không tồn tại!',
            'code' => 'Mã code đã hết hạn hoặc không tồn tại!',
            'email_not_found' => 'Email không tồn tại!',
            'account_not_active' => 'Tài khoản chưa được kích hoạt!',
            'password_not_correct' => 'Mật khẩu không chính xác!',
            'password_old_not_correct' => 'Mật khẩu cũ không chính xác!',
        ],
        'address' => [
            'create' => 'Tạo địa chỉ không thành công!',
            'edit' => 'Sửa địa chỉ không thành công!',
            'delete' => 'Xóa địa chỉ không mục thành công!',
            'not_found' => 'Địa chỉ không tồn tại!',
        ],
        'image' => [
            'not_available' => 'Ảnh không hợp lệ!',
            'required' => 'Vui lòng lựa chọn ảnh cho sản phẩm!',
        ],
        'date' => [
            'not_available' => 'Ngày không hợp lệ!',
        ],
        'rules' => [
            'required'  => ':attribute là trường bắt buộc.',
            'string'    => ':attribute phải là một chuỗi ký tự.',
            'in'        => 'Giá trị của trường :attribute phải là một trong các giá trị sau: :value',
            'not_in'    => ':attribute đã chọn không hợp lệ.',
            'min'       => ':attribute phải có ít nhất :value ký tự.',
            'url'       => ':attribute phải là địa chỉ URL hợp lệ.',
            'max'       => ':attribute không được vượt quá :value ký tự.',
            'integer'   => ':attribute phải là số nguyên.',
            'mimes'     => ':attribute phải có định dạng là: :value',
            'email'     => ':attribute phải có định dạng email.',
            'unique'    => ':attribute đã tồn tại.',
            'json'      => ':attribute phải là một chuỗi JSON hợp lệ.',
            'image'     => ':attribute phải là hình ảnh.',
            'array'     => 'Trường :attribute phải là một dãy giá trị.',
            'boolean'   => 'Trường :attribute phải có thể chuyển đổi thành true hoặc false.',
            'regex'     => ':attribute không đúng định dạng.',
            'exist'     => ':attribute không tồn tại.',
            'same'      => ':attribute và :other phải giống nhau.',
            'uploaded'  => 'Không thể tải tệp lên.',
            'numeric'   => ':attribute phải là số.',
            'after'     => ':attribute phải lớn hơn :value.',
            'date'        => 'Trường :attribute phải là ngày hợp lệ.',
            'date_format' => 'Trường :attribute phải theo định dạng :value.'
        ],
        'promotion' => [
            'not_found' => 'Khuyến mãi không tồn tại',
            'percent-min' => 'Giảm giá nhỏ nhất bằng 0%',
            'percent-max' => 'Giảm giá lớn nhất bằng 100%',
            'max_discount-min' => 'Giá tiền nhỏ nhất bằng 0',
        ],
        'notification'=>[
            'not_found' => 'Thông báo không tồn tại!'
        ]
    ]
];
