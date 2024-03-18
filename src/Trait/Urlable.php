<?php

namespace PicPerf\StatamicPicPerf\Trait;

trait Urlable
{
    public function isValidUrl($url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
