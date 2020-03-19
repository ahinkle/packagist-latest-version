<?php

namespace ahinkle\PackagistLatestVersion;

use Exception;
use GuzzleHttp\Client;

class PackagistLatestVersion
{
    /**
     * The Guzzle Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The Packagist API.
     *
     * @var \Spatie\Packagist\Packagist
     */
    protected $packagist;

    /**
     * The latest version of the package.
     *
     * @var string|null
     */
    protected $latestVersion = null;

    /**
     * Release tags that are considered `developmental` releases.
     *
     * @var array
     */
    protected $developmentalTags = [
        'alpha',
        'beta',
        'dev',
        'develop',
        'development',
        'master',
        'rc',
        'untagged',
        'wip',
    ];

    /**
     * @param \GuzzleHttp\Client $client
     * @param string             $baseUrl
     */
    public function __construct(Client $client, $baseUrl = 'https://packagist.org')
    {
        $this->client = $client;

        $this->packagist = new \Spatie\Packagist\Packagist($client, $baseUrl);
    }

    /**
     * The latest release of the specified package.
     *
     * @param string $vendor
     *
     * @return string|null
     */
    public function getLatestRelease($package)
    {
        if ($package === '') {
            throw new Exception('You must pass a package value');
        }

        $package = $this->packagist->getPackageMetaData($package);

        if (! isset($package['package']['versions'])) {
            return;
        }

        return $this->resolveLatestRelease($package['package']['versions']);
    }

    /**
     * Resolves the latest release by the provided array.
     *
     * @param  array  $releases
     * @return array|null
     */
    public function resolveLatestRelease($releases)
    {
        if (empty($releases)) {
            return;
        }

        foreach ($releases as $release) {
            if ($this->isDevelopmentalRelease($release['version_normalized'])) {
                continue;
            }

            if ($this->latestVersion) {
                if (version_compare($release['version_normalized'], $this->latestVersion['version_normalized'], '>')) {
                    $this->latestVersion = $release;
                }
            } else {
                $this->latestVersion = $release;
            }
        }

        return $this->latestVersion;
    }

    /**
     * If the the release tag is a developmental release.
     *
     * @param  string  $release
     * @return bool
     */
    public function isDevelopmentalRelease($release)
    {
        foreach ($this->developmentalTags as $developmentalTag) {
            if (strpos($release, $developmentalTag) !== false) {
                return true;
            }
        }

        return false;
    }
}
