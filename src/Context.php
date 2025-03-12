<?php

/*
 * This file is part of the Regulator package, an OpenSky project.
 *
 * Copyright (c) 2009 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Regulator;

/**
 * Regulator Context.
 *
 * The Context contains facts with which to evaluate a Rule or other Proposition.
 *
 * Derived from Pimple, by Fabien Potencier:
 *
 * https://github.com/fabpot/Pimple
 *
 * @author Fabien Potencier
 * @author Justin Hileman <justin@justinhileman.info>
 *
 * @template ArrayAccess<string>
 */
class Context implements \ArrayAccess
{
    private array $keys = [];
    private array $values = [];
    private array $frozen = [];
    private array $raw = [];

    private $shared;
    private $protected;

    /**
     * Context constructor.
     *
     * Optionally, bootstrap the context by passing an array of fact names and
     * values.
     *
     * @param array $values (default: array())
     */
    public function __construct(array $values = [])
    {
        $this->shared    = new \SplObjectStorage();
        $this->protected = new \SplObjectStorage();

        foreach ($values as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * Check if a fact is defined.
     *
     * @param string $offset The unique offset for the fact
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->keys[$offset]);
    }

    /**
     * Get the value of a fact.
     *
     * @param string $offset The unique offset for the fact
     *
     * @return mixed The resolved value of the fact
     * @throws \InvalidArgumentException if the offset is not defined
     *
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException(sprintf('Fact "%s" is not defined.', $offset));
        }

        $value = $this->values[$offset];

        // If the value is already frozen, or if it's not callable, or if it's protected, return the raw value
        if (isset($this->frozen[$offset]) || !\is_object($value) || $this->protected->contains($value) || !$this->isCallable($value)) {
            return $value;
        }

        // If this is a shared value, resolve, freeze, and return the result
        if ($this->shared->contains($value)) {
            $this->frozen[$offset] = true;
            $this->raw[$offset]    = $value;

            return $this->values[$offset] = $value($this);
        }

        // Otherwise, resolve and return the result
        return $value($this);
    }

    /**
     * Set a fact offset and value.
     *
     * A fact will be lazily evaluated if it is a Closure or invokable object.
     * To define a fact as a literal callable, use Context::protect.
     *
     * @param string $offset The unique offset for the fact
     * @param mixed $value The value or a closure to lazily define the value
     *
     * @throws \RuntimeException if a frozen fact overridden
     */
    public function offsetSet($offset, $value) : void
    {
        if (isset($this->frozen[$offset])) {
            throw new \RuntimeException(sprintf('Cannot override frozen fact "%s".', $offset));
        }

        $this->keys[$offset]   = true;
        $this->values[$offset] = $value;
    }

    /**
     * Unset a fact.
     *
     * @param string $offset The unique offset for the fact
     */
    public function offsetUnset($offset) : void
    {
        if ($this->offsetExists($offset)) {
            $value = $this->values[$offset];

            if (\is_object($value)) {
                $this->shared->detach($value);
                $this->protected->detach($value);
            }

            unset($this->keys[$offset], $this->values[$offset], $this->frozen[$offset], $this->raw[$offset]);
        }
    }

    /**
     * Define a fact as "shared". This lazily evaluates and stores the result
     * of the callable for the scope of this Context instance.
     *
     * @param callable $callable A fact callable to share
     *
     * @return callable The passed callable
     * @throws \InvalidArgumentException if the callable is not a Closure or invokable object
     *
     */
    public function share($callable)
    {
        if (!$this->isCallable($callable)) {
            throw new \InvalidArgumentException('Value is not a Closure or invokable object.');
        }

        $this->shared->attach($callable);

        return $callable;
    }

    /**
     * Protect a callable from being interpreted as a lazy fact definition.
     *
     * This is useful when you want to store a callable as the literal value of
     * a fact.
     *
     * @param callable $callable A callable to protect from being evaluated
     *
     * @return callable The passed callable
     * @throws \InvalidArgumentException if the callable is not a Closure or invokable object
     *
     */
    public function protect($callable)
    {
        if (!$this->isCallable($callable)) {
            throw new \InvalidArgumentException('Callable is not a Closure or invokable object.');
        }

        $this->protected->attach($callable);

        return $callable;
    }

    /**
     * Get a fact or the closure defining a fact.
     *
     * @param string $name The unique name for the fact
     *
     * @return mixed The value of the fact or the closure defining the fact
     * @throws \InvalidArgumentException if the name is not defined
     *
     */
    public function raw($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException(sprintf('Fact "%s" is not defined.', $name));
        }

        if (isset($this->frozen[$name])) {
            return $this->raw[$name];
        }

        return $this->values[$name];
    }

    /**
     * Get all defined fact names.
     */
    public function keys() : array
    {
        return array_keys($this->keys);
    }

    /**
     * Check whether a value is a Closure or invokable object.
     *
     * @param mixed $callable
     */
    protected function isCallable($callable) : bool
    {
        return \is_object($callable) && \is_callable($callable);
    }
}
