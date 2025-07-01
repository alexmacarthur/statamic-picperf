<?php

namespace PicPerf\StatamicPicPerf\Trait;

trait Environmentable
{
    public function isProduction(): bool
    {
        return app()->isProduction();
    }

    public function getEnvironment(): string
    {
        return app()->environment();
    }  
}
