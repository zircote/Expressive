<?php
namespace Expressive\Context;
/**
 * @package  Expressive
 * @category Context
 */
/**
 * @package  Expressive
 * @category Context
 */
class ExpScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        $value = parent::evaluate();
        return exp($value);
    }
}

