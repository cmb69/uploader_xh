<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>title</title>
    <script type="text/javascript" src="<?php echo $this->libFolder?>plupload.full.min.js"></script>
</head>
<body>
	<div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
	<div id="container">
		<button id="pickfiles">Select files</button>
		<button id="uploadfiles">Upload files</button>
	</div>
	<pre id="console"></pre>

	<script type="text/javascript" src="<?=$this->libFolder?>../uploader.min.js"></script>
    <script type="text/javascript">
	(function () {
		var config = <?=$this->getJsonConfig()?>;
		config.init = uploader.init;
		new plupload.Uploader(config).init();
	}());
    </script>
</body>
</html>
