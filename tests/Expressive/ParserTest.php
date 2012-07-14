<?php
namespace ExpressiveTests;
/**
 * @package  ExpressiveTests
 * @category Parser
 */
use Expressive\Parser;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @package  ExpressiveTests
 * @category Parser
 */
class ParserTest extends TestCase
{

    /**
     * @var Parser
     */
    protected $parser;

    public function setup()
    {
        $this->parser = new Parser;
    }

    public function tearDown()
    {
        $this->parser = null;
    }
    public function testConstructor()
    {
        $this->parser = new Parser('2+2');
        $this->assertEquals((string)4, $this->parser->getResult());
    }

    public function testToString()
    {
        $this->parser->setExpression('2+2');
        $expected = (string)4;
        $this->assertEquals($expected, (string)$this->parser);
        $this->assertEquals($expected, $this->parser->__toString());
    }

    public function testSetExpression()
    {
        $this->parser->setExpression('3+3');
        $expected = (string)6;
        $this->assertEquals($expected, (string)$this->parser);
    }

    public function testContextMethods()
    {
        $mock = $this->getMock('Expressive\Context\Scope');
        $this->assertNull($this->parser->popContext());
        $this->parser->pushContext($mock);
        $this->assertInstanceOf(
            'Expressive\Context\ContextInterface', $this->parser->popContext()
        );
        $this->assertNull($this->parser->popContext());
        $this->parser->pushContext($mock);
        $this->assertInstanceOf(
            'Expressive\Context\ContextInterface', $this->parser->getContext()
        );
    }
}
