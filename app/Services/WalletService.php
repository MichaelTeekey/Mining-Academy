<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Exception;

/**
 * Handles all wallet logic — deposits, withdrawals, purchases, and refunds.
 */
class WalletService
{
    /**
     * Retrieve or create a wallet for a given user.
     */
    public function getUserWallet(string $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'id' => Str::uuid(),
                'balance' => '0.00',
                'currency' => 'USD',
            ]
        );
    }

    /**
     * Generate a clean, unique transaction reference.
     */
    private function generateReference(string $prefix = 'TXN_'): string
    {
        return strtoupper($prefix . Str::random(10));
    }

    /**
     * Deposit funds into a wallet (used for top-ups or refunds).
     */
    public function deposit(
        string $userId,
        float $amount,
        ?string $description = null,
        ?string $reference = null,
        ?string $currency = 'USD'
    ): Wallet {
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($userId, $amount, $description, $reference, $currency) {
            try {
                $wallet = $this->getUserWallet($userId);

                // Prevent currency mismatch
                if ($currency && $wallet->currency !== $currency) {
                    throw new Exception('Currency mismatch.');
                }

                // Prevent duplicate references
                if ($reference && WalletTransaction::where('reference', $reference)->exists()) {
                    throw new Exception('Duplicate transaction reference.');
                }

                $amountStr = sprintf('%.2f', $amount);
                $newBalance = bcadd($wallet->balance ?? '0.00', $amountStr, 2);
                $wallet->balance = (float) $newBalance;
                $wallet->save();

                // Log transaction
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'deposit',
                    'amount' => $amountStr,
                    'description' => $description,
                    'reference' => $reference ?? $this->generateReference('DEP_'),
                ]);

                return $wallet->refresh();
            } catch (Exception $e) {
                Log::error('Wallet deposit failed', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Withdraw funds from wallet (for purchases or transfers).
     */
    public function withdraw(
        string $userId,
        float $amount,
        ?string $description = null,
        ?string $reference = null
    ): Wallet {
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($userId, $amount, $description, $reference) {
            try {
                $wallet = $this->getUserWallet($userId);
                $amountStr = sprintf('%.2f', $amount);

                // Check sufficient funds
                if (bccomp($wallet->balance, $amountStr, 2) === -1) {
                    throw new Exception('Insufficient balance.');
                }

                // Prevent duplicate reference
                if ($reference && WalletTransaction::where('reference', $reference)->exists()) {
                    throw new Exception('Duplicate transaction reference.');
                }

                // Deduct funds safely
                $wallet->balance = (float) bcsub($wallet->balance ?? '0.00', $amountStr, 2);
                $wallet->save();

                // Log transaction
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'withdrawal',
                    'amount' => $amountStr,
                    'description' => $description,
                    'reference' => $reference ?? $this->generateReference('WDR_'),
                ]);

                return $wallet->refresh();
            } catch (Exception $e) {
                Log::error('Wallet withdrawal failed', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Purchase a course using wallet funds.
     * This both withdraws and logs a Payment record.
     */
    public function purchaseCourse(string $userId, float $amount, string $courseTitle, ?string $reference = null): Wallet
    {
        $description = "Purchased course: {$courseTitle}";

        return DB::transaction(function () use ($userId, $amount, $courseTitle, $description, $reference) {
            $reference = $reference ?? $this->generateReference('PUR_');

            // Withdraw from wallet
            $wallet = $this->withdraw($userId, $amount, $description, $reference);

            // Log a Payment record
            Payment::create([
                'user_id' => $userId,
                'amount' => $amount,
                'currency' => 'USD',
                'payment_method' => 'wallet',
                'transaction_id' => $reference,
                'status' => 'success',
                'meta' => ['course' => $courseTitle],
            ]);

            return $wallet;
        });
    }

    /**
     * Refund a payment (adds money back to the wallet).
     */
    public function refund(
        string $userId,
        float $amount,
        ?string $description = 'Refund',
        ?string $reference = null
    ): Wallet {
        $reference = $reference ?? $this->generateReference('REF_');
        return $this->deposit($userId, $amount, $description, $reference);
    }

    /**
     * Get all wallet transactions for a user.
     */
    public function transactions(string $userId): Collection
    {
        $wallet = $this->getUserWallet($userId);
        return $wallet->transactions()->orderBy('created_at', 'desc')->get();
    }
}
