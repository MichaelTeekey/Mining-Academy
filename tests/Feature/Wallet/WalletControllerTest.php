<?php

namespace Tests\Feature\Wallet;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_can_view_wallet()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/v1/wallet')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['id', 'user_id', 'balance', 'currency']]);
    }

    public function test_authenticated_can_deposit_into_wallet()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $payload = [
            'amount' => 150.75,
            'description' => 'Test deposit',
            'reference' => 'dep-ref-1',
            'currency' => 'USD',
        ];

        $this->postJson('/api/v1/wallet/deposit', $payload)
            ->assertStatus(200)
            ->assertJson(['status' => true, 'message' => 'Deposit successful']);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);

        $wallet = \App\Models\Wallet::where('user_id', $user->id)->first();
        $this->assertEquals('150.75', number_format((float)$wallet->balance, 2, '.', ''));
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 150.75,
            'reference' => 'dep-ref-1',
        ]);
    }

    public function test_authenticated_can_withdraw_from_wallet()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        // deposit first
        $this->postJson('/api/v1/wallet/deposit', ['amount' => 200.00])
            ->assertStatus(200);

        // withdraw
        $this->postJson('/api/v1/wallet/withdraw', ['amount' => 75.25, 'description' => 'Purchase', 'reference' => 'wd-ref-1'])
            ->assertStatus(200)
            ->assertJson(['status' => true, 'message' => 'Withdrawal successful']);

        $wallet = \App\Models\Wallet::where('user_id', $user->id)->first();
        $this->assertEquals('124.75', number_format((float)$wallet->balance, 2, '.', ''));

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'withdrawal',
            'amount' => 75.25,
            'reference' => 'wd-ref-1',
        ]);
    }

    public function test_withdraw_returns_400_on_insufficient_funds()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/v1/wallet/withdraw', ['amount' => 10.00])
            ->assertStatus(400)
            ->assertJson(['status' => false]);
    }

    public function test_transactions_returns_list()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/v1/wallet/deposit', ['amount' => 50.00])->assertStatus(200);
        $this->postJson('/api/v1/wallet/withdraw', ['amount' => 20.00])->assertStatus(200);

        $response = $this->getJson('/api/v1/wallet/transactions')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);

        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }
}