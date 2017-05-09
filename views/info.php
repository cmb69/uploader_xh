<h1>Uploader – <?=$this->text('menu_info')?></h1>
<img src="<?=$this->logo()?>" width="128" height="128" alt="plugin
     logo" style="float: left; margin: 10px 10px 10px 0"/>
<p>Version: <?=$this->version()?></p>
<p>Copyright © 2011-2017 Christoph M. Becker</p>
<p>
    Uploader_XH is powered by <a target="_blank"
    href="http://www.plupload.com/">Plupload</a>.
</p>
<p style="text-align: justify">
    Uploader_XH is free software: you can redistribute it and/or modify it under
    the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p style="text-align: justify">
    Uploader_XH is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p style="text-align: justify">
    You should have received a copy of the GNU General Public License along with
    Uploader_XH. If not, see <a target="_blank"
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
<h2><?=$this->text('syscheck_title')?></h2>
<ul style="list-style: none">
<?php foreach ($this->checks as $check => $state):?>
    <li>
        <img src="<?=$this->iconFolder()?><?=$this->escape($state)?>.png" alt="<?=$this->escape($state)?>"
             style="margin: 0; height: 1em; padding-right: 1em"/>
        <span><?=$check?></span>
    </li>
<?php endforeach?>
</ul>
