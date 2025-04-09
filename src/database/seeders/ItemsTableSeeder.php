<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userIds = User::pluck('id');

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 5,
            'img_url' => '/storage/app/public/item_img/Armani+Mens+Clock.jpg',
            'item_name' => '腕時計',
            'brand_name' => Str::random(10),
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 2,
            'img_url' => '/storage/app/public/item_img/HDD+Hard+Disk.jpg',
            'item_name' => 'HDD',
            'brand_name' => Str::random(10),
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
            'condition' => 2
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 10,
            'img_url' => '/storage/app/public/item_img/iLoveIMG+d.jpg',
            'item_name' => '玉ねぎ3束',
            'brand_name' => Str::random(10),
            'price' => 300,
            'description' => '新鮮な玉ねぎ3束のセット',
            'condition' => 3
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 1,
            'img_url' => '/storage/app/public/item_img/Leather+Shoes+Product+Photo.jpg',
            'item_name' => '革靴',
            'brand_name' => Str::random(10),
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
            'condition' => 4
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 2,
            'img_url' => '/storage/app/public/item_img/Living+Room+Laptop.jpg',
            'item_name' => 'ノートPC',
            'brand_name' => Str::random(10),
            'price' => 45000,
            'description' => '高性能なノートパソコン',
            'condition' => 1
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 2,
            'img_url' => '/storage/app/public/item_img/Music+Mic+4632231.jpg',
            'item_name' => 'マイク',
            'brand_name' => Str::random(10),
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
            'condition' => 2
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 4,
            'img_url' => '/storage/app/public/item_img/Purse+fashion+pocket.jpg',
            'item_name' => 'ショルダーバッグ',
            'brand_name' => Str::random(10),
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
            'condition' => 3
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 10,
            'img_url' => '/storage/app/public/item_img/Tumbler+souvenir.jpg',
            'item_name' => 'タンブラー',
            'brand_name' => Str::random(10),
            'price' => 500,
            'description' => '使いやすいタンブラー',
            'condition' => 4
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 10,
            'img_url' => '/storage/app/public/item_img/Waitress+with+Coffee+Grinder.jpg',
            'item_name' => 'コーヒーミル',
            'brand_name' => Str::random(10),
            'price' => 4000,
            'description' => '手動のコーヒーミル',
            'condition' => 1
        ];
        DB::table('items')->insert($param);

        $param = [
            'user_id' => $userIds[mt_rand(0,count($userIds)-1)],
            'category_id' => 4,
            'img_url' => '/storage/app/public/item_img/MakeUp.jpg',
            'item_name' => 'メイクセット',
            'brand_name' => Str::random(10),
            'price' => 2500,
            'description' => '便利なメイクアップセット',
            'condition' => 2
        ];
        DB::table('items')->insert($param);
    }
}
