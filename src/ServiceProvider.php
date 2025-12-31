<?php

namespace PicPerf\StatamicPicPerf;

use Illuminate\Support\Facades\Artisan;
use PicPerf\StatamicPicPerf\Service\RegisterSitemapService;
use PicPerf\StatamicPicPerf\Trait\Configurable;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    use Configurable;

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
        $packageConfig = require __DIR__.'/../config/picperf.php';
        $publishedConfigPath = config_path('picperf.php');

        $publishedConfig = file_exists($publishedConfigPath)
            ? require $publishedConfigPath
            : [];

        // User config overrides package defaults
        config(['statamic-picperf' => array_replace_recursive($packageConfig, $publishedConfig)]);

        $this->publishes([
            __DIR__.'/../config/picperf.php' => config_path('picperf.php'),
        ], 'picperf-config');

        return $this;
    }

    protected function setUpSitemap()
    {
        Artisan::command('picperf:register-sitemap', function () {
            (new RegisterSitemapService)->handle();
        })->purpose('Add the PicPerf sitemap to your robots.txt file.');

        if (! $this->getConfig('register_sitemap', true)) {
            return;
        }

        Statamic::afterInstalled(function () {
            (new RegisterSitemapService)->handle();
        });
    }
}
