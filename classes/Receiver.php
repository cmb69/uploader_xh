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
    /** @var string */
    private $dir;

    /** @var string */
    private $filename;

    /** @var int */
    private $chunks;

    /** @var int */
    private $chunk;

    /** @var int */
    private $maxFilesize;

    public function __construct(string $dir, string $filename, int $chunks, int $chunk, int $maxFilesize)
    {
        $this->dir = $dir;
        $this->chunks = $chunks;
        $this->chunk = $chunk;
        $this->maxFilesize = $maxFilesize;
        $this->filename = $filename;
    }

    private function cleanFilename(string $filename): string
    {
        $filename = (string) preg_replace('/[^a-z0-9_\.-]+/i', '', $filename);
        if ($this->chunks <= 1 && file_exists($this->dir . $filename)) {
            $pathinfo = pathinfo($filename);
            $extension = isset($pathinfo['extension']) ? ".{$pathinfo['extension']}" : "";
            $count = 0;
            do {
                $count++;
                $path = "{$this->dir}{$pathinfo['filename']}_{$count}{$extension}";
            } while (file_exists($path));
            $filename = basename($path);
        }
        return $filename;
    }

    public function handleUpload(string $filename): void
    {
        $destFilename = "{$this->dir}/" . $this->cleanFilename($this->filename);
        if ($out = @fopen($destFilename, $this->chunk == 0 ? 'wb' : 'ab')) {
            if ($in = @fopen($filename, 'rb')) {
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
