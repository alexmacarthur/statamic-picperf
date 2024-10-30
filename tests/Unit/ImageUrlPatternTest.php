<?php

use PicPerf\StatamicPicPerf\Constants;

describe('ImageUrlPatternTest', function () {
    describe('should match', function () {
        it('matches full image URL', function () {
            expect('https://macarthur.me/image.jpg')->toMatch(Constants::IMAGE_URL_PATTERN);
        });

        it('matches modern image format', function () {
            expect('https://macarthur.me/image.avif')->toMatch(Constants::IMAGE_URL_PATTERN);
        });

        it('matches images with query string parameters', function () {
            expect('https://macarthur.me/image.avif?whatever=true')->toMatch(Constants::IMAGE_URL_PATTERN);
        });

        it('matches relative paths', function () {
            expect('/path/to/image.png')->toMatch(Constants::IMAGE_URL_PATTERN);
        });
    });

    describe('should not match', function () {
        it('does not match image URL without extension', function () {
            expect('https://macarthur.me/image')->not->toMatch(Constants::IMAGE_URL_PATTERN);
        });

        it('does not match malformed URL', function () {
            expect('//macarthur.me/image')->not->toMatch(Constants::IMAGE_URL_PATTERN);
        });
    });
});
