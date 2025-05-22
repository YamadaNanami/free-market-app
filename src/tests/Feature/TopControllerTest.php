<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ItemsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function test_item_get_all(){
        $expectedItems = $this->items;

        $response = $this->get(route('top.index'));
        $response->assertStatus(200);

        $viewItems = $response->viewData('items');

        $this->assertCount($expectedItems->count(), $viewItems);
        $this->assertEquals($expectedItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

    public function test_item_get_sold(){
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);

        // ここあとでやる


    }

    public function test_items_not_get_sell(){
        $response = $this->actingAs($this->user)->get('/');

        $response->assertStatus(200);

        // 自分が出品していないアイテムを取得
        $notSellItems = Item::whereNotIn('user_id', [$this->user->id])->get();

        $this->assertEquals($response['items']->pluck('id')->sortBy('id')->values(),$notSellItems->pluck('id')->sortBy('id')->values());
    }

    /* No.5 */
    public function test_get_mylist(){
        // ダミーデータとしていいねしたアイテムを2件作成する
        User::find($this->user)->like()->attach(1);
        User::find($this->user)->like()->attach(2);

        $response = $this->actingAs($this->user)
            ->get('/?page=mylist');

        $response->assertStatus(200);

        // 自分が出品していないアイテムを取得
        $likeItems = $this->user->like;

        dd($response['items']->pluck('id')->sortBy('id')->values());

        $this->assertEquals($response['items']->pluck('id')->sortBy('id')->values(),$likeItems->pluck('id')->sortBy('id')->values());
    }

    /* No.6 */
    public function test_search_items(){
        $response = $this->get('/search',['keyword' => '靴']);
        $response->assertRedirectContains('/');

        $searchItems = Item::itemsSearch('靴');

        // ここエラー
        $viewItems = $response->get('items');

        $this->assertCount($searchItems->count(), $viewItems);
        $this->assertEquals($searchItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

}
