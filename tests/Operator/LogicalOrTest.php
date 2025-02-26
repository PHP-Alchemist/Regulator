<?php

namespace Regulator\Test\Operator;

use PHPUnit\Framework\TestCase;
use Regulator\Context;
use Regulator\Operator;
use Regulator\Test\Fixtures\FalseProposition;
use Regulator\Test\Fixtures\TrueProposition;

class LogicalOrTest extends TestCase
{
    public function testInterface()
    {
        $true = new TrueProposition();

        $op = new Operator\LogicalOr([$true]);
        $this->assertInstanceOf(\Regulator\Proposition::class, $op);
    }

    public function testConstructor()
    {
        $true    = new TrueProposition();
        $false   = new FalseProposition();
        $context = new Context();

        $op = new Operator\LogicalOr([$true, $false]);
        $this->assertTrue($op->evaluate($context));
    }

    public function testAddPropositionAndEvaluate()
    {
        $true    = new TrueProposition();
        $false   = new FalseProposition();
        $context = new Context();

        $op = new Operator\LogicalOr();

        $op->addProposition($false);
        $this->assertFalse($op->evaluate($context));

        $op->addProposition($false);
        $this->assertFalse($op->evaluate($context));

        $op->addOperand($true);
        $this->assertTrue($op->evaluate($context));
    }

    public function testExecutingALogicalOrWithoutPropositionsThrowsAnException()
    {
        $this->expectException(\LogicException::class);
        $op = new Operator\LogicalOr();
        $op->evaluate(new Context());
    }
}
