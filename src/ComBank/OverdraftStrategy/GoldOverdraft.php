<?php

namespace ComBank\OverdraftStrategy;

use Override;

class GoldOverdraft implements Contracts\OverdraftInterface
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
        return 10;
    }
}
