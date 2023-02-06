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

class FileSystemServiceTest extends TestCase
{
    public function testFindsOnlyItselfInEmptyFolder(): void
    {
        vfsStream::setup("root/");
        $sut = new FileSystemService();
        $result = $sut->getSubdirsOf(vfsStream::url("root/"));
        $this->assertEquals(["/"], $result);
    }

    public function testFindsAllSubdirsInAlphabeticOrderInNotEmptyFolder(): void
    {
        vfsStream::setup("root/");
        mkdir(vfsStream::url("root/c/"));
        mkdir(vfsStream::url("root/b/"));
        mkdir(vfsStream::url("root/a/"));
        $sut = new FileSystemService();
        $result = $sut->getSubdirsOf(vfsStream::url("root/"));
        $this->assertEquals(["/", "/a/", "/b/", "/c/"], $result);
    }

    public function testFindsAllNestedSubdirsInAlphabeticOrderInNotEmptyFolder(): void
    {
        vfsStream::setup("root/");
        mkdir(vfsStream::url("root/c/"));
        mkdir(vfsStream::url("root/c/d/"));
        mkdir(vfsStream::url("root/b/"));
        mkdir(vfsStream::url("root/a/"));
        $sut = new FileSystemService();
        $result = $sut->getSubdirsOf(vfsStream::url("root/"));
        $this->assertEquals(["/", "/a/", "/b/", "/c/", "/c/d/"], $result);
    }

    public function testIsDir(): void
    {
        vfsStream::setup("root/");
        mkdir(vfsStream::url("root/a/"));
        touch(vfsStream::url("root/b/"));
        $sut = new FileSystemService();
        $result = $sut->isDir(vfsStream::url("root/a/"));
        $this->assertTrue($result);
        $result = $sut->isDir(vfsStream::url("root/b/"));
        $this->assertFalse($result);
        $result = $sut->isDir(vfsStream::url("root/c/"));
        $this->assertFalse($result);
    }
}
