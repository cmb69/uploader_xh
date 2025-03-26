<?php

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
