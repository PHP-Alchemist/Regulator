<?php

namespace Ruler\Test\Operator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ruler\Context;
use Ruler\Operator;
use Ruler\Variable;

class MaxTest extends TestCase
{
    public function testInterface()
    {
        $var = new Variable('a', [5, 2, 9]);

        $op = new Operator\Max($var);
        $this->assertInstanceOf(\Ruler\VariableOperand::class, $op);
    }

    #[DataProvider('invalidData')]
    public function testInvalidData($datum)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('max: all values must be numeric');
        $var = new Variable('a', $datum);
        $context = new Context();

        $op = new Operator\Max($var);
        $op->prepareValue($context);
    }

    public static function invalidData()
    {
        return [
            ['string'],
            [['string']],
            [[1, 2, 3, 'string']],
            [['string', 1, 2, 3]],
        ];
    }

    #[DataProvider('maxData')]
    public function testMax($a, $result)
    {
        $var = new Variable('a', $a);
        $context = new Context();

        $op = new Operator\Max($var);
        $this->assertEquals(
            $result,
            $op->prepareValue($context)->getValue()
        );
    }

    public static function maxData()
    {
        return [
            [5, 5],
            [[], null],
            [[5], 5],
            [[-2, -5, -242], -2],
            [[2, 5, 242], 242],
        ];
    }
}
