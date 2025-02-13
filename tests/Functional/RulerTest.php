<?php

namespace Ruler\Test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ruler\Context;
use Ruler\RuleBuilder;

class RulerTest extends TestCase
{
    #[DataProvider('truthTableTwo')]
    public function testDeMorgan($p, $q)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalNot(
                    $rb->logicalAnd(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalOr(
                    $rb->logicalNot(
                        $rb['p']->equalTo(true)
                    ),
                    $rb->logicalNot(
                        $rb['q']->equalTo(true)
                    )
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableTwo')]
    public function testDeMorganTwo($p, $q)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalNot(
                    $rb->logicalOr(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalAnd(
                    $rb->logicalNot(
                        $rb['p']->equalTo(true)
                    ),
                    $rb->logicalNot(
                        $rb['q']->equalTo(true)
                    )
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableTwo')]
    public function testCommutation($p, $q)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true)
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalOr(
                    $rb['q']->equalTo(true),
                    $rb['p']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableTwo')]
    public function testCommutationTwo($p, $q)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['q']->equalTo(true)
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalAnd(
                    $rb['q']->equalTo(true),
                    $rb['p']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableThree')]
    public function testAssociation($p, $q, $r)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q', 'r'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb->logicalOr(
                        $rb['q']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalOr(
                    $rb->logicalOr(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    ),
                    $rb['r']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableThree')]
    public function testAssociationTwo($p, $q, $r)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q', 'r'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb->logicalAnd(
                        $rb['q']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalAnd(
                    $rb->logicalAnd(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    ),
                    $rb['r']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableThree')]
    public function testDistribution($p, $q, $r)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q', 'r'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb->logicalOr(
                        $rb['q']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalOr(
                    $rb->logicalAnd(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    ),
                    $rb->logicalAnd(
                        $rb['p']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableThree')]
    public function testDistributionTwo($p, $q, $r)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p', 'q', 'r'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb->logicalAnd(
                        $rb['q']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context),
            $rb->create(
                $rb->logicalAnd(
                    $rb->logicalOr(
                        $rb['p']->equalTo(true),
                        $rb['q']->equalTo(true)
                    ),
                    $rb->logicalOr(
                        $rb['p']->equalTo(true),
                        $rb['r']->equalTo(true)
                    )
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableOne')]
    public function testDoubleNegation($p)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p'));
        $this->assertEquals(
            $rb->create(
                $rb['p']->equalTo(true)
            )->evaluate($context),
            $rb->create(
                $rb->logicalNot(
                    $rb->logicalNot(
                        $rb['p']->equalTo(true)
                    )
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableOne')]
    public function testTautology($p)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p'));
        $this->assertEquals(
            $rb->create(
                $rb['p']->equalTo(true)
            )->evaluate($context),
            $rb->create(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb['p']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableOne')]
    public function testTautologyTwo($p)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p'));
        $this->assertEquals(
            $rb->create(
                $rb['p']->equalTo(true)
            )->evaluate($context),
            $rb->create(
                $rb->logicalAnd(
                    $rb['p']->equalTo(true),
                    $rb['p']->equalTo(true)
                )
            )->evaluate($context)
        );
    }

    #[DataProvider('truthTableOne')]
    public function testExcludedMiddle($p)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalOr(
                    $rb['p']->equalTo(true),
                    $rb->logicalNot(
                        $rb['p']->equalTo(true)
                    )
                )
            )->evaluate($context),
            true
        );
    }

    #[DataProvider('truthTableOne')]
    public function testNonContradiction($p)
    {
        $rb = new RuleBuilder();
        $context = new Context(\compact('p'));
        $this->assertEquals(
            $rb->create(
                $rb->logicalNot(
                    $rb->logicalAnd(
                        $rb['p']->equalTo(true),
                        $rb->logicalNot(
                            $rb['p']->equalTo(true)
                        )
                    )
                )
            )->evaluate($context),
            true
        );
    }

    public static function truthTableOne()
    {
        return [
            [true],
            [false],
        ];
    }

    public static function truthTableTwo()
    {
        return [
            [true,  true],
            [true,  false],
            [false, true],
            [false, false],
        ];
    }

    public static function truthTableThree()
    {
        return [
            [true,  true,  true],
            [true,  true,  false],
            [true,  false, true],
            [true,  false, false],
            [false, true,  true],
            [false, true,  false],
            [false, false, true],
            [false, false, false],
        ];
    }
}
