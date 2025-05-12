<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $items;

    protected function setUp(): void
    {
        parent::setUp();

        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // シードデータの作成
        $this->seed([
            ProfilesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class,
            CategoryItemTableSeeder::class,
        ]);

        // 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->items = Item::with('categories')->get();
    }

    /* No.13 */
    public function test_view_profile(){
        // コントローラーの処理
        $response = $this->actingAs($this->user)
            ->get(route('mypage.index'));
        $response->assertViewIs('mypage');

        $userInfo = $response['user'];
        $userImg = $userInfo['profile']['img_url'];
        $userName = $userInfo['name'];

        // プロフィール画像
        $this->assertEquals($userImg, $this->user->profile->img_url);

        // ユーザー名
        $this->assertEquals($userName, $this->user->name);
    }

    public function test_sell_items(){
        // コントローラーの処理
        $response = $this->actingAs($this->user)
        ->get(route('mypage.index',['tab' => 'sell']));
        $response->assertStatus(200);

        $sellItems = [];
        foreach($response['items'] as $item){
            $sellItems = [
                'id' => $item['id'],
                'user_id' => $item['user_id'],
                'price' => $item['price'],
                'condition' =>  $item['condition']
            ];
        }

        $dbSellItems = [];
        foreach($this->items->toArray() as $item){
            if($this->user->id == $item['user_id']){
                array_push($dbSellItems, $item);
            }
        }

        $this->assertEquals(
            sort($sellItems),
            sort($dbSellItems)
        );
    }

    public function test_buy_items(){
        // 購入済み商品のデータ作成
        $buyItem = Purchase::make([
            'user_id' => $this->user->id,
            'item_id' => $this->items->first()->id
        ]);

        // コントローラーの処理
        $response = $this->actingAs($this->user)
        ->get(route('mypage.index',['tab' => 'buy']));
        $response->assertViewIs('mypage');

        $dbBuyItems = [];
        foreach($this->items as $item){
            if($item['user_id'] == $this->user->id){
                array_push($dbBuyItems, $item);
            }
        }

        $this->assertEquals($buyItem['item_name'], $dbBuyItems['item_name']);

    }
}
