<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\FileUploader;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Messages\GlobalMessageRegistry;
use Loki\Field\Component\FileUploader\FileUploaderRepository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileUploaderRepositoryTest extends TestCase
{
    public function testGetValueIsAlwaysFalse(): void
    {
        $repository = new FileUploaderRepository(
            $this->createMock(Filesystem::class),
            $this->createMock(UploaderFactory::class),
            $this->createMock(Http::class)
        );

        $this->assertFalse($repository->getValue());
    }

    public function testSuccessfulUploadAddsSuccessMessage(): void
    {
        $uploader = $this->getUploader(['file' => 'test.png']);
        $uploader->expects($this->once())->method('save')->with('/var/www/yireo-test');
        $uploader->expects($this->once())->method('setAllowedExtensions')->with(['jpg', 'pdf', 'doc', 'png', 'zip']);
        $uploader->expects($this->once())->method('setAllowCreateFolders')->with(true);
        $uploader->expects($this->once())->method('setAllowRenameFiles')->with(true);

        $messageRegistry = $this->createMock(GlobalMessageRegistry::class);
        $messageRegistry->expects($this->once())->method('addSuccess')->with('File has been successfully uploaded.');
        $messageRegistry->expects($this->never())->method('addError');

        $repository = $this->getRepository($uploader, $messageRegistry);
        $repository->saveValue('ignored');
    }

    public function testFailedUploadAddsErrorMessage(): void
    {
        $uploader = $this->getUploader([]);

        $messageRegistry = $this->createMock(GlobalMessageRegistry::class);
        $messageRegistry->expects($this->once())->method('addError')->with('File could not be uploaded.');
        $messageRegistry->expects($this->never())->method('addSuccess');

        $repository = $this->getRepository($uploader, $messageRegistry);
        $repository->saveValue('ignored');
    }

    private function getUploader(array $result): Uploader&MockObject
    {
        $uploader = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'setAllowedExtensions',
                'setAllowCreateFolders',
                'setAllowRenameFiles',
                'save',
                'getUploadedFileName',
            ])
            ->getMock();
        $uploader->method('save')->willReturn($result);
        $uploader->method('getUploadedFileName')->willReturn('test.png');

        return $uploader;
    }

    private function getRepository(Uploader $uploader, GlobalMessageRegistry $messageRegistry): FileUploaderRepository
    {
        $directoryWriter = $this->createMock(WriteInterface::class);
        $directoryWriter->method('getAbsolutePath')->with('yireo-test')->willReturn('/var/www/yireo-test');

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('getDirectoryWrite')->with(DirectoryList::ROOT)->willReturn($directoryWriter);

        $uploaderFactory = $this->createMock(UploaderFactory::class);
        $uploaderFactory->expects($this->once())
            ->method('create')
            ->with(['fileId' => 'componentValue'])
            ->willReturn($uploader);

        $httpRequest = $this->createMock(Http::class);
        $httpRequest->method('getFiles')->with('componentValue')->willReturn(['name' => 'test.png']);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getGlobalMessageRegistry')->willReturn($messageRegistry);

        $repository = new FileUploaderRepository($filesystem, $uploaderFactory, $httpRequest);
        $repository->setComponent($component);

        return $repository;
    }
}
