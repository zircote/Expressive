<?php
namespace ExpressiveTests;
/**
 * @package  ExpressiveTests
 * @category Scope
 */
use Expressive\Parser;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @package  ExpressiveTests
 * @category Scope
 */
class ScopeTest extends TestCase
{

    public function testAddition()
    {
        $parser = new Parser('2+2');
        $this->assertEquals(4, $parser->getResult());
    }

    public function testSubtraction()
    {
        $parser = new Parser('4-2');
        $this->assertEquals(2, $parser->getResult());
        $parser = new Parser('((4-2)/2)');
        $this->assertEquals(1, $parser->getResult());
    }

    public function testDivision()
    {
        $parser = new Parser('16/4');
        $this->assertEquals(4, $parser->getResult());
    }

    public function testMultiplication()
    {
        $parser = new Parser('2*2');
        $this->assertEquals(4, $parser->getResult());
    }

    public function testXor()
    {
        $parser = new Parser('2^2');
        $this->assertEquals(4, $parser->getResult());
        $parser = new Parser('4^2');
        $this->assertEquals(16, $parser->getResult());
        $parser = new Parser('8^2');
        $this->assertEquals(64, $parser->getResult());
    }

    public function testCosine()
    {
        $parser = new Parser('cos(60)');
        $this->assertEquals(0.5, $parser->getResult());
    }

    public function testTangent()
    {
        $parser = new Parser('tan(45)');
        $this->assertEquals(1, $parser->getResult());
    }

    public function testSine()
    {
        $parser = new Parser('sin(90)');
        $this->assertEquals(1, $parser->getResult());
    }

    public function testExp()
    {
        $parser = new Parser('exp(0.01)');
        $this->assertEquals(1.0100501670842, $parser->getResult());
    }

    public function testPi()
    {
        $parser = new Parser('PI');
        $this->assertEquals(pi(), $parser->getResult());
        $parser = new Parser('pi');
        $this->assertEquals(pi(), $parser->getResult());
    }

    public function testSqrt()
    {
        $parser = new Parser('sqrt(4)');
        $this->assertEquals(2, $parser->getResult());
    }

    public function complexExpressionProvider()
    {
        return array(
            array( 'sqrt(4)*2 + (4/2) * cos(60)', '5' ),
            array('(tan(45) * sqrt(4)) * (sqrt(4)*2 + (4/2) * cos(60))', '10'),
            array( '(-1 * (pi * 5^2)) -(12)', '-90.539816339745' ),
            array( 'sin(90) * tan(45)', '1' ),
            array('729^(1/3)','9')
        );
    }

    /**
     * @dataProvider complexExpressionProvider
     *
     * @param $expression
     * @param $expexted
     */
    public function testComplexExpression($expression, $expexted)
    {
        $parser = new Parser($expression);
        $this->assertEquals($expexted, $parser->getResult());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testOutOfScopeException()
    {
        $parser = new Parser(' This is a test 4-2');
        $parser->setThrowExceptions();
        echo $parser->getResult();
    }
}
