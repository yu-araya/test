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
const SERVER_URL = "https://demo.touchpay.biz/TouchPay.standard/app/pitTouch/proc";
// 送信先メソッド
const SERVER_METHOD = "POST";
// 送信先タイムアウト
const SERVER_TIMEOUT = 3 * 1000;
// ICカード取込タイムアウト
const SERVER_TIMEOUT_IC_CARD_IMPORT = 60 * 1000;
// ICカード取込リトライ間隔
const RETRY_IC_CARD_IMPORT = 10 * 1000;

// 処理区分（食事タッチ情報登録）
const FOOD_REGISTRATION = 1;
// 処理区分（ICカード件数取得）
const IC_CARD_COUNT_DVISION = 2;
// 処理区分（ICカード取得）
const IC_CARD_INFO_DVISION = 3;

// １リクエストに対するICカード取得件数
const SELECT_CARD_COUNT = 100;
// ログの最大件数
const MAX_LOG = 10000;

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
const DATABASE_SIZE = 1024 * 1024 * 2;

// 機器区分リスト
const IDIVISION_LIST = [
    [1, "本社"],
    [2, "工場"],
];

//食事リスト
const FNO_LIST = [
    [1, "定食", "定食<br>（本社）"],
    [2, "丼", "丼<br>（本社）"],
    [3, "工場定食", "定食<br>（工場）"],
    [4, "工場丼", "丼<br>（工場）"],
];

//時間で表示メニューを変更する場合は1にする。その他は0
const CHANGE_FNO_VALID = 0;

//時間で表示メニューを変更[機器区分, 食事区分, 時, 分], 秒
const CHANGE_FNO = [
    [1, 1, 12, 00, 00],
    [1, 2, 18, 00, 00],
];
