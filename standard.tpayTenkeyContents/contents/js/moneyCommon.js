/*
 * 社員食堂精算管理システム
 * キーパッド入力タイプ用 共通
 *
 * COPYRIGHT (C) 2012 AGILECORE, INC.  ALL RIGHTS RESERVED.
 *
 * @author AGILECORE, INC.
 * @version 1.0
 *
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 */

//通信中keypad抜けフラグ
var keypadError = 0;

//通信中フラグ
var nowAjax = 0;

/*
 *	モーダルダイアログ表示
 */
function confirmDialog(dialogName, callbackObject) {
    if (typeof callbackObject === "undefined") callbackObject = {};

    callbackObject.elementName = dialogName;

    new PitTouch_MODAL().modalDialog(callbackObject);
}

/*
 *	モーダルダイアログ表示
 */
function confirmDialogUnder(dialogName, callbackObject) {
    var css = {
        width: "440px",
        height: "68px",
        top: "185px",
        left: "20px",
        textAlign: "left",
    };
    var overlayCSS = {
        opacity: 0.3,
    };

    if (typeof callbackObject === "undefined") callbackObject = {};

    callbackObject.elementName = dialogName;

    new PitTouch_MODAL().modalDialog(callbackObject, css, overlayCSS);
}

/*
 *	keypad:状態チェック
 */
function keypadCheck() {
    var result = false;

    var op = new ProOperateWrapper();
    var keypadStat = op.getKeypadConnected();

    // keypadが抜けた
    if (keypadStat == 0) {
        //非接触IC通信の停止
        op.stopCommunication();

        //通信中keypad抜けフラグを立てる
        keypadError = 1;

        //Ajax通信中はエラーモーダルを表示しない
        if (!nowAjax) {
            keypadErrorModal();
        }

        result = true;
    }

    return result;
}

/*
 *	keypad:表示状態のクリア
 */
function clearKeyDisplay() {
    var op = new ProOperateWrapper();

    var displayParam = {};

    displayParam.firstLine = "                ";
    displayParam.secondLine = "                ";
    displayParam.backLight = true;

    op.setKeypadDisplay(displayParam);

    var lightParam = {};

    lightParam.light = "000000";

    op.setKeypadLed(lightParam);
}

function keypadErrorModal() {
    confirmDialog("keypadconfirm", {
        // 確認ボタン
        ok: function () {
            // Top画面に戻る
            location.href = "index.html";

            // エラー画面：モーダル解除
            return true;
        },
    });
}

/*
 *	keypad:キーが押された
 */

function keypadKeyDown(keyCode) {
    switch (keyCode) {
        case 111: // 終了
            location.href = "../index.html";
            break;
        case 109: // 戻る
            break;
        case 13: // 決定
            break;
        case 107: // 次へ
            break;
        case 106: // クリア
            break;
        case 58: // 00
            break;
        default:
            var c = keyCode - 48;
            break;
    }
}

//
//	keypad接続状態表示
//
function showKeypadIcon() {
    var op = new ProOperateWrapper();
    var keypadStat = op.getKeypadConnected();

    if (keypadStat == 0) changeCSS("keypad", "background-image", "url('./css/image/Keypadd.png')");
    else changeCSS("keypad", "background-image", "url('./css/image/Keypad.png')");

    return keypadStat;
}
