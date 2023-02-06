<?php

/**
 * Copyright 2023 Christoph M. Becker
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

class Response
{
    /** @var string */
    private $body;

    /** @var string|null */
    private $contentType;

    /** @var int|null */
    private $statusCode;

    public function __construct(string $body, ?string $contentType = null, ?int $statusCode = null)
    {
        $this->body = $body;
        $this->contentType = $contentType;
        $this->statusCode = $statusCode;
    }

    /** @return string|never */
    public function trigger()
    {
        if ($this->contentType !== null) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            if ($this->statusCode !== null) {
                switch ($this->statusCode) {
                    case 403:
                        header("HTTP/1.1 403 Forbidden");
                        break;
                    case 500:
                        header("HTTP/1.1 500 Internal Server Error");
                        break;
                    default:
                        assert(false);  // @phpstan-ignore-line
                }
            }
            header("Content-Type: {$this->contentType}; charset=UTF-8");
            echo $this->body;
            exit;
        }
        return $this->body;
    }

    public function body(): string
    {
        return $this->body;
    }
}
