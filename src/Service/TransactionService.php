<?php
declare(strict_types=1);

namespace App\Service;
use App\Constants\CommissionRate;
use App\Constants\TransactionLimit;
use App\Enums\Currency;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Transaction;
use Exception;

class TransactionService
{
    public array $userTransactions = [];

    public function calculateCommission(Transaction $transaction)
    {
        if ($transaction->operationType === TransactionType::DEPOSIT->value) {
            $commision = $this->deposit($transaction->amount);
        }

        if ($transaction->operationType === TransactionType::WITHDRAW->value) {
            $commision = $this->withdraw($transaction);
        }

        return number_format($commision, 2, '.', '');
    }

    private function deposit($amount)
    {
        return $this->roundUp($amount * (CommissionRate::DEPOSIT / 100));
    }

    private function withdraw(Transaction $transaction)
    {
        $amount = $transaction->amount;

        if ($transaction->userType === UserType::PRIVATE->value) {
            if ($transaction->currency !== Currency::EUR->value) {
                $currencyRate = $this->getCurrencyRate($transaction->currency, Currency::EUR->value);
                $amount *= $currencyRate;
                $transaction->amount = $this->roundUp($amount);
            }
            $this->addTransaction($transaction);
            $totalWeekTransactions = $this->sumTransactionsInWeek($transaction->userIdentificator, $transaction->date);

            if ($this->checkTransactionsExceedsLimit($transaction->userIdentificator, $transaction->date)) {
                $amount = $this->roundUp($amount * (CommissionRate::WITHDRAW_PRIVATE / 100));

            } else {
                if ($totalWeekTransactions <= TransactionLimit::WITHDRAW_PRIVATE_LIMIT) {
                    $amount = 0;

                } else {
                    $excessAmount = $totalWeekTransactions - TransactionLimit::WITHDRAW_PRIVATE_LIMIT;
                    $commission = $this->roundUp($excessAmount * (CommissionRate::WITHDRAW_PRIVATE / 100));
                    $amount = ($amount > $excessAmount) ? $commission : $this->roundUp($amount * (CommissionRate::WITHDRAW_PRIVATE / 100));
                }
            }

            if ($transaction->currency !== Currency::EUR->value) {
                $currencyRate = $this->getCurrencyRate(Currency::EUR->value, $transaction->currency);
                $amount *= $currencyRate;
            }

            return $this->roundUp($amount);
        }

        if ($transaction->userType === UserType::BUSINESS->value) {
            return $this->roundUp($transaction->amount * (CommissionRate::WITHDRAW_BUSINESS / 100));
        }
    }

    private function roundUp(float|int $value, int $precission = 2)
    {
        $factor = pow(10, $precission);
        return ceil($value * $factor) / $factor;
    }

    private function addTransaction(Transaction $transaction): void
    {
        $userId = $transaction->userIdentificator;

        if (!isset($this->userTransactions[$userId])) {
            $this->userTransactions[$userId] = [];
        }

        $this->userTransactions[$userId][] = [
            'date' => $transaction->date,
            'type' => $transaction->userType,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
        ];
    }

    public function checkTransactionsExceedsLimit($userId, $date) {
        $specifiedWeek = date('o-W', strtotime($date));
        $count = 0;

        foreach ($this->userTransactions[$userId] as $transaction) {
            $transactionWeek = date('o-W', strtotime($transaction['date']));

            if ($transactionWeek === $specifiedWeek) {
                $count++;
                if ($count > TransactionLimit::WITHDRAW_WEEK_LIMIT) {
                    return true;
                }
            }
        }

        return false;
    }

    public function sumTransactionsInWeek($userId, $date) {
        if (!isset($this->userTransactions[$userId])) {
            return 0; // No transactions for this user.
        }

        $specifiedWeek = date('o-W', strtotime($date));
        $totalAmount = 0;

        foreach ($this->userTransactions[$userId] as $transaction) {
            $transactionWeek = date('o-W', strtotime($transaction['date']));
            if ($transactionWeek === $specifiedWeek) {
                $totalAmount += $transaction['amount'];
            }
        }

        return $totalAmount;
    }

    private function getCurrencyRate($base, $currencyCode) {
        $env = parse_ini_file('.env');
        $apiKey = $env['API_KEY'];
        $url = "https://api.exchangeratesapi.net/v1/exchange-rates/latest?access_key=$apiKey&base=$base";
    
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
    
        // Execute the cURL session
        $response = curl_exec($curl);
    
        // Close the cURL session
        curl_close($curl);
    
        // Decode the JSON response
        $data = json_decode($response, true);
    
        // Check if the currency code is in the response and return its rate
        if (isset($data['rates'][$currencyCode])) {
            return $data['rates'][$currencyCode];
        } else {
            throw new Exception("Currency code not found: " . $currencyCode);
        }
    }
}