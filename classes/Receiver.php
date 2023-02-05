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

class Receiver
{
    /**
     * The path of the destination folder.
     *
     * @var string
     */
    private $dir;

    /**
     * The name of the destination file.
     *
     * @var string
     */
    private $filename;

    /**
     * The number of chunks of the upload.
     *
     * @var int
     */
    private $chunks;

    /**
     * The number of the currently uploaded chunk.
     *
     * @var int
     */
    private $chunk;

    /**
     * @var int
     */
    private $maxFilesize;

    /**
     * @param string $dir      Path of the destination folder.
     * @param string $filename Name of the destination file.
     * @param int    $chunks   The number of chunks of the upload.
     * @param int    $chunk    The number of the currently uploaded chunk.
     * @param int    $maxSize
     */
    public function __construct($dir, $filename, $chunks, $chunk, $maxFilesize)
    {
        $this->dir = $dir;
        $this->chunks = $chunks;
        $this->chunk = $chunk;
        $this->maxFilesize = $maxFilesize;
        $this->filename = $this->cleanFilename($filename);
    }

    /**
     * @param string $filename
     * @return string
     */
    private function cleanFilename($filename)
    {
        $filename = preg_replace('/[^a-z0-9_\.-]+/i', '', $filename);
        if ($this->chunks <= 1 && file_exists($this->dir . $filename)) {
            $pathinfo = pathinfo($filename);
            $count = 0;
            do {
                $count++;
                $path = "{$this->dir}{$pathinfo['filename']}_{$count}.{$pathinfo['extension']}";
            } while (file_exists($path));
            $filename = basename($path);
        }
        return $filename;
    }

    /**
     * @param string $filename Name of the input file.
     * @return void
     */
    public function handleUpload($filename)
    {
        if ($out = fopen("$this->dir/$this->filename", $this->chunk == 0 ? 'wb' : 'ab')) {
            if ($in = fopen($filename, 'rb')) {
                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }
                fclose($in);
                fclose($out);
                if (filesize("$this->dir/$this->filename") > $this->maxFilesize) {
                    unlink("$this->dir/$this->filename");
                    throw new FilesizeException;
                }
            } else {
                fclose($out);
                throw new ReadException;
            }
        } else {
            throw new WriteException;
        }
    }
}
