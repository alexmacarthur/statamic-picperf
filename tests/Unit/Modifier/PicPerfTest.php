<?php

namespace PicPerf\StatamicPicPerf;

use PicPerf\StatamicPicPerf\Modifier\PicPerf;

it('transformMarkup() is not called when URL is valid', function () {
    $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);

    $picPerfMock->expects($this->never())->method('transformMarkup');

    $picPerfMock->method('transformUrl')->willReturnCallback(function ($url) {
        return $url;
    });

    $validUrl = 'https://example.com/image.jpg';
    $result = $picPerfMock->index($validUrl, [], []);

    expect($result)->toBe($validUrl);
});

it("transformUrl() is not called when HTML is passed", function () {
    $picPerfMock = $this->createPartialMock(PicPerf::class, ['transformMarkup', 'transformUrl']);

    $picPerfMock->expects($this->never())->method('transformUrl');

    $picPerfMock->method('transformMarkup')->willReturnCallback(function ($url) {
        return 'markup';
    });

    $validUrl = '<img src="https://example.com/image.jpg">';
    $result = $picPerfMock->index($validUrl, [], []);

    expect($result)->toBe('markup');
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
