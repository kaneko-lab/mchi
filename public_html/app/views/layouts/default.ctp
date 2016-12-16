<!DOCTYPE html>
<html>
<head>
<title>多言語チャットシステム</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=1;" name="viewport" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<?php

echo $html->css(
	array(
		'common',
		'jquery.mobile.splitview',
		'jquery.mobile.structure-1.3.2.min',
		'jquery.mobile.theme-1.3.2.min',
		'jquery.mobile.grids.collapsible'
		));

		echo $scripts_for_layout;
		echo $html->script(
			array(
					"jquery-1.12.4.min",
					"jquery.mobile.splitview",
					"jquery.mobile-1.3.2.min",
					"chatting"
				)
		);
echo "\r\n";
?>
</head>
<body onunload="">
<?php echo $content_for_layout; ?>
</body>
</html>
