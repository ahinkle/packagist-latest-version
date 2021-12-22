<?php

namespace ahinkle\PackagistLatestVersion;

use Exception;
use Spatie\Packagist\PackagistClient;

class PackagistLatestVersion
{
    /**
     * The Packagist API.
     *
     * @var PackagistClient
     */
    protected PackagistClient $packagist;

    /**
     * The latest version of the package.
     *
     * @var array|null
     */
    protected ?array $latestVersion = null;

    /**
     * Release tags that are considered `developmental` releases.
     *
     * @var array
     */
    protected array $developmentalTags = [
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

    public function __construct()
    {
        $this->packagist = new PackagistClient(new \GuzzleHttp\Client(), new \Spatie\Packagist\PackagistUrlGenerator());
    }

    /**
     * The latest release of the specified package.
     *
     * @param  string  $package
     * @return array|null
     *
     * @throws Exception
     */
    public function getLatestRelease(string $package): ?array
    {
        if ($package === '') {
            throw new Exception('You must pass a package value');
        }

        $metadata = $this->packagist->getPackageMetaData($package);

        if (! isset($metadata['packages'][$package])) {
            return null;
        }

        return $this->resolveLatestRelease($metadata['packages'][$package]);
    }

    /**
     * Resolves the latest release by the provided array.
     *
     * @param  array  $releases
     * @return array|null
     */
    public function resolveLatestRelease(array $releases): ?array
    {
        if (empty($releases)) {
            return null;
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
     * If the release tag is a developmental release.
     *
     * @param  string  $release
     * @return bool
     */
    public function isDevelopmentalRelease(string $release): bool
    {
        foreach ($this->developmentalTags as $developmentalTag) {
            if (stripos($release, $developmentalTag) !== false) {
                return true;
            }
        }

        return false;
    }
}
