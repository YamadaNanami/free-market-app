<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Tests\TestCase;

class PurchaseStripeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;
    protected $stripe;

    protected function setUp(): void
    {
        parent::setUp();

        // エンコーディングを指定
        DB::statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // シードデータの作成
        $this->seed([
            ProfilesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoriesTableSeeder::class,
            CategoryItemTableSeeder::class,
        ]);

        // 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->item = Item::with('categories')->first();
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));

    }

    /* No.10 */
    public function test_can_charge(){
        // アイテムの出品者がログインユーザーにならないようデータを修正する（商品一覧画面に表示されなくなるため）
        Item::find($this->item->id)->update([
            'user_id' => 2
        ]);
        // ダミーの送付先住所
        $address = [
            'post' => "000-1111",
            'address' => "東京都世田谷区",
            'building' => "〇〇マンション"
        ];

        $this->actingAs($this->user)
            ->get(route('stripe.success', [
                'item_id' => $this->item->id,
                'address' => $address
            ]));

        //購入完了 = Purchasesテーブルに対象のデータが存在する
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 購入した商品が商品一覧画面にて「sold」と表示されることを確認
        $response = $this->actingAs($this->user)->get('/');

        $contents = $response->content();
        $this->assertStringContainsString($this->item->item_name . '　Sold', $contents);

        // 「プロフィール/購入した商品一覧」に追加されていることを確認
        $response = $this->actingAs($this->user)
            ->get(route('mypage.index',[
                'tab' => 'buy'
            ]));

        $contents = $response->content();
        $this->assertStringContainsString($this->item->item_name, $contents);
    }

    /* No.11 */
    public function test_can_select_card(){
        $response = $this->actingAs($this->user)
            ->get(route('purchase.index', [
                'item_id' => $this->item->id
            ]));

        $response->assertViewIs('purchase');

        $response = $this->actingAs($this->user)
            ->post('/purchase/payment', ['payment' => 'card']);

        $this->assertEquals(session('payment'), 'card');
    }

    public function test_can_select_konbini(){
        $response = $this->actingAs($this->user)
            ->get(route('purchase.index', [
                'item_id' => $this->item->id
            ]));

        $response->assertViewIs('purchase');

        $response = $this->actingAs($this->user)
            ->post('/purchase/payment', ['payment' => 'konbini']);

        $this->assertEquals(session('payment'), 'konbini');
    }

    /* No.12 */
    public function test_display_edit_address(){
        $response = $this->actingAs($this->user)
            ->followingRedirects()
            ->post(route('address.store', [
                'item_id' => $this->item->id,
                'post' => "000-1111",
                'address' => "東京都世田谷区",
                'building' => "〇〇マンション"
            ]));

        $content = $response->content();

        $this->assertStringContainsString('000-1111', $content);
        $this->assertStringContainsString('東京都世田谷区', $content);
        $this->assertStringContainsString('〇〇マンション', $content);
    }

    public function test_stripe_charge_success(){
        // ダミーの送付先住所
        $address = [
            'post' => "000-1111",
            'address' => "東京都世田谷区",
            'building' => "〇〇マンション"
        ];

        $this->actingAs($this->user)
        ->get(route('stripe.success', [
            'item_id' => $this->item->id,
            'address' => $address
        ]));

        //送付先住所変更画面で登録した住所が紐付けできているか確認する
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'post' => "000-1111",
            'address' => "東京都世田谷区",
            'building' => "〇〇マンション"
        ]);
    }

}
