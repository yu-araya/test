/*
 * ピットタッチ・プロ サンプルコンテンツ
 * AJAX ライブラリ
 * 
 * COPYRIGHT (C) 2011 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */

// XMLHTTPRequestをラップするクラス
// ピットタッチ・プロで利用されることを目的としているため
// クロスブラウザ対応には、なっていない

PitTouch_AJAX = function()
{
}

PitTouch_AJAX.prototype = function()
{
	// static private variables
	
	var defaultOptions =
	{
		url: location.href,
		type: "GET",
		contentType: "application/x-www-form-urlencoded; charset=UTF-8",
		async: true,
		timeout: 0,
		data: null,
		dataType: null,
		username: null,
		password: null,
		success: null,
		error: null,
		complete: null,
		accepts: {
			xml: "application/xml, text/xml",
			html: "text/html",
			text: "text/plain",
			json: "application/json, text/javascript",
			"*": "*/*"
		}
	};
	
	const rnoContent = /^(?:GET|HEAD)$/

	// Instance variables
	this.optionsValue = {};
	this.timeoutID = -1;
	this.isAbort = false;
	this.xhr = null;

	// private method
	// URLエンコード
	var uriEncode = function(sendData)
	{
		var data = "";
		
		if (typeof sendData === "object")
		{
			var isFirst = true;
			
			for (var i in sendData)
			{
				if (!isFirst)
					data += "&";
				data += i;
				data += "=";
				data += sendData[i];
				
				isFirst = false;
			}
			
		}
		else if (typeof sendData === "string")
		{
			data = sendData;
		}
		
		if ((typeof sendData !== "undefined") && (data != ""))
		{
			// &と=で一旦分解しencode
			var encdata = '';
			var datas = data.split('&');
			
			for (i=0;i<datas.length;i++)
			{
				var dataq = datas[i].split('=');
				if (i != 0)
					encdata += '&';
				encdata += encodeURIComponent(dataq[0]) + '=' + encodeURIComponent(dataq[1]);
			}
		}
		else
		{
			encdata = "";
		}
		
		return encdata;
	};
	
	return {
	
		// リクエスト要求を送信
		sendRequest : function(options)
		{
			var ajaxobj = this;
			this.optionsValue = extend(extend({},defaultOptions), options || {});
			this.xhr = new XMLHttpRequest();
			
			// 通信終了時のコールバック登録
			this.xhr.onreadystatechange = function () 
			{
				var statusMessage = "";
				
				// 通信終了
				if ( ajaxobj.xhr.readyState == 4 )
				{
					// タイムアウトのクリア
					if (ajaxobj.timeoutID != -1)
					{
						clearTimeout(ajaxobj.timeoutID);
						ajaxobj.timeoutID = -1;
					}
					
					if ( ajaxobj.xhr.status >= 200 && ajaxobj.xhr.status < 300 || ajaxobj.xhr.status === 304 )
					{
						// 成功の場合
						if (ajaxobj.optionsValue.success)
							ajaxobj.optionsValue.success(ajaxobj.xhr.responseText);

						statusMessage = "success";
					}
					else
					{
						// 失敗の場合
						statusMessage = ajaxobj.xhr.statusText || "error";
						
						// タイムアウトでabortした場合は、メッセージ入替
						if (ajaxobj.isAbort)
							statusMessage = "abort";
						
						if (ajaxobj.optionsValue.error)
							ajaxobj.optionsValue.error(statusMessage);
					}
					
					// 通信終了通知
					if (ajaxobj.optionsValue.complete)
						ajaxobj.optionsValue.complete(statusMessage);

					ajaxobj.isAbort = false;
				}
			}

			this.optionsValue.type = this.optionsValue.type.toUpperCase();
			this.optionsValue.dataType = this.optionsValue.dataType.toLowerCase();
			this.optionsValue.hasContent = !rnoContent.test( this.optionsValue.type );

			// URLエンコード
			this.optionsValue.data = uriEncode(this.optionsValue.data);
			
			// GETメソッドの場合は、URL以降にクエリを付加
			if (this.optionsValue.type == 'GET')
			{
				this.optionsValue.url += this.optionsValue.data;
			}

			// open メソッド
			if (this.optionsValue.username)
			{
				// ユーザ認証の場合
				this.xhr.open(this.optionsValue.type,this.optionsValue.url,this.optionsValue.async,this.optionsValue.username,this.optionsValue.password);
			}
			else
			{
				this.xhr.open(this.optionsValue.type,this.optionsValue.url,this.optionsValue.async);
			}
			
			// ヘッダセット
			this.xhr.setRequestHeader("Accept",
				this.optionsValue.dataType && this.optionsValue.accepts[ this.optionsValue.dataType ] ?
				this.optionsValue.accepts[ this.optionsValue.dataType ] + ( this.optionsValue.dataType !== "*" ? ", */*; q=0.01" : "" ) :
				this.optionsValue.accepts[ "*" ]);

			this.xhr.setRequestHeader('Content-Type',this.optionsValue.contentType);

//			xhr.setRequestHeader('X-Requested-With',"XMLHttpRequest");
			
			// 送信
			this.xhr.send( ( this.optionsValue.hasContent && this.optionsValue.data ) || null );
			
			// タイムアウトコールバック
			function ajaxTimeout()
			{
				ajaxobj.isAbort = true;
				ajaxobj.xhr.abort();
			};
			
			// タイムアウト開始
			if (this.optionsValue.timeout != 0)
				this.timeoutID = setTimeout(ajaxTimeout,this.optionsValue.timeout);
		},
	}
}();

