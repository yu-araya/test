/*
 * 社員食堂精算管理システム
 * index トップ画面
 * 有効IDカード取得
 *
 * COPYRIGHT (C) 2012 AGILECORE, INC.  ALL RIGHTS RESERVED.
 *
 * @author AGILECORE, INC.
 * @version 1.0
 *
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 */

// 有効IDカード取得開始時間
const hh = 10;	// 時間（24時間表記）
const mm = 00;	// 分
const ss = 00;	// 秒

// 再起動開始時間
const reboot_hh = 03;	// 時間（24時間表記）
const reboot_mm = 00;	// 分
const reboot_ss = 00;	// 秒

(function()
{
	var date = null;
	var intervalId = setInterval(function(){
		date = new Date();
		if(date.getHours() == hh && date.getMinutes() == mm && date.getSeconds() == ss) {
			importIcCard();
		} else if(date.getHours() == reboot_hh && date.getMinutes() == reboot_mm && date.getSeconds() == reboot_ss) {
			var op = new ProOperateWrapper();
			op.reboot();
		}
	}, 1000);
})();
