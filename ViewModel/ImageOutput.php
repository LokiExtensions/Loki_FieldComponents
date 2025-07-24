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
        private readonly AssetRepository $assetRepository,
        private readonly DirectoryList $directoryList,
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
        $sourceFile = $asset->getSourceFile();
        if (false === $sourceFile) {
            return '';
        }

        if (false === $this->fileDriver->isFile($sourceFile)) {
            return '';
        }

        if (str_ends_with($sourceFile, '.svg')) {
            $iconPath = str_replace($this->directoryList->getRoot(), '', $sourceFile);
            return $this->fileDriver->readFile($iconPath);
        }

        return $this->getByUrl($asset->getUrl());
    }
}
