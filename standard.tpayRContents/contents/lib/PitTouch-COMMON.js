/*
 * ピットタッチ・プロ サンプルコンテンツ
 * common ライブラリ
 * 
 * COPYRIGHT (C) 2011 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */

// ピットタッチ・プロで利用されることを目的としているため
// クロスブラウザ対応には、なっていない


// 指定された要素のHTMLを変更する
// elementName:ID
// txt:変更するHTML
function changeHTML(elementName,txt)
{
	var obj = document.getElementById(elementName);
	if (obj)
		obj.innerHTML = txt;
}

// 指定された要素のCSSを変更する
// elementName:ID
// cssName:変更するCSS名
// cssValue:変更するCSS値
function changeCSS(elementName,cssName,cssValue)
{
	var obj = document.getElementById(elementName);
	if (obj)
		obj.style[cssName] = cssValue;
}

// 指定された要素のCSSを取得する
// elementName:ID
// cssName:取得するCSS名
// return:CSS値 string
function getCSSById(elementName,style)
{
	var obj = document.getElementById(elementName);
	
	return getCSS(obj,style);
}

// 指定された要素のCSSを取得する
// element:要素
// cssName:取得するCSS名
// return:CSS値 string
function getCSS(element,style)
{
	return document.defaultView.getComputedStyle(element, null).getPropertyValue(style);
}

// 指定されたクラス要素の中で指定した要素の順序を取得する
// className:クラス名
// element:要素
// return:順序 number
function getElementIndexByClassName(className,element)
{
	var elements = document.getElementsByClassName(className);
	var index = -1;
	
	for (var i = 0; i < elements.length; i++)
	{
		if (elements[i] ===  element)
		{
			index = i;
			break;
		}
	}
	
	return index;
}

// 指定されたクラス要素の中で指定した順の要素を取得する
// className:クラス名
// index:順
// return:要素 object
function getElementByClassNameAndIndex(className,index)
{
	var elements = document.getElementsByClassName(className);
	
	return elements[index];
}


// マウスイベントを発生させる
// elementName:ID
// type:発生させるイベント名
function fireMouseEvent(elementName,type)
{
	var target = document.getElementById(elementName);
	var event = document.createEvent('MouseEvents');
	event.initEvent( type, true, true );
	event.element = function (){ return evt.target; }
	
	if (target)
		target.dispatchEvent(event);
}

// オブジェクトのコピー
// destination:コピー先
// source:コピー元
var extend = function (destination, source)
{
	for (var property in source)
	{
		destination[property] = source[property];
	}
	
	return destination;
}

