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
use MageWorx\HtmlSitemap\Helper\StoreUrl as StoreUrlHelper;

class AdditionalLinks implements ResolverInterface
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var StoreUrlHelper
     */
    protected $storeUrlHelper;

    /**
     * AdditionalLinks constructor.
     *
     * @param HelperData $helper
     * @param StoreUrlHelper $storeUrlHelper
     */
    public function __construct(HelperData $helper, StoreUrlHelper $storeUrlHelper)
    {
        $this->helper         = $helper;
        $this->storeUrlHelper = $storeUrlHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $storeId = isset($value['store_id']) ? (int)$value['store_id'] :
            (int)$context->getExtensionAttributes()->getStore()->getId();

        return [
            'items' => $this->getAdditionalLinks($storeId),
        ];
    }

    /**
     * @param int|null $storeId
     * @return array|null
     */
    protected function getAdditionalLinks(int $storeId = null): ?array
    {
        if (!$this->helper->isShowLinks($storeId)) {
            return null;
        }

        $links    = [];
        $addLinks = $this->helper->getAdditionalLinks($storeId);

        if (count($addLinks)) {
            foreach ($addLinks as $linkString) {
                $link = explode(',', $linkString, 2);

                if (count($link) !== 2) {
                    continue;
                }

                $links[] =
                    [
                        'url'   => $this->buildUrl($link[0], $storeId),
                        'title' => htmlspecialchars(strip_tags(trim($link[1])))
                    ];
            }
        }

        return $links ?: null;
    }

    /**
     * Convert URL to store URL if schema don't exist.
     *
     * @param string $rawUrl
     * @param int|null $storeId
     * @return string
     */
    protected function buildUrl(string $rawUrl, int $storeId = null): string
    {
        $url = trim($rawUrl);

        return (strpos($url, '://') !== false) ? $url : $this->storeUrlHelper->getUrl(ltrim($url, '/'), $storeId);
    }
}
