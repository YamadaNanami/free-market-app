<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $items;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // シードデータの作成
        $this->seed([
            DatabaseSeeder::class
        ]);

        // 外部キー制約を有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->items = Item::with('categories')->get();
    }


    /* No.4 */
    public function test_get_items_all(){
        $expectedItems = $this->items;
        $response = $this->get(route('top.index'));

        $contents = $response->content();

        foreach($expectedItems as $item){
            $this->assertStringContainsString($item->item_name, $contents);
        }
    }

    public function test_item_get_sold(){
        //購入された商品データを作成する
        $item = $this->items->last();
        Purchase::insert([
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'post' => $this->user->profile->post,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ]);

        $response = $this->actingAs($this->user)->get('/');

        $contents = $response->content();
        $this->assertStringContainsString($item->item_name . '　Sold', $contents);
    }

    public function test_items_not_get_sell(){
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);

        // 自分が出品していないアイテムを取得
        $notSellItems = Item::whereNotIn('user_id', [$this->user->id])->get();

        $contents = $response->content();

        foreach($notSellItems as $item){
            $this->assertStringContainsString($item->item_name, $contents);
        }
    }

    /* No.5 */
    public function test_get_mylist(){
        // ダミーデータとしていいねした商品データを1件作成する
        DB::table('item_user_like')->insert([
            'item_id' => $this->items->first()->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/?page=mylist');

        $response->assertStatus(200);

        $contents = $response->content();

        // // いいねしたアイテムが表示されているかをテストする
        $this->assertStringContainsString($this->items->first()->item_name, $contents);

        // いいねしたアイテム以外が表示されていないかをテストする
        foreach($this->items as $item){
            if($item->id != $this->items->first()->id){
                $this->assertStringNotContainsString($item->item_name, $contents);
            }
        }
    }

    public function test_check_sold_item_in_mylist(){
        // ダミーデータとしていいねがある＆購入済みの商品データを1件作成する
        $item = $this->items->first();

        DB::table('item_user_like')->insert([
            'item_id' => $item->id,
            'user_id' => $this->user->id
        ]);

        Purchase::insert([
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'post' => $this->user->profile->post,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/?page=mylist');

        $response->assertStatus(200);

        $contents = $response->content();

        // ダミーデータとしていいねがある＆購入済みの商品に「Sold」ラベルが表示されるかをテストする
        $this->assertStringContainsString($item->item_name . '　Sold', $contents);
    }

    public function test_not_get_sell_item_in_mylist(){
        // ダミーデータとしていいねした商品データを2件作成する
        $item1 = $this->items->first();
        $item2 = $this->items->last();

        DB::table('item_user_like')->insert([
            [
            'item_id' => $item1->id,
            'user_id' => $this->user->id
            ],[
                'item_id' => $item2->id,
                'user_id' => $this->user->id
            ]]
        );

        // 上記の商品のうち、1件をログインユーザーが出品した商品とする
        Item::find($item1->id)->update([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->get('/?page=mylist');

        $response->assertStatus(200);

        // 自分が出品していないアイテムを取得
        $contents = $response->content();

        // いいねした商品のうち、ログインユーザーが出品した商品が表示されていないかテストする
        $this->assertStringNotContainsString($item1->item_name, $contents);
        // いいねした商品のうち、他ユーザーが出品した商品が表示されているかテストする
        $this->assertStringContainsString($item2->item_name, $contents);
    }

    public function test_not_display_item_in_mylist_for_not_login(){
        $this->get('/?page=mylist')
            ->assertDontSee('<ul class="item-list">')
            ->assertStatus(200);
    }

    /* No.6 */
    public function test_search_items_in_reclist(){
        $response = $this->followingRedirects()
            ->get(route('search', ['keyword' => '靴']));

        // 検索欄と同じ条件で取得できる商品データ(商品名：革靴)
        // 取得できるのは1件なので、first()を使用
        $searchItem = Item::where('item_name', 'like','%'. '靴'. '%')
            ->first();

        // 画面上で表示されない商品
        $notSearchItems = Item::where('item_name', 'Not Like','%'. '靴'. '%')
            ->get();

        $contents = $response->content();

        $this->assertStringContainsString($searchItem->item_name, $contents);

        foreach($notSearchItems as $item){
            $this->assertStringNotContainsString($item->item_name, $contents);
        }
    }

    public function test_search_items_in_mylist(){
        $response = $this->actingAs($this->user)
            ->get(route('search', ['keyword' => '靴']));

        $response = $this->get(route('top.index',['page' => 'mypage']));

        $response->assertSessionHas('keyword', '靴');
    }

}
