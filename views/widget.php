<div class="uploader_controls">
    <?=$this->typeSelect()?>
    <?=$this->subdirSelect()?>
    <?=$this->resizeSelect()?>
</div>
<div id="uploader_filelist"><?=$this->text('message_no_support')?></div>
<div id="uploader_container">
	<button id="uploader_pickfiles"><?=$this->text('label_select_files')?></button>
	<button id="uploader_uploadfiles"><?=$this->text('label_upload_files')?></button>
</div>
<pre id="uploader_console"></pre>

<script type="text/javascript">
	var uploader_config = <?=$this->pluploadConfig()?>;
</script>
