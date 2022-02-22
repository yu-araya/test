/*
 * 社員食堂精算管理システム
 * index トップ画面
 * 非接触ICタイプ用
 *
 * COPYRIGHT (C) 2012 AGILECORE, INC.  ALL RIGHTS RESERVED.
 *
 * @author AGILECORE, INC.
 * @version 1.0
 *
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 */

(function()
{
	window.onload = function(){ready()};

	// static private variables
	// メッセージ表示時間
	const SHOW_MESSAGE_TIME = 10 * 1000;
	// 結果表示タイムアウト
	const SHOW_RESULT_TIMEOUT = 0 * 1000;
	// 結果表示通信エラータイムアウト
	const SHOW_RESULT_SEND_ERR_TIMEOUT = 3 * 1000;
	// 結果表示チェックエラータイムアウト
	const SHOW_RESULT_CHECK_ERR_TIMEOUT = 3 * 1000;

	// 1...確認表示
	const STATUS_CONFIRM_NO_ID	= 1;

	// 2...結果表示
	const STATUS_RESULT	= 2;

	// 3...通信エラー表示
	const STATUS_ERR	= 3;

	// 4...チェックエラー表示
	const CHECK_ERR	= 4;

	var op = new ProOperateWrapper();

	// 表示クリアタイムアウトID
	var displayClearTimeoutID = -1;

	// 消失検知フラグ
	var vanished = false;

	// Ajax終了フラグ
	var returnAjax = false;

	// 社員コード
	var eid = null;

	/**
	 *	初期処理
	 */
	function ready()
	{
		// ネットワーク状態表示
		showNetwork();

		// ProOperateのコールバック登録
		var callback = {};
		callback.onEvent = updateEvent;		// 状態通知登録

		op.startEventListen(callback);

		// 非接触IC開始
		startCom();
	};

	/**
	 *	状態更新
	 */
	function updateEvent(eventCode)
	{
		showNetwork();
	}

	/**
	 *	ネットワーク状態表示
	 */
	function showNetwork()
	{
		var netStat = op.getNetworkStat();

		if (netStat == 0)
			changeCSS("network","background-image","url('./css/image/Networkd.png')");
		else
			changeCSS("network","background-image","url('./css/image/Network.png')");
	}

	/**
	 *	メッセージクリア
	 */
	function clearDisplayMessage()
	{
		changeHTML("message","");
	}

	/**
	 *	非接触IC開始
	 */
	function startCom()
	{
		// 消失フラグと、Ajax終了フラグの初期化
		vanished = false;
		returnAjax = false;
		// FeliCaパラメータオブジェクト
		var felicaParam = {};
		felicaParam.systemCode = "FFFF";	// 読み出し対象のシステムコードを指定する

		var felicaAry = [felicaParam];

		// MIFAREパラメータオブジェクト
		var mifare1k = {};
		mifare1k.type = 1;	// 検出するMIFARE種別。1:standerd 1k

		var mifare4k = {};
		mifare4k.type = 2;	// 検出するMIFARE種別。2:standerd 4k

		var mifareUL = {};
		mifareUL.type = 3;	// 検出するMIFARE種別。3:standerd UL

		var mifareAry = [mifare1k,mifare4k,mifareUL];

		// オプションパラメータ
		var param = {};

		// 成功音と失敗音はデフォルト音
		param.successLamp = "BB0N";						// 成功ランプ
		param.failLamp = "RR0S";						// 失敗ランプ
		param.waitLamp = "WW1L";						// 待ちうけランプ
		param.felica = felicaAry;
		param.mifare = mifareAry;
		param.onetime = true;							// 1度だけ読む
		param.onEvent = onEventCommunication;			// コールバック登録

		try
		{
			var result = op.startCommunication(param);
		}
		catch(e)
		{
			console.error("startCommunication:" + e.name + ":" + e.message);
		}
	}

	/**
	 *	非接触IC通知
	 */
	function onEventCommunication(eventCode, responseObject)
	{
		// 消失
		if (eventCode == 0)
		{
			vanished = true;
		}
		// 検出
		else if (eventCode == 1)
		{
			// メッセージ表示のクリア
			clearDisplayMessage();

			var idm = responseObject.idm;

			// サーバ通信中：モーダル
			var obj = document.getElementById("wait");
			new PitTouch_MODAL().modal(
			{
				message: obj,
				css :
				{
				width:'440px',
				height:'232px',
				top:'20px',
				left:'20px',
				textAlign:'left',
				}
			});

			// ICカードから社員チェック
			checkIcCard(idm);
		}
	}

	/**
	 *	ICカードから社員情報取得
	 */
	function checkIcCard(cid)
	{
		var param = {};
		param.cardkbn = RESERVATION_IC_CARD_CHECK;	// 処理区分
		param.cid = cid;							// カードID

		// サーバ通信
		var ajax = new PitTouch_AJAX();
		ajax.sendRequest(
		{
			url: SERVER_URL,
			data: param,
			type: SERVER_METHOD,
			dataType: "text",
			timeout: SERVER_TIMEOUT,
			success: function(data)
			{
				ajaxSuccess(data);
			},
			error: function(msg)
			{
				ajaxFail("送信に失敗");
				//通信結果の表示
				changeDisplayStatus(STATUS_ERR);
			},
		});
	}

	/**
	 *	サーバ通信成功
	 */
	function ajaxSuccess(data)
	{
		// c.f. BFR-421 サーバ送受信仕様書
		// レスポンスは、key=valueで送られてくる

		// res=XX					結果：2バイト固定 ASCII 16進数字 00：成功
		// snd=XXXX					音声：4バイト固定 ASCII 10進数字
		// lmp=XXXX					ランプ：2バイト固定 ASCII 10進数字、今回は送られない
		// sts=XX					ステータス変更：2バイト固定 ASCII 16進数字、今回は送られない
		// fnc=XX					機能実行：2バイト固定 ASCII 16進数字、今回は送られない

		// レスポンスをパースし、連想配列へ
		var ary = getArrayFromResponse(data);

		// ICカード判定
		if (ary.dsp != '') {
			// 音声
			// snd=XXXX				音声：4バイト固定 ASCII 10進数字
			// XXXX+「.wav」のファイルを再生
			// 指定された音声ファイルパスを取得
			var soundPath = "./sound/" + ary.snd + ".wav";

			var soundparam = {};
			soundparam.filePath = soundPath;
			soundparam.loop = false;

			// 指定された音声を再生
			op.playSound(soundparam);

			// メッセージ表示
			changeHTML("message",ary.msg);

			if (displayClearTimeoutID != -1)
				clearTimeout(displayClearTimeoutID);
			displayClearTimeoutID = setTimeout(function(){clearDisplayMessage();displayClearTimeoutID = -1;},SHOW_MESSAGE_TIME);

			// サーバ通信中：モーダル解除
//			new PitTouch_MODAL().unmodal();
			returnAjax = true;

			var obj = JSON.parse(ary.dsp);

			// 通信結果の表示
			eid = obj.employee_id;
			changeDisplayStatus(STATUS_RESULT);
		} else {
			// 通信結果の表示
			changeDisplayStatus(CHECK_ERR);
			//エラー音出力
			errVoice();
		}
	}

	/**
	 *	画面：状態の変更
	 */
	function changeDisplayStatus(newStatus)
	{
		displayStatus = newStatus;

		// 画面更新
		updateMessage();
	}

	/**
	 *	画面：更新
	 */
	function updateMessage()
	{
		if (displayStatus == STATUS_CONFIRM_NO_ID)
		{
			// 1...確認画面
			changeCSS("thanks","display","none");
			changeCSS("touch","display","");
		}
		else if (displayStatus == STATUS_RESULT)
		{
			// 2...結果表示
			var empid = eid;
			location.href = 'reservation.html?eid=' + empid;
		}
		else if (displayStatus == STATUS_ERR)
		{
			// 3...通信エラー
			confirmDialogUnder2("alertmessage", MSG_ERROR_01);
			// 指定時間経過後トップ画面に戻る
			setTimeout(function()
			{
				location.href = 'index.html';
			}, SHOW_RESULT_SEND_ERR_TIMEOUT);
		}
		else if (displayStatus == CHECK_ERR)
		{
			// 4...チェックエラー
			confirmDialogUnder3("alertmessage", MSG_ERROR_03);
			// 指定時間経過後トップ画面に戻る
			setTimeout(function()
			{
				location.href = 'index.html';
			}, SHOW_RESULT_CHECK_ERR_TIMEOUT);
		}
	}

	/**
	 *	サーバ通信失敗
	 */
	function ajaxFail(msg)
	{
		// サーバ通信中：モーダル解除
		new PitTouch_MODAL().unmodal();

		returnAjax = true;
		// 非接触IC通信再開
		if(vanished && returnAjax)
		{
			startCom();
		}
	}

	/**
	 *	errVoice
	 */
	function errVoice(){
		// 指定された音声ファイルパスを取得
		var soundPath = "./sound/pipipipi.wav";

		var soundparam = {};
		soundparam.filePath = soundPath;
		soundparam.loop = false;

		op.playSound(soundparam);
	}

})();
