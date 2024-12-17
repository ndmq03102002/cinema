<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WeeklyTicketPrice extends Model
{
    use HasFactory, SoftDeletes;

    // Đặt tên bảng nếu tên bảng không theo quy tắc Laravel
    protected $table = 'ci_weekly_ticket_prices';

    // Các cột có thể được gán hàng loạt
    protected $fillable = [
        'name',
        'description',
        'extra_fee',
        'start_time',
    ];
}
