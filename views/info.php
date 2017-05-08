<!-- Uploader_XH: info -->
<h1>Uploader &ndash; <?php echo self::l10n('menu_info');?></h1>
<img src="<?php echo self::logoPath();?>" width="128" height="128" alt="plugin
logo" style="float: left; margin: 10px 10px 10px 0"/>
<p>Version: <?php echo UPLOADER_VERSION;?></p>
<p>Copyright &copy; 2011-2017 <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p>Uploader_XH is powered by <a href="http://www.plupload.com/">Plupload</a>.</p>
<p style="text-align: justify">This program is free software: you can
redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.</p>
<p style="text-align: justify">This program is distributed in the hope that it
will be useful, but <em>without any warranty</em>; without even the implied warranty of
<em>merchantability</em> or <em>fitness for a particular purpose</em>. See the GNU General
Public License for more details.</p>
<p style="text-align: justify">You should have received a copy of the GNU
General Public License along with this program. If not, see
<a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>
<div style="clear: both"></div>
<h4><?php echo self::l10n('syscheck_title');?></h4>
<ul style="list-style: none">
<?php foreach (self::systemChecks() as $check => $state):?>
    <li>
        <img src="<?php echo self::stateIconPath($state);?>" alt="<?php echo $state;?>"
             style="margin: 0; height: 1em; padding-right: 1em"/>
        <span><?php echo $check;?></span>
    </li>
<?php endforeach;?>
</ul>
