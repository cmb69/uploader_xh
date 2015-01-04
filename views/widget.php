<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>title</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->libFolder?>jquery.plupload.queue/css/jquery.plupload.queue.css">
    <script type="text/javascript" src="<?php echo $this->libFolder?>jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->libFolder?>plupload.full.min.js"></script>
<?php if (file_exists($this->languageFile)):?>
    <script type="text/javascript" src="<?php echo $this->languageFile?>"></script>
<?php endif?>
    <script type="text/javascript" src="<?php echo $this->libFolder?>jquery.plupload.queue/jquery.plupload.queue.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
    jQuery(function() {
	jQuery("#uploader").pluploadQueue({
	    runtimes: '<?php echo $this->config['runtimes']?>',
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
	    rename: true,
	    multiple_queues: true,
	    dragdrop: true,
	    file_data_name: "uploader_file"
	});
    });
    /* ]]> */
    </script>
</head>
<body>
    <form method="POST" action="#">
	<div id="uploader">
	    <img src="<?php echo $this->imageFolder?>loading.gif" alt="loading &hellip;" style="display:none">
	    <script type="text/javascript">
		jQuery('#uploader img').show()
	    </script>
	    <noscript><?php echo $this->l10n['message_no_js']?></noscript>
	</div>
    </form>
</body>
</html>
