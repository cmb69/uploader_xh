<div class="uploader_controls">
    <?=$this->typeSelect()?>
    <?=$this->subdirSelect()?>
    <?=$this->resizeSelect()?>
</div>
<div id="uploader_filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
<div id="uploader_container">
	<button id="uploader_pickfiles">Select files</button>
	<button id="uploader_uploadfiles">Upload files</button>
</div>
<pre id="uploader_console"></pre>

<script type="text/javascript">
	var uploader_config = <?=$this->pluploadConfig()?>;
</script>
