<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->string('chi_nhanh')->nullable()->after('email'); // Thay 'column_name' bằng tên cột muốn thêm sau, hoặc bỏ đi nếu muốn thêm cuối.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ci_account', function (Blueprint $table) {
            $table->dropColumn('chi_nhanh');
        });
    }
};
