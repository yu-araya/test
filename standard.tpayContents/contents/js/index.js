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

	// 送信ステータス
	const SENDSTAT_SENT = 0; // 送信済み
	const SENDSTAT_UNSENT = 1; // 未送信

	var op = new ProOperateWrapper();
	var database = null;
	var division = -1;

	// クエリ情報
	var quary = null;
	// 送信クエリ
	var sendQuary = null;
	// 再送信クエリ
	var resendQuary = null;

	// 再送タイムアウトID
	var reSendTimeoutID = -1;

	// 表示クリアタイムアウトID
	var displayClearTimeoutID = -1;

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
		// 未送信データ件数の取得
		getUnsendDataCount();

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
		// 非接触ICを一旦停止
		try
		{
			var result = op.stopCommunication();
		}
		catch(e)
		{
			console.error("stopCommunication:" + e.name + ":" + e.message);
		}
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
				var sql = 'select * from ' + IC_CARD_LICENSE_TABLE_NAME + ' where upper(cardID) = "' + idm.toUpperCase() +'"';

				tx.executeSql(sql,
				null,
				function (tx,rs)// 成功
				{
					// 該当行がない場合
					if (rs.rows.length == 0)
					{
						outputInfoLog("未登録ICカード：" + idm.toUpperCase());
						changeDisplayStatus(CHECK_ERR);
						//エラー音出力
						errVoice();
					}
					// 有効カードIDが存在する場合
					else
					{
						quary = makeQuary(idm, rs.rows.item(0).employeeId, rs.rows.item(0).employeeKbn);
						deleteAndAddLog();
					}
				}
				,function (tx,e)// 失敗
				{
					outputInfoLog("未登録ICカード：" + idm.toUpperCase());
					changeDisplayStatus(CHECK_ERR);
					//エラー音出力
					errVoice();
				});
			});
		}
	}

	/**
	 *	クエリ作成
	 */
	function makeQuary(idm, empId, empKbn)
	{
		var now = new Date();
		var format = new DateFormat("yyyyMMddHHmmss");
		var tid = op.getTerminalID();

		var data = {};
		data.datetime = now.getTime();					// 打刻時刻 DB用

		data.tid = tid;									// 端末ID
		data.cid = idm;									// カードID
		data.empid = empId;								// 社員コード
		data.empkbn = empKbn;							// 社員区分
		data.tim = format.format(now);					// 打刻時刻 YYYYMMDDhhmmss
		data.sts = ("0" + (division + 1)).slice(-2);	// 打刻区分 01-04
		data.typ = "00";								// カードタイプ
		data.sendstat = 0;								// 再送ステータス
		data.idivision = document.getElementById("id_idivision").value;		// 機器区分
		data.fno = document.getElementById("id_fno").value;					// 定食区分
		data.cardkbn = FOOD_REGISTRATION;				// 処理区分

		return data;
	}

	/**
	 *	DBのレコードチェックと作成
	 */
	function deleteAndAddLog()
	{
		// 最大件数をチェックし、最古のレコードを取得
		database.readTransaction(function (tx)
		{
			var sql = 'select * from ' + LOG_TABLE_NAME + ' where (select count(id) from ' + LOG_TABLE_NAME + ' ) >= ' + MAX_LOG + ' and id = (select min(id) FROM ' + LOG_TABLE_NAME + ');';

			tx.executeSql(sql,
			null,
			function (tx,rs)// 成功
			{
				// 該当行がない
				if (rs.rows.length == 0)
				{
					// ログの追加
					addLog();
				}
				// 未送信
				else if (rs.rows.item(0).status == 1)
				{
					// alert：モーダル
					new PitTouch_MODAL().modalDialog(
					{
						elementName : "confirm",
						// 確認ボタン
						yes : function()
						{
							returnAjax = true;
							// 非接触IC再開
							if(vanished && returnAjax)
							{
								startCom();
							}
							return true;
						},
					});

					quary = null;
				}
				// 送信済み
				else
				{
					// 削除へ
					deleteLog(rs.rows.item(0).id);
				}
			}
			,function (tx,e)// 失敗
			{
				console.error('エラー' + e.message);

				// サーバ通信中：モーダル解除
				new PitTouch_MODAL().unmodal();

				returnAjax = true;
				// 非接触IC再開
				if(vanished && returnAjax)
				{
					startCom();
				}
			});
		});
	}

	/**
	 *	DB：レコード削除
	 */
	function deleteLog(id)
	{
		database.transaction(function (tx)
		{
			var sql = 'delete from ' + LOG_TABLE_NAME + ' where id = ?';

			tx.executeSql(sql,
			[id],
			function (tx,rs)
			{
				// 成功
				addLog();

			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
				quary = null;

				returnAjax = true;
				// 非接触IC再開
				if(vanished && returnAjax)
				{
					startCom();
				}
			});
		});
	}

	/**
	 *	DBにレコード作成
	 */
	function addLog()
	{
		database.transaction(function (tx)
		{
			var sql = 'insert into ' + LOG_TABLE_NAME + '(createDate,modifyDate,cardID,employeeId,employeeKbn,idivision,fno,status) values (?,?,?,?,?,?,?,?)';

			tx.executeSql(sql,
			// [quary.datetime,quary.datetime,quary.cid,quary.empid,quary.empkbn,quary.idivision,quary.fno,quary.sendstat],
			// function (tx,rs)
			[quary.datetime,quary.datetime,quary.cid,quary.empid,quary.empkbn,quary.idivision,quary.fno,SENDSTAT_UNSENT],
			function (tx,rs)
			{
				//作成したレコードのid取得
				quary.id = rs.insertId;

				// 送信するクエリ情報
				sendQuary = extend({},quary);
				delete sendQuary.id;

				// ネットワーク状態の取得
				var netStat = op.getNetworkStat();

				if (netStat)
				{
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
					// サーバ通信
					var ajax = new PitTouch_AJAX();
					ajax.sendRequest(
					{
						url: SERVER_URL,
						data: sendQuary,
						type: SERVER_METHOD,
						dataType: "text",
						timeout: SERVER_TIMEOUT,
						success: function(data)
						{
							ajaxSuccess(data);
						},
						error:function(msg)
						{
							ajaxFail("送信に失敗");
							//通信結果の表示
							changeDisplayStatus(STATUS_ERR);
						},
					});
				}
				else
				{
					ajaxFail("offline");
					changeDisplayStatus(STATUS_ERR);
				}
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
				quary = null;

				returnAjax = true;
				// 非接触IC再開
				if(vanished && returnAjax)
				{
					startCom();
				}
			});
		});
	}

	/**
	 *	ログを未送信から送信済みに変換
	 */
	function sendSuccess(targetId)
	{
		// ログに記録、送信状態から未送信状態へ
		database.transaction(function (tx)
		{
			var now = new Date();
			var sql = 'update ' + LOG_TABLE_NAME + ' set modifyDate=?,status=' + SENDSTAT_SENT +' where id=?';

			tx.executeSql(sql,
			[now,targetId],
			function (tx,rs)
			{
				//成功
				quary = null;
			}
			,function (tx,e)
			{
				// 失敗
				quary = null;
			});
		});
	}

	// /**
	//  *	ログを送信済みから未送信に変換
	//  */
	// function sendFail()
	// {
	// 	// ログに記録、送信状態から未送信状態へ
	// 	database.transaction(function (tx)
	// 	{
	// 		var now = new Date();
	// 		var sql = 'update ' + LOG_TABLE_NAME + ' set modifyDate=?,status=1 where id=?';

	// 		tx.executeSql(sql,
	// 		[now,quary.id],
	// 		function (tx,rs)
	// 		{
	// 			//成功
	// 			quary = null;
	// 		}
	// 		,function (tx,e)
	// 		{
	// 			// 失敗
	// 			quary = null;
	// 		});
	// 	});
	// }

	/**
	 *	サーバ通信成功
	 */
	function ajaxSuccess(data)
	{
		// 送信ログを未送信から送信済みに変更
		sendSuccess(quary.id);

		// c.f. BFR-421 サーバ送受信仕様書
		// レスポンスは、key=valueで送られてくる

		// res=XX					結果：2バイト固定 ASCII 16進数字 00：成功
		// snd=XXXX					音声：4バイト固定 ASCII 10進数字
		// lmp=XXXX					ランプ：2バイト固定 ASCII 10進数字、今回は送られない
		// sts=XX					ステータス変更：2バイト固定 ASCII 16進数字、今回は送られない
		// fnc=XX					機能実行：2バイト固定 ASCII 16進数字、今回は送られない

		// レスポンスをパースし、連想配列へ
		var ary = getArrayFromResponse(data);

		// 音声
		// snd=XXXX					音声：4バイト固定 ASCII 10進数字
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

		quary = null;

		// サーバ通信中：モーダル解除
		new PitTouch_MODAL().unmodal();
		returnAjax = true;

		//通信結果の表示
		changeDisplayStatus(STATUS_RESULT);
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
	 *	サーバ通信失敗
	 */
	function ajaxFail(msg)
	{
		//ログを送信済みから未送信に変換
		// sendFail();
		outputErrorLog(msg);

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
	 *	未送信データ件数の取得
	 */
	function getUnsendDataCount()
	{
		database.readTransaction(function (tx)
		{
			var sql = 'select count(*) from ' + LOG_TABLE_NAME + ' where status = 1';

			tx.executeSql(sql,
			null,
			function (tx,rs)
			{
				// 成功
				var row = rs.rows.item(0);
				var count = row["count(*)"];

				changeHTML("senddata","未送信データ：" + count + "件");

				setTimeout(function(){getUnsendDataCount();},RESEND_DETECT_INTERVAL);

				// 再送の処理を起動
				if ((count > 0) && (reSendTimeoutID == -1))
					reSendTimeoutID = setTimeout(function(){reSend();},RESEND_INTERVAL);
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
			});

		});
	}

	/**
	 *	再送処理
	 */
	function reSend()
	{
		database.readTransaction(function (tx)
		{
			tx.executeSql('select * from ' + LOG_TABLE_NAME + ' where status = 1 order by createDate asc limit 1;', null,
			function (tx,rs)
			{
				if (rs.rows.length == 0)
				{
					// 未送信がない場合
					reSendTimeoutID = -1;
					// getUnsendDataCountで、未送信を検知
				}
				else
				{
					// 未送信がある場合
					reSendData(rs.rows.item(0));
				}
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);

				reSendTimeoutID = -1;
			});
		});
	}

	/**
	 *	再送処理
	 */
	function reSendData(item)
	{
		resendQuary = makeReSendQuary(item);

		// サーバ通信
		var ajax = new PitTouch_AJAX();
		ajax.sendRequest(
		{
			url: SERVER_URL,
			data: resendQuary,
			type: SERVER_METHOD,
			dataType: "text",
			timeout: SERVER_TIMEOUT,
			success: function(data)
			{
				var resultAry = getArrayFromResponse(data);
				if (resultAry.snd === '1002') {
					// ログに記録
					updateLog();
				} else {
					resendQuary = null;
					reSendTimeoutID = -1;
				}
			},
			error:function(msg)
			{
				resendQuary = null;
				reSendTimeoutID = -1;
			},
		});
	}

	/**
	 *	再送信：クエリ作成
	 */
	function makeReSendQuary(item)
	{
		var date = new Date();
		date.setTime(item.createDate);
		var format = new DateFormat("yyyyMMddHHmmss");
		var tid = op.getTerminalID();

		var data = {};
		data.id = item.id;									// シーケンスID DB用

		data.tid = tid;										// 端末ID
		data.cid = item.cardID;								// カードID
		data.empid = item.employeeId;						// 社員コード
		data.empkbn = item.employeeKbn;						// 社員区分
		data.tim = format.format(date)						// 打刻時刻 YYYYMMDDhhmmss
		data.sts = ("0" + (item.division + 1)).slice(-2);	// 打刻区分 01-04
		data.typ = "00";									// カードタイプ
		data.sendstat = 1;									// 再送ステータス
		data.idivision = item.idivision;					// 機器区分
		data.fno = item.fno;								// 定食区分
		data.cardkbn = FOOD_REGISTRATION;					// 処理区分

		return data;
	}

	/**
	 *	DBのレコードを更新
	 */
	function updateLog()
	{
		database.transaction(function (tx)
		{
			var now = new Date();
			var sql = 'update ' + LOG_TABLE_NAME + ' set modifyDate=?,status=0 where id=?';

			tx.executeSql(sql,
			[now,resendQuary.id],
			function (tx,rs)
			{
				resendQuary = null;

				// 再送を起動
				setTimeout(function(){reSend();},RESEND_INTERVAL);
			}
			,function (tx,e)
			{
				// 失敗
				// 更新に失敗しているので、いつまでも再送し続ける
				resendQuary = null;
				reSendTimeoutID = -1;
			});
		});
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
					document.getElementById("label_fno").innerHTML = "設定画面で機器・定食を<br>設定してください";
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
		// ログボタン
		document.getElementById("log").addEventListener("mousedown", function(e) {
			btnShowLog();
		});

		// 設定ボタン
		document.getElementById("setting").addEventListener("mousedown", function(e) {
			btnSetting();
		});
	}

	/**
	 * ログボタン押下
	 */
	function btnShowLog()
	{
		location.href = 'showLog.html';
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
			var sql1 = 'create table if not exists ' + LOG_TABLE_NAME + '('
				+ 'id integer primary key autoincrement,'
				+ 'createDate integer not null,'
				+ 'modifyDate integer not null,'
				+ 'cardID text not null,'
				+ 'employeeId text not null,'
				+ 'employeeKbn text not null,'
				+ 'idivision integer not null,'
				+ 'fno integer not null,'
				+ 'status integer not null'
				+ ')';

			var sql2 = 'create table if not exists ' + IC_CARD_LICENSE_TABLE_NAME + '('
				+ 'cardID text not null,'
				+ 'employeeId text not null,'
				+ 'employeeKbn text not null'
				+ ')';

			var sql3 = 'create table if not exists ' + T_IC_CARD_LICENSE_TABLE_NAME + '('
				+ 'cardID text not null,'
				+ 'employeeId text not null,'
				+ 'employeeKbn text not null'
				+ ')';

			var sql4 = 'create table if not exists ' + SETTING_TABLE_NAME + '('
				+ 'idivision integer not null,'
				+ 'fno integer not null'
				+ ')';

			database.transaction(function (tx)
			{
				// create
				executeSql(tx, sql1);
				executeSql(tx, sql2);
				executeSql(tx, sql3);
				executeSql(tx, sql4);
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
