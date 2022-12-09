<?php

namespace ahinkle\PackagistLatestVersion\Tests;

use ahinkle\PackagistLatestVersion\PackagistLatestVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PackagistLatestVersionTest extends TestCase
{
    /**
     * @var PackagistLatestVersion
     */
    protected PackagistLatestVersion $packagist;

    public function setUp(): void
    {
        parent::setUp();

        $this->packagist = new PackagistLatestVersion();
    }

    public function test_it_can_instantiate_package_latest_version()
    {
        $this->assertInstanceOf(PackagistLatestVersion::class, $this->packagist);
    }

    public function test_it_can_get_latest_version()
    {
        $latest = $this->packagist->getLatestRelease('laravel/framework');

        $this->assertIsArray($latest);
        $this->assertArrayHasKey('version', $latest);
    }

    public function test_it_returns_null_when_empty_releases()
    {
        $this->assertNull($this->packagist->resolveLatestRelease([]));
    }

    public function test_it_returns_null_on_only_developmental_releases()
    {
        $this->assertNull($this->packagist->resolveLatestRelease([['version_normalized' => '0.1-dev']]));
    }

    public function test_it_resolves_latest_release()
    {
        $resolve = $this->packagist->resolveLatestRelease([
            [
                'version_normalized' => '1.0.0.0',
                'version' => 'v1.0',
            ],
        ]);

        $this->assertEquals('1.0.0.0', $resolve['version_normalized']);
    }

    public function test_it_properly_compares_latest_release()
    {
        $resolve = $this->packagist->resolveLatestRelease([
            [
                'version_normalized' => '1.2.0.0',
                'version' => 'v1.2',
            ],
            [
                'version_normalized' => '2.0.0.0',
                'version' => 'v2.0',
            ],
            [
                'version_normalized' => '1.0.0.0',
                'version' => 'v1.0',
            ],
        ]);

        $this->assertEquals('v2.0', $resolve['version']);
    }

    public function test_it_returns_proper_bool_on_is_dev_release()
    {
        $this->assertTrue($this->packagist->isDevelopmentalRelease('dev'));
        $this->assertTrue($this->packagist->isDevelopmentalRelease('v0.01-dev'));
        $this->assertTrue($this->packagist->isDevelopmentalRelease('dev-0.01'));
        $this->assertTrue($this->packagist->isDevelopmentalRelease('dev-master'));
        $this->assertTrue($this->packagist->isDevelopmentalRelease('2.3.4.0-RC1'));

        $this->assertFalse($this->packagist->isDevelopmentalRelease('1.0.0.0'));
        $this->assertFalse($this->packagist->isDevelopmentalRelease('1.2.3.4'));
        $this->assertFalse($this->packagist->isDevelopmentalRelease('1.2.3.4-foo'));
    }

    public function test_it_can_use_custom_guzzle_client()
    {
        $response = new Response(
            200,
            [],
            json_encode(['packages' => ['my/package' => [
                ['version_normalized' => '1.0.0'],
                ['version_normalized' => '1.2.0'],
                ['version_normalized' => '1.2.3'],
            ]]])
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with('https://repo.packagist.org/p/my/package.json', ['query' => []])
            ->willReturn($response);

        $packagist = new PackagistLatestVersion($client);
        $result = $packagist->getLatestRelease('my/package');

        $this->assertEquals(['version_normalized' => '1.2.3'], $result);
    }
}
