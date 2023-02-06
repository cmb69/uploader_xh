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

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FileSystemService
{
    /** @return list<string> */
    public function getSubdirsOf(string $dirname): array
    {
        $result = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname));
        foreach ($files as $file) {
            if ($file->getFilename() === '.') {
                $dir = str_replace('\\', '/', rtrim(substr($file->getPathname(), strlen($dirname) - 1), '.'));
                $result[] = $dir;
            }
        }
        natcasesort($result);
        return array_values($result);
    }

    public function isDir(string $filename): bool
    {
        return is_dir($filename);
    }
}
