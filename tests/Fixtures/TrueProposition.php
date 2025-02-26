<?php

namespace Regulator\Test\Fixtures;

use Regulator\Context;
use Regulator\Proposition;

class TrueProposition implements Proposition
{
    public function evaluate(Context $context): bool
    {
        return true;
    }
}
