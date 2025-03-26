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

class Receiver
{
    /** @var int */
    private $maxFilesize;

    public function __construct(int $maxFilesize)
    {
        $this->maxFilesize = $maxFilesize;
    }

    private function cleanFilename(string $dir, string $filename, int $chunks): string
    {
        $filename = (string) preg_replace('/[^a-z0-9_\.-]+/i', '', $filename);
        if ($chunks <= 1 && file_exists($dir . $filename)) {
            $pathinfo = pathinfo($filename);
            $extension = isset($pathinfo['extension']) ? ".{$pathinfo['extension']}" : "";
            $count = 0;
            do {
                $count++;
                $path = "{$dir}{$pathinfo['filename']}_{$count}{$extension}";
            } while (file_exists($path));
            $filename = basename($path);
        }
        return $filename;
    }

    public function handleUpload(string $dir, string $filename, string $tmpName, int $chunks, int $chunk): void
    {
        $destFilename = "{$dir}/" . $this->cleanFilename($dir, $filename, $chunks);
        if ($out = @fopen($destFilename, $chunk == 0 ? 'wb' : 'ab')) {
            if ($in = @fopen($tmpName, 'rb')) {
                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }
                fclose($in);
                fclose($out);
                if (filesize($destFilename) > $this->maxFilesize) {
                    unlink($destFilename);
                    throw new FilesizeException();
                }
            } else {
                fclose($out);
                throw new ReadException();
            }
        } else {
            throw new WriteException();
        }
    }
}
