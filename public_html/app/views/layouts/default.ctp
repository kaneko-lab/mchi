<!DOCTYPE html>
<html>
<head>
<title>多言語チャットシステム</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0;" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<?php
		echo $scripts_for_layout;
	    echo $html->script(array('jquery-1.6.4.min.js','common','jquery.mobile-1.0'));
        echo $html->css(array('jquery.mobile-1.0','common'));
		echo "\r\n";
?>
</head>
<body onunload="">
<?php echo $content_for_layout; ?>
</body>
</html>
