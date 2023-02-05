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

/**
 * @phpstan-import-type FileFolders from UploadController
 */
class MainController extends UploadController
{
    /** @var string */
    protected $type;

    /** @var string */
    protected $subdir;

    /** @var string */
    protected $resize;

    /**
     * @param array<string,string> $config
     * @param array<string,string> $lang
     * @param FileFolders $fileFolders
     * @param string $type
     * @param string $subdir
     * @param string $resize
     */
    public function __construct(
        array $config,
        array $lang,
        string $pluginFolder,
        array $fileFolders,
        string $scriptName,
        $type,
        $subdir,
        $resize
    ) {
        parent::__construct($config, $lang, $pluginFolder, $fileFolders, $scriptName);
        $this->type = $type;
        $this->subdir = $subdir;
        $this->resize = $resize;
    }

    /**
     * @return bool
     */
    protected function isUploadAllowed()
    {
        return ($this->type === '*' || $this->getType() === $this->type)
            && ($this->subdir === '*' || $this->getSubfolder() === $this->subdir)
            && isset($_POST['name'])
            && $this->isExtensionAllowed($_POST['name'])
            && isset($_FILES['uploader_file']['tmp_name'])
            && filesize($_FILES['uploader_file']['tmp_name']) <= $this->config['size_max'];
    }

    /**
     * @param string $filename
     * @return bool
     */
    private function isExtensionAllowed($filename)
    {
        return in_array(
            strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
            explode(',', $this->config['ext_' . $this->getType()])
        );
    }
}
