<?php
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
 */
use \Expressive\Exception\OutOfScopeException;
use \Expressive\Exception\UnknownTokenException;
use \Expressive\Parser;

/**
 * @package
 * @category
 * @subcategory
 */
class Scope implements ContextInterface
{
    /**
     * @var Parser
     */
    protected $builder = null;
    /**
     * @var array
     */
    protected $childrenContexts = array();
    /**
     * @var array
     */
    protected $raw = array();
    /**
     * @var array
     */
    protected $ops = array();

    const T_NUM   = 1;
    const T_OP    = 2;
    const T_SCOPE = 3;
    const T_CLOSE = 4;
    const T_SIN   = 5;
    const T_COS   = 6;
    const T_TAN   = 7;
    const T_SQRT  = 8;
    const T_EXP   = 9;

    /**
     * @param Parser $builder
     */
    public function setBuilder(Parser $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('', $this->raw);
    }

    /**
     * @param $operation
     */
    public function addOp($operation)
    {
        $this->ops[] = $operation;
    }

    /**
     *
     * @param $token
     * @throws \Expressive\Exception\UnknownTokenException
     * @throws \Expressive\Exception\OutOfScopeException
     */
    public function handleToken($token)
    {
        $type = null;
        if (in_array($token, array('*', '/', '+', '-', '^'))) {
            $type = self::T_OP;
        }
        if ($token === ')') {
            $type = self::T_CLOSE;
        }
        if ($token === '(') {
            $type = self::T_SCOPE;
        }
        if ($token === 'sin(') {
            $type = self::T_SIN;
        }
        if ($token === 'cos(') {
            $type = self::T_COS;
        }
        if ($token === 'tan(') {
            $type = self::T_TAN;
        }
        if ($token === 'sqrt(') {
            $type = self::T_SQRT;
        }
        if ($token === 'exp(') {
            $type = self::T_EXP;
        }

        if (is_null($type)) {
            if (is_numeric($token)) {
                $type  = self::T_NUM;
                $token = (float)$token;
            }
        }
        switch ($type) {
            case self::T_NUM:
            case self::T_OP:
                $this->ops[] = $token;
                break;
            case self::T_SCOPE:
                $this->builder->pushContext(new Scope());
                break;
            case self::T_SIN:
                $this->builder->pushContext(new SinScope());
                break;
            case self::T_COS:
                $this->builder->pushContext(new CosScope());
                break;
            case self::T_TAN:
                $this->builder->pushContext(new TanScope());
                break;
            case self::T_SQRT:
                $this->builder->pushContext(new SqrtScope());
                break;
            case self::T_EXP:
                $this->builder->pushContext(new ExpScope());
                break;
            case self::T_CLOSE:
                $operation  = $this->builder->popContext();
                $newContext = $this->builder->getContext();
                if (is_null($operation) || (!$newContext)) {
                    throw new OutOfScopeException();
                }
                $newContext->addOp($operation);
                break;
            default:
                throw new UnknownTokenException($token);
                break;
        }
    }

    /**
     * order of operations:
     * - parentheses, these should all ready be executed before this method is called
     * - exponents, first order
     * - mult/divi, second order
     * - addi/subt, third order
     *
     * @param $operationList
     * @return bool|mixed
     */
    protected function _expressionLoop(& $operationList)
    {
        while (list($i, $operation) = each($operationList)) {
            if (!in_array($operation, array('^', '*', '/', '+', '-'))) {
                continue;
            }

            $left         = isset($operationList[$i - 1]) ?
                (float)$operationList[$i - 1] : null;
            $right        = isset($operationList[$i + 1]) ?
                (float)$operationList[$i + 1] : null;
            $first_order  = (in_array('^', $operationList));
            $second_order = (in_array('*', $operationList) ||
                             in_array('/', $operationList));
            $third_order  = (in_array('-', $operationList) ||
                             in_array('+', $operationList));

            $remove_sides = true;
            if ($first_order) {
                switch ($operation) {
                    case '^':
                        $operationList[$i] = pow((float)$left, (float)$right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            } elseif ($second_order) {
                switch ($operation) {
                    case '*':
                        $operationList[$i] = (float)($left * $right);
                        break;
                    case '/':
                        $operationList[$i] = (float)($left / $right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            } elseif ($third_order) {
                switch ($operation) {
                    case '+':
                        $operationList[$i] = (float)($left + $right);
                        break;
                    case '-':
                        $operationList[$i] = (float)($left - $right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            }

            if ($remove_sides) {
                unset($operationList[$i + 1], $operationList[$i - 1]);
                reset($operationList = array_values($operationList));
            }
        }
        if (count($operationList) === 1) {
            return end($operationList);
        }
        return false;
    }

    /**
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function evaluate()
    {
        foreach ($this->ops as $i => $operation) {
            if (is_object($operation)) {
                /** @var Scope $operation */
                $this->ops[$i] = $operation->evaluate();
            }
        }

        $operationList = $this->ops;

        while (true) {
            $operationCheck = $operationList;
            $result         = $this->_expressionLoop($operationList);
            if ($result !== false) {
                return $result;
            }
            if ($operationCheck === $operationList) {
                break;
            } else {
                reset($operationList = array_values($operationList));
            }
        }
        throw new \Exception(__METHOD__);
    }
}
