<div class="uploader_widget" data-config="<?=$this->pluploadConfig()?>">
	<div class="uploader_controls">
<?php if (!empty($this->typeOptions)):?>
		<select class="uploader_type" title="<?=$this->text('label_type')?>" data-url="<?=$this->typeSelectChangeUrl()?>">
<?php 	foreach ($this->typeOptions as $type => $selected):?>
			<option value="<?=$this->escape($type)?>" <?=$this->escape($selected)?>><?=$this->escape($type)?></option>
<?php 	endforeach?>
		</select>
<?php endif?>
<?php if (!empty($this->subdirOptions)):?>
		<select class="uploader_subdir" title="<?=$this->text('label_subdir')?>" data-url="<?=$this->subdirSelectChangeUrl()?>">
<?php 	foreach ($this->subdirOptions as $subdir => $selected):?>
			<option value="<?=$this->escape($subdir)?>" <?=$this->escape($selected)?>><?=$this->escape($subdir)?></option>
<?php	endforeach?>
		</select>
<?php endif?>
<?php if (!empty($this->resizeOptions)):?>
		<select class="uploader_resize" title="<?=$this->text('label_resize')?>" data-url="<?=$this->resizeSelectChangeUrl()?>">
<?php 	foreach ($this->resizeOptions as $size => $selected):?>
			<option value="<?=$this->escape($size)?>" <?=$this->escape($selected)?>><?=$this->escape($size)?></option>
<?php 	endforeach?>
		</select>
<?php endif?>
	</div>
	<div class="uploader_filelist"><?=$this->text('message_no_support')?></div>
	<div class="uploader_buttons">
		<button class="uploader_pickfiles"><?=$this->text('label_select_files')?></button>
		<button class="uploader_uploadfiles"><?=$this->text('label_upload_files')?></button>
	</div>
	<pre class="uploader_console"></pre>
</div>
