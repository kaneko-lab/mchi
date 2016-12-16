var previousRequestMessageId = -1;
$(document).ready(function(e) {
	$.ajaxSetup({ cache: false });
	documentHeight=$(document).height();
	chatAreaHeight=documentHeight-160+"px";
	$("html").keydown(function(key){
		if(key.keyCode===13){
		doSubmit();
		return false;
		}
	});
	$("#chatButton").click(function(ev){
		doSubmit();
		return false;
	});

	$("#msgInput").submit(function(){
		return false;
		});

	$("#chatArea").css('height',chatAreaHeight);
	start();
});
function doSubmit(){
		inputText = $("#msgInputArea").val();
		if(inputText.length<1){
		alert("Input messgage");
		}else{
		//Message input
		addUserMsg(inputText);			
		}
		$('input,textarea').not('input[type="radio"],input[type="checkbox"],:hidden,:button, :submit,:reset').val('');
}


function start(){
	getLatestMsg();
}
function getLatestMsg(){

	latestMsgUrl="/Messages/get/"
	+currentLatestMsgId;


	if(previousRequestMessageId != currentLatestMsgId) {
		previousRequestMessageId = currentLatestMsgId;
		$.getJSON(latestMsgUrl, function (json) {
			updateChatAreaMsg(json);
		});
	}
	timerID = setTimeout("getLatestMsg()",1000);
}

function updateChatAreaMsg(msgs){
	if(msgs.length < 1)return;
	for( i in msgs){
		customizedMsg = getCustomizedMessage(msgs[i]);
		originalMsg = getOriginalMessage(msgs[i]);
		//if(msgs[i].Message.user_id==currentUserId){
		//	customizedMsg=getCustomizedOthersMsg(msgs[i] );
		//}else{
		//	customizedMsg=getCustomizedOthersMsg(msgs[i]);
		////翻訳追加
		//}
		$('#chatArea').append(customizedMsg);
		$('#originalChatArea').append(originalMsg);
		chatAreaScroll();
	}
	currentLatestMsgId=msgs[i].Message.id;
	previousRequestMessageId = -1;
}

function chatAreaScroll(){
	var pageBottom=99999999999;
	$('#chatArea').scrollTop(pageBottom);
}

function addUserMsg(msg){
	//メッセージ登録
	//var jsData = $.ajax({
	//url :"/Messages/add?msg="+encodeURI(msg)+"&user_id="+currentUserId+"&lang="+currentUserLang
	//}).responseText;
	
	url = "/Messages/add?msg="+encodeURI(msg)+"&user_id="+currentUserId+"&lang="+currentUserLang;
	//respond=(eval("(" + jsData + ")"));
	
	$.getJSON(url,function(respond){
			if(respond.result=="success"){
				previousRequestMessageId = -1;
				getLatestMsg();
			}else{
			alert("msg failed");
			}
	});

	
	//結果取得
	
};
/**
 * Previous version
 * @param msg
 * @returns {string}
 */
function getCustomizedOthersMsg(msg){
	timeWait=0;
	getTranslatedMsg(msg.Message.content,msg.Message.id,msg.Message.lang);
	//getParsedMsg(msg.Message.content,msg.Message.id,msg.Message.lang);
	imageDivId="msgCategory"+msg.Message.id;
	asyncUpdateCategory(msg.Message.id,imageDivId,timeWait);

	return		"<div id='msgArea"+msg.Message.id+"' class=\"othersMsgArea\">"+
			    "<div class=\"othersIconArea\">"+
				"<img src=\"/img/thumb_60_60.jpg\"></div>"+
				"<div class=\"ui-corner-all othersMsgBox\">"+
				"<div class=\"subInfoArea\">"+
				"<div class=\"nickName\">"+msg.User.nickname+"</div>"+
				"<div id='"+imageDivId+"' class=\"msgCategory\" >Loading...</div>"+
				"</div>"+
				"<div id='msg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
				"</div>"+
				"</div>";

}

function asyncUpdateCategory(id,divId,timeWait){
	getCategoryUrl="/Categorizations/jsonGetCategory/"+id+"/"+currentUserLang+"/"+timeWait;
	$.getJSON(getCategoryUrl, function(json){
		$("#"+divId).text("["+json.category1+"] ["+json.category2+"]["+json.category3+"]");
	 });

}

function getCustomizedUserMsg(msg){
	timeWait=1;//1sec
	
	//自分の発言の下に分析結果を表示
	//getParsedMsg(msg.Message.content,msg.Message.id,msg.Message.lang);
	imageDivId="msgCategory"+msg.Message.id;
	asyncUpdateCategory(msg.Message.id,imageDivId,timeWait);
	
	return		"<HR size ='10'><div id='msgArea"+msg.Message.id+"' class=\"userMsgArea\">"+
			    "<div class=\"userIconArea\">"+
				"<img src=\"/img/thumb_60_60.jpg\"></div>"+
				"<div class=\"ui-corner-all userMsgBox\">"+
				"<div class=\"subInfoArea\">"+
				"<div class=\"nickName\">"+msg.User.nickname+"</div>"+
				"<div id='"+imageDivId+"' class=\"msgCategory\" >Loading..</div>"+
				"</div>"+
				"<div id='msg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
				"</div>"+
				"</div>";
}
function getTranslatedMsg(msg,msgId,srcLang){
	if(currentUserLang==srcLang){
		return;
	}
	url='/Messages/getTranslated?msg='+encodeURI(msg)+'&src_lang='+srcLang+'&tgt_lang='+currentUserLang+'&msg_id='+msgId;
	$.getJSON(url,function(json){
			$('#msg'+msgId).html(json.data.translations[0].translatedText);
	});
}
function getParsedMsg(msg,msgId,srcLang){
	url='/Messages/getParsed/'+srcLang+'?msg='+encodeURI(msg);
	$.getJSON(url,function(json){
	custom="<div class='parseArea'>";
		for( i in json){
			custom+="["+json[i]+"] ";
		}
	custom+="</div>";
	if(json.length>0)$('#msg'+msgId).append(custom);

		});
	}


function getCustomizedMessage(msg){
	timeWait=0;
	getTranslatedMsg(msg.Message.content,msg.Message.id,msg.Message.lang);
	//getParsedMsg(msg.Message.content,msg.Message.id,msg.Message.lang);
	imageDivId="msgImage"+msg.Message.id;

	return "<HR size = 1 color='white'> <div id='msgArea"+msg.Message.id+"' class=\"othersMsgArea\">"+
		"<div class=\"othersIconArea\">"+
		"<img src=\"/img/flags/"+msg.Message.lang+".png\" width=60></div>"+
		"<div class=\"ui-corner-all othersMsgBox\">"+
		"<div class=\"subInfoArea\">"+
		"<div class=\"nickName\">"+msg.User.nickname+"</div>"+
		"</div>"+
		"<div id='msg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
		"<div id='"+imageDivId+"' class=\"msgCategory\" ><img src='abc'><img src='abc'><img src='abc'></div>"+
		"<a href='#rightpanel3' ><img src='abc'></a>"+
		"</div>"+
		"</div>";
}

function getOriginalMessage(msg){
	return "<HR size = 1 color='white'><div id='msgArea"+msg.Message.id+"' class=\"othersMsgArea\">"+
		"<div class=\"othersIconArea\">"+
		"<img src=\"/img/flags/"+msg.Message.lang+".png\" width=45></div>"+
		"<div class=\"ui-corner-all originalMsgBox\">"+
		"<div class=\"originalMsgSubInfoArea\">"+
		"<div class=\"originalMsgBoxNickname\">"+msg.User.nickname+"</div>"+
		"</div>"+
		"<div id='orgMsg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
		"</div>"+
		"</div>";
}