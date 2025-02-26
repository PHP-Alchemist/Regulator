<?php

namespace Regulator\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Regulator\Context;
use Regulator\Operator;
use Regulator\Variable;

class SubtractionTest extends TestCase
{
    public function testInterface()
    {
        $varA = new Variable('a', 1);
        $varB = new Variable('b', [2]);

        $op = new Operator\Subtraction($varA, $varB);
        $this->assertInstanceOf(\Regulator\VariableOperand::class, $op);
    }

    public function testInvalidData()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Arithmetic: values must be numeric');
        $varA    = new Variable('a', 'string');
        $varB    = new Variable('b', 'blah');
        $context = new Context();

        $op = new Operator\Subtraction($varA, $varB);
        $op->prepareValue($context);
    }

    #[DataProvider('subtractData')]
    public function testSubtract($a, $b, $result)
    {
        $varA    = new Variable('a', $a);
        $varB    = new Variable('b', $b);
        $context = new Context();

        $op = new Operator\Subtraction($varA, $varB);
        $this->assertEquals($op->prepareValue($context)->getValue(), $result);
    }

    public static function subtractData()
    {
        return [
            [6, 2, 4],
            [7, -3, 10],
            [2.5, 1.4, 1.1],
        ];
    }
}
