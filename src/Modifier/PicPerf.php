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
     * @param string  $value   The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return string
     */
    public function index(string $content, $params, $context): string
    {
        if ($this->isValidUrl($content)) {
            return $this->transformUrl($content);
        }

        return $this->transformMarkup($content);
    }
}
