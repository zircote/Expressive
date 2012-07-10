<?php
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
 * Date: 7/10/12T12:40 PM
 */
/**
 * @package
 * @category
 * @subcategory
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

