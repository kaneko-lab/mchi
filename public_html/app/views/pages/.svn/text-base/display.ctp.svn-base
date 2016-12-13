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
<div data-role="page" data-theme="a">
        <div data-role="header" >
		<h1 style="margin:0;padding:5px;">多言語チャットシステム</h1>
		</div>
        
		<div data-role="content" data-theme="c">
			<div id="chatArea">
			</div><!--End of chatArea -->
		</div><!--End of Content-->
		
		<div class="msgInputForm" ><input id="msgInputArea" data-theme="c">
			<div class="ui-grid-a">
			<div class="ui-block-a" id="chatButton"><button type="submit" >CHAT</button></div>
			<div class="ui-block-b"><a data-ajax="false" href="/Users/logout" type="button" >ログアウト</a></div>
			</div>
		</div>
</div>
