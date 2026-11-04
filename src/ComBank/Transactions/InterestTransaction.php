<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;



class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $amount = 0.0)
    {
        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $interestRate = $account->getOverdraft()->getIntrestRate();

        $balance = $account->getBalance();

        return ($balance * (($interestRate / 100) + 1));
    }

    public function getTransactionInfo(): string
    {
        return 'INTEREST_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
