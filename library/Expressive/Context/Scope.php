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

    const T_NUMBER          = 1;
    const T_OPERATOR        = 2;
    const T_SCOPE_OPEN      = 3;
    const T_SCOPE_CLOSE     = 4;
    const T_SIN_SCOPE_OPEN  = 5;
    const T_COS_SCOPE_OPEN  = 6;
    const T_TAN_SCOPE_OPEN  = 7;
    const T_SQRT_SCOPE_OPEN = 8;

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
            $type = self::T_OPERATOR;
        }
        if ($token === ')') {
            $type = self::T_SCOPE_CLOSE;
        }
        if ($token === '(') {
            $type = self::T_SCOPE_OPEN;
        }
        if ($token === 'sin(') {
            $type = self::T_SIN_SCOPE_OPEN;
        }
        if ($token === 'cos(') {
            $type = self::T_COS_SCOPE_OPEN;
        }
        if ($token === 'tan(') {
            $type = self::T_TAN_SCOPE_OPEN;
        }
        if ($token === 'sqrt(') {
            $type = self::T_SQRT_SCOPE_OPEN;
        }

        if (is_null($type)) {
            if (is_numeric($token)) {
                $type  = self::T_NUMBER;
                $token = (float)$token;
            }
        }

        switch ($type) {
            case self::T_NUMBER:
            case self::T_OPERATOR:
                $this->ops[] = $token;
                break;
            case self::T_SCOPE_OPEN:
                $this->builder->pushContext(new Scope());
                break;
            case self::T_SIN_SCOPE_OPEN:
                $this->builder->pushContext(new SinScope());
                break;
            case self::T_COS_SCOPE_OPEN:
                $this->builder->pushContext(new CosineScope());
                break;
            case self::T_TAN_SCOPE_OPEN:
                $this->builder->pushContext(new TanScope());
                break;
            case self::T_SQRT_SCOPE_OPEN:
                $this->builder->pushContext(new SqrtScope());
                break;
            case self::T_SCOPE_CLOSE:
                $scope_operation = $this->builder->popContext();
                $newContext     = $this->builder->getContext();
                if (is_null($scope_operation) || (!$newContext)) {
                    throw new OutOfScopeException();
                }
                $newContext->addOp($scope_operation);
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
     * @param $operation_list
     * @return bool|mixed
     */
    protected function _expressionLoop(& $operation_list)
    {
        while (list($i, $operation) = each($operation_list)) {
            if (!in_array($operation, array('^', '*', '/', '+', '-'))) {
                continue;
            }

            $left  = isset($operation_list[$i - 1]) ?
                (float)$operation_list[$i - 1] : null;
            $right = isset($operation_list[$i + 1]) ?
                (float)$operation_list[$i + 1] : null;
            $first_order  = (in_array('^', $operation_list));
            $second_order = (in_array('*', $operation_list) ||
                             in_array('/', $operation_list));
            $third_order  = (in_array('-', $operation_list) ||
                             in_array('+', $operation_list));

            $remove_sides = true;
            if ($first_order) {
                switch ($operation) {
                    case '^':
                        $operation_list[$i] = pow((float)$left, (float)$right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            } elseif ($second_order) {
                switch ($operation) {
                    case '*':
                        $operation_list[$i] = (float)($left * $right);
                        break;
                    case '/':
                        $operation_list[$i] = (float)($left / $right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            } elseif ($third_order) {
                switch ($operation) {
                    case '+':
                        $operation_list[$i] = (float)($left + $right);
                        break;
                    case '-':
                        $operation_list[$i] = (float)($left - $right);
                        break;
                    default:
                        $remove_sides = false;
                        break;
                }
            }

            if ($remove_sides) {
                unset($operation_list[$i + 1], $operation_list[$i - 1]);
                reset($operation_list = array_values($operation_list));
            }
        }
        if (count($operation_list) === 1) {
            return end($operation_list);
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

        $operation_list = $this->ops;

        while (true) {
            $operation_check = $operation_list;
            $result          = $this->_expressionLoop($operation_list);
            if ($result !== false) {
                return $result;
            }
            if ($operation_check === $operation_list) {
                break;
            } else {
                reset($operation_list = array_values($operation_list));
            }
        }
        throw new \Exception(__METHOD__);
    }
}
