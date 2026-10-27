<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface
{
    private float $balance;
    private bool $status;
    private OverdraftInterface $overdraft;

    public function __construct(float $balance, ?OverdraftInterface $overdraft = null)
    {
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = $overdraft ?? new NoOverdraft();
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function closeAccount(): void
    {
        if (!$this->status) {
            throw new BankAccountException('The account is already closed.');
        }

        $this->status = false;
    }

    public function reopenAccount(): void
    {
        if ($this->status) {
            throw new BankAccountException('The account is already open.');
        }

        $this->status = true;
    }

    public function isOpen(): bool
    {
        return $this->status;
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->status) {
            throw new BankAccountException('Cannot transact on a closed account.');
        }

        try { 
            $newBalance = $transaction->applyTransaction($this);
        } catch (InvalidOverdraftFundsException $exception) {
            throw new FailedTransactionException($exception->getMessage(), 0, $exception);
        }

        $this->balance = $newBalance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}