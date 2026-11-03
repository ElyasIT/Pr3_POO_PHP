<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;

require_once 'bootstrap.php';

try {
    // Account 1: creation, balance, close/reopen, deposit, withdrawal without overdraft.
    $bankAccount1 = new BankAccount(400.0);
    pl('--------- [Account 1: no overdraft] --------');
    pl('Initial balance: ' . $bankAccount1->getBalance());

    // close account
    $bankAccount1->closeAccount();
    pl('Closing account ' . $bankAccount1->isOpen());
    $bankAccount1->reopenAccount();
    pl('Reopening account' . $bankAccount1->isOpen());

    pl('Doing deposit transaction (+150) with current balance');
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('Balance after deposit of 150: ' . $bankAccount1->getBalance());

    pl('Doing withdraw transaction (+150) with current balance');
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('Balance after withdrawal of 25: ' . $bankAccount1->getBalance());

    $bankAccount1->transaction(new WithdrawTransaction(600.0));

    pl('Balance after rejected withdrawal: ' . $bankAccount1->getBalance());
} catch (FailedTransactionException $exception) {
    pl('Withdrawal rejected: ' . $exception->getMessage());
} catch (InvalidArgsException $exception) {
    pl('Invalid argument: ' . $exception->getMessage());
}

$bankAccount2 = new BankAccount(100.0);
$bankAccount2->applyOverdraft(new SilverOverdraft());

try {
    // show balance account
    pl($bankAccount2->getBalance());
    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());

    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100.0));

    // withdrawal -300
    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300.0));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50.0));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120.0));
} catch (FailedTransactionException $exception) {
    pl('Withdrawal beyond the -100 limit rejected: ' . $exception->getMessage());
}
pl('Final balance: ' . $bankAccount2->getBalance());


try {
    pl('--------- [Account 3: Email Validation] --------');

    $bankAccount3 = new BankAccount(500.0, 'john.smith@example.com');
    pl('Account 3 email: ' . $bankAccount3->getemail());

    pl('--------- [Account 4: Email Validation Error] --------');
    $bankAccount4 = new BankAccount(300.0, '   johnexample.com');
    pl('Account 4 email: ' . $bankAccount4->getemail());
} catch (InvalidArgsException $e) {
    pl('Error creating account: ' . $e->getMessage());
}
