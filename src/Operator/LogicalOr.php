<?php

/*
 * This file is part of the Regulator package, an OpenSky project.
 *
 * (c) 2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Regulator\Operator;

use Regulator\Context;
use Regulator\Proposition;

/**
 * A logical OR operator.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class LogicalOr extends LogicalOperator
{
    /**
     * @param Context $context Context with which to evaluate this Proposition
     */
    public function evaluate(Context $context): bool
    {
        /** @var Proposition $operand */
        foreach ($this->getOperands() as $operand) {
            if (true === $operand->evaluate($context)) {
                return true;
            }
        }

        return false;
    }

    protected function getOperandCardinality()
    {
        return static::MULTIPLE;
    }
}
