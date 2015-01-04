<?php

/**
 * Receiver of Uploader_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Uploader
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Uploader_XH
 */

/**
 * The receiver class.
 *
 * @category CMSimple_XH
 * @package  Uploader
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Uploader_XH
 */
class Uploader_Receiver
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
     * Initialize a new object.
     *
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

    /**
     * Cleans the filename and makes it unique.
     *
     * @return void
     */
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

    /**
     * Emit HTTP headers.
     *
     * @return void
     */
    public function emitHeaders()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * Handles the file upload.
     *
     * @param string $filename Name of the input file.
     *
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
                return '{"jsonrpc": "2.0", "error": {"code": 101, "message":'
                    . ' "Failed to open input stream."}, "id" : "id"}';
            }
            fclose($in);
            fclose($out);
        } else {
            return '{"jsonrpc": "2.0", "error": {"code": 102, "message":'
                . ' "Failed to open output stream."}, "id" : "id"}';
        }
    }
}

?>
