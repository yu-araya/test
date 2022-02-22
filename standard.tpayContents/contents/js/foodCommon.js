/*
 * 社員食堂精算管理システム
 * 非接触ICタイプ用 共通
 * 
 * COPYRIGHT (C) 2012 AGILECORE, INC.  ALL RIGHTS RESERVED.
 * 
 * @author AGILECORE, INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */

var cardArray = null;
var retryCount = 0;

/**
 *	モーダルダイアログ表示
 */
function confirmDialog(dialogName,callbackObject)
{
	if (typeof callbackObject === "undefined")
		callbackObject = {};
		
	callbackObject.elementName = dialogName;

	new PitTouch_MODAL().modalDialog
	(
		callbackObject
	);
}

/**
 *	モーダルダイアログ表示
 */
function confirmDialogUnder(dialogName,callbackObject)
{
	var css =
	{
		width:'440px',
		height:'102px',
		top:'150px',
		left:'20px',
		textAlign:'left',
	}
	var overlayCSS =
	{
		opacity:0.3
	}
	
	if (typeof callbackObject === "undefined")
		callbackObject = {};
		
	callbackObject.elementName = dialogName;
	
	new PitTouch_MODAL().modalDialog
	(
		callbackObject,
		css,
		overlayCSS
	);
}

/**
 *	API返却値をオブジェクトに変換
 */
function getArrayFromResponse(text)
{
	var lines = text.split("\n");
	var obj = {};

	for (var no in lines)
	{
		var p;
		var line = lines[no];

		line = line.replace(/\r$/, "");		// 行末のCRは削除
		line = line.replace(/^[ \t]+/,"");	// 先頭の空白は削除

		// 空行は無視
		if (line.match(/^[ \t]*$/))
			continue;

		if ((p=line.indexOf('='))>=0)
		{
			var keyname = line.substr(0, p).replace(/[ \t]+$/, "");
			var value   = line.substr(p+1, line.length-p-1);

			obj[keyname] = value;
		}
	}

	return obj;
}

/**
 * ICカード取込
 */
function importIcCard()
{
	outputInfoLog("ICカード取込開始");

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

	//ICカード取込テーブルレコード削除
	deleteTable(T_IC_CARD_LICENSE_TABLE_NAME);
}

/**
 * ICカード情報取得
 */
function getIcCard()
{
	var param = {};
	param.cardkbn = IC_CARD_INFO_DVISION;								// 処理区分
	param.idivision = document.getElementById("id_idivision").value;	// 機器区分
	param.fno = document.getElementById("id_fno").value;				// 食事区分

	// サーバ通信
	var ajax = new PitTouch_AJAX();
	ajax.sendRequest(
	{
		url: SERVER_URL,
		data: param,
		type: SERVER_METHOD,
		dataType: "text",
		timeout: SERVER_TIMEOUT_IC_CARD_IMPORT,
		success: function(data)
		{
			//カードID取得
			ajaxGetCardSuccess(data);
		},
		error: function(msg)
		{
			if (retryCount != null && retryCount > 2) {
				retryCount = null;
				// 通信エラー
				var e = {};
				e.message = "サーバーとの通信に失敗しました。";
				errorDialog('ajaxerr', e);
			} else {
				retryCount = (retryCount == null) ? 1 : (retryCount + 1);
				outputInfoLog("ICカード取込リトライ" + retryCount);
				setTimeout(function() {
					getIcCard();
				}, RETRY_IC_CARD_IMPORT);
			}
		},
	});
}

/**
 *	通信：サーバ通信成功のコールバック
 */
function ajaxGetCardSuccess(data)
{
	// レスポンスをパースし、連想配列へ
	var resultAry = getArrayFromResponse(data);
	var card_info = resultAry.dsp;

	if (card_info == undefined) {
		var e = {};
		e.message = "ICカードの取得に失敗しました。";
		errorDialog('ajaxerr', e);
	} else if (card_info.length != 0){
		//カンマ分割配列格納
		cardArray = card_info.split(",");

		var database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

		//ICカードDBにレコード作成
		database.transaction(function (tx)
		{
			//カード情報をテーブルに追加
			insertCardInfo(tx, 0);
		});
	} else {
		//ICカードテーブルレコード削除
		deleteTable(IC_CARD_LICENSE_TABLE_NAME);
	}
}

/**
 * カード情報をテーブルに追加
 */
function insertCardInfo(tx, index)
{
	var card_id = cardArray[index];
	var arr_info = card_id.split("-"); // ICカード番号-社員コード-社員区分を分割

	//ICカードDBにレコード作成
	var sql = 'insert into ' + T_IC_CARD_LICENSE_TABLE_NAME + '(cardID,employeeId,employeeKbn) values (?,?,?)';

	tx.executeSql(sql,
	[arr_info[0],arr_info[1],arr_info[2]],
	function (tx,rs)
	{
		// 成功
		if (cardArray.length - 1 > index) {
			//カード情報をテーブルに追加
			insertCardInfo(tx, index + 1);
		} else {
			//ICカードテーブルレコード削除
			deleteTable(IC_CARD_LICENSE_TABLE_NAME);
		}
	}
	,function (tx,e)
	{
		// 失敗
		cardArray = null;
		retryCount = null;
		errorDialog('commonerr', e);
	});
}

/**
 *	DB：対象テーブルのレコード削除
 */
function deleteTable(table_name)
{
	var database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

	database.transaction(function (tx)
	{
		var sql = 'delete from ' + table_name;

		tx.executeSql(sql,
		null,
		function (tx,rs)
		{
			// 成功
			if (table_name == T_IC_CARD_LICENSE_TABLE_NAME) {
				//ICカードを取得し登録
				getIcCard();
			} else if (table_name == IC_CARD_LICENSE_TABLE_NAME) {
				//ICカードテーブルにICカード取込テーブルのレコードを追加
				margeIcCardLicenseTable();
			}
		}
		,function (tx,e)
		{
			// 失敗
			cardArray = null;
			retryCount = null;
			errorDialog('commonerr', e);
		});
	});
}

/**
 *	ICカードテーブルにICカード取込テーブルのレコードを追加
 */
function margeIcCardLicenseTable()
{
	var database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

	database.transaction(function (tx)
	{
		var sql = 'insert into ' + IC_CARD_LICENSE_TABLE_NAME + ' select * from ' + T_IC_CARD_LICENSE_TABLE_NAME;

		tx.executeSql(sql,
		null,
		function (tx,rs)
		{
			// 成功
			getIcCardCount();
		}
		,function (tx,e)
		{
			// 失敗
			errorDialog('commonerr', e);
		});
	});

	cardArray = null;
	retryCount = null;
}

/**
 * カード取込件数取得
 */
function getIcCardCount()
{
	var database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

	database.readTransaction(function (tx)
	{
		tx.executeSql('select count(*) from ' + IC_CARD_LICENSE_TABLE_NAME,
		null,
		function (tx,rs)
		{
			// 成功
			var row = rs.rows.item(0);
			var count = row["count(*)"];

			var now = new Date();
			var dateFormat = new DateFormat("yyyy/MM/dd");
			var timeFormat = new DateFormat("HH:mm:ss");

			// 取り込み件数をログに出力
			outputInfoLog("ICカード取込終了【取込件数：" + count + "件】");
			new PitTouch_MODAL().unmodal();
		}
		,function (tx,e)
		{
			// 失敗
			errorDialog('commonerr', e);
		});
	});
}

/**
 * エラーダイアログを表示
 */
function errorDialog(errorType, e)
{
	if (e == undefined || e == null) {
		e = {};
		e.message = "処理中にエラーが発生しました。";
	}

	outputErrorLog(e.message);

	confirmDialogUnder(errorType);

	setTimeout(function() {
		new PitTouch_MODAL().unmodal();
	}, 3000);
}

/**
 * インフォメーションログをコンソールに出力
 */
function outputInfoLog(message)
{
	var now = new Date();
	var dateFormat = new DateFormat("yyyy/MM/dd");
	var timeFormat = new DateFormat("HH:mm:ss");

	console.log(dateFormat.format(now) + " " + timeFormat.format(now) + " [INFO]" + message);
}

/**
 * エラーログをコンソールに出力
 */
function outputErrorLog(message)
{
	var now = new Date();
	var dateFormat = new DateFormat("yyyy/MM/dd");
	var timeFormat = new DateFormat("HH:mm:ss");

	console.error(dateFormat.format(now) + " " + timeFormat.format(now) + " [ERROR]" + message);
}

/**
 * クラスを追加（jquery未使用）
 */
function addClassName(element, classNameValue) {
	var classNames;
 
	if (!element || typeof element.className === 'undefined' || typeof classNameValue !== 'string') return element;
 
	if (element.classList) {
		element.classList.add(classNameValue);
	} else {
		var inArray = function(searchValue, arrayData) {
			var key, result = -1;
			if ((searchValue || searchValue === 0) && arrayData) {
				if ((typeof searchValue === 'string' || typeof searchValue === 'number') && typeof arrayData === 'object') {
					for (key in arrayData) {
						if (arrayData[key] === searchValue) {
							result = key;
							break;
						}
					}
				}
			}
			return result;
		};
 
		classNames = element.className.replace(/^\s+|\s+$/g, '').split(' ');
 
		if (classNames.toString() === '') {
			classNames = [];
		}
 
		if (inArray(classNameValue, classNames) > -1) return element;
 
		classNames.push(classNameValue);
 
		element.className = classNames.join(' ');
	}
 
	return element;
}

/**
 * クラスを削除（jquery未使用）
 */
function removeClassName(element, classNameValue) {
	if (!element || !element.className || typeof classNameValue !== 'string') return;
 
	if (element.classList) {
		element.classList.remove(classNameValue);
	} else {
		var classNames   = element.className.replace(/^\s+|\s+$/g, '').split(' '),
			hasClassName = false;
 
		var inArray = function(searchValue, arrayData) {
			var key, result = -1;
			if ((searchValue || searchValue === 0) && arrayData) {
				if ((typeof searchValue === 'string' || typeof searchValue === 'number') && typeof arrayData === 'object') {
					for (key in arrayData) {
						if (arrayData[key] === searchValue) {
							result = key;
							break;
						}
					}
				}
			}
			return result;
		};
 
		if (inArray(classNameValue, classNames) === -1) return;
 
		if (classNames.toString() === '') {
			classNames = [];
		}
 
		for (var i = 0, len = classNames.length; i < len; i++) {
			if (classNames[i] !== classNameValue) continue;
 
			classNames.splice(i, 1);
			hasClassName = true;
			break;
		}
 
		if (hasClassName) {
			element.className = classNames.join(' ');
		}
	}
 
	return element;
}

/**
 * クラス存在チェック（jquery未使用）
 */
function hasClass(element, selector, orFlag) {
	var i, len, hitCount = 0;

	if (!element || typeof element.className !== 'string') return;

	var strTrim = function(str) {
		return (str) ? str.replace(/^\s+|\s+$/g, '') : str;
	};

	var inArray = function(searchValue, arrayData) {
		var key, result = -1;

		if (!searchValue || !arrayData) return result;
		if (typeof searchValue !== 'string' || typeof arrayData !== 'object') return result;

		for (key in arrayData) {
			if (arrayData[key] === searchValue) {
				result = key;
				break;
			}
		}

		return result;
	};

	if (typeof selector === 'string') {
		selector = (selector.match(/^\./)) ? selector.replace(/^\./, '').split('.') : strTrim(selector).split(' ');
	}

	for (i = 0, len = selector.length; i < len; i++) {
		if (inArray(selector[i], element.className.split(' ')) !== -1) {
			hitCount++;
		}
	}

	if (orFlag) {
		if (hitCount > 0) return true;
	} else {
		if (hitCount === len) return true;
	}

	return false;
}
