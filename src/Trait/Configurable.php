<?php

namespace PicPerf\StatamicPicPerf\Trait;

const CONFIG_KEY = 'statamic-picperf';

trait Configurable
{
    public function getConfig($key, $default = null)
    {
        return config(CONFIG_KEY . '.' . $key, $default);
    }
}
