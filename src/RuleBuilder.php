<?php

/*
 * This file is part of the Regulator package, an OpenSky project.
 *
 * (c) 2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Regulator;

/**
 * RuleBuilder.
 *
 * The RuleBuilder provides a DSL and fluent interface for constructing
 * Rules.
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class RuleBuilder implements \ArrayAccess
{
    private array $variables          = [];
    private array $operatorNamespaces = [];

    /**
     * Create a Rule with the given propositional condition.
     *
     * @param Proposition $condition Propositional condition for this Rule
     * @param callable    $action    Action (callable) to take upon successful Rule execution (default: null)
     */
    public function create(Proposition $condition, $action = null): Rule
    {
        return new Rule($condition, $action);
    }

    /**
     * Register an operator namespace.
     *
     * Note that, depending on your filesystem, operator namespaces are most likely case sensitive.
     */
    public function registerOperatorNamespace(string $namespace): self
    {
        $this->operatorNamespaces[$namespace] = true;

        return $this;
    }

    /**
     * Create a logical AND operator proposition.
     *
     * @param Proposition ...$props One or more Propositions
     */
    public function logicalAnd(Proposition ...$props): Operator\LogicalAnd
    {
        return new Operator\LogicalAnd($props);
    }

    /**
     * Create a logical OR operator proposition.
     *
     * @param Proposition ...$props One or more Propositions
     */
    public function logicalOr(Proposition ...$props): Operator\LogicalOr
    {
        return new Operator\LogicalOr($props);
    }

    /**
     * Create a logical NOT operator proposition.
     *
     * @param Proposition $prop Exactly one Proposition
     */
    public function logicalNot(Proposition $prop): Operator\LogicalNot
    {
        return new Operator\LogicalNot([$prop]);
    }

    /**
     * Create a logical XOR operator proposition.
     *
     * @param Proposition ...$props One or more Propositions
     */
    public function logicalXor(Proposition ...$props): Operator\LogicalXor
    {
        return new Operator\LogicalXor($props);
    }

    /**
     * Check whether a Variable is already set.
     *
     * @param string $offset The Variable offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->variables[$offset]);
    }

    /**
     * Retrieve a Variable by offset.
     *
     * @param string $offset The Variable offset
     */
    public function offsetGet($offset): RuleBuilder\Variable
    {
        if (!isset($this->variables[$offset])) {
            $this->variables[$offset] = new RuleBuilder\Variable($this, $offset);
        }

        return $this->variables[$offset];
    }

    /**
     * Set the default value of a Variable.
     *
     * @param string $offset  The Variable offset
     * @param mixed  $value The Variable default value
     */
    public function offsetSet($offset, mixed $value): void
    {
        $this->offsetGet($offset)->setValue($value);
    }

    /**
     * Remove a defined Variable from the RuleBuilder.
     *
     * @param string $offset The Variable offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->variables[$offset]);
    }

    /**
     * Find an operator in the registered operator namespaces.
     *
     * @throws \LogicException if a matching operator is not found
     */
    public function findOperator(string $name): string
    {
        $operator = ucfirst($name);
        foreach (array_keys($this->operatorNamespaces) as $namespace) {
            $class = $namespace.'\\'.$operator;
            if (class_exists($class)) {
                return $class;
            }
        }

        throw new \LogicException(sprintf('Unknown operator: "%s"', $name));
    }
}
