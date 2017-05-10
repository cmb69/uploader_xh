<?php

/**
 * Copyright 2011-2017 Christoph M. Becker
 *
 * This file is part of Uploader_XH.
 *
 * Uploader_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Uploader_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
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
     * @param string $dir      Path of the destination folder.
     * @param string $filename Name of the destination file.
     * @param int    $chunks   The number of chunks of the upload.
     * @param int    $chunk    The number of the currently uploaded chunk.
     */
    public function __construct($dir, $filename, $chunks, $chunk)
    {
        $this->dir = $dir;
        $this->filename = $filename;
        $this->chunks = $chunks;
        $this->chunk = $chunk;
        $this->cleanFilename();
    }

    private function cleanFilename()
    {
        $this->filename = preg_replace('/[^\w\._]+/', '', $this->filename);
        if ($this->chunks < 2 && file_exists($this->dir . $this->filename)) {
            $ext = strrpos($this->filename, '.');
            $beginning = substr($this->filename, 0, $ext);
            $end = substr($this->filename, $ext);
            $count = 1;
            $path = $this->dir . $beginning . '_' . $count . $end;
            while (file_exists($path)) {
                $count++;
                $path = $this->dir . $beginning . '_' . $count . $end;
            }
            $this->filename = $beginning . '_' . $count . $end;
        }
    }

    /**
     * @param string $filename Name of the input file.
     * @return string JSON response.
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
            } else {
                fclose($out);
                throw new ReadException;
            }
        } else {
            throw new WriteException;
        }
    }
}
