<?php

namespace Uploader;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\Jquery;
use Plib\UploadedFile;
use Plib\View;
use Uploader\Model\FilesizeException;
use Uploader\Model\FileSystemService;
use Uploader\Model\ReadException;
use Uploader\Model\Receiver;
use Uploader\Model\WriteException;

class UploadControllerTest extends TestCase
{
    /** @var UploadController */
    private $sut;

    /** @var Jquery&MockObject */
    private $jquery;

    /** @var FileSystemService&MockObject */
    private $fileSystemService;

    /** @var Receiver&MockObject */
    private $receiver;

    /** @var CsrfProtector&MockObject */
    private $csrfProtector;

    public function setUp(): void
    {
        $plugin_cf = XH_includeVar("./config/config.php", 'plugin_cf');
        $conf = $plugin_cf['uploader'];
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['uploader'];
        $fileFolders = [
            'images' => 'irrelevant_images',
            'downloads' => 'irrelevant_downloads',
            'media' => 'irrelevant_media',
            'userfiles' => 'irrelevant_userfiles',
        ];
        $this->jquery = $this->createStub(Jquery::class);
        $this->fileSystemService = $this->createMock(FileSystemService::class);
        $this->receiver = $this->createStub(Receiver::class);
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->csrfProtector->method("token")->willReturn("the_csrf_token");
        $this->sut = new UploadController(
            1,
            $conf,
            "./",
            $fileFolders,
            $this->jquery,
            $this->fileSystemService,
            $this->receiver,
            $this->csrfProtector,
            "2M",
            new View("./views/", $lang)
        );
    }

    public function testDefaultActionRendersPlaceholder(): void
    {
        $response = ($this->sut)(new FakeRequest(), null, null, null, true);
        $this->assertSame("Uploader – Upload", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testWidgetActionRendersWidget(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/"]);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=widget&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        Approvals::verifyHtml($response->output());
    }

    public function testWidgetIgnoresRegularRequests(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/"]);
        $request = new FakeRequest(["url" => "http://example.com/?&uploader_action=widget&uploader_serial=1"]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("", $response->output());
    }

    public function testWidgetIgnoresUnrelatedRequests(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/"]);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=widget&uploader_serial=17",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("", $response->output());
    }

    public function testRendersDynamicWidget(): void
    {
        $this->fileSystemService->method('getSubdirsOf')->willReturn(["/foo"]);
        $this->fileSystemService->method("isDir")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=widget&uploader_type=downloads&uploader_subdir=%2Ffoo"
                . "&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        Approvals::verifyHtml($response->output());
    }

    public function testUploadIgnoresRegularRequests(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("", $response->output());
    }

    public function testUploadIgnoresUnrelatedRequests(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=17",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("", $response->output());
    }

    public function testUploadsAChunkSuccessfully(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("Done", $response->output());
        $this->assertSame("text/plain", $response->contentType());
    }

    public function testProtectsUploadAgainstCsrf(): void
    {
        $this->receiver->method("handleUpload")->willThrowException(new ReadException());
        $this->csrfProtector->method("check")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("Forbidden", $response->output());
        $this->assertSame(403, $response->status());
    }

    public function testReportsTooLargeFilesOnUpload(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->receiver->method("handleUpload")->willThrowException(new FilesizeException());
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("Forbidden", $response->output());
        $this->assertSame(403, $response->status());
    }

    public function testUploadReportsFailureToRead(): void
    {
        $this->receiver->method("handleUpload")->willThrowException(new ReadException());
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("Read error", $response->output());
        $this->assertSame(500, $response->status());
    }

    public function testUploadReportsFailureToWrite(): void
    {
        $this->receiver->method("handleUpload")->willThrowException(new WriteException());
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, null, null, null, false);
        $this->assertSame("Write error", $response->output());
        $this->assertSame(500, $response->status());
    }

    public function testCatchesUploadOfForbiddenFileExtension(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->receiver->method("handleUpload")->willThrowException(new WriteException());
        $request = new FakeRequest([
            "url" => "http://example.com/?&uploader_action=upload&uploader_serial=1",
            "header" => ["X-CMSimple-XH-Request" => "uploader"],
            "post" => ["name" => "foo.jpeg"],
            "files" => ["uploader_file" => $this->fooJpeg()],
        ]);
        $response = ($this->sut)($request, "downloads", "*", "*", false);
        $this->assertSame("Forbidden", $response->output());
        $this->assertSame(403, $response->status());
    }

    private function fooJpeg(): UploadedFile
    {
        return new UploadedFile("foo.jpeg", "image/jpeg", 1234, "/tmp/123.jpeg", 0);
    }
}
