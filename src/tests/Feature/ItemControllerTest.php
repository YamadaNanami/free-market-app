<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // シードデータの作成
        $this->seed([
            ProfilesTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class,
        ]);

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->item = Item::with('categories')->first();
    }

    public function test_item_detail(){
        $expectedItem = Item::with('categories')->find($this->item->id);

        $categories = $expectedItem->categories->pluck('id')->sortBy('id')->values();

        // 商品にコメントといいねをつける
        $expectedItem->comments()->attach($this->user->id, [
            'comment' => 'テストコメント'
        ]);

        $expectedItem->like()->attach($this->user->id);

        // コメント数と最初の商品の最初のコメント内容を取得
        $countCmt = $expectedItem->comments->count();
        $comment = $expectedItem->comments()->first();
        $firstCmt = [
            'user' => User::with('profile')->find($comment->pivot->user_id),
            'comment' => $comment->pivot->comment,
            'status' => 'has_comments'
        ];

        // いいね数を取得
        $countLike = $expectedItem->like->count();

        // responseから値を取得する
        $response = $this->get('/item/:'.$this->item->id);
        $response->assertStatus(200);

        $viewItem = $response['item'];
        $viewCategories = $response['categories']->pluck('id')->sortBy('id')->values();
        $viewCmt = $response['comment'];
        $viewLike= $response['like'];

        // 商品画像
        $this->assertEquals($expectedItem['img_url'], $viewItem['img_url']);

        // 商品名
        $this->assertEquals($expectedItem['item_name'], $viewItem['item_name']);

        // ブランド名
        $this->assertEquals($expectedItem['brand_name'], $viewItem['brand_name']);

        // 商品価格
        $this->assertEquals($expectedItem['price'], $viewItem['price']);

        //いいね数
        $this->assertEquals($countLike, $viewLike['count']);

        // コメント数
        $this->assertEquals($countCmt, $viewCmt['count']);

        // 商品の説明
        $this->assertEquals($expectedItem['description'], $viewItem['description']);

        // カテゴリー
        $this->assertEquals($categories, $viewCategories);

        // 商品の状態
        $this->assertEquals($expectedItem['condition'], $viewItem['condition']);

        //コメントしたユーザ情報
        $this->assertEquals($firstCmt['user']['profile']['img_url'], $viewCmt['user']['profile']['img_url']);

        $this->assertEquals($firstCmt['user']['name'], $viewCmt['user']['name']);

        // 表示しているコメント内容
        $this->assertEquals($firstCmt['comment'], $viewCmt['comment']);
    }

    public function test_store_like(){
        // 最初から怪しい。。
        $response = $this->actingAs($this->user)
            ->post(route('like',['item_id' => $this->item->id,'like' => false]));

        $response->assertStatus(200);

        // データベースを確認
        $this->assertDatabaseHas('likes', [
            'item_id' => $$this->item->id,
            'user_id' => $this->user->id,
        ]);
    }

    // いいねのテストコードここに書くよ

    public function test_comment_success(){
        $user = User::with('profile')->first();

        $response = $this->actingAs($user)
            ->post(route('comment',['item_id' => 1, 'comment' => 'テストコメント']));
    }

}
