<?php

namespace PicPerf\StatamicPicPerf\Trait;

use Illuminate\Support\Str;
use PicPerf\StatamicPicPerf\Constants;

trait Urlable
{
    public function isValidUrl($url): bool
    {
        return Str::of($url)->isMatch(Constants::STRICT_IMAGE_URL_PATTERN);
    }

    public function isRootRelativeUrl($url): bool
    {
        return Str::of($url)->startsWith('/') && $this->isValidUrl($url);
    }

    public function appendQueryParams(string $url, array $params): string
    {
        $parsedUrl = parse_url($url);
        if (! isset($parsedUrl['query'])) {
            $parsedUrl['query'] = '';
        }

        parse_str($parsedUrl['query'], $existingParams);
        $mergedParams = array_merge($existingParams, $params);
        $parsedUrl['query'] = http_build_query($mergedParams);

        $newUrl = isset($parsedUrl['scheme']) && isset($parsedUrl['host'])
        ? $parsedUrl['scheme'].'://'.$parsedUrl['host'].$parsedUrl['path'].'?'.$parsedUrl['query']
        : $parsedUrl['path'].'?'.$parsedUrl['query'];

        if (isset($parsedUrl['fragment'])) {
            $newUrl .= '#'.$parsedUrl['fragment'];
        }

        return $newUrl;
    }
}
