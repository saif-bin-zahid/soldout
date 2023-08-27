<?php
namespace Custom\SoldOut\Block\Product;

use Magento\Catalog\Block\Product\View as DefaultView;

class View extends DefaultView
{
    public function isProductSoldOut()
    {
        return !$this->getProduct()->isSalable();
    }
}
