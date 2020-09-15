<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\HtmlSitemapGraphQl\Model\Resolver\HtmlSitemap;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use MageWorx\HtmlSitemap\Helper\Data as HelperData;
use MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\CategoryFactory;
use MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\ProductFactory;

class Categories implements ResolverInterface
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var array|null
     */
    protected $categories;

    /**
     * @var int|null
     */
    protected $storeId;

    /**
     * Categories constructor.
     *
     * @param HelperData $helper
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(HelperData $helper, CategoryFactory $categoryFactory, ProductFactory $productFactory)
    {
        $this->helper          = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory  = $productFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = isset($value['store_id']) ? (int)$value['store_id'] :
            (int)$context->getExtensionAttributes()->getStore()->getId();

        return [
            'items' => $this->getCategories($storeId)
        ];
    }

    /**
     * @param int|null $storeId
     * @return array|null
     */
    protected function getCategories(int $storeId = null): ?array
    {
        if (!$this->helper->isShowCategories($storeId)) {
            return null;
        }

        $collection       = $this->categoryFactory->create()->getCollection($storeId);
        $this->categories = [];
        $this->storeId    = $storeId;

        if (!empty($collection)) {
            foreach ($collection as $key => $item) {
                if (!isset($level)) {
                    $level = $item->getLevel();
                }

                if ($item->getLevel() == $level) {
                    $this->categories[] = $this->prepareCategoryData($item);

                    unset($collection[$key]);
                    $this->addChildren((int)$item->getId(), $collection);
                }
            }
        }

        return $this->categories;
    }

    /**
     * @param \Magento\Framework\DataObject $category
     * @return array
     */
    protected function prepareCategoryData(\Magento\Framework\DataObject $category): array
    {
        return [
            'title'    => $category->getName(),
            'url'      => $category->getUrl(),
            'level'    => $category->getLevel(),
            'products' => [
                'items' => $this->getCategoryProducts($category)
            ]
        ];
    }

    /**
     * Convert categories to tree
     *
     * @param int $parentId
     * @param array $collection
     */
    protected function addChildren(int $parentId, array &$collection): void
    {
        foreach ($collection as $key => $item) {
            if ($item->getParentId() != $parentId) {
                continue;
            }

            $this->categories[] = $this->prepareCategoryData($item);

            if ($item->getChildrenCount()) {
                $this->addChildren((int)$item->getId(), $collection);
            }
        }
    }

    /**
     * @param \Magento\Framework\DataObject $category
     * @return array|null
     */
    protected function getCategoryProducts(\Magento\Framework\DataObject $category): ?array
    {
        if (!$this->helper->isShowProducts($this->storeId)) {
            return null;
        }

        if ($this->helper->isUseCategoryDisplayMode($this->storeId)) {
            if ($category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
                return null;
            }
        }

        $products   = [];
        $collection = $this->productFactory->create()->getCollection((int)$category->getId(), $this->storeId);

        foreach ($collection as $item) {
            $products[] = [
                'title' => $item->getName(),
                'url'   => $item->getUrl()
            ];
        }

        return $products;
    }
}
