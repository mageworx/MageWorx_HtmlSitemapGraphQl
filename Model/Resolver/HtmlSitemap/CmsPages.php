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
use MageWorx\HtmlSitemap\Model\ResourceModel\Cms\PageFactory;

class CmsPages implements ResolverInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * CmsPages constructor.
     *
     * @param PageFactory $pageFactory
     * @param HelperData $helper
     */
    public function __construct(PageFactory $pageFactory, HelperData $helper)
    {
        $this->pageFactory = $pageFactory;
        $this->helper      = $helper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $storeId = isset($value['store_id']) ? (int)$value['store_id'] :
            (int)$context->getExtensionAttributes()->getStore()->getId();

        return [
            'items' => $this->getCmsPages($storeId)
        ];
    }

    /**
     * @param int|null $storeId
     * @return array|null
     */
    protected function getCmsPages(?int $storeId = null): ?array
    {
        if (!$this->helper->isShowCmsPages($storeId)) {
            return null;
        }

        $collection = $this->pageFactory->create()->getCollection($storeId);

        if (!$collection) {
            return null;
        }

        $data = [];

        foreach ($collection as $id => $page) {
            $data[$id] = [
                'url'   => $page->getUrl(),
                'title' => $page->getTitle()
            ];
        }

        return $data;
    }
}
