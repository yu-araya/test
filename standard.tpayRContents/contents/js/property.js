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

// 処理区分（食事タッチ情報登録）
const FOOD_REGISTRATION = 1;
// 処理区分（ICカード件数取得）
const IC_CARD_COUNT_DVISION = 2;
// 処理区分（ICカード取得）
const IC_CARD_INFO_DVISION = 3;
// 処理区分（ICカードチェック）
const RESERVATION_IC_CARD_CHECK = 4;
// 処理区分（個人別予約情報取得）
const RESERVATION_INFO = 5;
// 処理区分（予約情報登録）
const RESERVATION_REGISTRATION = 6;

// メッセージ
const MSG_INFO_01 = "予約を受け付けました。";

const MSG_ERROR_01 = "サーバーとの通信に失敗しました。";
const MSG_ERROR_02 = "処理中にエラーが発生しました。";
const MSG_ERROR_03 = "ご使用のカードはご利用できません。";
const MSG_ERROR_04 = "予約日または予約の取り消し日を<br>選択してください。";

// 食事リスト
// ※内訳（食事区分、メニュー表示名、カレンダー内略名、事業所区分）
const FNO_LIST = [
    [5, "定食（本社）", "定本", "1"],
    [6, "丼（本社）", "丼本", "1"],
    [7, "定食（工場）", "定工", "2"],
    [8, "丼（工場）", "丼工", "2"],
];
