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

        switch($expectedItem->condition){
            case 1:
                $condition = '良好';
                break;
            case 2:
                $condition = '目立った傷や汚れなし';
                break;
            case 3:
                $condition = 'やや傷や汚れあり';
                break;
            default:
                $condition = '状態が悪い';
                break;
        }

        $categories = $expectedItem->categories->sortBy('id')->values();

        // 商品にコメントといいねをつける
        $expectedItem->comments()->attach($this->user->id, [
            'comment' => 'テストコメント'
        ]);
        $expectedItem->like()->attach($this->user->id);

        // コメント数と商品に紐づく1番目のコメント内容を取得
        $countCmt = $expectedItem->comments->count();
        $comment = $expectedItem->comments()->first();
        $firstCmt = [
            'user' => User::with('profile')->find($comment->pivot->user_id),
            'comment' => $comment->pivot->comment,
            'status' => 'has_comments'
        ];

        // いいね数を取得
        $countLike = $expectedItem->like->count();

        $response = $this->get('/item/:'.$this->item->id);
        $contents = $response->content();

        //各項目が表示されているか確認する
        $this->assertStringContainsString('<img src="http://localhost/storage/img/'.$expectedItem->img_url.'" alt="商品画像" class="item-img">', $contents);

        $this->assertStringContainsString('<h2 class="page-title">' . $expectedItem->item_name . '</h2>', $contents);

        $this->assertStringContainsString('<p class="brand-name">' . $expectedItem->brand_name . '</p>',$contents);

        $this->assertStringContainsString('<p class="price">¥' . number_format($expectedItem->price) . ' (税込)</p>',$contents);

        $this->assertStringContainsString('<p class="pieces">' . $countLike . '</p>', $contents);

        $this->assertStringContainsString('<p class="pieces">' . $countCmt . '</p>', $contents);

        $this->assertStringContainsString('<p class="item-info">' . $expectedItem->description . '</p>', $contents);

        foreach($categories as $category){
            $categoryText = $category->category;

            $this->assertStringContainsString('<p class="category">'.$categoryText.'</p>', $contents);
        }

        $this->assertStringContainsString($condition,$contents);

        $this->assertStringContainsString('<h3 class="sec-title">コメント(' . $countCmt . ')</h3>', $contents);

        $this->assertStringContainsString('<img src=" http://localhost/storage/img/noImage.png " alt="プロフィール画像" class="user-img">', $contents);

        $this->assertStringContainsString('<p class="user-name">'.$firstCmt['user']['name'].'</p>',$contents);

        $this->assertStringContainsString('<p class="cmt">'.$firstCmt['comment'].'</p>',$contents);
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

        // 商品詳細画面を表示した時点でのいいね数を取得
        $likeData = $response->viewData('like');
        $count = $likeData['count'];

        // いいねを解除する
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

        $commentData = $response->viewData('comment');
        $count = $commentData['count'];

        $response = $this->actingAs($this->user)
            ->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => 'テストコメント'
            ]));

        // 再表示してコメント数を取得する
        $response = $this->get(route('item.detail', ['item_id' => $this->item->id]));
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

    public function test_fails_store_comment(){
        $this->get(route('item.detail', ['item_id' => $this->item->id]));

        $response = $this->post(route('comment', [
                'item_id' => $this->item->id,
                'comment' => 'テストコメント'
            ]));

        // 送信したコメントがDBに登録されていないことを確認する
        $this->assertDatabaseMissing('comments', [
            'item_id' => $this->item->id,
            'comment' => 'テストコメント'
        ]);

        // ログインが必要な操作のため、login画面に遷移されることを確認する
        $response->assertRedirect('/login');
    }

    public function test_fails_send_without_comment(){
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
    public function test_fails_send_over_comment(){
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
