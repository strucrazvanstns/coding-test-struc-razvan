<?php
declare(strict_types=1);

namespace App\Service\File;

use App\Enums\UserType;
use App\Transaction;
use App\Service\File\DataProcessorInterface;
use App\Enums\TransactionType;
use App\Enums\Currency;
use DateTime;
use Exception;

class TransactionProcessor implements DataProcessorInterface
{
    /**
     * @throws Exception
     */
    public function processRow(array $rowData): Transaction
    {
        if (count($rowData) < 6) {
            throw new Exception("Invalid row data. Not enough columns.");
        }

        $date = $this->validateDate($rowData[0]);
        $userIdentificator = $this->validateInteger($rowData[1]);
        $userType = $this->validateUserType($rowData[2]);
        $operationType = $this->validateOperationType($rowData[3]);
        $ammount = $this->validateFloat($rowData[4]);
        $currency = $this->validateCurrency($rowData[5]);

        return new Transaction($date, $userIdentificator, $userType, $operationType, $ammount, $currency);
    }

    private function validateDate($date)
    {
        if (DateTime::createFromFormat('Y-m-d', $date) !== false) {
            return $date;
        }
        throw new Exception("Invalid date format: $date");
    }

    private function validateInteger($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }
        throw new Exception("Expected integer, received: $value");
    }

    private function validateFloat($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
            return (float) $value;
        }
        throw new Exception("Expected float, received: $value");
    }

    private function validateUserType($type)
    {
        if (!in_array($type, UserType::values())) {
            throw new Exception("Invalid user type: $type. Allowed user types are: " . implode(', ', UserType::values()));
        }
        
        return $type;
    }

    private function validateOperationType($type)
    {
        if (!in_array($type, TransactionType::values())) {
            throw new Exception("Invalid operation type: $type. Allowed operations are: " . implode(', ', TransactionType::values()));
        }

        return $type;
    }

    private function validateCurrency($currency)
    {
        if (!in_array($currency, Currency::values())) {
            throw new Exception("Invalid currency: $currency. Allowed currencies are: " . implode(', ', Currency::values()));
        }
        
        return $currency;
    }
}
