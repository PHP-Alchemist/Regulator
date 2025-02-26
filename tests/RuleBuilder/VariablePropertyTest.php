<?php

namespace Regulator\Test\RuleBuilder;

use PHPUnit\Framework\TestCase;
use Regulator\Context;
use Regulator\RuleBuilder;
use Regulator\RuleBuilder\Variable;
use Regulator\RuleBuilder\VariableProperty;

class VariablePropertyTest extends TestCase
{
    public function testConstructor()
    {
        $name = 'evil';
        $prop = new VariableProperty(new Variable(new RuleBuilder()), $name);
        $this->assertEquals($name, $prop->getName());
        $this->assertNull($prop->getValue());
    }

    public function testGetSetValue()
    {
        $values = explode(', ', 'Plug it, play it, burn it, rip it, drag and drop it, zip, unzip it');

        $prop = new VariableProperty(new Variable(new RuleBuilder()), 'technologic');
        foreach ($values as $valueString) {
            $prop->setValue($valueString);
            $this->assertEquals($valueString, $prop->getValue());
        }
    }

    public function testPrepareValue()
    {
        $values = [
            'root' => [
                'one' => 'Foo',
                'two' => 'BAR',
            ],
        ];

        $context = new Context($values);

        $var = new Variable(new RuleBuilder(), 'root');

        $propA = new VariableProperty($var, 'undefined', 'default');
        $this->assertInstanceOf(\Regulator\Value::class, $propA->prepareValue($context));
        $this->assertEquals(
            'default',
            $propA->prepareValue($context)->getValue(),
            "VariableProperties should return the default value if it's missing from the context."
        );

        $propB = new VariableProperty($var, 'one', 'FAIL');
        $this->assertEquals(
            'Foo',
            $propB->prepareValue($context)->getValue()
        );
    }

    public function testFluentInterfaceHelpersAndAnonymousVariables()
    {
        $context = new Context([
            'root' => [
                'a' => 1,
                'b' => 2,
                'c' => [1, 4],
                'd' => [
                    'foo' => 1,
                    'bar' => 2,
                    'baz' => [
                        'qux' => 3,
                    ],
                ],
                'e' => 'string',
                'f' => 'ring',
                'g' => 'stuff',
                'h' => 'STRING',
            ],
        ]);

        $root = new Variable(new RuleBuilder(), 'root');

        $varA = $root['a'];
        $varB = $root['b'];
        $varC = $root['c'];
        $varD = $root['d'];
        $varE = $root['e'];
        $varF = $root['f'];
        $varG = $root['g'];
        $varH = $root['h'];

        $this->assertInstanceOf(\Regulator\Operator\GreaterThan::class, $varA->greaterThan(0));
        $this->assertTrue($varA->greaterThan(0)->evaluate($context));
        $this->assertFalse($varA->greaterThan(2)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\GreaterThanOrEqualTo::class, $varA->greaterThanOrEqualTo(0));
        $this->assertTrue($varA->greaterThanOrEqualTo(0)->evaluate($context));
        $this->assertTrue($varA->greaterThanOrEqualTo(1)->evaluate($context));
        $this->assertFalse($varA->greaterThanOrEqualTo(2)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\LessThan::class, $varA->lessThan(0));
        $this->assertTrue($varA->lessThan(2)->evaluate($context));
        $this->assertFalse($varA->lessThan(0)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\LessThanOrEqualTo::class, $varA->lessThanOrEqualTo(0));
        $this->assertTrue($varA->lessThanOrEqualTo(1)->evaluate($context));
        $this->assertTrue($varA->lessThanOrEqualTo(2)->evaluate($context));
        $this->assertFalse($varA->lessThanOrEqualTo(0)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\EqualTo::class, $varA->equalTo(0));
        $this->assertTrue($varA->equalTo(1)->evaluate($context));
        $this->assertFalse($varA->equalTo(0)->evaluate($context));
        $this->assertFalse($varA->equalTo(2)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\NotEqualTo::class, $varA->notEqualTo(0));
        $this->assertFalse($varA->notEqualTo(1)->evaluate($context));
        $this->assertTrue($varA->notEqualTo(0)->evaluate($context));
        $this->assertTrue($varA->notEqualTo(2)->evaluate($context));

        $this->assertFalse($varA->greaterThan($varB)->evaluate($context));
        $this->assertTrue($varA->lessThan($varB)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\StringContains::class, $varE->stringContains('ring'));
        $this->assertTrue($varE->stringContains($varF)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\StringContainsInsensitive::class, $varE->stringContainsInsensitive('STRING'));
        $this->assertTrue($varE->stringContainsInsensitive($varH)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\StringDoesNotContain::class, $varE->stringDoesNotContain('cheese'));
        $this->assertTrue($varE->stringDoesNotContain($varG)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\SetContains::class, $varC->setContains(1));
        $this->assertTrue($varC->setContains($varA)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\SetDoesNotContain::class, $varC->setDoesNotContain(1));
        $this->assertTrue($varC->setDoesNotContain($varB)->evaluate($context));

        $this->assertInstanceOf(VariableProperty::class, $varD['bar']);
        $this->assertEquals($varD['foo']->getName(), 'foo');
        $this->assertTrue($varD['foo']->equalTo(1)->evaluate($context));

        $this->assertInstanceOf(VariableProperty::class, $varD['foo']);
        $this->assertEquals($varD['bar']->getName(), 'bar');
        $this->assertTrue($varD['bar']->equalTo(2)->evaluate($context));

        $this->assertInstanceOf(VariableProperty::class, $varD['baz']['qux']);
        $this->assertEquals($varD['baz']['qux']->getName(), 'qux');
        $this->assertTrue($varD['baz']['qux']->equalTo(3)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\EndsWith::class, $varE->endsWith('string'));
        $this->assertTrue($varE->endsWith($varE)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\EndsWithInsensitive::class, $varE->endsWithInsensitive('STRING'));
        $this->assertTrue($varE->endsWithInsensitive($varE)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\StartsWith::class, $varE->startsWith('string'));
        $this->assertTrue($varE->startsWith($varE)->evaluate($context));

        $this->assertInstanceOf(\Regulator\Operator\StartsWithInsensitive::class, $varE->startsWithInsensitive('STRING'));
        $this->assertTrue($varE->startsWithInsensitive($varE)->evaluate($context));
    }
}
