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
}
