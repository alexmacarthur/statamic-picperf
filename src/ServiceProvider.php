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
        $this->bootAddonConfig();
    }

    protected function bootAddonConfig(): self
    {
        $configPath = config_path('picperf.php');

        $this->mergeConfigFrom($configPath, 'statamic-picperf');

        $this->publishes([
            __DIR__ . '/../config/picperf.php' => config_path('picperf.php'),
        ], 'picperf-config');

        return $this;
    }
}
