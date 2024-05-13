<?php
declare(strict_types=1);

namespace App;

class Transaction
{
    public string $date;
    public int $userIdentificator;
    public string $userType;
    public string $operationType;
    public float $amount;
    public string $currency;

    public function __construct(
        string $date,
        int $userIdentificator,
        string $userType,
        string $operationType,
        float $amount,
        string $currency
        )
    {
        $this->date = $date;
        $this->userIdentificator = $userIdentificator;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function __toString()
    {
        return "Transaction on {$this->date} - {$this->userIdentificator}, {$this->userType}, {$this->operationType}, Amount: {$this->amount} {$this->currency}\n";
    }
}
