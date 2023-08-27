<?php
namespace Custom\SoldOut\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class ProductObserver implements ObserverInterface
{
    protected $productRepository;
    protected $scopeConfig;

    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($this->isInDesiredCategory($product) && $product->getQty() == 0) {
            $product->setStockStatus(\Magento\CatalogInventory\Model\Stock\Status::STATUS_OUT_OF_STOCK);
            $this->productRepository->save($product);
        }
    }

    protected function isInDesiredCategory($product)
    {
        $selectedCategories = $this->scopeConfig->getValue(
            'soldout/general/selected_categories',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!is_array($selectedCategories)) {
            $selectedCategories = explode(',', $selectedCategories);
        }

        foreach ($product->getCategoryIds() as $categoryId) {
            if (in_array($categoryId, $selectedCategories)) {
                return true;
            }
        }

        return false;
    }
}
