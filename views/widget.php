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

    <script type="text/javascript">
    var uploader = new plupload.Uploader({
	    runtimes: 'html5,silverlight,html4',
		browse_button: "pickfiles",
		container: "container",
	    url: '<?php echo CMSIMPLE_ROOT?>?function=uploader_upload&uploader_type=<?php echo $this->type?>&uploader_subdir=<?php echo urlencode($this->subdir)?>',
	    max_file_size: '<?php echo $this->config['size_max']?>',
<?php if (!empty($this->config['size_chunk'])):?>
	    chunk_size: '<?php echo $this->config['size_chunk']?>',
<?php endif?>
<?php if (isset($this->width, $this->height, $this->quality)):?>
	    resize: {
		width: <?php echo $this->width?>,
		height: <?php echo $this->height?>,
		quality: <?php echo $this->quality, "\n"?>
	    },
<?php elseif ($this->resize != ''):?>
	    resize: {
		width: <?php echo $this->config['resize-' . $this->resize . '_width']?>,
		height: <?php echo $this->config['resize-' . $this->resize . '_height']?>,
		quality: <?php echo $this->config['resize-' . $this->resize . '_quality'], "\n"?>
	    },
<?php endif?>
	    filters: [{
		title: '<?php echo $this->l10n['title_' . $this->type]?>',
		extensions: '<?php echo $this->config['ext_' . $this->type]?>'
	    }],
	    flash_swf_url: '<?php echo $this->libFolder?>Moxie.swf',
	    silverlight_xap_url: '<?php echo $this->libFolder?>Moxie.xap',
	    file_data_name: "uploader_file",
		init: {
			PostInit: function() {
				document.getElementById('filelist').innerHTML = '';
	 
				document.getElementById('uploadfiles').onclick = function() {
					uploader.start();
					return false;
				};
			},
			FilesAdded: function(up, files) {
				plupload.each(files, function(file) {
					document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				});
			},
			UploadProgress: function(up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			},
	 
			Error: function(up, err) {
				document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			}
		}
	});
	uploader.init();
    </script>
</body>
</html>
