<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'category' => 'ファッション'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => '家電'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'インテリア'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'レディース'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'メンズ'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'コスメ'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => '本'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'ゲーム'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'スポーツ'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'キッチン'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'ハンドメイド'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'アクセサリー'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'おもちゃ'
        ];
        DB::table('categories')->insert($param);
        $param = [
            'category' => 'ベビー・キッズ'
        ];
        DB::table('categories')->insert($param);
    }
}