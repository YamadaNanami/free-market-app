<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellControllerTest extends TestCase
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
            CategoriesTableSeeder::class
        ]);

        // 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
    }

    public function test_store_item(){
        $response = $this->actingAs($this->user)
            ->get('/sell');
        $response->assertStatus(200);

        $requestData = [
            'user_id' => $this->user->id,
            'item_name' => 'テスト商品',
            'brand_name' => 'brand',
            'price' => 100,
            'categories' => [1,2],
            'description' => '商品説明です',
            'condition' => 1,
        ];

        Storage::fake('public');
        // テスト用ダミーを作成
        Storage::disk('public')->put('img/temp/noImage.png', 'dummy');

        $response = $this->withSession(['itemImg' => 'noImage.png'])
            ->actingAs($this->user)
            ->post('/sell', $requestData);

        $response->assertRedirect('mypage');

        //DBへの登録テスト
        $this->assertDatabaseHas('items', [
            'condition' => 1,
            'item_name' => 'テスト商品',
            'description' => '商品説明です',
            'price' => 100,
        ]);

        $item = Item::where('item_name', 'テスト商品')->first();
        $this->assertDatabaseHas('category_item', [
                'item_id'     => $item->id,
                'category_id' => 1,
            ]);
        $this->assertDatabaseHas('category_item', [
                'item_id'     => $item->id,
                'category_id' => 2,
            ]);

    }

}
