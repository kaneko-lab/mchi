<?php
$script= "
 var currentUserId={$currentUserId};
 var userNickName='{$nickName}';
 var lastLogin='{$lastLogin}';
 var currentUserLang='{$userLang}';
 var currentLatestMsgId='{$currentLatestMsgId}';
";
	$this->javascript->codeBlock($script, array('inline' =>false));

?>

<div data-role="panel" data-id="menu" data-hash="crumbs" data-context="a#default">
	<!-- Start of first page -->
	<div data-role="page" id="main" data-hash="false" data-theme="b">

		<div data-role="header" data-theme="b" >
			<h1 style="margin-left:0; margin-right:0;">Original Messages</h1>
		</div><!-- /header -->

		<div data-role="content">

			<div id="originalChatArea"></div><!--End of chatArea -->

		</div><!-- /content -->

		<div data-role="footer" data-position="fixed" data-theme="b" >
			<h2>Kaneko Lab @ TUAT All rights reserved</h2>
		</div><!-- /footer -->
	</div><!-- /page -->





</div><!-- panel menu -->

<div data-role="panel" data-id="main">
	<div data-role="page" id="ChattingPage">

		<div data-role="header">
			<h1>Multilingual Chat System with Help by Images</h1>
			<a data-ajax="false" href="/Users/logout" type="button" class="ui-btn-right" >ログアウト</a>


		</div><!-- /header -->

		<div data-role="content" data-theme="c">
			<div id="chatArea"></div><!--End of chatArea -->
		</div><!--End of Content-->


		<div data-role="footer" data-position="fixed" >
			 <!--style="position:fixed;bottom:0;"-->
			<div id="chatButton" style="margin-right:130px"><button type="submit" class="ui-btn-right">CHAT</button></div>
			<div class="msgInputForm" >
				<input id="msgInputArea" data-theme="c">
			</div>
		</div><!-- /footer -->
		<!--Right Panel-->
		<div data-role="panel" id="imageDetails0" data-position="right" data-display="overlay" data-theme="b">

		</div>

		<div data-role="panel" id="imageDetails2" data-position="right" data-display="overlay" data-theme="b">

		</div>

		<div data-role="panel" id="imageDetails1" data-position="right" data-display="overlay" data-theme="b">

		</div>

	</div><!-- /page -->

</div><!-- /page -->

</div><!-- panel main -->