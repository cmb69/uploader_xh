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
        this.selects.forEach((el) => {
            el.onchange = () => {
                var url = el.dataset.url.replace("FIXME", encodeURIComponent(el.value));
                this.fetchWidget(url);
            };
        });

        this.uploader = /** @type {plupload} */ (new plupload.Uploader(this.config));
        this.uploader.init();
        this.uploader.bind("FilesAdded", (uploader, files) => {
            files.forEach((file) => this.addFile(file));
        });
        this.uploader.bind("QueueChanged", () => this.updateControls());
        this.uploader.bind("UploadProgress", (uploader, file) => {
            this.setUploadProgress(file.id, file.percent + "%");
        });
        this.uploader.bind("FileUploaded", (uploader, file, result) => {
            this.setUploadProgress(file.id, result.response);
        });
        this.uploader.bind("Error", (uploader, error) => {
            if (error.code === plupload.HTTP_ERROR && error.response) {
                this.setUploadProgress(error.file.id, error.response);
            } else {
                this.reportError(error);
            }
        });
        this.uploader.bind("UploadComplete", () => this.updateControls());
        let uploadFilesButton = this.uploadFilesButton;
        uploadFilesButton.disabled = true;
        uploadFilesButton.onclick = () => this.uploader.start();
    }

    /** @type {object} */
    get config() {
        return Object.assign(JSON.parse(this.element.dataset.config), {
            browse_button: this.element.querySelector(".uploader_pickfiles"),
            container: this.element.querySelector(".uploader_buttons"),
            drop_element: this.element,
            headers: { "X-CMSimple-XH-Request": "uploader" },
        });
    }

    /** @type {NodeListOf<HTMLInputElement>} */
    get selects() {
        return this.element.querySelectorAll(".uploader_type, .uploader_subdir, .uploader_resize");
    }

    /** @type {HTMLButtonElement} */
    get uploadFilesButton() {
        return this.element.querySelector(".uploader_uploadfiles");
    }

    /** @type {(url: string) => void} */
    fetchWidget(url) {
        let request = new XMLHttpRequest();
        request.open("GET", url);
        request.setRequestHeader("X-CMSimple-XH-Request", "uploader");
        request.onload = () => {
            replaceWidget(this.element, request.responseText);
        };
        request.send();
    }

    /** @type {(id: string, text: string) => void} */
    setUploadProgress(id, text) {
        document.getElementById(id).querySelector(".uploader_progress").textContent = text;
    }

    /** @type {(error: object) => void} */
    reportError(error) {
        let message = error.message;
        if (error.file) {
            message = error.file.name + ": " + message;
        }
        alert(message);
    }

    /** @type {(file: File) => void} */
    addFile(file) {
        var clone = /** @type {HTMLTableRowElement} */ (
            this.element.querySelector(".uploader_row_template").cloneNode(true)
        );
        clone.classList.remove("uploader_row_template");
        clone.classList.add("uploader_row");
        // @ts-ignore
        clone.id = file.id;
        clone.querySelector(".uploader_filename").textContent = file.name;
        // @ts-ignore
        var size = plupload.formatSize(file.size);
        clone.querySelector(".uploader_size").textContent = size;
        clone.querySelector(".uploader_progress").textContent = "0%";
        /** @type {HTMLButtonElement} */ (clone.querySelector(".uploader_remove")).onclick = (
            event
        ) => {
            var button = /** @type {HTMLButtonElement} */ (event.currentTarget);
            this.removeFile(button.closest(".uploader_row").id);
        };
        this.element.querySelector(".uploader_filelist").append(clone);
    }

    /** @type {(id: string) => void} */
    removeFile(id) {
        this.uploader.removeFile(id);
        document.getElementById(id).remove();
    }

    /** @type {() => void} */
    updateControls() {
        var hasPendingUploads =
            this.uploader.files.length > this.uploader.total.uploaded + this.uploader.total.failed;
        this.selects.forEach(function (el) {
            el.disabled = hasPendingUploads;
        });
        this.uploadFilesButton.disabled = !hasPendingUploads;
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
