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
	/**
	 * 画面起動前の処理
	 */
	window.addEventListener("DOMContentLoaded", function() {
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
	}, false);

	window.onload = function(){ready()};

	// static private variables
	// 結果表示通信エラータイムアウト
	const SHOW_RESULT_SEND_ERR_TIMEOUT = 3 * 1000;
	// 結果表示タイムアウト
	const SHOW_RESULT_TIMEOUT = 3 * 1000;
	// 結果表示タイムアウト（エラーあり時）
	const SHOW_RESULT_ERR_TIMEOUT = 5 * 1000;
	// TOPに戻るまでのタイマー
	const RETURN_TO_TOP_TIMER = 5 * 60 * 1000;

	var op = new ProOperateWrapper();

	// 前画面から渡ってくるパラメータ
	var paramList = null;

	// 対象年月
	var targetYm = null;

	// 社員区分
	var employeeKbn = null;

	// 上期下期区分
	var period = 1;

	// TOPへ戻るタイマー
	var timer = null;

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

		// イベント作成
		makeEvent();

		// パラメータ取得
		getParameter();

		// 社員コードから予約情報取得
		getReservationInfo(null);
	}

	/**
	 * パラメータ取得
	 */
	function getParameter()
	{
		var params = location.href.split("?")[1];
		params = params.split("&");
		paramList = {};
		for (var i = 0; i < params.length; i++) {
			var param = params[i].split("=");
			paramList[param[0]] = param[1];
		}
	}

	/**
	 * 初期化処理
	 */
	function clear()
	{
		removeEvent();

		op.stopEventListen();
		op = null;
		paramList = null;
		targetYm = null;
		employeeKbn = null;
		period = null;
		timer = null;
	}

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
	 *	社員コードから予約情報取得
	 */
	function getReservationInfo(ym)
	{
		var param = {};
		param.cardkbn = RESERVATION_INFO;			// 処理区分
		param.empid = paramList['eid'];				// 社員コード
		if (ym != null) {
			param.date = ym;						// 対象年月
		}

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
				editCalendar(data);
			},
			error: function(msg)
			{
				//通信結果の表示
				displayErrorMessage();
			},
		});
	}

	/**
	 *	予約情報を送信
	 */
	function sendReservationInfo(strAppend, strRemove)
	{
		var param = {};
		param.cardkbn = RESERVATION_REGISTRATION;	// 処理区分
		param.empid = paramList['eid'];				// 社員コード
		param.empkbn = employeeKbn;					// 社員区分
		param.date = targetYm;						// 対象年月
		param.adddate = strAppend;					// 予約追加
		param.deletedate = strRemove;				// 予約取消

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
				// レスポンスをパースし、連想配列へ
				var ary = getArrayFromResponse(data);
				var obj = JSON.parse(ary.dsp);

				// メッセージ出力
				if (obj['message'] == '') {
					confirmDialogUnder2("alertmessage", MSG_INFO_01);
					setTimeout(function() {
						getReservationInfo(targetYm);
					}, SHOW_RESULT_TIMEOUT);
				} else {
					var message = obj['message'];
					document.getElementById('confirm').getElementsByTagName('p')[0].innerHTML = message;

					// alert：モーダル
					new PitTouch_MODAL().modalDialog({
						elementName : "confirm",
						// 確認ボタン
						yes : function() {
							getReservationInfo(targetYm);
							return true;
						},
					});
				}
			},
			error: function(msg)
			{
				//通信結果の表示
				displayErrorMessage();
			},
		});
	}

	/**
	 * カレンダーを編集
	 */
	function editCalendar(data)
	{
		// レスポンスをパースし、連想配列へ
		var ary = getArrayFromResponse(data);
		var obj = JSON.parse(ary.dsp);

		var htmlText = '';
		var count = 0;
		var week = 1;

		if (period == 1) {
			htmlText += '<tr class="period1">';

			addClassName(document.getElementById('up'), 'disabled');
			removeClassName(document.getElementById('down'), 'disabled');
		} else {
			htmlText += '<tr class="period1 display_none">';

			removeClassName(document.getElementById('up'), 'disabled');
			addClassName(document.getElementById('down'), 'disabled');
		}

		// 社員情報編集
		var empInfo = document.getElementById('emp_info');
		empInfo.innerHTML = obj['employee_info']['employee_id'] + ' ' + obj['employee_info']['employee_name1'];
		// 社員区分を保存
		employeeKbn = obj['employee_info']['employee_kbn'];

		// 対象年月編集
		targetYm = obj['target_ym'];
		var targetDate = document.getElementById('target_date');
		targetDate.innerHTML = targetYm.substring(0, 4) + '年' + Number(targetYm.substring(4, 6)) + '月';

		// カレンダー編集
		var calendarList = obj['calendar'];
		for (var i = 0; i < calendarList.length; i++) {
			if (calendarList[i]['day'] == '') {
				htmlText += '<td>';
			} else {
				var date = targetYm + ('00' + calendarList[i]['day']).slice(-2);
				var className = '';

				// TD背景色の設定
				if (calendarList[i]['day_off'] == '1') {
					className = 'day_off'; // 休日色
				} else {
					if (calendarList[i]['edit_flag'] == '0') {
						className = 'disabled'; // 営業日の締めが過ぎた日
					}
				}

				htmlText += '<td class="' + className + '">';
				htmlText += '<div class="date_box">';
				htmlText += '<span class="date">' + calendarList[i]['day'] + '</span>';

				var foodList = [];
				if (calendarList[i]['food_division'] != '') {
					foodList = calendarList[i]['food_division'].split(',');
				}

				if (calendarList[i]['day_off'] == '0') {
					if (foodList.length > 0) {
						var baseKbn = '';
						for (var a = 0; a < FNO_LIST.length; a++) {
							if (foodList[0] == FNO_LIST[a][0]) {
								baseKbn = FNO_LIST[a][3];
								break;
							}
						}

						if (foodList.length > 1) {
							htmlText += '<span class="reservation base' + baseKbn + '">予約' + foodList.length + '</span>';
						} else {
							htmlText += '<span class="reservation base' + baseKbn + '">' + FNO_LIST[a][2] + '</span>';
						}
					}
					if (calendarList[i]['edit_flag'] == '1') {
						htmlText += '<div id="id_' + date + '" class="click_area" onmousedown="selectMenu(\'' + date + '\')" title="' + foodList[0] + '"><div class="comment"></div></div>';
					}
				}

				htmlText += '</div>';
			}

			htmlText += '</td>';
			count++;

			if (count == 7) {
				htmlText += '</tr>';
				count = 0;
				week++;

				if (week <= 3) {
					if (period == 1) {
						htmlText += '<tr class="period1">';
					} else {
						htmlText += '<tr class="period1 display_none">';
					}
				} else {
					if (period == 2) {
						htmlText += '<tr class="period2">';
					} else {
						htmlText += '<tr class="period2 display_none">';
					}
				}
			}
		}

		htmlText += '</tr>';

		var tbody = document.getElementById('tbody');
		tbody.innerHTML = htmlText;

		// サーバ通信中：モーダル解除
		new PitTouch_MODAL().unmodal();

		setTimer();
	}

	/**
	 * イベント作成
	 */
	function makeEvent()
	{
		// 左ボタン
		document.getElementById("left").addEventListener("mousedown", function(e) {
			left();
		});

		// 右ボタン
		document.getElementById("right").addEventListener("mousedown", function(e) {
			right();
		});

		// 上ボタン
		document.getElementById("up").addEventListener("mousedown", function(e) {
			up();
		});

		// 下ボタン
		document.getElementById("down").addEventListener("mousedown", function(e) {
			down();
		});

		// 戻るボタン
		document.getElementById("back").addEventListener("mousedown", function(e) {
			back();
		});

		// 保存ボタン
		document.getElementById("save").addEventListener("mousedown", function(e) {
			save(e);
		});

		// カレンダー部分
		document.getElementById("tbody").addEventListener("mousedown", function(e) {
			setTimer();
		});
	}

	/**
	 * イベント削除
	 */
	function removeEvent()
	{
		document.getElementById("left").removeEventListener("mousedown", function(e) {
			left();
		});
		document.getElementById("right").removeEventListener("mousedown", function(e) {
			right();
		});
		document.getElementById("up").removeEventListener("mousedown", function(e) {
			up();
		});
		document.getElementById("down").removeEventListener("mousedown", function(e) {
			down();
		});
		document.getElementById("back").removeEventListener("mousedown", function(e) {
			back();
		});
		document.getElementById("save").removeEventListener("mousedown", function(e) {
			save(e);
		});
		document.getElementById("tbody").removeEventListener("mousedown", function(e) {
			setTimer();
		});
	}

	/**
	 * 左ボタン押下処理
	 */
	function left() {
		if (targetYm == null) {
			return;
		}

		var y = targetYm.substring(0, 4);
		var m = targetYm.substring(4, 6);

		if (m == '01') {
			y = Number(y) - 1;
			m = '12'
		} else {
			m = ('00' + (Number(m) - 1)).slice(-2);
		}

		period = 1;

		getReservationInfo(y + m);
	}

	/**
	 * 右ボタン押下処理
	 */
	function right() {
		if (targetYm == null) {
			return;
		}

		var y = targetYm.substring(0, 4);
		var m = targetYm.substring(4, 6);

		if (m == '12') {
			y = Number(y) + 1;
			m = '01'
		} else {
			m = ('00' + (Number(m) + 1)).slice(-2);
		}

		period = 1;

		getReservationInfo(y + m);
	}

	/**
	 * 上ボタン押下処理
	 */
	function up() {
		setTimer();
		if (period == 2) {
			var obj1 = document.getElementsByClassName('period1');
			var obj2 = document.getElementsByClassName('period2');

			for (var i = 0; i < obj1.length; i++) {
				removeClassName(obj1[i], 'display_none');
			}
			for (var i = 0; i < obj2.length; i++) {
				addClassName(obj2[i], 'display_none');
			}

			period = 1;

			addClassName(document.getElementById('up'), 'disabled');
			removeClassName(document.getElementById('down'), 'disabled');
		}
	}

	/**
	 * 下ボタン押下処理
	 */
	function down() {
		setTimer();
		if (period == 1) {
			var obj1 = document.getElementsByClassName('period1');
			var obj2 = document.getElementsByClassName('period2');

			for (var i = 0; i < obj1.length; i++) {
				addClassName(obj1[i], 'display_none');
			}
			for (var i = 0; i < obj2.length; i++) {
				removeClassName(obj2[i], 'display_none');
			}

			period = 2;

			removeClassName(document.getElementById('up'), 'disabled');
			addClassName(document.getElementById('down'), 'disabled');
		}
	}

	/**
	 * 戻るボタン押下処理
	 */
	function back() {
		clear();
		location.href = 'index.html';
	}

	/**
	 * 保存ボタン押下処理
	 */
	function save(event) {
		setTimer();

		var appendList = document.getElementsByClassName('append');
		var removeList = document.getElementsByClassName('remove');

		var strAppend = '';
		for (var i = 0; i < appendList.length; i++) {
			if (i != 0) {
				strAppend += ',';
			}
			strAppend += appendList[i].getElementsByClassName('click_area')[0].id.replace('id_', '') + '-' + appendList[i].getElementsByClassName('click_area')[0].childNodes[0].title;
		}

		var strRemove = '';
		for (var i = 0; i < removeList.length; i++) {
			if (i != 0) {
				strRemove += ',';
			}
			strRemove += removeList[i].getElementsByClassName('click_area')[0].id.replace('id_', '') + '-' + removeList[i].getElementsByClassName('click_area')[0].title;
		}

		if (strAppend == '' && strRemove == '') {
			// 登録情報がない場合のエラー
			confirmDialogUnder2("alertmessage", MSG_ERROR_04);
			// 指定時間経過後ポップアップを閉じる
			setTimeout(function() {
				// サーバ通信中：モーダル解除
				new PitTouch_MODAL().unmodal();
			}, SHOW_RESULT_TIMEOUT);
			event.preventDefault();
			return false;
		}

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

		// 予約情報を送信
		sendReservationInfo(strAppend, strRemove);
	}

	/**
	 *	エラーメッセージ表示
	 */
	function displayErrorMessage()
	{
		// 通信エラー
		confirmDialogUnder2("alertmessage", MSG_ERROR_01);
		// 指定時間経過後ポップアップを閉じる
		setTimeout(function() {
			// サーバ通信中：モーダル解除
			new PitTouch_MODAL().unmodal();
		}, SHOW_RESULT_SEND_ERR_TIMEOUT);
	}

	/**
	 * タイマー設定
	 */
	function setTimer()
	{
		// タイマークリア
		if (timer != null) {
			clearInterval(timer);
		}

		// タイマー設定
		timer = setInterval(function(){
			back();
		}, RETURN_TO_TOP_TIMER);
	}

})();

function selectMenu(date) {
	var obj = document.getElementById('id_' + date).parentNode.parentNode;
	if (hasClass(obj, 'remove')) {
		removeClassName(obj, 'remove');
		return;
	}

	document.getElementById('selectdate').innerText = date.substring(0, 4) + "年" + Number(date.substring(4, 6)) + "月" + Number(date.substring(6, 8)) + "日";

	var obj = {};
	obj.elementName = "selectmenu";

	for (var i = 0; i < FNO_LIST.length; i++) {
		(function() {
			var a = i;
			obj["menu" + (a + 1)] = function() {
				addReservation(date, a);
			};
		})();
	}

	obj.close = function() {
		new PitTouch_MODAL().unmodal();
	};
	obj.cancel = function() {
		deleteReservation(date);
	};

	new PitTouch_MODAL().modalDialog(obj);
}

/**
 * 予約追加の状態にする
 */
function addReservation(date, index) {
	document.getElementById('id_' + date).childNodes[0].innerText = FNO_LIST[index][2];
	document.getElementById('id_' + date).childNodes[0].title = FNO_LIST[index][0]; // タイトル要素に区分を格納

	var obj = document.getElementById('id_' + date).parentNode.parentNode;
	if (hasClass(obj, 'append')) {
		// 既にある場合は何もしない
	} else {
		addClassName(obj, 'append');
	}
	new PitTouch_MODAL().unmodal();
}

/**
 * 予約取消の状態にする
 */
function deleteReservation(date) {
	document.getElementById('id_' + date).childNodes[0].innerText = "消";

	var obj = document.getElementById('id_' + date).parentNode.parentNode;
	var resObj = obj.getElementsByClassName('reservation');

	if (resObj.length > 0) {
		// 登録済みの予約がある
		removeClassName(obj, 'append');
		addClassName(obj, 'remove');
	} else {
		// 登録済みの予約がない
		if (hasClass(obj, 'append')) {
			removeClassName(obj, 'append');
		}
	}
	new PitTouch_MODAL().unmodal();
}
