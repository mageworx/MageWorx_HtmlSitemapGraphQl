<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\HtmlSitemapGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use MageWorx\HtmlSitemapGraphQl\Model\Resolver\DataProvider\HtmlSitemap as HtmlSitemapDataProvider;

class HtmlSitemap implements ResolverInterface
{
    /**
     * @var HtmlSitemapDataProvider
     */
    protected $dataProvider;

    /**
     * HtmlSitemap constructor.
     *
     * @param HtmlSitemapDataProvider $dataProvider
     */
    public function __construct(HtmlSitemapDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $storeId = isset($args['storeId']) ? (int)$args['storeId'] :
            (int)$context->getExtensionAttributes()->getStore()->getId();

        return $this->dataProvider->getData($storeId);
    }
}
