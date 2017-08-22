<?php

namespace ju1ius\WebpackAssetsBundle\Helper;

/**
 * @author ju1ius
 */
class AssetHelper
{
    /**
     * Path to the JSON revision manifest supplied to wepback's AssetPlugin
     *
     * @var string
     */
    private $manifestPath;

    /**
     * Cached contents of the JSON manifest.
     *
     * @var array
     */
    private $manifest;

    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    public function getAssetUrl($bundle, $type='js', $throwOnEmpty = true)
    {
        $publicPath = $this->getAssetVersion($bundle, $type, $throwOnEmpty);

        return $publicPath;
    }

    public function getAssetVersion($bundle, $type='js', $throwOnEmpty = true)
    {
        $this->loadManifest();

        $hasNoBundle = !isset($this->manifest[$bundle]);
        $hasTypeAndBundle = !$hasNoBundle && isset($this->manifest[$bundle][$type]);
        if ($hasNoBundle && $throwOnEmpty) {
            throw new \RuntimeException(sprintf('No bundle "%s" in the version manifest!', $bundle));
        }
        if (!$hasTypeAndBundle && $throwOnEmpty) {
            throw new \RuntimeException(sprintf(
                'No type "%s" for bundle "%s" in the version manifest!',
                $type,
                $bundle
            ));
        }

        return $hasTypeAndBundle?
            $this->manifest[$bundle][$type] : null;
    }

    private function loadManifest()
    {
        if ($this->manifest) {
            return;
        }
        if (!file_exists($this->manifestPath)) {
            throw new \RuntimeException(sprintf('Cannot find manifest file: "%s"', $this->manifestPath));
        }
        $this->manifest = json_decode(file_get_contents($this->manifestPath), true);
    }
}
