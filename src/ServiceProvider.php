<?php

namespace PicPerf\StatamicPicPerf;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $modifiers = [
        Modifier\PicPerf::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            Middleware\TransformHtml::class,
        ],
    ];

    public function bootAddon()
    {
        // dd("HIII");
    }
}
