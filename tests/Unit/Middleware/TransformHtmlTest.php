<?php

namespace PicPerf\StatamicPicPerf;

use PicPerf\StatamicPicPerf\Middleware\TransformHtml;

it('returns early when it is not a GET request', function () {
    $partialMock = $this->createPartialMock(TransformHtml::class, ['getConfig', 'transformMarkup']);

    $request = new \Illuminate\Http\Request();
    $request->setMethod('POST');
    $response = new \Illuminate\Http\Response();
    $response->headers->set('content-type', 'text/html');

    $next = function () use ($response) {
        return $response;
    };

    $partialMock->expects($this->never())->method('transformMarkup');

    $result = $partialMock->handle($request, $next);

    expect($result)->toBe($response);
});

it('returns early when it is not a HTML response', function () {
    $partialMock = $this->createPartialMock(TransformHtml::class, ['getConfig', 'transformMarkup']);

    $request = new \Illuminate\Http\Request();
    $request->setMethod('POST');
    $response = new \Illuminate\Http\Response();
    $response->headers->set('content-type', 'application/json');

    $next = function () use ($response) {
        return $response;
    };

    $partialMock->expects($this->never())->method('transformMarkup');

    $result = $partialMock->handle($request, $next);

    expect($result)->toBe($response);
});

it('returns early when config option is disabled', function () {
    $partialMock = $this->createPartialMock(TransformHtml::class, ['getConfig', 'transformMarkup']);

    $request = new \Illuminate\Http\Request();
    $request->setMethod('GET');
    $response = new \Illuminate\Http\Response();
    $response->headers->set('content-type', 'text/html');

    $next = function () use ($response) {
        return $response;
    };

    $partialMock->method('getConfig')->willReturn(false);

    $partialMock->expects($this->never())->method('transformMarkup');

    $result = $partialMock->handle($request, $next);

    expect($result)->toBe($response);
});

it('transforms the HTML', function () {
    $partialMock = $this->createPartialMock(TransformHtml::class, [
        'getConfig', 
        'transformUrl'
    ]);

    $request = new \Illuminate\Http\Request();
    $request->setMethod('GET');
    $response = new \Illuminate\Http\Response();
    $response->headers->set('content-type', 'text/html');
    $response->setContent('<img src="https://example.com/image.jpg">');

    $next = function () use ($response) {
        return $response;
    };

    $partialMock->method('getConfig')->willReturn(true);
    $partialMock->method('transformUrl')->willReturn('https://picperf.io/https://example.com/image.jpg');

    $result = $partialMock->handle($request, $next);

    expect($result->getContent())->toBe('<img src="https://picperf.io/https://example.com/image.jpg">');
});

describe('adding sitemap_path', function () {
    it('adds root path when on home page', function () {
        $partialMock = $this->createPartialMock(TransformHtml::class, [
            'getConfig', 
            'transformUrl'
        ]);

        $request = new \Illuminate\Http\Request();
        $request->setMethod('GET');
        $request->server->set('REQUEST_URI', '/');
        $response = new \Illuminate\Http\Response();
        $response->headers->set('content-type', 'text/html');
        $response->setContent('<img src="https://example.com/image.jpg">');

        $next = function () use ($response) {
            return $response;
        };

        $partialMock->method('getConfig')
            ->willReturnCallback(function($key, $default = null) {
                if ($key === 'transform_all_markup') return true;
                if ($key === 'add_sitemap_paths') return true;
                return $default;
            });
        
        $partialMock->method('transformUrl')
            ->willReturnCallback(function($url, $sitemapPath = null) {
                if ($sitemapPath) {
                    return 'https://picperf.io/https://example.com/image.jpg?sitemap_path=' . urlencode($sitemapPath);
                }
                return 'https://picperf.io/https://example.com/image.jpg';
            });

        $result = $partialMock->handle($request, $next);

        expect($result->getContent())->toBe('<img src="https://picperf.io/https://example.com/image.jpg?sitemap_path=%2F">');
    });

    it('adds non-root path when on home page', function () {
        $partialMock = $this->createPartialMock(TransformHtml::class, [
            'getConfig', 
            'transformUrl'
        ]);

        $request = new \Illuminate\Http\Request();
        $request->setMethod('GET');
        $request->server->set('REQUEST_URI', '/some/other/page?with=query');
        $response = new \Illuminate\Http\Response();
        $response->headers->set('content-type', 'text/html');
        $response->setContent('<img src="https://example.com/image.jpg">');

        $next = function () use ($response) {
            return $response;
        };

        $partialMock->method('getConfig')
            ->willReturnCallback(function($key, $default = null) {
                if ($key === 'transform_all_markup') return true;
                if ($key === 'add_sitemap_paths') return true;
                return $default;
            });
        
        $partialMock->method('transformUrl')
            ->willReturnCallback(function($url, $sitemapPath = null) {
                if ($sitemapPath) {
                    return 'https://picperf.io/https://example.com/image.jpg?sitemap_path=' . urlencode($sitemapPath);
                }
                return 'https://picperf.io/https://example.com/image.jpg';
            });

        $result = $partialMock->handle($request, $next);

        expect($result->getContent())->toBe('<img src="https://picperf.io/https://example.com/image.jpg?sitemap_path=%2Fsome%2Fother%2Fpage">');
    });
});
