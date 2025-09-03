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
    public function test_display_profile(){
        // コントローラーの処理
        $response = $this->actingAs($this->user)
            ->get(route('mypage.index'));

        $contents = $response->content();

        // プロフィール画像
        $this->assertStringContainsString('storage/img/noImage.png',$contents);

        // ユーザー名
        $this->assertStringContainsString($this->user->name,$contents);
    }

    public function test_display_sell_items(){
        // コントローラーの処理
        $response = $this->actingAs($this->user)
        ->get(route('mypage.index',['tab' => 'sell']));

        // 出品者がログインユーザーの商品を配列に格納
        $dbSellItems = [];
        foreach($this->items->toArray() as $item){
            if($this->user->id == $item['user_id']){
                array_push($dbSellItems, $item);
            }
        }

        // 画面に出品した商品が表示されていることを確認する
        $contents = $response->content();

        foreach($dbSellItems as $item){
            $this->assertStringContainsString($item['item_name'], $contents);
        }
    }

    public function test_display_buy_items(){
        // 購入済み商品のデータ作成
        Purchase::create([
            'user_id' => $this->user->id,
            'item_id' => $this->items->last()->id,
            'post' => $this->user->profile->post,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building
        ]);

        // コントローラーの処理
        $response = $this->actingAs($this->user)
        ->get(route('mypage.index',['tab' => 'buy']));

        // 画面に購入した商品が表示されていることを確認する
        $contents = $response->content();

        $this->assertStringContainsString($this->items->last()->item_name, $contents);

    }
}
