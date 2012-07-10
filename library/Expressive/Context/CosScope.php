<?php
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
 * Date: 7/10/12T11:06 AM
 */
/**
 * @package
 * @category
 * @subcategory
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
