<?php
namespace Expressive\Context;
/**
 * @package
 * @category
 * @subcategory
 * Date: 7/10/12T9:18 AM
 */
class SqrtScope extends Scope
{
    /**
     * @return bool|float|mixed
     */
    public function evaluate()
    {
        return sqrt(parent::evaluate());
    }
}
