<?php

/**
 * Copyright 2023 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

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

    public function testThrowsFileSizeExceptionIfFileIsTooLarge(): void
    {
        vfsStream::setup("root/");
        file_put_contents(vfsStream::url("root/upload"), "data");
        $sut = new Receiver(vfsStream::url("root/"), "image.jpg", 1, 0, 1);
        $this->expectException(FileSizeException::class);
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
}
