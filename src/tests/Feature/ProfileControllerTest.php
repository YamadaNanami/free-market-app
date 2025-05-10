<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // シードデータの作成
        $this->seed([
            ProfilesTableSeeder::class,
        ]);

        // 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
    }

    public function test_view_profile(){
        // コントローラー側の処理
        $response = $this->actingAs($this->user)
            ->get(route('profile.index'));

        // データ整形
        $responseData = [
            'img_url' => $response['profile']['profile']['img_url'],
            'name' => $response['profile']['name'],
            'post' => $response['profile']['profile']['post'],
            'address' => $response['profile']['profile']['address'],
            'building' => $response['profile']['profile']['building'],
        ];

        $dbData = [
            'img_url' => $this->user->profile->img_url,
            'name' => $this->user->name,
            'post' => $this->user->profile->post,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ];

        $this->assertEquals(
            $responseData,
            $dbData
        );
    }

}
