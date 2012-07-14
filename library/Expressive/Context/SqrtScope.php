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
class SqrtScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        $value = parent::evaluate();
        return sqrt($value);
    }
}
