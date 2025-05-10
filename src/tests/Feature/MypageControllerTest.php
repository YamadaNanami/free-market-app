<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PhpParser\Node\Stmt\Echo_;
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

    public function test_view_profile(){
        // コントローラーの処理
        $response = $this->actingAs($this->user)
            ->get(route('mypage.index'));
        $response->assertStatus(200);

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
        ->get(route('mypage.index',['page' => 'sell']));
        $response->assertStatus(200);
        $sellItems = [];
        foreach($response['items'] as $item){
            $sellItems = [
                'id' => $response['items']['id'],
                'user_id' => $response['items']['user_id'],
                'price' => $response['items']['price'],
                'condition' =>  $response['items']['condition']
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
        $items = $this->items->users()->attach($this->user->pluck('id')->toArray());
        dd($items);

        // コントローラーの処理
        $response = $this->actingAs($this->user)
        ->get(route('mypage.index',['page' => 'buy']));
        $response->assertStatus(200);

        dd($response['items']);
        $buyItems = [];
        foreach($response['items'] as $item){
            array_push($buyItems, $item['id']);
        }

        $dbBuyItems = [];
        foreach($items as $item){
            if($item['user_id'] == $this->user->id){
                array_push($dbBuyItems, $item['id']);
            }
        }

        $this->assertEquals($buyItems, $dbBuyItems);

    }
}
