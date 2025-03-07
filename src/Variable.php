<?php

/*
 * This file is part of the Ruler package, an OpenSky project.
 *
 * (c) 2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruler;

/**
 * A propositional Variable.
 *
 * Variables are placeholders in Propositions and Comparison Operators. During
 * evaluation, they are replaced with terminal Values, either from the Variable
 * default or from the current Context.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class Variable implements VariableOperand
{
    private $name;
    private $value;

    /**
     * Variable class constructor.
     *
     * @param string|null $name  Variable name (default: null)
     * @param mixed       $value Default Variable value (default: null)
     */
    public function __construct(?string $name = null, mixed $value = null)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Return the Variable name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the default Variable value.
     *
     * @param mixed $value The default Variable value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * Get the default Variable value.
     *
     * @return mixed Variable value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Prepare a Value for this Variable given the current Context.
     *
     * @param Context $context The current Context
     */
    public function prepareValue(Context $context): Value
    {
        if (isset($this->name) && isset($context[$this->name])) {
            $value = $context[$this->name];
        } elseif ($this->value instanceof VariableOperand) {
            $value = $this->value->prepareValue($context);
        } else {
            $value = $this->value;
        }

        return ($value instanceof Value) ? $value : new Value($value);
    }
}
