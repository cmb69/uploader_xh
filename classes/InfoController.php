<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
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

class InfoController
{
    /** @var string */
    private $pluginsFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /** @param array<string,string> $lang */
    public function __construct(string $pluginsFolder, array $lang, SystemChecker $systemChecker)
    {
        $this->pluginsFolder = $pluginsFolder;
        $this->lang = $lang;
        $this->systemChecker = $systemChecker;
    }

    /** @return void */
    public function defaultAction()
    {
        $systemCheckService = new SystemCheckService($this->pluginsFolder, $this->lang, $this->systemChecker);
        $view = new View("{$this->pluginsFolder}uploader/views/", $this->lang);
        echo $view->render('info', [
            'version' => Plugin::VERSION,
            'checks' => $systemCheckService->getChecks(),
        ]);
    }
}
