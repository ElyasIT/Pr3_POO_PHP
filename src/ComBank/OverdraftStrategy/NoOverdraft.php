<?php

namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

class NoOverdraft implements Contracts\OverdraftInterface
{
    public function isGrantOverdraftFunds(float $amount): bool
    {
        return false;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 0.0;
    }

    public function getIntrestRate(): int
    {
        return 1;
    }
}
