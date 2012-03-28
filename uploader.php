<?php

session_start();

if (!isset($_SESSION['uploader'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$uploader =& $_SESSION['uploader'];

$uploader['type'] = isset($_GET['type']) && isset($uploader['folder'][$_GET['type']])
	? $_GET['type'] : 'images';
$uploader['subdir'] = isset($_GET['subdir']) && file_exists($uploader['folder'][$uploader['type']].$_GET['subdir'])
	? $_GET['subdir'] : '';
define('RESIZE',
	isset($_GET['resize']) && in_array($_GET['resize'], array('small', 'medium', 'large'))
	? $_GET['resize'] : '');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>title</title>
    <link rel="stylesheet" type="text/css" href="lib/jquery.plupload.queue/css/jquery.plupload.queue.css">
    <script type="text/javascript" src="lib/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> <!-- TODO: integrate -->
    <script type="text/javascript" src="lib/plupload.full.js"></script>
<?php if (file_exists('lib/i18n/'.$uploader['lang'].'.js')) {?>
    <script type="text/javascript" src="lib/i18n/<?php echo $uploader['lang']?>.js"></script>
<?php }?>
    <script type="text/javascript" src="lib/jquery.plupload.queue/jquery.plupload.queue.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
    jQuery(function() {
	jQuery("#uploader").pluploadQueue({
	    runtimes : '<?php echo $uploader['runtimes']?>',
	    url : 'lib/upload.php',
	    max_file_size : '<?php echo $uploader['max_size']?>',
	    <?php echo $uploader['chunking']?>
<?php if (RESIZE != '') {?>
	    resize : {
		width : <?php echo $uploader['resize'][RESIZE]['width']?>,
		height : <?php echo $uploader['resize'][RESIZE]['height']?>,
		quality : <?php echo $uploader['resize'][RESIZE]['quality'], "\n"?>
	    },
<?php }?>
	    filters : [{
		title : '<?php echo $uploader['title'][$uploader['type']]?>',
		extensions : '<?php echo $uploader['exts'][$uploader['type']]?>'
	    }],
	    flash_swf_url : 'lib/plupload.flash.swf',
	    silverlight_xap_url : 'lib/plupload.silverlight.xap',
	    rename: true,
	    multiple_queues: true,
	    dragdrop: true
	});
    
	jQuery('form').submit(function(e) {
	    var uploader = jQuery('#uploader').pluploadQueue();
	    if (uploader.files.length > 0) {
		uploader.bind('StateChanged', function() {
		    if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
			jQuery('form')[0].submit();
		    }
		});
		uploader.start();
	    } else {
		alert('You must queue at least one file.');
	    }
	    return false;
	});
    });
    /* ]]> */
    </script>
</head>
<body>
    <form method="POST" action="#">
	<div id="uploader">
	    <img src="images/loading.gif" alt="loading …">
	</div>
    </form>
</body>
</html>
