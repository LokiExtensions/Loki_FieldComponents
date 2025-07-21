<?php
declare(strict_types=1);

namespace Loki\Field\Component\FileUploader;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Loki\Field\Component\Base\Field\FieldRepository;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class FileUploaderRepository extends FieldRepository
{
    public function __construct(
        private Filesystem $filesystem,
        private UploaderFactory $uploaderFactory,
        private Http $httpRequest
    ) {
    }

    public function getValue(): mixed
    {
        return false;
    }

    public function saveValue(mixed $value): void
    {
        // @todo: Move this to uploader utility instead
        $folder = 'yireo-test';
        $input = 'componentValue';
        $file = $this->httpRequest->getFiles($input);
        $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;
        $target = $this->getDirectoryWriter()->getAbsolutePath($folder);

        $uploader = $this->uploaderFactory->create(['fileId' => $input]);
        $uploader->setAllowedExtensions(['jpg', 'pdf', 'doc', 'png', 'zip']);
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($target);

        if ($result['file']) {
            $this->getGlobalMessageRegistry()->addSuccess('File has been successfully uploaded.');
        }

        $absoluteFilename = $target . $uploader->getUploadedFileName();
        // @todo: Save $absoluteFilename somewhere
    }

    private function getDirectoryWriter():  WriteInterface
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }
}
