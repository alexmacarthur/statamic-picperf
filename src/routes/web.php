<?php

namespace PicPerf\StatamicPicPerf;

use Illuminate\Support\Facades\Route;
use PicPerf\StatamicPicPerf\Controller\SitemapController;

Route::prefix('picperf')->group(function () {
    Route::get('/sitemap', [SitemapController::class, 'index'])->name('picperf.sitemap');
});
