<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payment::create([
            'name' => 'Ví ShopeePay',
            'priority' => 0,
            'key' => 'ShopeePay',
            'description' => 'Điện thoại bạn phải được cài đặt ứng dụng ShoppePay',
            'promotion_content' => 'Nhập mã TCB80K-giảm 80K cho đơn vé xe từ 500K',
            'status' => 1,
            'logo' => 'payment/airpay.svg'
        ]);
    }
}
