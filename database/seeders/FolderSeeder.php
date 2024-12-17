<?php

namespace Database\Seeders;

use App\Models\Folder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Folder::create([
            'name' => 'Hình ảnh đã upload',
            'parent_id' => 0,
            'slug' => Str::slug('Hình ảnh đã upload'),
        ]);
        Folder::create([
            'name' => 'Thư viện ảnh',
            'parent_id' => 0,
            'slug' => Str::slug('Thư viện ảnh'),
        ]);
        Folder::create([
            'name' => 'Hình ảnh cho khách hàng, đối tác',
            'parent_id' => 0,
            'slug' => Str::slug('Hình ảnh cho khách hàng, đối tác'),
        ]);
        Folder::create([
            'name' => 'Hình ảnh cho slider',
            'parent_id' => 0,
            'slug' => Str::slug('Hình ảnh cho slider'),
        ]);
    }
}
