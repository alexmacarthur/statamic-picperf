<?php

namespace PicPerf\StatamicPicPerf\Trait;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PicPerf\StatamicPicPerf\Constants;

trait Transformable
{
    use Urlable, Configurable;

    public function transformUrl($url): string
    {
        try {
            $urlString = Str::of($url);

            // It already starts with the PicPerf host.
            if ($urlString->startsWith(Constants::PIC_PERF_HOST)) {
                return $url;
            }

            // It's not a valid URL.
            if (!$this->isValidUrl($url)) {
                return $url;
            }

            if ($this->isRootRelativeUrl($url)) {
                // The host is configured! Prepend it.
                $configuredHost = Str::of($this->getConfig('host'))
                    ->trim()
                    ->replaceEnd('/', '')
                    ->toString();

                if (!empty($configuredHost)) {
                    return $urlString
                        ->prepend($configuredHost)
                        ->prepend(Constants::PIC_PERF_HOST)
                        ->toString();
                }

                return $url;
            }

            // It's probably a local image.
            if (Str::of(parse_url($url)['host'])->isMatch("/localhost|\.test$/")) {
                return $url;
            }

            return $urlString->prepend(Constants::PIC_PERF_HOST)->toString();
        } catch (\Exception $e) {
            Log::error("Failed to parse URL: $url");

            return $url;
        }
    }

    public function transformMarkup($content)
    {
        return $this->transformImageHtml(
            $this->transformStyleTags(
                $this->transformInlineStyles($content)
            )
        );
    }

    private function transformImageHtml($content)
    {
        // Find every image tag.
        return preg_replace_callback('/(<img)[^\>]*(\>|>)/is', function ($match) {

            return preg_replace_callback(Constants::IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }

    private function transformStyleTags($content)
    {
        // Find every style tag.
        return preg_replace_callback('/<style.*?>(.*?)<\/style>/is', function ($match) {

            // Find every URL.
            return preg_replace_callback(Constants::IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }

    private function transformInlineStyles($content)
    {
        // Find every inline style.
        return preg_replace_callback('/style=(?:"|\')([^"]*)(?:"|\')/is', function ($match) {

            // Find every URL.
            return preg_replace_callback(Constants::IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }
}
