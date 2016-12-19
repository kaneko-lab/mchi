var presentedMessages = {};
var translateInfo = {};
$(document).ready(function(e) {
	$("#msgInputArea").on('keydown',function(ev){
		ev.stopPropagation();
		if(ev.keyCode===13){
			doSubmit();
			return ;
		}
	});


	$("#chatButton").click(function(ev){
		doSubmit();
		return false;
	});
	$.ajaxSetup({ cache: false });
	documentHeight=$(document).height();
	chatAreaHeight=documentHeight-160+"px";



	$("#msgInput").submit(function(){
		return false;
	});

	$("#chatArea").css('height',chatAreaHeight);
	$( ".photopopup" ).on({
		popupbeforeposition: function() {
			var maxHeight = $( window ).height() - 60 + "px";
			$( ".photopopup img" ).css( "max-height", maxHeight );
		}
	});
	start();
});



/**
 *
 */
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


/**
 *
 */
function start(){

	presentedMessages = {};
	getLatestMsg();
}


/**
 *
 */
function getLatestMsg(){
	latestMsgUrl="/Messages/get/" +currentLatestMsgId;

	//previousRequestMessageId = currentLatestMsgId;
	$.getJSON(latestMsgUrl, function (json) {
		updateChatArea(json);
	});
	timerID = setTimeout("getLatestMsg()",1000);
}

/**
 *
 * @param msgs
 */
function updateChatArea(msgs){
	if(msgs.length < 1)return;
	for( i in msgs){

		//Todo Check why system update twice.
		if(msgs[i].Message.id in presentedMessages)
			continue;
		presentedMessages[msgs[i].Message.id] = msgs[i].Message.id;
		customizedMsg = getCustomizedMessage(msgs[i]);
		originalMsg = getOriginalMessage(msgs[i]);
		$('#chatArea').append(customizedMsg);
		$('#originalChatArea').append(originalMsg);
		chatAreaScrollToBottom();
	}
	currentLatestMsgId=msgs[i].Message.id;
}

/**
 *
 */
function chatAreaScrollToBottom(){
	var pageBottom=99999999999;
	$('#chatArea').scrollTop(pageBottom);
}

/**
 *
 * @param msg
 */
function addUserMsg(msg){
	url = "/Messages/add?msg="+encodeURIComponent(msg)+"&user_id="+currentUserId+"&lang="+currentUserLang;
	$.getJSON(url,function(respond){
			if(respond.result=="success"){
				//previousRequestMessageId = -1;
				//getLatestMsg();
			}else{
				alert("msg failed");
			}
	});
}



/**
 *
 * @param msgId
 * @param srcLang
 */
function asyncUpdateTranslatedMsgAndHelpers(msgId) {
	if (translateInfo[msgId] != undefined) {
		return;
	}

	url = '/Messages/getTranslatedMessageAndHelpers/' + msgId + '/' + currentUserLang;
	$.getJSON(url, function (json) {
		//Save Message Data.
		translateInfo[msgId] = json.result.data;

		if(translateInfo[msgId].help_data.length > 0){
			//Insert First Image.

			helpImages = "";
			for(i = 0 ; i < translateInfo[msgId].help_data.length ; i ++) {
				aId = "helpImage_" + msgId + "_" + i;
				helpImages += "<a id = '" + aId + "' href='#imageDetails" + i + "'><img width = '60' src='" + translateInfo[msgId].help_data[i][0].image.thumbnailLink + "'></a>";

			}
			$('#msgImage'+msgId).html(helpImages);

			//Create Image Details.
			for(i = 0 ; i < translateInfo[msgId].help_data.length ; i ++){
				aId = "helpImage_"+msgId+"_"+i;

				$('#'+aId).on("click",{value:i},function(event){
					//todo fixed bug for always 0
					$imgDetail = getImagesDetail(msgId,translateInfo[msgId].help_data[event.data.value]);
					$('#imageDetails'+event.data.value).html($imgDetail);
				});
			}

		}


		//Todo check following code. why two type?
		if(translateInfo[msgId].translated_message.translated_message != undefined)
			$('#msg' + msgId).html(translateInfo[msgId].translated_message.translated_message);
		else
			$('#msg' + msgId).html(translateInfo[msgId].translated_message);

	});

}

/**
 *
 * @param msg
 * @returns {string}
 */
function getCustomizedMessage(msg){
	timeWait=0;
	asyncUpdateTranslatedMsgAndHelpers(msg.Message.id);

	imageDivId="msgImage"+msg.Message.id;

	return "<HR size = 1 color='white'> <div id='msgArea"+msg.Message.id+"' class=\"othersMsgArea\">"+
		"<div class=\"othersIconArea\">"+
		"<img src=\"/img/flags/"+msg.Message.lang+".png\" width=60></div>"+
		"<div class=\"ui-corner-all othersMsgBox\">"+
			"<div class=\"subInfoArea\">"+
				"<div class=\"nickName\">"+msg.User.nickname+"</div>"+
			"</div>"+
			"<div id='msg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
			"<div id='"+imageDivId+"' class=\"msg\" >"+"</div>"+
		"</div>";
}

/**
 *
 * @param msg
 * @returns {string}
 */
function getOriginalMessage(msg){
	return "<HR size = 1 color='white'><div id='msgArea"+msg.Message.id+"' class=\"othersMsgArea\">"+
		"<div class=\"othersIconArea\">"+
		"<img src=\"/img/flags/"+msg.Message.lang+".png\" width=45></div>"+
		"<div class=\"ui-corner-all originalMsgBox\">"+
		"<div class=\"originalMsgSubInfoArea\">"+
		"<div class=\"originalMsgBoxNickname\">"+msg.User.nickname+"</div>"+
		"</div>"+ "<div id='orgMsg"+msg.Message.id+"' class=\"msg\" >"+msg.Message.content+"</div>"+
		"</div>"+
		"</div>";
}

/**
 *
 * @param msgId
 * @param images
 * @returns {string}
 */
function getImagesDetail(msgId,images){

	bigSizeImgAreaId = 'bigSigImageArea'+msgId;
	imgHtmls ="<div><div style='width:300px;height=300px;overflow: hidden;'><h2 style='text-align: center'>Enlarged image</h2><br><img style='width:250px!important;height:250px!important;' id='"+bigSizeImgAreaId+"' src="+images[0].image.thumbnailLink+"  ><br><p style='text-align: center'>Other Candidates</p></div>";
	for (i = 0 ; i < images.length ; i ++ ){
		imageData = images[i];
		img = imageData.image;
		imgHtmls+="<div style='float:left;padding: 1px;margin:1px;background-color: #ffffff;text-align:center'>"
		+"<img src = "+img.thumbnailLink+" style='height:50px!important;' onmouseover=\"updateBigSizeImasge('"+bigSizeImgAreaId+"','"+img.thumbnailLink+"');\"  ></div>";

	}
	imgHtmls +="</div>"

	return "<br>"+ imgHtmls;
}

function updateBigSizeImasge(bigSizeAreaId,src){
	document.getElementById(bigSizeAreaId).src = src;
}