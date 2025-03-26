<?php

namespace Uploader;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ReceiverTest extends TestCase
{
    public function testCanUploadFile(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 1, 0, 1000);
        $sut->handleUpload(vfsStream::url("root/upload"));
        $this->assertStringEqualsFile(vfsStream::url("root/image.jpg"), "data");
    }

    public function testRenamesUploadedFileIfAlreadyExisting(): void
    {
        vfsStream::setup("root/");
        touch(vfsStream::url("root/image.jpg"));
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 1, 0, 1000);
        $sut->handleUpload(vfsStream::url("root/upload"));
        $this->assertStringEqualsFile(vfsStream::url("root/image_1.jpg"), "data");
    }

    public function testCanUploadFileInChunks(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "da");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 2, 0, 1000);
        $sut->handleUpload(vfsStream::url("root/upload"));
        file_put_contents(vfsStream::url("root/upload"), "ta");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 2, 1, 1000);
        $sut->handleUpload(vfsStream::url("root/upload"));
        $this->assertStringEqualsFile(vfsStream::url("root/image.jpg"), "data");
    }

    public function testThrowsFilesizeExceptionIfFileIsTooLarge(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 1, 0, 1);
        $this->expectException(FilesizeException::class);
        $sut->handleUpload(vfsStream::url("root/upload"));
    }

    public function testThrowsReadExceptionIfFileIsNotReadable(): void
    {
        vfsStream::setup("root/");
        chmod(vfsStream::url("root/upload"), 0444);
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 1, 0, 1000);
        $this->expectException(ReadException::class);
        $sut->handleUpload(vfsStream::url("root/upload"));
    }

    public function testThrowsWriteExceptionIfFileIsNotWritable(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "da");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 2, 0, 1000);
        $sut->handleUpload(vfsStream::url("root/upload"));
        chmod(vfsStream::url("root/image.jpg"), 0444);
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 2, 1, 1000);
        $this->expectException(WriteException::class);
        $sut->handleUpload(vfsStream::url("root/upload"));
    }
}
