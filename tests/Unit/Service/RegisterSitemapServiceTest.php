<?php

use PicPerf\StatamicPicPerf\Service\RegisterSitemapService;

it('adds sitemap to robots.txt if not already present.', function () {
    $partialMock = $this->createPartialMock(RegisterSitemapService::class, ['getConfig', 'getRobotsTxt', 'putRobotsTxt']);

    $partialMock->method('getConfig')->willReturn('https://example.com');
    $partialMock->method('getRobotsTxt')->willReturn('');
    $partialMock->expects($this->once())->method('putRobotsTxt')->with("\nSitemap: https://example.com/picperf/sitemap.xml");

    $partialMock->handle();
});

it('does not add sitemap to robots.txt if already present.', function () {
    $partialMock = $this->createPartialMock(RegisterSitemapService::class, ['getConfig', 'getRobotsTxt', 'putRobotsTxt']);

    $partialMock->method('getConfig')->willReturn('https://example.com');
    $partialMock->method('getRobotsTxt')->willReturn("\nSitemap: https://example.com/picperf/sitemap.xml");
    $partialMock->expects($this->never())->method('putRobotsTxt');

    $partialMock->handle();
});
