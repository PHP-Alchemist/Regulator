<?php

namespace Regulator\Test\Fixtures;

use Regulator\Context;
use Regulator\Proposition;

class CallbackProposition implements Proposition
{
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        if (!\is_callable($callback)) {
            throw new \InvalidArgumentException('CallbackProposition expects a callable argument');
        }

        $this->callback = $callback;
    }

    public function evaluate(Context $context): bool
    {
        return \call_user_func($this->callback, $context);
    }
}
