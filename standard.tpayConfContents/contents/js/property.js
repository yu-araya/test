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

// 送信先サーバ
const SERVER_URL = "http://192.168.21.48/TouchPay.standard/pit_touch/proc";
// 送信先メソッド
const SERVER_METHOD = "POST";
// 送信先タイムアウト
const SERVER_TIMEOUT = 3 * 1000;
// ICカード取込タイムアウト
const SERVER_TIMEOUT_IC_CARD_IMPORT = 60 * 1000;
// ICカード取込リトライ間隔
const RETRY_IC_CARD_IMPORT = 10 * 1000;

// 処理区分（食事タッチ情報登録）
const FOOD_REGISTRATION	= 1;
// 処理区分（ICカード件数取得）
const IC_CARD_COUNT_DVISION	= 2;
// 処理区分（ICカード取得）
const IC_CARD_INFO_DVISION	= 3;
// 処理区分（ICカードチェック）
const RESERVATION_IC_CARD_CHECK	= 4;
// 処理区分（個人別予約情報取得）
const RESERVATION_INFO	= 5;
// 処理区分（予約情報登録）
const RESERVATION_REGISTRATION	= 6;
// 処理区分（予約情報確認）
const IC_CARD_RESERVATION= 7;

// １リクエストに対するICカード取得件数
const SELECT_CARD_COUNT = 100;

// データベース名
const SYOKUDO_DATABASE_NAME = "syokudo_database";
// ICカードテーブル名
const IC_CARD_LICENSE_TABLE_NAME = "ic_card_license";
// ICカード取込テーブル名
const T_IC_CARD_LICENSE_TABLE_NAME = "t_ic_card_license";
// ログテーブル名
const LOG_TABLE_NAME = "log_menu_lunchA";
// 設定テーブル名
const SETTING_TABLE_NAME = "t_setting";
// データベースサイズ
const DATABASE_SIZE = 1024*1024*2;

// 機器区分リスト
const IDIVISION_LIST = [
	[1, "本社"],
	[2, "工場"]
];

// 食事リスト
const FNO_LIST = [
	[5, "定食(予約)", "定食(予約)<br>（本社）"],
	[6, "丼(予約)", "丼(予約)<br>（本社）"],
	[7, "工定食(予約)", "定食(予約)<br>（工場）"],
	[8, "工丼(予約)", "丼(予約)<br>（工場）"]
];
