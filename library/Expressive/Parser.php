<?php
namespace Expressive;
/**
 * @package  Expressive
 * @category Parser
 */
use Expressive\Context\ContextInterface;
use Expressive\Context\Scope;
use Expressive\Exception\ParseTreeNotFoundException;

/**
 * @package  Expressive
 * @category Parser
 */
class Parser
{

    const EXPR_REGEX = '@([\d\.]+)|(sin\(|cos\(|tan\(|sqrt\(|exp\(|\+|\-|\*|/|\^|\(|\))@';

    protected $_throwExceptions = true;
    /**
     * @var Scope
     */
    protected $_expression;

    /**
     * @var array
     */
    protected $_stack = array();

    /**
     * @var Scope
     */
    protected $_queue;

    /**
     * @var array
     */
    protected $_tokens = array();

    /**
     *
     * @param string|null $content
     */
    public function __construct($content = null, $throwExceptions = false)
    {
        $this->setThrowExceptions($throwExceptions);
        if ($content) {
            $this->setExpression($content);
        }
    }

    /**
     * @param null $content
     *
     * @return Parser
     */
    public function setExpression($content = null)
    {
        $this->_expression = $content;
        return $this;
    }

    /**
     *
     * @param Scope $context
     */
    public function pushContext(ContextInterface $context)
    {
        array_push($this->_stack, $context);
        $this->getContext()
            ->setBuilder($this);
    }

    /**
     *
     * @return Scope
     */
    public function popContext()
    {
        return array_pop($this->_stack);
    }

    /**
     * @param bool $throwException
     *
     * @return Parser
     */
    public function setThrowExceptions($throwException = true)
    {
        $this->_throwExceptions = (bool) $throwException;
        return $this;
    }
    /**
     *
     * @return Scope
     */
    public function getContext()
    {
        return end($this->_stack);
    }

    /**
     * @return string
     */
    public function getResult()
    {
        try {
            $result = (string)$this->_tokenize()
                ->_parse()
                ->_evaluate();
        }
        catch (\Exception $e) {
            if(!$this->_throwExceptions){
                $result = (string)$e->getMessage() . PHP_EOL;
            } else {
                throw $e;
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getResult();
    }

    /**
     *
     * @return Parser
     */
    protected function _tokenize()
    {
        $this->_expression = str_replace(
            array( "\n", "\r", "\t", " " ), null, $this->_expression
        );
        $this->_expression =
            str_ireplace('pi', (string)pi(), $this->_expression);
        $this->_expression = str_replace('**', '^', $this->_expression);
        $this->_tokens = preg_split(
            self::EXPR_REGEX,
            $this->_expression,
            null,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        return $this;
    }

    /**
     *
     * @return Parser
     */
    protected function _parse()
    {
        $this->pushContext(new Scope());
        foreach ($this->_tokens as $token) {
            $this
                ->getContext()
                ->handleToken($token);
        }
        $this->_queue = $this->popContext();

        return $this;
    }

    /**
     *
     * @return mixed
     * @throws Exception\ParseTreeNotFoundException
     */
    protected function _evaluate()
    {
        if (!$this->_queue) {
            throw new ParseTreeNotFoundException();
        }
        return $this->_getQueue()
            ->evaluate();
    }

    /**
     * @return Scope
     */
    protected function _getQueue()
    {
        return $this->_queue;
    }

    /**
     *
     * @return array
     */
    protected function _getTokens()
    {
        return $this->_tokens;
    }
}
