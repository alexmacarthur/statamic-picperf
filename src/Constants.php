<?php

namespace PicPerf\StatamicPicPerf;

class Constants
{
    const PIC_PERF_HOST = 'https://picperf.io/';
    const IMAGE_URL_PATTERN = '/(?:https?:\/)?\/[^ ,]+\.(jpg|jpeg|png|gif|webp|avif)/i';
    const STRICT_IMAGE_URL_PATTERN = '/^(?:https?:\/)?\/[^ ,]+\.(jpg|jpeg|png|gif|webp|avif)/i';
}
