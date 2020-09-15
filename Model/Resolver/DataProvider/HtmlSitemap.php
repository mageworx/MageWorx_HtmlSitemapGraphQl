<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\HtmlSitemapGraphQl\Model\Resolver\DataProvider;

use MageWorx\HtmlSitemap\Helper\Data as HelperData;

class HtmlSitemap
{
    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * HtmlSitemap constructor.
     *
     * @param HelperData $helper
     */
    public function __construct(HelperData $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getData(int $storeId = null): array
    {
        return [
            'title'            => $this->helper->getTitle($storeId),
            'meta_description' => $this->helper->getMetaDescription($storeId),
            'meta_keywords'    => $this->helper->getMetaKeywords($storeId),
            'store_id'         => $storeId
        ];
    }
}
