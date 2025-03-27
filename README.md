# Uploader_XH

Uploader_XH facilitates the upload of files to the `images/`,
`downloads/`, `userfiles/` and `media/` folders of CMSimple_XH.
Contrary to the standard filemanager it allows queued and chunked
uploading, so it is an alternative to FTP, if you want to upload many and/or
large files. Uploader_XH is, however, not an alternative filemanager.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
  - [Back-End](#back-end)
  - [Front-End](#front-end)
    - [Examples](#examples)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Uploader_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, PHP ≥ 7.1.0,
and a browser that is supported by the jQuery version in use.
Uploader_XH also requires [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.5;
if that is not already installed (see `Settings` → `Info`),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/uploader_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins. See the
[CMSimple_XH wiki](https://wiki.cmsimple-xh.org/?for-users/working-with-the-cms/plugins#id3_install-plugin)
for further details.

1. **Backup the data on your server.**
1. Unzip the distribution on your computer.
1. Upload the whole directory `uploader/` to your server into the
   `plugins/` folder of CMSimple_XH.
1. Set write permissions for the subdirectories `config/`,
   `css/`, and `languages/`.
1. Navigate to `Plugins` → `Uploader` in the back-end to check if all
   requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins in
the back-end of the Website. Select `Plugins` → `Uploader`.

You can change the default settings of Uploader_XH under `Config`.
Hints for the options will be displayed when hovering over the help icon
with your mouse.

Localization is done under `Language`. You can translate the character
strings to your own language if there is no appropriate language file
available, or customize them according to your needs.

The look of Uploader_XH can be customized under `Stylesheet`.

## Usage

### Back-End

In the back-end under `Plugins` → `Uploader` → `Upload`
you can find the upload form. Its usage is pretty much self-explaining. Use
the select-boxes to choose the type of upload, the subfolder and the size
for JPEG and PNG images.

### Front-End

It is possible to use Uploader_XH on a CMSimple_XH page.
**You are strongly advised to make use of this feature only on pages that are *not* publicly
available** (e.g. pages protected by [Register_XH](https://github.com/cmb69/register_xh)
or [Memberpages](https://github.com/cmsimple-xh/memberpages)).
Otherwise the disk space of your server might be quickly filled up with useless
or even dangerous files.

To display the upload widget insert in the content:

    {{{uploader('%TYPE%', '%SUBDIR%', '%RESIZE%')}}}

The placeholders have the following meaning:

- `%TYPE%`:
  The upload type, i.e. `images`, `downloads`, `media` or `userfiles`.
  `*` will display a selectbox to the user. Defaults to `images`.

- `%SUBDIR%`:
  The subfolder (terminated by `/`) relative to the folder of that type
  (set in the configuration of CMSimple_XH) where the files should be uploaded to.
  Note that the subfolder has to exist. `*` will display a
  selectbox to the user. Defaults to `/`.

- `%RESIZE%`:
  The resize mode for uploaded JPEG or PNG images, i.e. `blank` (no resizing),
  `small`, `medium`, `large`.
  `*` will display a selectbox to the user. Defaults to `blank`.
  The desired sizes can be set in the configuration of Uploader_XH.
  Note that this will not upscale images, but the sizes are rather a maximum,
  where the image ratio will be maintained.
  Also note that the images are resized in the browser, so bandwidth can be
  saved during the upload.
  However, this resizing may not yield the best quality,
  so check for yourself whether you want to apply any resize options.

#### Examples

Uploading images directly to the configured image folder:

    {{{uploader()}}}

Uploading images directly to the configured image folder always resized to small size:

    {{{uploader('images', '', 'small')}}}

Uploading documents to the subfolder `extern/` of the configured download folder:

    {{{uploader('downloads', 'extern/')}}}

Uploading files to a selectable subfolder of the configured userfiles folder:

    {{{uploader('userfiles', '*')}}}

Uploading with full flexibility as available in the back-end:

    {{{uploader('*', '*', '*')}}}

Separate upload widgets for images and media files on the same page:

    {{{uploader('images', '', '')}}}
    {{{uploader('media', '', '')}}}

## Limitations

The full feature set of Uploader_XH is only supported on contemporary
browsers. Older browsers may not offer all features, such as chunked
uploading, image resizing etc.,
or may not be supported at all.

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/uploader_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Uploader_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Uploader_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Uploader_XH.  If not, see <http://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Slovak translation © Dr. Martin Sereday<br>
Czech translation © Josef Němec<br>
Danish translation © Jens Maegard

## Credits

Uploader_XH uses [Plupload](https://www.plupload.com/)</a>. Many thanks
to [Ephox](https://www.ephox.com/) for releasing it under AGPL.

The plugin logo is designed by [schollidesign](https://www.deviantart.com/schollidesign).
Many thanks for publishing this icon under GPL.

Many thanks to the community at the [CMSimple_XH-Forum](http://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Particularly I want to thank *twc*, who made me aware of Plupload,
and *wolfgang_58* and *Tata* for testing and reporting bugs.
And many thanks to *Holger*, who tested the primordial API and helped improving it.
Also many thanks to *pmschulze* who reported a severe bug in 1.0beta1.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
