<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // シードデータの作成
        $this->seed([
            ProfilesTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class,
        ]);

        // 外部キー制約を有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->item = Item::with('categories')->first();
    }

    /* No.7 */
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

    /* No.8 */
    public function test_store_like(){
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        // 商品詳細画面を表示した時点でのいいね数を取得
        $likeData = $response->viewData('like');
        $count = $likeData['count'];

        $response = $this->actingAs($this->user)
            ->post('/item/:'.$this->item->id.'/like',[
                'like' => ''
            ]);

        // 更新後のいいね数を再取得
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $newLikeData = $response->viewData('like');
        $newCount = $newLikeData['count'];

        // いいね数が1増加していることを確認
        $this->assertEquals($count + 1, $newCount);

        // いいねした商品として登録していることを確認
        $this->assertDatabaseHas('item_user_like', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id
        ]);

        // hasLikedItemがtrueであることを確認（いいねアイコンの色が変化するクラスを付与する条件）
        $this->assertEquals('hasLikedItem', true);
    }

    public function test_detach_like(){
        //テストデータ用にいいねを登録する
        $this->item->like()->attach($this->user->id);

        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        // 商品詳細画面を表示した時点でのいいね数を取得
        $likeData = $response->viewData('like');
        $count = $likeData['count'];

        $response = $this->actingAs($this->user)
            ->post('/item/:'.$this->item->id.'/like',[
                'like' => 'hasLikedItem'
            ]);

        // 更新後のいいね数を再取得
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $newLikeData = $response->viewData('like');
        $newCount = $newLikeData['count'];

        // いいね数が1減少していることを確認
        $this->assertEquals($count - 1, $newCount);

        // いいねの登録が解除されていることを確認
        $this->assertDatabaseMissing('item_user_like', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id
        ]);
    }

    /* No. 9 */
    public function test_store_comment_success(){
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        $commentData = $response->viewData('comment');
        $count = $commentData['count'];

        $response = $this->actingAs($this->user)
            ->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => 'テストコメント'
            ]));

        // 再ログインしてコメント数を取得する
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $commentData = $response->viewData('comment');
        $newCount = $commentData['count'];

        // コメント数が1増減していることを確認する
        $this->assertEquals($count + 1, $newCount);

        // コメントが登録されていることを確認する
        $this->assertDatabaseHas('comments', [
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'comment' => 'テストコメント'
        ]);

    }

    public function test_can_not_store_comment(){
        $response = $this->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        $response = $this->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => 'テストコメント'
            ]));

        // ログインが必要な操作のため、login画面に遷移されることを確認する
        $response->assertRedirect('/login');
    }

    public function test_not_send_without_comment(){
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)
            ->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => ''
            ]));

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('コメントを入力してください', $errors['comment'][0]);
    }
    public function test_not_send_over_comment(){
        $response = $this->actingAs($this->user)
            ->get(route('item.detail', ['item_id' => $this->item->id]));
        $response->assertStatus(200);

        $overComment = "この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです。文字の大きさ、量、字間、行間等を確認するために入れています。この文章はダミーです";

        $response = $this->actingAs($this->user)
            ->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => $overComment
            ]));

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('コメントは255字以内で入力してください', $errors['comment'][0]);
    }

}
