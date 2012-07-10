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
class CosineScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        return cos(deg2rad(parent::evaluate()));
    }
}
