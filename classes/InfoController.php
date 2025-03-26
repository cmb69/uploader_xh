<?php

/**
 * Copyright (c) Christoph M. Becker
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

use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        return Response::create($this->view->render('info', [
            'version' => UPLOADER_VERSION,
            'checks' => $this->getChecks(),
        ]))->withTitle($this->view->esc("Uploader " . UPLOADER_VERSION));
    }

    /**
     * @return list<array{class:string,label:string,stateLabel:string}>
     */
    private function getChecks()
    {
        return array(
            $this->checkPhpVersion('7.1.0'),
            $this->checkXhVersion('1.7.0'),
            $this->checkPlibVersion("1.4"),
            $this->checkPlugin('jquery'),
            $this->checkWritability("{$this->pluginFolder}config/"),
            $this->checkWritability("{$this->pluginFolder}css/"),
            $this->checkWritability("{$this->pluginFolder}languages/")
        );
    }

    /**
     * @param string $version
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkPhpVersion($version)
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain("syscheck_phpversion", $version),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /**
     * @param string $version
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkXhVersion($version)
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain("syscheck_xhversion", $version),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /**
     * @param string $version
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkPlibVersion($version)
    {
        $state = $this->systemChecker->checkPlugin("plib", $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain("syscheck_plibversion", $version),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /**
     * @param string $plugin
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkPlugin($plugin)
    {
        $state = $this->systemChecker->checkPlugin("jquery") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain("syscheck_plugin", $plugin),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }

    /**
     * @param string $folder
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkWritability($folder)
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => $this->view->plain("syscheck_writable", $folder),
            'stateLabel' => $this->view->plain("syscheck_$state"),
        ];
    }
}
