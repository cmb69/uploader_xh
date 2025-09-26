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

/* global alert,plupload */

/** @type {(element: Element, html: string) => void} */
function replaceWidget(element, html) {
    element.innerHTML = html;
    new Widget(element.querySelector(".uploader_widget"));
}

class Widget {
    constructor(/** @type {HTMLElement} */ element) {
        this.element = element;
        /** @type {NodeListOf<HTMLInputElement>} */ (
            element.querySelectorAll(".uploader_type, .uploader_subdir, .uploader_resize")
        ).forEach(function (el) {
            el.onchange = function () {
                var url = el.dataset.url.replace("FIXME", encodeURIComponent(el.value));
                var request = new XMLHttpRequest();
                request.open("GET", url);
                request.setRequestHeader("X-CMSimple-XH-Request", "uploader");
                request.onload = function () {
                    replaceWidget(element, request.responseText);
                };
                request.send();
            };
        });

        var config = Object.assign(JSON.parse(element.dataset.config), {
            browse_button: element.querySelector(".uploader_pickfiles"),
            container: element.querySelector(".uploader_buttons"),
            drop_element: element,
            headers: { "X-CMSimple-XH-Request": "uploader" },
        });

        this.uploader = /** @type {plupload} */ (new plupload.Uploader(config));
        this.uploader.init();
        this.uploader.bind("FilesAdded", function (uploader, files) {
            files.forEach(function (file) {
                var clone = /** @type {HTMLTableRowElement} */ (
                    element.querySelector(".uploader_row_template").cloneNode(true)
                );
                clone.classList.remove("uploader_row_template");
                clone.classList.add("uploader_row");
                clone.id = file.id;
                clone.querySelector(".uploader_filename").textContent = file.name;
                // @ts-ignore
                var size = plupload.formatSize(file.size);
                clone.querySelector(".uploader_size").textContent = size;
                clone.querySelector(".uploader_progress").textContent = "0%";
                /** @type {HTMLButtonElement} */ (clone.querySelector(".uploader_remove")).onclick =
                    function (event) {
                        uploader.removeFile(file);
                        var button = /** @type {HTMLButtonElement} */ (event.currentTarget);
                        button.closest(".uploader_row").remove();
                    };
                element.querySelector(".uploader_filelist").append(clone);
            });
        });
        this.uploader.bind("QueueChanged", () => this.updateControls());
        this.uploader.bind("UploadProgress", function (uploader, file) {
            document.querySelector("#" + file.id + " .uploader_progress").textContent =
                file.percent + "%";
        });
        this.uploader.bind("FileUploaded", function (uploader, file, result) {
            document.querySelector("#" + file.id + " .uploader_progress").textContent =
                result.response;
        });
        this.uploader.bind("Error", function (uploader, error) {
            if (error.code === plupload.HTTP_ERROR && error.response) {
                document.querySelector("#" + error.file.id + " .uploader_progress").textContent =
                    error.response;
            } else {
                var message = error.message;
                if (error.file) {
                    message = error.file.name + ": " + message;
                }
                alert(message);
            }
        });
        this.uploader.bind("UploadComplete", () => this.updateControls());
        var uploadFilesButton = /** @type {HTMLButtonElement} */ (
            element.querySelector(".uploader_uploadfiles")
        );
        uploadFilesButton.disabled = true;
        uploadFilesButton.onclick = (event) => {
            this.uploader.start();
            event.stopPropagation();
        };
    }

    /** @type {() => void} */
    updateControls() {
        var hasPendingUploads =
            this.uploader.files.length > this.uploader.total.uploaded + this.uploader.total.failed;
        /** @type {NodeListOf<HTMLInputElement>} */ (
            this.element.querySelectorAll(".uploader_type, .uploader_subdir, .uploader_resize")
        ).forEach(function (el) {
            el.disabled = hasPendingUploads;
        });
        /** @type {HTMLButtonElement} */ (
            this.element.querySelector(".uploader_uploadfiles")
        ).disabled = !hasPendingUploads;
    }
}

/** @type {NodeListOf<HTMLElement>} */ (document.querySelectorAll(".uploader_placeholder")).forEach(
    function (el) {
        var params = new URLSearchParams();
        params.append("uploader_action", "widget");
        params.append("uploader_serial", el.dataset.serial);
        var request = new XMLHttpRequest();
        request.open("GET", location.href + "&" + params.toString());
        request.setRequestHeader("X-CMSimple-XH-Request", "uploader");
        request.onload = function () {
            replaceWidget(el, request.responseText);
        };
        request.send();
    }
);
