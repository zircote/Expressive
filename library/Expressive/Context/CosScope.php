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
class CosScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        $value = parent::evaluate();
        $value = deg2rad($value);
        return cos($value);
    }
}
