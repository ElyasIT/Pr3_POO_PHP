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

require_once 'bootstrap.php';


// Account 1: creation, balance, close/reopen, deposit, withdrawal without overdraft.
$bankAccount1 = new BankAccount(400.0);
pl('--------- [Account 1: no overdraft] --------');
pl('Initial balance: ' . $bankAccount1->getBalance());

$bankAccount1->closeAccount();
pl('Account open after closing: ' . ($bankAccount1->isOpen() ? 'yes' : 'no'));
$bankAccount1->reopenAccount();
pl('Account open after reopening: ' . ($bankAccount1->isOpen() ? 'yes' : 'no'));

$bankAccount1->transaction(new DepositTransaction(150.0));
pl('Balance after deposit of 150: ' . $bankAccount1->getBalance());
$bankAccount1->transaction(new WithdrawTransaction(25.0));
pl('Balance after withdrawal of 25: ' . $bankAccount1->getBalance());

try {
    $bankAccount1->transaction(new WithdrawTransaction(600.0));
} catch (FailedTransactionException $exception) {
    pl('Withdrawal rejected: ' . $exception->getMessage());
}
pl('Balance after rejected withdrawal: ' . $bankAccount1->getBalance());

// Account 2: Silver overdraft allows the balance to go down to -100.
$bankAccount2 = new BankAccount(100.0, new SilverOverdraft());
pl('--------- [Account 2: Silver overdraft (100)] --------');
$bankAccount2->transaction(new WithdrawTransaction(200.0));
pl('Balance after withdrawal of 200: ' . $bankAccount2->getBalance());

try {
    $bankAccount2->transaction(new WithdrawTransaction(1.0));
} catch (FailedTransactionException $exception) {
    pl('Withdrawal beyond the -100 limit rejected: ' . $exception->getMessage());
}
pl('Final balance: ' . $bankAccount2->getBalance());
