<?php declare(strict_types=1);

namespace Loki\Field\ViewModel;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File as AssetFile;
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
        return $this->getIconOutput($asset);
    }

    public function getByUrl(string $imageUrl): string
    {
        return '<img src="'.$imageUrl.'" />';
    }

    private function getIconOutput(AssetFile $asset): string
    {
        $iconPath = $asset->getSourceFile();
        if (false === $iconPath) {
            return '';
        }

        if (false === $this->fileDriver->isFile($iconPath)) {
            return '';
        }

        if (str_ends_with($iconPath, '.svg')) {
            return $this->fileDriver->readFile($iconPath);
        }

        return $this->getByUrl($asset->getUrl());
    }
}
