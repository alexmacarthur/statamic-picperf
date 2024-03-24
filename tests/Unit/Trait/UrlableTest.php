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

describe("isRootRelativeUrl()", function () use ($testClass) {
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
