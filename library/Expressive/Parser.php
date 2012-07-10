<?php
namespace Expressive;
/**
 * @package
 * @category
 * @subcategory
 */
use \Expressive\Context\ContextInterface;
use \Expressive\Context\Scope;
use \Expressive\Exception\ParseTreeNotFoundException;

/**
 * @package
 * @category
 * @subcategory
 */
class Parser
{
    /**
     * @var Scope
     */
    protected $expression;
    /**
     * @var array
     */
    protected $stack = array();
    /**
     * @var Scope
     */
    protected $queue;
    /**
     * @var array
     */
    protected $tokens = array();

    /**
     *
     * @param string|null $content
     */
    public function __construct($content = null)
    {
        if ($content) {
            $this->setExpression($content);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->tokenize()
            ->parse()
            ->evaluate();
    }

    /**
     *
     * @return Parser
     */
    public function tokenize()
    {
        $this->expression = str_replace(
            array("\n", "\r", "\t", " "), null, $this->expression
        );
        $this->expression = str_ireplace('pi', (string)pi(), $this->expression);
        $this->expression = str_replace('**', '^', $this->expression);
        $this->tokens     = preg_split(
            '@([\d\.]+)|exp\(|sqrt\(|(sin\(|cos\(|tan\(|\+|\-|\*|/|\^|\(|\))@',
            $this->expression,
            null,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        return $this;
    }

    /**
     *
     * @return Parser
     */
    public function parse()
    {
        $this->pushContext(new Scope());
        foreach ($this->tokens as $token) {
            $this
                ->getContext()
                ->handleToken($token);
        }
        $this->queue = $this->popContext();

        return $this;
    }

    /**
     *
     * @return mixed
     * @throws Exception\ParseTreeNotFoundException
     */
    public function evaluate()
    {
        if (!$this->queue) {
            throw new ParseTreeNotFoundException();
        }
        return $this
            ->getQueue()
            ->evaluate();
    }

    /**
     * @return Scope
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param null $content
     * @return Parser
     */
    public function setExpression($content = null)
    {
        $this->expression = $content;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     *
     * @param Scope $context
     */
    public function pushContext(ContextInterface $context)
    {
        array_push($this->stack, $context);
        $this
            ->getContext()
            ->setBuilder($this);
    }

    /**
     *
     * @return Scope
     */
    public function popContext()
    {
        return array_pop($this->stack);
    }

    /**
     *
     * @return Scope
     */
    public function getContext()
    {
        return end($this->stack);
    }
}
