<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Requests\WalletDepositRequest;
use App\Http\Requests\WalletWithdrawRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class WalletController extends BaseController
{
    protected WalletService $service;

    public function __construct(WalletService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /wallet
     * Return the authenticated user's wallet
     */
    public function show(Request $request): JsonResponse
    {
        try {
            Log::info('Fetching wallet for user', ['user_id' => $request->user()->id]);
            $wallet = $this->service->getUserWallet($request->user()->id);
            return response()->json(['status' => true, 'data' => $wallet]);
        } catch (Throwable $e) {
            Log::error('Error fetching wallet', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            return response()->json(['status' => false, 'message' => 'Failed to get wallet', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /wallet/deposit
     * Add funds to the authenticated user's wallet
     */
    public function deposit(WalletDepositRequest $request): JsonResponse
    {
        try {

            $payload = $request->validated();
            Log::info('Depositing to wallet', [
                'user_id' => $request->user()->id,
                'amount' => $payload['amount'],
                'description' => $payload['description'] ?? null,
                'reference' => $payload['reference'] ?? null,
                'currency' => $payload['currency'] ?? null
            ]);
            $wallet = $this->service->deposit(
                $request->user()->id,
                (float)$payload['amount'],
                $payload['description'] ?? null,
                $payload['reference'] ?? null,
                $payload['currency'] ?? null
            );
            Log::info('Deposit successful', [
                'user_id' => $request->user()->id,
                'amount' => $payload['amount']
            ]);

            return response()->json(['status' => true, 'message' => 'Deposit successful', 'data' => $wallet]);
        } catch (Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Deposit failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /wallet/withdraw
     * Withdraw funds or purchase via wallet
     */
    public function withdraw(WalletWithdrawRequest $request): JsonResponse
    {
        try {
            $payload = $request->validated();
            Log::info('Withdrawing from wallet', [
                'user_id' => $request->user()->id,
                'amount' => $payload['amount'],
                'description' => $payload['description'] ?? null,
                'reference' => $payload['reference'] ?? null
            ]);
            $wallet = $this->service->withdraw(
                $request->user()->id,
                (float)$payload['amount'],
                $payload['description'] ?? null,
                $payload['reference'] ?? null
            );
            Log::info('Withdrawal successful', [
                'user_id' => $request->user()->id,
                'amount' => $payload['amount']
            ]);

            return response()->json(['status' => true, 'message' => 'Withdrawal successful', 'data' => $wallet]);
        } catch (Throwable $e) {
            Log::error('Error during withdrawal', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * GET /wallet/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $txs = $this->service->transactions($request->user()->id);
            Log::info('Fetched wallet transactions', ['user_id' => $request->user()->id, 'count' => count($txs)]);
            return response()->json(['status' => true, 'data' => $txs]);
        } catch (Throwable $e) {
            Log::error('Error fetching wallet transactions', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            return response()->json(['status' => false, 'message' => 'Failed to fetch transactions', 'error' => $e->getMessage()], 500);
        }
    }
}
