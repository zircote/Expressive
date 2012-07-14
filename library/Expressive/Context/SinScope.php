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
class SinScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        $value = parent::evaluate();
        $value = deg2rad($value);
        return sin($value);
    }
}
