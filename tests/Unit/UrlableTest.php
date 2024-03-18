<?php

namespace PicPerf\StatamicPicPerf;

$testClass = new class()
{
    use \PicPerf\StatamicPicPerf\Trait\Urlable;
};

describe('validating URLs', function () use ($testClass) {
    it('returns true for a valid URL', function () use ($testClass) {
        $result = $testClass->isValidUrl('http://urmom.com/something.jpg');

        expect($result)->toBeTrue();
    });

    it('returns false for an invalid URL', function () use ($testClass) {
        $result = $testClass->isValidUrl('blahblahblah');

        expect($result)->toBeFalse();
    });
});
