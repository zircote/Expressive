<?php
/**
 * @package
 * @category
 * @subcategory
 * Date: 7/10/12T11:06 AM
 */
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
 */
class TanScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        return tan(deg2rad(parent::evaluate()));
    }
}
