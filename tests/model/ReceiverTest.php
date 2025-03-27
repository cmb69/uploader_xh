<?php

namespace Uploader\Model;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ReceiverTest extends TestCase
{
    public function testCanUploadFile(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(1000);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 1, 0);
        $this->assertStringEqualsFile(vfsStream::url("root/image.jpg"), "data");
    }

    public function testRenamesUploadedFileIfAlreadyExisting(): void
    {
        vfsStream::setup("root/");
        touch(vfsStream::url("root/image.jpg"));
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(1000);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 1, 0);
        $this->assertStringEqualsFile(vfsStream::url("root/image_1.jpg"), "data");
    }

    public function testCanUploadFileInChunks(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "da");
        $sut = new Receiver(1000);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 2, 0);
        file_put_contents(vfsStream::url("root/upload"), "ta");
        $sut = new Receiver(1000);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 2, 1);
        $this->assertStringEqualsFile(vfsStream::url("root/image.jpg"), "data");
    }

    public function testThrowsFilesizeExceptionIfFileIsTooLarge(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(1);
        $this->expectException(FilesizeException::class);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 1, 0);
    }

    public function testThrowsReadExceptionIfFileIsNotReadable(): void
    {
        vfsStream::setup("root/");
        chmod(vfsStream::url("root/upload"), 0444);
        $sut = new Receiver(1000);
        $this->expectException(ReadException::class);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 1, 0);
    }

    public function testThrowsWriteExceptionIfFileIsNotWritable(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "da");
        $sut = new Receiver(1000);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 2, 0);
        chmod(vfsStream::url("root/image.jpg"), 0444);
        $sut = new Receiver(1000);
        $this->expectException(WriteException::class);
        $sut->handleUpload(vfsStream::url("root/"), "image.jpg", vfsStream::url("root/upload"), 2, 1);
    }
}
