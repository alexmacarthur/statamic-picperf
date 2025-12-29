# PicPerf for Statamic

> This thing is a Statamic addon that automatically reformats, optimizes, and aggressively caches your website's images using [PicPerf](https://picperf.io).

## Features

- Automatically reformat images to modern, lightweight formats like AVIF and WebP.
- Serve the modern format that's _actually_ lighter, and only if your visitor's browser supports it.
- Aggressively cache the bananas out of your images starting from the browser, all the way up to the best global CDN out there.
- The original versions of your images remain untouched.
- Use the [auto-generated sitemap](https://picperf.io/docs/sitemap) for an extra boost of SEO.

## Getting Started

### Create an account.

In order to benefit from this addon, you'll need to first create an account at [PicPerf](https://picperf.io). 14-day free trial!

### Add your domain.

Add your website's domain inside the PicPerf dashboard.

## How to Install

Search for the PicPerf addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or install it with Composer in the root of your project:

```bash
composer require picperf/statamic-picperf
```

## How to Use

Out of the box, you need to do nothing. The addon will prefix all image URLs with `https://picperf.io`, allowing them to be automatically optimized & globally cached. Specifically, only the images in HTML `<img>` tags (including responsive images), inline `style` attributes, and CSS within `<style>` tags will be transformed. Image URLs in separate `.css` files or those injected with client-side JavaScript will not be affected.

### Publishing Your Configuration

If you need to customize the addon's behavior, you can publish the configuration file to your project:

```bash
php artisan vendor:publish --tag=picperf-config
```

This will copy the `picperf.php` configuration file to your `config/` directory, where you can modify it as needed.

### Local Development

In order for prefixed images to load, PicPerf requires them to be publicly available, which is likely not the case when working locally. To address this, in local development, all images that _will be_ optimized with PicPerf will have the `picperf_local=true` query parameter:

```
https://example.com/my-image.jpg?picperf_local=true
```

This behavior will only occur in lower environments &mdash; by default, defined as either `local` or `testing`. If you'd like to change the environments where local images will be loaded, modify the `lower_enviornments` configuration option:

```php
<?php

// config/picperf.php

return [
    'lower_environments' => [
        'local',
        'testing'
    ],
];
```

### Disabling Universal Transformation

You can opt out of universal image transformation by setting `transform_all_markup` to `false` inside your `config/picperf.php` file. When it's disabled like this, only content processed with modifiers will be transformed:

```php
<?php

// config/picperf.php

return [
    'transform_all_markup' => false,
];
```

### Using a Modifier

The `picperf` modifier will perform same image transformations in a more controlled way. You can use it on single URLs...

```
{{ featured_image }}
    <img src="{{ url | picperf }}" alt="{{ alt }}" />
{{ /featured_image }}
```

Or chunks of HTML:

```
<article>
    {{ content | picperf }}
</article>
```

### Transforming Root-Relative Paths

By default, the addon won't transform root-relative images (ex: `/some/image.jpg`). However, if you set a `host` in your `config/picperf.php` file, it'll be tacked onto those paths so your images can reap the performance gains:

```php
<?php

// config/picperf.php

return [
    'host' => 'https://macarthur.me',
];
```

### Adding Images to the Auto-Generated Sitemap

PicPerf will automatically [generate an image sitemap](https://picperf.io/docs/sitemap) for you based on image requests from the last 90 days.

### Enabling Image Sitemap Inclusions

By default, no images are included in this sitemap. To enable it for all images, set the `add_sitemap_path` configuration property to `true`:

```php
<?php

// config/picperf.php

return [
    // ... other configuration values
    'add_sitemap_paths' => true,
];
```

Setting this will add a `sitemap_path` query parameter to each image based on the current page path.

#### Adding Images to Sitemap via Modifier

If you'd like more control over which images are included in the sitemap, use the `add_to_sitemap` parameter on the `picperf` modifier:

```
{{ featured_image }}
    <img src="{{ url | picperf:add_to_sitemap }}" alt="{{ alt }}" />
{{ /featured_image }}
```

Again, this will cause the `sitemap_path` parameter to be appended with the current page.

#### Auto-Registered Sitemap Endpoint

By default, this addon will automatically register an image sitemap route at `https://ur-site.com/picperf/sitemap.xml`. All this does is proxy the contents of the auto-generated sitemap at `https://picperf.io/sitemap/ur-site.com`. The benefit, however, is that Google will be able to successfully parse it since it'll be served from your domain.

If you _don't_ want a sitemap endpoint to be registered, set the `register_sitemap` configuration key to `false`:

```php
<?php

// config/picperf.php

return [
    // ... other configuration values
    'register_sitemap' => false,
];
```

#### Auto-Added Sitemap robots.txt Entry

On installation, the addon will also add a `Sitemap` entry to your `robots.txt`. If it was removed and you ever need to add it again, you can run the following command:

```
php please picperf:register-sitemap
```

## More Documentation

See it at [picperf.io/docs](https://picperf.io/docs).

## Feedback?

I need to hear it! Find me on [X](https://x.com/amacarthur) or [contact me](https://macarthur.me/contact/).
