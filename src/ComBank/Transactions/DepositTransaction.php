<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction implements BankTransactionInterface
{
    public function __construct(private float $amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException('The transaction amount must be greater than zero.');
        }
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        return $account->getBalance() + $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return 'DEPOSIT_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

}
