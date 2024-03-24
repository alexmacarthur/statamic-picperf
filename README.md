# PicPerf for Statamic

> This thing is a Statamic addon that automatically reformats, optimizes, and aggressively caches your website's images using [PicPerf](https://picperf.io).

## Features

- Automatically reformat images to modern, lightweight formats like AVIF and WebP.
- Serve the modern format that's _actually_ lighter, and only if your visitor's browser supports it.
- Aggressively cache the bananas out of your images starting from the browser, all the way up to the global CDN.
- The original versions of your images remain untouched.

## Getting Started

### Create an account.

In order to benefit from this addon, you'll need to first create an account at [PicPerf](https://picperf.io). A 14-day free trial is available, but in order keep your images fast & globally available beyond that, upgrade to a regular plan.

### Add your domain.

Add your website's domain inside the PicPerf dashboard.

## How to Install

Search for the PicPerf addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or install it with Composer in the root of your project:

```bash
composer require picperf/statamic-picperf
```

## How to Use

Out of the box, you need to do nothing. The addon will prefix all image URLs with `https://picperf.io`, allowing them to be automatically optimized & globally cached. Specifically, only the images in HTML `<img>` tags (including responsive images), inline `style` attributes, and CSS within `<style>` tags will be transformed. Image URLs in separate `.css` files or those injected with client-side JavaScript will not be affected.

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

## More Documentation

See it at [picperf.io/docs](https://picperf.io/docs).

## Feedback?

I need to hear it! Find me on [X](https://x.com/amacarthur) or [contact me](https://macarthur.me/contact/).
