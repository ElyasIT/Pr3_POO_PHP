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
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\Transactions\InterestTransaction;
use ComBank\Transactions\TransferTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\ZeroAmountException;

require_once 'bootstrap.php';

    // Account 1: creation, balance, close/reopen, deposit, withdrawal without overdraft.
    $bankAccount1 = new BankAccount(400.0);
pl('[Start testing bank account #1, No overdraft]');
try {
    pl('My balance: ' . $bankAccount1->getBalance());
    
    $bankAccount1->closeAccount();
    pl('My account is now closed.');
    
    $bankAccount1->reopenAccount();
    pl('My account is now reopened.');

    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('My new balance after deposit (+150): ' . $bankAccount1->getBalance());

    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('My new balance after withdrawal (-25): ' . $bankAccount1->getBalance());

    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600.0));
} catch (ZeroAmountException | BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction ' . $bankAccount1->getBalance());

$bankAccount1->closeAccount();
pl('My account is now closed.');


pl(''); 


//---[Bank account 2]---/
$bankAccount2 = new BankAccount(200.0);
$bankAccount2->applyOverdraft(new SilverOverdraft());
pl('[Start testing bank account #2, Silver overdraft (100.0 funds)]');
try {
    pl('My balance ' . $bankAccount2->getBalance());
    
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100.0));
    pl('My new balance after deposit (+100): ' . $bankAccount2->getBalance());

    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300.0));
    pl('My new balance after withdrawal (300) ' . $bankAccount2->getBalance());

    pl('Doing transaction withdrawal (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50.0));
    pl('My new balance after withdrawal (-50) with funds ' . $bankAccount2->getBalance());

    pl('Doing transaction withdrawal (120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120.0));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction: ' . $bankAccount2->getBalance());

try {
    pl('Doing transaction withdrawal (-20) with current balance: ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20.0));
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My new balance after withdrawal (20) with funds' . $bankAccount2->getBalance());

try {
    $bankAccount2->closeAccount();
    pl('My account is now closed.');
    
    $bankAccount2->closeAccount();
} catch (BankAccountException $e) {
    pl('Error: ' . $e->getMessage());
}


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

try {
    pl('--------- [Account 5: Email Validation] --------');
    $bankAccount5 = new BankAccount(500.0);
    $bankAccount5->applyOverdraft(new GoldOverdraft());
    $bankAccount5->transaction(new InterestTransaction());
    pl('Balance after Interest Transaction: ' . $bankAccount5->getBalance());
} catch (InvalidArgsException $exception) {
    pl('Invalid argument: ' . $exception->getMessage());
}

try {
    pl('--------- [Account 6, 7: transfer transaction] --------');
    $bankAccount6 = new BankAccount(500.0);
    $bankAccount7 = new BankAccount(200.0);

    pl('Source Account Balance: ' . $bankAccount6->getBalance());
    pl('Destination Account Balance: ' . $bankAccount7->getBalance());
    pl('Transfer Amount: €100.00');

    $bankAccount6->transaction(new TransferTransaction(100.0, $bankAccount7));

    pl('Source Balance After Transaction: ' . $bankAccount6->getBalance());
    pl('Destination Balance After Transaction: ' . $bankAccount7->getBalance());
} catch (FailedTransactionException $exception) {
    pl('transfer rejected ' . $exception->getMessage());
} catch (InvalidArgsException $exception) {
    pl('transfer rejected ' . $exception->getMessage());
}

try {
    pl('--------- [Account 8, 9: transfer transaction error ] --------');
    $bankAccount8 = new BankAccount(50.00);
    $bankAccount9 = new BankAccount(200.00);
    pl('Source Account Balance: ' . $bankAccount8->getBalance());
    pl('Destination Account Balance: ' . $bankAccount9->getBalance());
    pl('Transfer Amount: €100.00');

    $bankAccount8->transaction(new TransferTransaction(100.00, $bankAccount9));
    pl('Source Balance After Transaction: ' . $bankAccount8->getBalance());
    pl('Destination Balance After Transaction: ' . $bankAccount9->getBalance());
} catch (FailedTransactionException $exception) {
    pl('Result: ' . $exception->getMessage());
} catch (InvalidArgsException $exception) {
    pl('Result: ' . $exception->getMessage());
}
