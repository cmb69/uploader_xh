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
    protected $dir;

    /**
     * The name of the destination file.
     *
     * @var string
     */
    protected $filename;

    /**
     * The number of chunks of the upload.
     *
     * @var int
     */
    protected $chunks;

    /**
     * The number of the currently uploaded chunk.
     *
     * @var int
     */
    protected $chunk;

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

    protected function cleanFilename()
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

    public function emitHeaders()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * @param string $filename Name of the input file.
     * @return string JSON response.
     */
    public function handleUpload($filename)
    {
        $out = fopen(
            $this->dir . '/' . $this->filename,
            $this->chunk == 0 ? 'wb' : 'ab'
        );
        if ($out) {
            $in = fopen($filename, 'rb');
            if ($in) {
                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }
                return '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                return '{"jsonrpc": "2.0", "error": {"code": 101, "message":'
                    . ' "Failed to open input stream."}, "id" : "id"}';
            }
            fclose($in);
            fclose($out);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            return '{"jsonrpc": "2.0", "error": {"code": 102, "message":'
                . ' "Failed to open output stream."}, "id" : "id"}';
        }
    }
}
