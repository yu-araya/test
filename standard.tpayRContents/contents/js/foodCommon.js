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
 *	モーダルダイアログ表示
 */
function confirmDialogUnder2(dialogName, message, callbackObject)
{
	document.getElementById(dialogName).getElementsByTagName('p')[0].innerHTML = message;

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
 *	モーダルダイアログ表示（文字色赤）
 */
function confirmDialogUnder3(dialogName, message, callbackObject)
{
	document.getElementById(dialogName).getElementsByTagName('p')[0].innerHTML = message;

	var css =
	{
		width:'440px',
		height:'102px',
		top:'150px',
		left:'20px',
		textAlign:'left',
		color:'#f00',
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
