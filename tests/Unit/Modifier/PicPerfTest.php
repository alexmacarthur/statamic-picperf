<?php

namespace PicPerf\StatamicPicPerf;

use PicPerf\StatamicPicPerf\Modifier\PicPerf;

describe("transformMarkup()", function () {
    it('is not called when URL is valid', function () {
        $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);

        $picPerfMock->expects($this->never())->method('transformMarkup');

        $picPerfMock->method('transformUrl')->willReturnCallback(function ($url) {
            return $url;
        });

        $validUrl = 'https://example.com/image.jpg';
        $result = $picPerfMock->index($validUrl, [], []);

        expect($result)->toBe($validUrl);
    });

    // Fix this test!
    it('appends sitemap_path query param if provided', function () {
        $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);
        $input = '<img src="https://example.com/image.jpg"><img srcset="https://example.com/image-small.jpg 480w, https://example.com/image-big.jpg 800w">';

        $picPerfMock->expects($this->once())->method('transformMarkup')->with($input, '/some/path')->willReturn('transformed html');

        $result = $picPerfMock->index($input, ['add_to_sitemap'], [
            'current_uri' => '/some/path',
        ]);

        expect($result)->toBe('transformed html');
    });
});

describe("transformUrl()", function () {
    it("is not called when HTML is passed", function () {
        $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);

        $picPerfMock->expects($this->never())->method('transformUrl');

        $picPerfMock->method('transformMarkup')->willReturnCallback(function ($url) {
            return 'markup';
        });

        $validUrl = '<img src="https://example.com/image.jpg">';
        $result = $picPerfMock->index($validUrl, [], []);

        expect($result)->toBe('markup');
    });

    it("adds sitemap_path query param if provided", function () {
        $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);

        $picPerfMock->expects($this->once())->method('transformUrl')->with('https://example.com/image.jpg', '/some/path')->willReturn('https://picperf.io/https://example.com/image.jpg?sitemap_path=/some/path');

        $result = $picPerfMock->index('https://example.com/image.jpg', ['add_to_sitemap'], [
            'current_uri' => '/some/path',
        ]);

        expect($result)->toBe('https://picperf.io/https://example.com/image.jpg?sitemap_path=/some/path');
    });
});

it("transforms a bunch of HTML", function () {
    $class = new PicPerf();

    $html = '
        <img src="https://example.com/image.jpg"><img srcset="https://example.com/image-small.jpg 480w, https://example.com/image-big.jpg 800w">

        <style>
            .image {
                background-image: url(https://example.com/image.jpg);
            }
        </style>

        <div style="background-image=\'https://example.com/image.jpg\'></div>
    ';
    $result = $class->index($html, [], []);

    expect($result)->toBe('
        <img src="https://picperf.io/https://example.com/image.jpg"><img srcset="https://picperf.io/https://example.com/image-small.jpg 480w, https://picperf.io/https://example.com/image-big.jpg 800w">

        <style>
            .image {
                background-image: url(https://picperf.io/https://example.com/image.jpg);
            }
        </style>

        <div style="background-image=\'https://picperf.io/https://example.com/image.jpg\'></div>
    ');
});

it("transforms a bunch of HTML with sitemap path", function () {
    $class = new PicPerf();

    $html = '
        <img src="https://example.com/image.jpg"><img srcset="https://example.com/image-small.jpg 480w, https://example.com/image-big.jpg 800w">

        <style>
            .image {
                background-image: url(https://example.com/image.jpg);
            }
        </style>

        <div style="background-image=\'https://example.com/image.jpg\'></div>
    ';
    $result = $class->index($html, [0 => 'add_to_sitemap'], [
        'current_uri' => '/some/path',
    ]);

    expect($result)->toBe('
        <img src="https://picperf.io/https://example.com/image.jpg?sitemap_path=/some/path"><img srcset="https://picperf.io/https://example.com/image-small.jpg?sitemap_path=/some/path 480w, https://picperf.io/https://example.com/image-big.jpg?sitemap_path=/some/path 800w">

        <style>
            .image {
                background-image: url(https://picperf.io/https://example.com/image.jpg?sitemap_path=/some/path);
            }
        </style>

        <div style="background-image=\'https://picperf.io/https://example.com/image.jpg?sitemap_path=/some/path\'></div>
    ');
});
