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

jQuery(function ($) {

    function replaceWidget(element, html) {
        initWidget($(html).replaceAll($(element)).filter(".uploader_widget").get(0));
    }

    function initWidget(element) {

        function updateControls(uploader) {
            var hasPendingUploads = uploader.files.length > uploader.total.uploaded + uploader.total.failed;
            $(".uploader_type, .uploader_subdir, .uploader_resize", element).prop("disabled", hasPendingUploads);
            $(".uploader_uploadfiles", element).prop("disabled", !hasPendingUploads);
        }

        $(".uploader_type, .uploader_subdir, .uploader_resize", element).change(function () {
            $.get($(this).data("url").replace("FIXME", encodeURIComponent(this.value)), {}, function (data) {
                replaceWidget(element, data);
            });
        });

        var config = $.extend($(element).data("config"), {
            browse_button: $(".uploader_pickfiles", element).get(0),
            container: $(".uploader_buttons", element).get(0),
            drop_element: element
        });

        var uploader = new plupload.Uploader(config);
        uploader.init();
        uploader.bind("FilesAdded", function (uploader, files) {
            $.each(files, function () {
                var file = this;
                $(".uploader_row_template", element).clone()
                    .removeClass("uploader_row_template").addClass("uploader_row")
                    .prop("id", this.id)
                    .find(".uploader_filename").text(this.name).end()
                    .find(".uploader_size").text(plupload.formatSize(this.size)).end()
                    .find(".uploader_progress").text("0%").end()
                    .find(".uploader_remove").click(function () {
                        uploader.removeFile(file);
                        $(this).parents(".uploader_row").remove();
                    }).end()
                    .appendTo($(".uploader_filelist", element));
            });
        });
        uploader.bind("QueueChanged", updateControls);
        uploader.bind("UploadProgress", function (uploader, file) {
            $("#" + file.id).find(".uploader_progress").text(file.percent + "%");
        });
        uploader.bind("FileUploaded", function (uploader, file, result) {
            $("#" + file.id).find(".uploader_progress").text(result.response);
        });
        uploader.bind("Error", function (uploader, error) {
            if (error.code === plupload.HTTP_ERROR && error.response) {
                $("#" + error.file.id).find(".uploader_progress").text(error.response);
            } else {
                var message = error.message;
                if (error.file) {
                    message = error.file.name + ": " + message;
                }
                alert(message);
            }
        });
        uploader.bind("UploadComplete", updateControls);
        $(".uploader_uploadfiles", element)
            .prop("disabled", true)
            .click(function () {
                uploader.start();
                return false;
            });
    }

    $(".uploader_placeholder").each(function () {
        var placeholder = this;
        $.get(location.href, {uploader_serial: $(this).data("serial")}, function (data) {
            replaceWidget(placeholder, data);
        });
    });
});
