<div class="uploader_controls">
    <?=$this->typeSelect()?>
    <?=$this->subdirSelect()?>
    <?=$this->resizeSelect()?>
</div>
<div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
<div id="container">
	<button id="pickfiles">Select files</button>
	<button id="uploadfiles">Upload files</button>
</div>
<pre id="console"></pre>

<script type="text/javascript">
	var uploader_config = <?=$this->pluploadConfig()?>;
</script>
