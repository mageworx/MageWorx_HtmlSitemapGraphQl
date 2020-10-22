# MageWorx_HtmlSitemapGraphQl

GraphQL API module for Mageworx [Magento 2 SEO Suite Ultimate](https://www.mageworx.com/magento-2-seo-extension.html) extension. 

## Installation
**1) Copy-to-paste method**
- Download this module and upload it to the `app/code/MageWorx/HtmlSitemapGraphQl` directory *(create "HtmlSitemapGraphQl" first if missing)*

**2) Installation using composer (from packagist)**
- Execute the following command: `composer require MageWorx_HtmlSitemapGraphQl`

## How to use
**HtmlSitemapGraphQl** module allows displaying the HTML sitemap:

**Syntax**:

```
products(
  search: String
  filter: ProductAttributeFilterInput
  pageSize: Int
  currentPage: Int
  sort: ProductAttributeSortInput
): Products
```

The query includes the following attributes:

- meta_description
- meta_keywords
- categories
- cms_pages
- additional_links
- custom_links

**Request:**

```
{mwHtmlSitemap (storeId: 1) {
  categories {
    items {
      title
      url
      level
    }
  }
}
}
```

**Response:**

```
{
  "data": {
    "mwHtmlSitemap": {
      "categories": {
        "items": [
          {
            "title": "What's New",
            "url": "https://store_url/default/what-is-new.html",
            "level": 2
          },
          {
            "title": "test",
            "url": "https://store_url/default/test.html",
            "level": 2
          }
        ]
      }
    }
  }
}
```

