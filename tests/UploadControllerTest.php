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

use PHPUnit\Framework\TestCase;

class UploadControllerTest extends TestCase
{
    public function testDefaultActionRendersPlaceholder(): void
    {
        $plugin_cf = XH_includeVar("./config/config.php", 'plugin_cf');
        $conf = $plugin_cf['uploader'];
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['uploader'];
        $jquery = $this->createStub(Jquery::class);
        $sut = new UploadController($conf, $lang, "./", [], "/", $jquery);
        $response = $sut->defaultAction();
        $this->assertEquals('<div class="uploader_placeholder" data-serial="1"></div>', $response->body());
    }

    public function testDefaultActionIncludesJqueryOnce(): void
    {
        $plugin_cf = XH_includeVar("./config/config.php", 'plugin_cf');
        $conf = $plugin_cf['uploader'];
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['uploader'];
        $jquery = $this->createStub(Jquery::class);
        $jquery->expects($this->once())->method('include');
        $sut = new UploadController($conf, $lang, "./", [], "/", $jquery);
        $sut->defaultAction();
        $sut->defaultAction();
    }
}
