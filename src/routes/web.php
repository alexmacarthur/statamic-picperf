<?php

namespace PicPerf\StatamicPicPerf;

use Illuminate\Support\Facades\Route;
use PicPerf\StatamicPicPerf\Controller\SitemapController;

Route::prefix('picperf')->group(function () {
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('picperf.sitemap');
});
