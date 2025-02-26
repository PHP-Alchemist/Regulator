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

use Regulator\Proposition;

/**
 * Logical operator base class.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
abstract class LogicalOperator extends PropositionOperator implements Proposition
{
    /**
     * array of propositions.
     *
     * @param Proposition[] $props
     */
    public function __construct(array $props = [])
    {
        foreach ($props as $operand) {
            $this->addOperand($operand);
        }
    }
}
