<?php

namespace Uploader;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    public function testDefaultActionShowsPluginInfo(): void
    {
        $sut = new InfoController(
            "./plugins/uploader/",
            new FakeSystemChecker(true),
            new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["uploader"])
        );
        $response = $sut();
        $this->assertSame("Uploader 1.1-dev", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
