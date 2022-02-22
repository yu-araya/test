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
	// 再送間隔
	const RESEND_INTERVAL = 1 * 1000;
	// 再送件数/再送検知間隔
	const RESEND_DETECT_INTERVAL = 60 * 1000;
	// 結果表示タイムアウト
	const SHOW_RESULT_TIMEOUT = 2 * 1000;
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
	var database = null;
	var division = -1;

	// 消失検知フラグ
	var vanished = false;

	// Ajax終了フラグ
	var returnAjax = false;

	/**
	 *	初期処理
	 */
	function ready()
	{
		// データベースの初期化
		initDatabase();

		// ネットワーク状態表示
		showNetwork();

		// イベント作成
		makeEvent();

		// 設定取得
		getSetting();

		// ProOperateのコールバック登録
		var callback = {};
		callback.onEvent = updateEvent;		// 状態通知登録

		op.startEventListen(callback);
	};

	/**
	 * 機器情報取得後の処理
	 */
	function readyAfter()
	{
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
	function onEventCommunication(eventCode,responseObject)
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

			/*
			*未送信としてログをDBに作成、
			*ログが作成できたらAjax通信を開始、
			*未送信ログが上限に達していたらAjax通信は行わない
			*/

			// 有効カードIDを取得
			database.readTransaction(function (tx)
			{
				var sql = 'select * from ' + IC_CARD_LICENSE_TABLE_NAME + ' where upper(cardID) = "' + idm.toUpperCase() +'"and foodDivision ="' + document.getElementById("id_fno").value + '"';
				
				tx.executeSql(sql,
				null,
				function (tx,rs)// 成功
				{
					// 該当行がない場合
					if (rs.rows.length == 0)
					{
						changeDisplayStatus(CHECK_ERR);
						//エラー音出力
						errVoice();
					}
					// 有効カードIDが存在する場合
					else
					{
						changeCSS("thanks","display","");
						changeCSS("label_fno","display","none");
						changeCSS("touch","display","none");

						// 指定時間経過後トップ画面に戻る
						setTimeout(function()
						{
							location.href = 'index.html';
						}, SHOW_RESULT_TIMEOUT);
					}
				}
				,function (tx,e)// 失敗
				{
					changeDisplayStatus(CHECK_ERR);
					//エラー音出力
					errVoice();
				});
			});
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
			changeCSS("thanks","display","");
			changeCSS("touch","display","none");

			// 指定時間経過後トップ画面に戻る
			setTimeout(function()
			{
				location.href = 'index.html';
			}, SHOW_RESULT_TIMEOUT);
		}
		else if (displayStatus == STATUS_ERR)
		{
			// 3...通信エラー
			confirmDialogUnder("requesterr");
			// 指定時間経過後トップ画面に戻る
			setTimeout(function()
			{
				location.href = 'index.html';
			}, SHOW_RESULT_SEND_ERR_TIMEOUT);
		}
		else if (displayStatus == CHECK_ERR)
		{
			// 4...チェックエラー
			confirmDialogUnder("checkerr");
			// 指定時間経過後トップ画面に戻る
			setTimeout(function()
			{
				location.href = 'index.html';
			}, SHOW_RESULT_CHECK_ERR_TIMEOUT);
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

	/**
	 *	DBから設定を取得
	 */
	function getSetting()
	{
		if (database == null) {
			// DB作成中の為、一定時間後に再読み込み
			changeCSS("touch","display","none");
			changeCSS("label_fno", "font-size", "36px");
			document.getElementById("label_fno").innerHTML = "初期処理中です<br>しばらくお待ちください";
			setTimeout(function() {
				location.href = 'index.html';
			}, 5 * 1000);
			return;
		}

		database.readTransaction(function (tx)
		{
			tx.executeSql('select * from ' + SETTING_TABLE_NAME, null,
			function (tx,rs)
			{
				if (rs.rows.length == 0)
				{
					// データがない場合
					changeCSS("touch","display","none");
					changeCSS("label_fno", "font-size", "36px");
					document.getElementById("label_fno").innerHTML = "設定画面で定食を<br>設定してください";
				}
				else
				{
					// データがある場合
					var idivision = rs.rows.item(0).idivision;
					var fno = rs.rows.item(0).fno;

					document.getElementById("id_idivision").value = idivision;
					document.getElementById("id_fno").value = fno;

					var fnoName = "";
					for (var i = 0; i < FNO_LIST.length; i++) {
						if (FNO_LIST[i][0] == fno) {
							fnoName = FNO_LIST[i][1];
							break;
						}
					}

					document.getElementById("label_fno").innerHTML = fnoName;

					readyAfter();
				}
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
			});
		});
	}

	/**
	 * イベント作成
	 */
	function makeEvent()
	{
		// 設定ボタン
		document.getElementById("setting").addEventListener("mousedown", function(e) {
			btnSetting();
		});
	}

	/**
	 * イベント削除
	 */
	function removeEvent()
	{
		document.getElementById("setting").removeEventListener("mousedown", function(e) {
			btnSetting();
		});
	}
	/**
	 * 設定ボタン押下
	 */
	function btnSetting()
	{
		location.href = 'setting.html';
	}

	/**
	 *	DB起動
	 */
	function initDatabase()
	{
		database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

		if (database != null) {
			var sql1 = 'create table if not exists ' + IC_CARD_LICENSE_TABLE_NAME + '('
				+ 'cardID text not null,'
				+ 'foodDivision text not null'
				+ ')';

			var sql2 = 'create table if not exists ' + T_IC_CARD_LICENSE_TABLE_NAME + '('
				+ 'cardID text not null,'
				+ 'foodDivision text not null'
				+ ')';

			var sql3 = 'create table if not exists ' + SETTING_TABLE_NAME + '('
				+ 'idivision integer not null,'
				+ 'fno integer not null'
				+ ')';

			database.transaction(function (tx)
			{
				// create
				executeSql(tx, sql1);
				executeSql(tx, sql2);
				executeSql(tx, sql3);
			});
		}
	}

	function executeSql(tx, sql)
	{
		tx.executeSql(sql,
		null,
		function (t,rs)
		{
			return true;
		},
		function (t,e)
		{
			console.error('エラー' + e.message);
			return false;
		});
	}

})();
