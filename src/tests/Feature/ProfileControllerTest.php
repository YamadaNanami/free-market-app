<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    /* No.14 */
    public function test_display_default_profile(){
        // コントローラー側の処理
        $response = $this->actingAs($this->user)
            ->get(route('profile.index'));

        // データ整形
        $userInfo = [
            'name' => $this->user->name,
            'post' => $this->user->profile->post,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ];

        // ユーザーが登録したプロフィール情報が初期値として表示されているかを確認する
        $content = $response->content();

        $this->assertStringContainsString('noImage.png', $content);
        $this->assertStringContainsString($userInfo['name'], $content);
        $this->assertStringContainsString($userInfo['post'], $content);
        $this->assertStringContainsString($userInfo['address'], $content);
        $this->assertStringContainsString($userInfo['building'], $content);
    }

}
