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
        return sin(deg2rad(parent::evaluate()));
    }
}
