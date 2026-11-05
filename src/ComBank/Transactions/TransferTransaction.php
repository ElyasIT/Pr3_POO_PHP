<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;



class TransferTransaction extends BaseTransaction implements BankTransactionInterface
{

    private BankAccountInterface $destinationAccount;
    public function __construct(float $amount, BankAccountInterface $destinationAccount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException('The transaction amount must be greater than zero.');
        }
        $this->amount = $amount;
        $this->destinationAccount = $destinationAccount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $balance = $account->getBalance();

        if (
            $balance < $this->amount
            && !$account->getOverdraft()->isGrantOverdraftFunds($balance - $this->amount)
        ) {
            throw new InvalidOverdraftFundsException("Transfer rejected when the source account has insufficient available funds.");
        }


        $newBalance = $balance - $this->amount;

        $this->destinationAccount->transaction(new DepositTransaction($this->amount));

        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return 'INTEREST_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
    public function setDestinationAccount(BankAccountInterface $destinationAccount)
    {

        return $this->destinationAccount = $destinationAccount;
    }
    public function getDestinationAccount(): BankAccountInterface
    {
        return $this->destinationAccount;
    }
}
