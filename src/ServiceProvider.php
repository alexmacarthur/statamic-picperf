<?php

namespace PicPerf\StatamicPicPerf;

use Illuminate\Support\Facades\Artisan;
use PicPerf\StatamicPicPerf\Service\RegisterSitemapService;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $modifiers = [
        Modifier\PicPerf::class,
    ];

    protected $routes = [
        'web' => __DIR__.'/routes/web.php',
    ];

    protected $middlewareGroups = [
        'web' => [
            Middleware\TransformHtml::class,
        ],
    ];

    public function bootAddon()
    {
        $this->bootAddonConfig();
        $this->setUpSitemap();
    }

    protected function bootAddonConfig(): self
    {
        $configPath = config_path('picperf.php');

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'statamic-picperf');
        }

        $this->publishes([
            __DIR__.'/../config/picperf.php' => config_path('picperf.php'),
        ], 'picperf-config');

        return $this;
    }

    protected function setUpSitemap()
    {
        Artisan::command('picperf:register-sitemap', function () {
            (new RegisterSitemapService())->handle();
        })->purpose('Add the PicPerf sitemap to your robots.txt file.');

        Statamic::afterInstalled(function () {
            (new RegisterSitemapService())->handle();
        });
    }
}
