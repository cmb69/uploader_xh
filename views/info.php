<h1>Uploader – <?=$this->text('menu_info')?></h1>
<img class="uploader_logo" src="<?=$this->escape($logo)?>" alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$this->escape($version)?></p>
<p>Copyright © 2011-2017 Christoph M. Becker</p>
<p>
  Uploader_XH is powered by <a target="_blank"
  href="http://www.plupload.com/">Plupload</a>.
</p>
<p class="uploader_license">
  Uploader_XH is free software: you can redistribute it and/or modify it under
  the terms of the GNU General Public License as published by the Free
  Software Foundation, either version 3 of the License, or (at your option)
  any later version.
</p>
<p class="uploader_license">
  Uploader_XH is distributed in the hope that it will be useful, but
  <em>without any warranty</em>; without even the implied warranty of
  <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
  the GNU General Public License for more details.
</p>
<p class="uploader_license">
  You should have received a copy of the GNU General Public License along with
  Uploader_XH. If not, see <a target="_blank"
  href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
<div class="uploader_syscheck">
  <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
  <p class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
