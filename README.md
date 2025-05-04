# WP Gutenberg JSON Exporter

Expose Gutenberg block data for any WordPress post via a REST API — now with support for slugs, ACF, and Yoast SEO metadata.

## 🔧 Features
- Retrieve parsed Gutenberg blocks as structured JSON.
- Query by post ID or slug.
- Include [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) if active.
- Include [Yoast SEO](https://yoast.com/wordpress/plugins/seo/) metadata if available.
- Fallback to raw HTML content if blocks aren't detected.

## 📦 Endpoints

```
GET /wp-json/gutenberg-json/v1/post/{id}
GET /wp-json/gutenberg-json/v1/slug/{slug}
```

### Example Response
```json
{
  "id": 123,
  "slug": "hello-world",
  "title": "Hello World",
  "blocks": [
    {
      "blockName": "core/paragraph",
      "attrs": [],
      "innerHTML": "<p>Welcome</p>",
      "innerBlocks": []
    }
  ],
  "raw_content": "<!-- wp:paragraph --><p>Welcome</p><!-- /wp:paragraph -->",
  "acf": {
    "custom_field": "Value"
  },
  "yoast": {
    "seo_title": "Custom SEO Title",
    "seo_description": "Custom description",
    "focus_keyword": "WordPress"
  }
}
```

## 🚀 Installation
1. Upload to `wp-content/plugins/wp-gutenberg-json-exporter`
2. Activate from WordPress admin

## 🧪 Tested On
- WordPress 5.8+
- PHP 7.4+
- ACF Pro / Free
- Yoast SEO

## 📄 License
GPLv2 or later
