<?php

namespace PicPerf\StatamicPicPerf\Modifier;

use PicPerf\StatamicPicPerf\Trait\Transformable;
use Statamic\Modifiers\Modifier;

class PicPerf extends Modifier
{
    use Transformable;

    protected static $handle = 'picperf';

    /**
     * Transform a URL or HTML content to use PicPerf URLs.
     *
     * @param  string  $value   The value to be modified
     */
    public function index(string $content, $params, $context): string
    {
        $sitemapPath = collect($params)->contains('add_to_sitemap')
        ? $context['current_uri']
        : null;

        if ($this->isValidUrl($content)) {
            return $this->transformUrl($content, $sitemapPath);
        }

        return $this->transformMarkup($content, $sitemapPath);
    }
}
