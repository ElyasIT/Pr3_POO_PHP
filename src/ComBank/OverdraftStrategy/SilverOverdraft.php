<?php

namespace ComBank\OverdraftStrategy;

use Override;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:39 PM
 */

/**
 * @description: Grant 100.00 overdraft funds.
 * */
class SilverOverdraft implements Contracts\OverdraftInterface
{

    public function isGrantOverdraftFunds(float $amount): bool
    {
        $quantity = $amount + $this->getOverdraftFundsAmount();
        if ($quantity >= 0) {
            return true;
        }
        return false;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 100.00;
    }

    public function getIntrestRate(): int
    {
        return 5;
    }
}
