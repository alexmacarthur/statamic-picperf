<?php

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PicPerf\StatamicPicPerf\Controller\SitemapController;

afterEach(function () {
    \Mockery::close();
});

it('returns XML content when host config is set', function () {
    $mockResponse = \Mockery::mock(Response::class);
    $mockResponse->shouldReceive([
        'status' => 200,
        'body' => 'xml',
    ]);

    Http::shouldReceive('get')
        ->with('https://picperf.io/sitemap/example.com')
        ->andReturn($mockResponse);

    $controller = $this->createPartialMock(SitemapController::class, ['getConfig']);

    $controller->method('getConfig')->willReturn('https://example.com');

    $response = $controller->index();

    expect($response->getContent())->toBe('xml');
    expect($response->status())->toBe(200);
    expect($response->headers->get('Content-Type'))->toBe('application/xml');
});

it('returns 404 when no host is configured', function () {
    $controller = $this->createPartialMock(SitemapController::class, ['getConfig']);
    $controller->method('getConfig')->willReturn(false);

    $response = $controller->index();

    expect($response->status())->toBe(404);
    expect($response->getContent())->toBe('Sitemap not found.');
});

it('returns 404 when register_sitemap is set to false', function () {
    $controller = $this->createPartialMock(SitemapController::class, ['getConfig']);
    $controller->method('getConfig')->willReturn(true, false);

    $response = $controller->index();

    expect($response->status())->toBe(404);
    expect($response->getContent())->toBe('Sitemap not found.');
});
