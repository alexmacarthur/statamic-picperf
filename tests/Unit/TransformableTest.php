<?php

namespace PicPerf\StatamicPicPerf;

$testClass = new class()
{
    use \PicPerf\StatamicPicPerf\Trait\Transformable;
};

describe('transforming URLs', function () use ($testClass) {
    it('returns same URL in local environment', function () use ($testClass) {
        $result = $testClass->transformUrl('http://localhost:3000/something.jpg');

        expect($result)->toBe('http://localhost:3000/something.jpg');
    });

    it('returns same URL on .test domain.', function () use ($testClass) {
        $result = $testClass->transformUrl('http://urmom.test/something.jpg');

        expect($result)->toBe('http://urmom.test/something.jpg');
    });

    it('transforms URL correctly', function () use ($testClass) {
        $result = $testClass->transformUrl('http://urmom.com/something.jpg');

        expect($result)->toBe('https://picperf.io/http://urmom.com/something.jpg');
    });

    it('does not transform a URL that is already transformed', function () use ($testClass) {
        $result = $testClass->transformUrl('https://picperf.io/http://urmom.com/something.jpg');

        expect($result)->toBe('https://picperf.io/http://urmom.com/something.jpg');
    });

    it('returns the same URL if it is a relative path', function () use ($testClass) {
        $result = $testClass->transformUrl('/something.jpg');

        expect($result)->toBe('/something.jpg');
    });

    it('returns the unchanged URL when parsing fails', function () use ($testClass) {
        $result = $testClass->transformUrl('blahblahblah');

        expect($result)->toBe('blahblahblah');
    });
});

describe('transforming <img> tags', function () use ($testClass) {
    it('transforms image URLs correctly', function () use ($testClass) {
        $result = $testClass->transformMarkup('<img src="http://urmom.com/something.jpg" />');

        expect($result)->toBe('<img src="https://picperf.io/http://urmom.com/something.jpg" />');
    });

    it('transforms multiple image URLs correctly', function () use ($testClass) {
        $result = $testClass->transformMarkup('<img src="http://urmom.com/something.jpg" /><img src="http://urmom.com/somethingelse.jpg" />');

        expect($result)->toBe('<img src="https://picperf.io/http://urmom.com/something.jpg" /><img src="https://picperf.io/http://urmom.com/somethingelse.jpg" />');
    });

    it('does not transform URLs that are already transformed', function () use ($testClass) {
        $result = $testClass->transformMarkup('<img src="https://picperf.io/http://urmom.com/something.jpg" />');

        expect($result)->toBe('<img src="https://picperf.io/http://urmom.com/something.jpg" />');
    });
});

describe('transforming <style> tags', function () use ($testClass) {
    it('transforms image URLs in style tags correctly', function () use ($testClass) {
        $result = $testClass->transformMarkup('<style>.something { background-image: url(http://urmom.com/something.jpg); }</style>');

        expect($result)->toBe('<style>.something { background-image: url(https://picperf.io/http://urmom.com/something.jpg); }</style>');
    });

    it('transforms multiple image URLs in style tags correctly', function () use ($testClass) {
        $result = $testClass->transformMarkup('<style>.something { background-image: url(http://urmom.com/something.jpg); } .somethingelse { background-image: url(http://urmom.com/somethingelse.jpg); }</style>');

        expect($result)->toBe('<style>.something { background-image: url(https://picperf.io/http://urmom.com/something.jpg); } .somethingelse { background-image: url(https://picperf.io/http://urmom.com/somethingelse.jpg); }</style>');
    });

    it('does not transform URLs that are already transformed', function () use ($testClass) {
        $result = $testClass->transformMarkup('<style>.something { background-image: url(https://picperf.io/http://urmom.com/something.jpg); }</style>');

        expect($result)->toBe('<style>.something { background-image: url(https://picperf.io/http://urmom.com/something.jpg); }</style>');
    });
});
