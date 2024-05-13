<?php

use PHPUnit\Framework\TestCase;
use App\Service\TransactionService;
use App\Transaction;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Enums\Currency;

class TransactionServiceTest extends TestCase
{
    private TransactionService $service;

    protected function setUp(): void
    {
        $this->service = new TransactionService();
    }

    public function testTransactionCommissions()
    {
        $transactions = [
            new Transaction('2014-12-31', 4, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 1200, Currency::EUR->value),
            new Transaction('2015-01-01', 4, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 1000, Currency::EUR->value),
            new Transaction('2016-01-05', 4, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 1000, Currency::EUR->value),
            new Transaction('2016-01-05', 1, UserType::PRIVATE->value, TransactionType::DEPOSIT->value, 200, Currency::EUR->value),
            new Transaction('2016-01-06', 2, UserType::BUSINESS->value, TransactionType::WITHDRAW->value, 300, Currency::EUR->value),
            new Transaction('2016-01-06', 1, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 30000, Currency::JPY->value),
            new Transaction('2016-01-07', 1, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 1000, Currency::EUR->value),
            new Transaction('2016-01-07', 1, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 100, Currency::USD->value),
            new Transaction('2016-01-10', 1, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 100, Currency::EUR->value),
            new Transaction('2016-01-10', 2, UserType::BUSINESS->value, TransactionType::DEPOSIT->value, 10000, Currency::EUR->value),
            new Transaction('2016-01-10', 3, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 1000, Currency::EUR->value),
            new Transaction('2016-02-15', 1, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 300, Currency::EUR->value),
            new Transaction('2016-02-19', 5, UserType::PRIVATE->value, TransactionType::WITHDRAW->value, 3000000, Currency::JPY->value),
            // Add more transactions as needed
        ];

        $expectedCommissions = ["0.60", "3.00", "0.00", "0.06", "1.50", "0.00", "0.54", "0.31", "0.30", "3.00", "0.00", "0.00", "8497.33"];  // Expected commission values for each transaction

        foreach ($transactions as $index => $transaction) {
            $commission = $this->service->calculateCommission($transaction);
            $this->assertEquals($expectedCommissions[$index], $commission, "Commission for transaction {$index} does not match expected value.");
        }
    }
}