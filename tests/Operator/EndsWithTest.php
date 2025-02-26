<?php

namespace Regulator\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Regulator\Context;
use Regulator\Operator;
use Regulator\Variable;

class EndsWithTest extends TestCase
{
    public function testInterface()
    {
        $varA = new Variable('a', 'foo bar baz');
        $varB = new Variable('b', 'foo');

        $op = new Operator\StartsWith($varA, $varB);
        $this->assertInstanceOf(\Regulator\Proposition::class, $op);
    }

    #[DataProvider('endsWithData')]
    public function testEndsWith($a, $b, $result)
    {
        $varA    = new Variable('a', $a);
        $varB    = new Variable('b', $b);
        $context = new Context();

        $op = new Operator\EndsWith($varA, $varB);
        $this->assertEquals($op->evaluate($context), $result);
    }

    public static function endsWithData()
    {
        return [
            ['supercalifragilistic', 'supercalifragilistic', true],
            ['supercalifragilistic', 'stic', true],
            ['supercalifragilistic', 'STIC', false],
            ['supercalifragilistic', 'super', false],
            ['supercalifragilistic', '', false],
        ];
    }
}
