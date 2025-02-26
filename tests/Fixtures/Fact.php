<?php

namespace Regulator\Test\Fixtures;

class Fact
{
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        if (null !== $value) {
            $this->value = $value;
        }
    }
}
