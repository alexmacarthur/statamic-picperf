<?php

namespace PicPerf\StatamicPicPerf;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/picperf-test-'.uniqid();
    mkdir($this->tempDir, 0777, true);
});

afterEach(function () {
    // Clean up temp files
    if (isset($this->tempDir) && is_dir($this->tempDir)) {
        array_map('unlink', glob($this->tempDir.'/*'));
        rmdir($this->tempDir);
    }
});

function writeConfigFile(string $path, array $config): void
{
    file_put_contents($path, '<?php return '.var_export($config, true).';');
}

/**
 * Simulates the bootAddonConfig merge logic.
 * This mirrors the actual implementation in ServiceProvider::bootAddonConfig()
 */
function simulateConfigMerge(string $packageConfigPath, string $publishedConfigPath): array
{
    $packageConfig = require $packageConfigPath;

    $publishedConfig = file_exists($publishedConfigPath)
        ? require $publishedConfigPath
        : [];

    return array_replace_recursive($packageConfig, $publishedConfig);
}

describe('bootAddonConfig', function () {
    it('uses package defaults when no published config exists', function () {
        $packageConfig = [
            'host' => null,
            'transform_all_markup' => true,
            'add_sitemap_paths' => false,
            'register_sitemap' => true,
            'lower_environments' => ['local', 'testing'],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/nonexistent.php'
        );

        expect($result)->toBe($packageConfig);
    });

    it('merges published config over package defaults', function () {
        $packageConfig = [
            'host' => null,
            'transform_all_markup' => true,
            'add_sitemap_paths' => false,
            'register_sitemap' => true,
            'lower_environments' => ['local', 'testing'],
        ];

        $publishedConfig = [
            'host' => 'https://custom.picperf.io',
            'transform_all_markup' => false,
        ];

        $expectedConfig = [
            'host' => 'https://custom.picperf.io',
            'transform_all_markup' => false,
            'add_sitemap_paths' => false,
            'register_sitemap' => true,
            'lower_environments' => ['local', 'testing'],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('recursively merges nested array config values', function () {
        $packageConfig = [
            'host' => null,
            'lower_environments' => ['local', 'testing'],
            'nested' => [
                'level1' => [
                    'a' => 'default_a',
                    'b' => 'default_b',
                ],
                'level1_only' => 'package_value',
            ],
        ];

        $publishedConfig = [
            'nested' => [
                'level1' => [
                    'a' => 'custom_a',
                    'c' => 'custom_c',
                ],
            ],
        ];

        $expectedConfig = [
            'host' => null,
            'lower_environments' => ['local', 'testing'],
            'nested' => [
                'level1' => [
                    'a' => 'custom_a',
                    'b' => 'default_b',
                    'c' => 'custom_c',
                ],
                'level1_only' => 'package_value',
            ],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('replaces array values for lower_environments when published has more items', function () {
        $packageConfig = [
            'host' => null,
            'lower_environments' => ['local', 'testing'],
        ];

        $publishedConfig = [
            'lower_environments' => ['staging', 'development', 'preview'],
        ];

        // array_replace_recursive replaces indexed arrays by index
        $expectedConfig = [
            'host' => null,
            'lower_environments' => ['staging', 'development', 'preview'],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('allows published config to set null values', function () {
        $packageConfig = [
            'host' => 'https://default.picperf.io',
            'transform_all_markup' => true,
        ];

        $publishedConfig = [
            'host' => null,
        ];

        $expectedConfig = [
            'host' => null,
            'transform_all_markup' => true,
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('handles empty published config file', function () {
        $packageConfig = [
            'host' => null,
            'transform_all_markup' => true,
            'register_sitemap' => true,
        ];

        $publishedConfig = [];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($packageConfig);
    });

    it('published config can add new keys not in package defaults', function () {
        $packageConfig = [
            'host' => null,
            'transform_all_markup' => true,
        ];

        $publishedConfig = [
            'custom_option' => 'custom_value',
            'another_option' => ['nested' => true],
        ];

        $expectedConfig = [
            'host' => null,
            'transform_all_markup' => true,
            'custom_option' => 'custom_value',
            'another_option' => ['nested' => true],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('preserves boolean false values from published config', function () {
        $packageConfig = [
            'transform_all_markup' => true,
            'register_sitemap' => true,
            'add_sitemap_paths' => true,
        ];

        $publishedConfig = [
            'transform_all_markup' => false,
            'register_sitemap' => false,
        ];

        $expectedConfig = [
            'transform_all_markup' => false,
            'register_sitemap' => false,
            'add_sitemap_paths' => true,
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('handles indexed array replacement by position', function () {
        // array_replace_recursive replaces indexed arrays by their numeric key
        $packageConfig = [
            'lower_environments' => ['local', 'testing'],
        ];

        $publishedConfig = [
            'lower_environments' => ['custom_local'], // Only one item
        ];

        // The first item is replaced, but the second from package remains
        $expectedConfig = [
            'lower_environments' => ['custom_local', 'testing'],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });

    it('uses actual package config structure correctly', function () {
        // Test with the exact default config from config/picperf.php
        $packageConfig = [
            'host' => null,
            'transform_all_markup' => true,
            'add_sitemap_paths' => false,
            'register_sitemap' => true,
            'lower_environments' => [
                'local',
                'testing',
            ],
        ];

        // Common user customization
        $publishedConfig = [
            'host' => 'https://mycustomhost.picperf.io',
            'register_sitemap' => false,
            'lower_environments' => ['local', 'testing', 'staging'],
        ];

        $expectedConfig = [
            'host' => 'https://mycustomhost.picperf.io',
            'transform_all_markup' => true,
            'add_sitemap_paths' => false,
            'register_sitemap' => false,
            'lower_environments' => ['local', 'testing', 'staging'],
        ];

        writeConfigFile($this->tempDir.'/package.php', $packageConfig);
        writeConfigFile($this->tempDir.'/published.php', $publishedConfig);

        $result = simulateConfigMerge(
            $this->tempDir.'/package.php',
            $this->tempDir.'/published.php'
        );

        expect($result)->toBe($expectedConfig);
    });
});
