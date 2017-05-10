<div class="uploader_widget" data-config="<?=$this->pluploadConfig()?>">
	<div class="uploader_controls">
		<?=$this->typeSelect()?>
		<?=$this->subdirSelect()?>
		<?=$this->resizeSelect()?>
	</div>
	<div class="uploader_filelist"><?=$this->text('message_no_support')?></div>
	<div class="uploader_container">
		<button class="uploader_pickfiles"><?=$this->text('label_select_files')?></button>
		<button class="uploader_uploadfiles"><?=$this->text('label_upload_files')?></button>
	</div>
	<pre class="uploader_console"></pre>
</div>
