<?php

namespace PicPerf\StatamicPicPerf;

$testClass = new class()
{
    use \PicPerf\StatamicPicPerf\Trait\Urlable;
};

describe('isValidUrl()', function () use ($testClass) {
    it('returns true for a valid URL', function () use ($testClass) {
        $result = $testClass->isValidUrl('http://urmom.com/something.jpg');

        expect($result)->toBeTrue();
    });

    it('returns false for an invalid URL', function () use ($testClass) {
        $result = $testClass->isValidUrl('blahblahblah');

        expect($result)->toBeFalse();
    });

    it('returns true for root-relative path', function () use ($testClass) {
        $result = $testClass->isValidUrl('/something.jpg');

        expect($result)->toBeTrue();
    });
});

describe('isRootRelativeUrl()', function () use ($testClass) {
    it('returns true for a root-relative URL', function () use ($testClass) {
        $result = $testClass->isRootRelativeUrl('/something.jpg');

        expect($result)->toBeTrue();
    });

    it('returns false for a non-root-relative URL', function () use ($testClass) {
        $result = $testClass->isRootRelativeUrl('http://urmom.com/something.jpg');

        expect($result)->toBeFalse();
    });

    it('returns false for an invalid URL', function () use ($testClass) {
        $result = $testClass->isRootRelativeUrl('blahblahblah');

        expect($result)->toBeFalse();
    });
});

describe('appendQueryParams()', function () use ($testClass) {
    it('appends query params to a URL', function () use ($testClass) {
        $result = $testClass->appendQueryParams('http://urmom.com/something.jpg', ['foo' => 'bar']);

        expect($result)->toBe('http://urmom.com/something.jpg?foo=bar');
    });

    it('appends query params to a root-relative URL', function () use ($testClass) {
        $result = $testClass->appendQueryParams('/something.jpg', ['foo' => 'bar']);

        expect($result)->toBe('/something.jpg?foo=bar');
    });

    it('appends query params to a URL with existing query params', function () use ($testClass) {
        $result = $testClass->appendQueryParams('http://urmom.com/something.jpg?existing=param', ['foo' => 'bar']);

        expect($result)->toBe('http://urmom.com/something.jpg?existing=param&foo=bar');
    });

    it('appends query params to a root-relative URL with existing query params', function () use ($testClass) {
        $result = $testClass->appendQueryParams('/something.jpg?existing=param', ['foo' => 'bar']);

        expect($result)->toBe('/something.jpg?existing=param&foo=bar');
    });

    it('appends query params to a URL with a fragment', function () use ($testClass) {
        $result = $testClass->appendQueryParams('http://urmom.com/something.jpg#fragment', ['foo' => 'bar']);

        expect($result)->toBe('http://urmom.com/something.jpg?foo=bar#fragment');
    });
});
