<?php

/*
 * This file is part of the Ruler package, an OpenSky project.
 *
 * (c) 2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruler\Operator;

use Ruler\Context;
use Ruler\Proposition;
use Ruler\VariableOperand;

/**
 * A Set Contains comparison operator.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class SetDoesNotContain extends VariableOperator implements Proposition
{
    /**
     * @param Context $context Context with which to evaluate this Proposition
     */
    public function evaluate(Context $context): bool
    {
        /** @var VariableOperand $left */
        /** @var VariableOperand $right */
        [$left, $right] = $this->getOperands();

        return false === $left->prepareValue($context)->getSet()->setContains($right->prepareValue($context));
    }

    protected function getOperandCardinality()
    {
        return static::BINARY;
    }
}
