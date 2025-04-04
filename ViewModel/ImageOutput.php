<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\ViewModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ImageOutput implements ArgumentInterface
{
    private ?Filesystem\Directory\ReadInterface $fileDriver = null;

    public function __construct(
        Filesystem $filesystem,
        private AssetRepository $assetRepository,
    ) {
        $this->fileDriver = $filesystem->getDirectoryRead(DirectoryList::PUB);
    }

    public function get(string $imageId): string
    {
        $asset = $this->assetRepository->createAsset($imageId);
        return $this->getIconOutput($asset->getSourceFile());
    }

    private function getIconOutput(false|string $iconPath): string
    {
        if (false === $iconPath) {
            return '';
        }

        if (false === $this->fileDriver->isFile($iconPath)) {
            return '';
        }

        if (str_ends_with($iconPath, '.svg')) {
            return $this->fileDriver->readFile($iconPath);
        }

        return '<img src="'.$iconPath.'" />';
    }
}
