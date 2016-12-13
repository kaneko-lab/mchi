  $.getJSONex = function(url, data, success, error){
  var JSON_SUCCESS = false;
  var timerID;

  // dataが省略された場合はパラメータをシフトする。
  if ($.isFunction(data)) {
      error = success;
      success = data;
      data = null;
  }

  // タイマーイベントで実行される関数
  var manager = function(){
    clearTimeout(timerID);
    if (!JSON_SUCCESS) {
      $("head script").each(function(){
        var status;
        var src = $(this).attr("src");
        if (src != undefined) {
          status = src.indexOf(url.substring(0, url.length-1), 0);
          if (status != -1){
            // 失敗したscriptタグを削除する。
            $(this).remove();
          }
        }
      });
      // error関数をコールする。
      error();
    }
  };

  // 3秒以内にコールバック関数が実行されなければエラーとなる。
  timerID = setTimeout(manager, 3*1000);

  $.getJSON(url, data, function(json){
    JSON_SUCCESS = true;
    // 呼び出しが成功した場合はsuccess関数をコールする。
    success(json);
  });
}

$(function(){
  var serverURL = "http://localhost/test.asp?&callback=?";
  // エラーの場合に実行する関数を指定する。
  $.getJSONex(serverURL, function(json){
      $(div).append("<p>データの取得に成功しました。</p>");
  }, function(){
      $(div).append("<p>データの取得に失敗しました。</p>");
  });
});
