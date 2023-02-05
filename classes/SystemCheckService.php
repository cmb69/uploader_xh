<?php

/**
 * Copyright 2017 Christoph M. Becker
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

class SystemCheckService
{
    /**
     * @var string
     */
    private $pluginsFolder;

    /**
     * @var string
     */
    private $pluginFolder;

    /**
     * @var array<string,string>
     */
    private $lang;

    public function __construct()
    {
        global $pth, $plugin_tx;

        $this->pluginsFolder = $pth['folder']['plugins'];
        $this->pluginFolder = "{$this->pluginsFolder}uploader";
        $this->lang = $plugin_tx['uploader'];
    }

    /**
     * @return list<array{class:string,label:string,stateLabel:string}>
     */
    public function getChecks()
    {
        return array(
            $this->checkPhpVersion('7.1.0'),
            $this->checkExtension('json'),
            $this->checkXhVersion('1.7.0'),
            $this->checkPlugin('jquery'),
            $this->checkWritability("$this->pluginFolder/config/"),
            $this->checkWritability("$this->pluginFolder/css/"),
            $this->checkWritability("$this->pluginFolder/languages/")
        );
    }

    /**
     * @param string $version
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkPhpVersion($version)
    {
        $state = version_compare(PHP_VERSION, $version, 'ge') ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_phpversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /**
     * @param string $extension
     * @param bool $isMandatory
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkExtension($extension, $isMandatory = true)
    {
        $state = extension_loaded($extension) ? 'success' : ($isMandatory ? 'fail' : 'warning');
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_extension'], $extension),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /**
     * @param string $version
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkXhVersion($version)
    {
        $state = version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH $version", 'ge') ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_xhversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /**
     * @param string $plugin
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkPlugin($plugin)
    {
        $state = is_dir("{$this->pluginsFolder}{$plugin}") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_plugin'], $plugin),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /**
     * @param string $folder
     * @return array{class:string,label:string,stateLabel:string}
     */
    private function checkWritability($folder)
    {
        $state = is_writable($folder) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_writable'], $folder),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }
}
