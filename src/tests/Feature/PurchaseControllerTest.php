<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\User;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\CategoryItemTableSeeder;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\ProfilesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\stringContains;

class PurchaseControllerTest extends TestCase
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
            ItemsTableSeeder::class,
            CategoriesTableSeeder::class,
            CategoryItemTableSeeder::class,
        ]);

        // 外部キー制約を再有効化
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 共通データの作成
        $this->user = User::with('profile')->first();
        $this->item = Item::with('categories')->first();
    }

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

    public function test_default_address(){
        $response = $this->actingAs($this->user)
            ->get(route('address.edit', [
                'item_id' => $this->item->id
            ]));
        $response->assertViewIs('address');

        $responseData = [
            'post' => $response['address']['post'],
            'address' => $response['address']['address'],
            'building' => $response['address']['building'],
        ];

        $findData = Profile::where('user_id', $this->user->id)->first();
        $dbData = [
            'post' => $findData['post'],
            'address' => $findData['address'],
            'building' => $findData['building'],
        ];

        $this->assertEquals($responseData, $dbData);

    }

    public function test_cache_success(){
        // ダミーの送付先住所
        $address = [
            'post' => "000-1111",
            'address' => "東京都世田谷区",
            'building' => "〇〇マンション"
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['address' => $address])
            ->post(route('stripe.checkout',[
                'item_id' => $this->item->id,
                'payment' => 'card',
                'address' => $address
            ]));

        $stripe = new \Stripe\StripeClient('sk_test_51RMJFq02GGFWUQA6Xxi53zGmunIEgX29CTYFCysklixEMdmHpqcQUENuO3G6v9Ng9mzZrBFcB0asGyCekUmM2NSM001Oa3W1Eg');

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => 500,
            'currency' => 'jpy',
            'payment_method' => 'pm_card_visa',
            'payment_method_types' => ['card'],
        ]);

        $this->assertDatabaseHas('purchases', [
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'post' => $address['post'],
            // 'address' => $address['address'],
            // 'building' => $address['building'],
        ]);
    }

}
