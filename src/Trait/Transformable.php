<?php

namespace PicPerf\StatamicPicPerf\Trait;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

const PIC_PERF_HOST = 'https://picperf.io/';
const IMAGE_URL_PATTERN = '/(?:https?:\/)?\/[^ ,]+\.(jpg|jpeg|png|gif|webp|avif)/i';

trait Transformable
{
    use Urlable, Configurable;

    public function transformUrl($url): string
    {
        try {
            $urlString = Str::of($url);

            // It already starts with the PicPerf host.
            if ($urlString->startsWith(PIC_PERF_HOST)) {
                return $url;
            }

            // It's a relative path, or otherwise invalid URL.
            if (!$this->isValidUrl($url)) {

                // The host is configured! Prepend it.
                $configuredHost = Str::of($this->getConfig('host'))
                    ->trim()
                    ->replaceEnd('/', '')
                    ->toString();

                if ($configuredHost && $urlString->startsWith('/')) {
                    return $urlString
                        ->prepend($configuredHost)
                        ->prepend(PIC_PERF_HOST)
                        ->toString();
                }

                return $url;
            }

            // It's probably a local image.
            if (Str::of(parse_url($url)['host'])->isMatch("/localhost|\.test$/")) {
                return $url;
            }

            return $urlString->prepend(PIC_PERF_HOST)->toString();
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

            return preg_replace_callback(IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }

    private function transformStyleTags($content)
    {
        // Find every style tag.
        return preg_replace_callback('/<style.*?>(.*?)<\/style>/is', function ($match) {

            // Find every URL.
            return preg_replace_callback(IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }

    private function transformInlineStyles($content)
    {
        // Find every inline style.
        return preg_replace_callback('/style=(?:"|\')([^"]*)(?:"|\')/is', function ($match) {

            // Find every URL.
            return preg_replace_callback(IMAGE_URL_PATTERN, function ($subMatch) {
                return $this->transformUrl($subMatch[0]);
            }, $match[0]);
        }, $content);
    }
}
