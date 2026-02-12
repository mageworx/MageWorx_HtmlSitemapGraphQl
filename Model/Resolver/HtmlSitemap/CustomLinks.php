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
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class CustomLinks implements ResolverInterface
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * CustomLinks constructor.
     *
     * @param HelperData $helper
     * @param EventManagerInterface $eventManager
     */
    public function __construct(HelperData $helper, EventManagerInterface $eventManager)
    {
        $this->helper       = $helper;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $storeId = isset($value['store_id']) ? (int)$value['store_id'] :
            (int)$context->getExtensionAttributes()->getStore()->getId();

        return [
            'sections' => $this->getCustomLinksGroupedBySection($storeId)
        ];
    }

    /**
     * @param int|null $storeId
     * @return array|null
     */
    protected function getCustomLinksGroupedBySection(?int $storeId = null): ?array
    {
        if (!$this->helper->isShowCustomLinks($storeId)) {
            return null;
        }

        $customLinks = new \Magento\Framework\DataObject;

        /**
         * @see \MageWorx\HtmlSitemap\Block\Sitemap\CustomLinks::getCustomLinkContainer()
         */
        $this->eventManager->dispatch(
            'mageworx_html_sitemap_load_additional_collection',
            [
                'object'   => $customLinks,
                'store_id' => $storeId
            ]
        );

        $sections = $customLinks->getData();

        if (empty($sections)) {
            return null;
        }

        $data = [];

        foreach ($sections as $section) {
            if (empty($section['section_title']) || empty($section['items'])) {
                continue;
            }

            $items = [];

            foreach ($section['items'] as $item) {
                if (empty($item['title'] || empty($item['url']))) {
                    continue;
                }

                $items[] = [
                    'title' => $item['title'],
                    'url'   => $item['url']
                ];
            }

            $data[] = [
                'section_title' => $section['section_title'],
                'items'         => $items ?: null
            ];
        }

        return $data;
    }
}
