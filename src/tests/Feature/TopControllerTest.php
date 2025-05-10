<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $item;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // シードデータの作成
        $this->seed([
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class
        ]);

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->item = Item::with('categories')->first();
    }


    public function test_item_get_all(){
        $expectedItems = $this->item;

        dd($expectedItems->id);

        $response = $this->get(route('top.index'));
        $response->assertStatus(200);

        $viewItems = $response->viewData('items');

        $this->assertCount($expectedItems->count(), $viewItems);
        $this->assertEquals($expectedItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

    public function test_item_get_sold(){
        $user = User::factory()->make([
            'email' => 'test@example.com'
        ]);

        $this->post('/login',[
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // ここからエラー出てる
        $expectedItems = User::find($user['id'])->items();

        $viewItems = [];

        foreach($response->viewData('items') as $item){
            if($item['soldOutItemExists']){
                $viewItems = [
                    'item' => $item
                ];
            }
        }

        $this->assertCount($expectedItems->count(), $viewItems);
        $this->assertEquals($expectedItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

    public function test_items_not_get_sell(){
        $user = User::factory()->make([
            'email' => 'test@example.com'
        ]);

        $this->post('/login',[
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // $response->viewData('items')->update([
        //     'user_id' => $user['id']
        // ]);

        // $expectedItems = Item::whereNotIn('user_id', [$user['id']])->get();

        // $viewItems = $response->viewData('items');

        // $this->assertCount($expectedItems->count(), $viewItems);
        // $this->assertEquals($expectedItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

    public function test_search_items(){
        $response = $this->get('/search',['keyword' => '靴']);
        $response->assertRedirectContains('/');

        $searchItems = Item::itemsSearch('靴');

        // ↓ここエラー
        $viewItems = $response->get('items');

        $this->assertCount($searchItems->count(), $viewItems);
        $this->assertEquals($searchItems->pluck('id')->sort()->values(), $viewItems->pluck('id')->sort()->values());
    }

}
