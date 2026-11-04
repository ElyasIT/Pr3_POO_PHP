<?php

namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction implements BankTransactionInterface
{
    public function __construct(private float $amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException('The transaction amount must be greater than zero.');
        }
        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $balance = $account->getBalance();
        if (
            $balance < $this->amount
            && !$account->getOverdraft()->isGrantOverdraftFunds($account->getBalance() - $this->amount)
        ) {
            throw new InvalidOverdraftFundsException("Insufficient funds for withdrawal.");
        }

        return $balance - $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return 'WITHDRAW_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
