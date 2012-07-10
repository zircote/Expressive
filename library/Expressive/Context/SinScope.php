<?php
/**
 * @package
 * @category
 * @subcategory
 */
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
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
