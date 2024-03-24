<?php

use const PicPerf\StatamicPicPerf\Trait\IMAGE_URL_PATTERN;

describe("ImageUrlPatternTest", function () {
    describe("should match", function () {
        it("matches full image URL", function () {
            expect("https://macarthur.me/image.jpg")->toMatch(IMAGE_URL_PATTERN);
        });

        it("matches modern image format", function () {
            expect("https://macarthur.me/image.avif")->toMatch(IMAGE_URL_PATTERN);
        });

        it("matches images with query string parameters", function () {
            expect("https://macarthur.me/image.avif?whatever=true")->toMatch(IMAGE_URL_PATTERN);
        });

        it("matches relative paths", function () {
            expect("/path/to/image.png")->toMatch(IMAGE_URL_PATTERN);
        });
    });

    describe("should not match", function () {
        it("does not match image URL without extension", function () {
            expect("https://macarthur.me/image")->not->toMatch(IMAGE_URL_PATTERN);
        });

        it("does not match malformed URL", function () {
            expect("//macarthur.me/image")->not->toMatch(IMAGE_URL_PATTERN);
        });
    });
});
