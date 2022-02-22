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

(function (hh, mm, ss) {
    // 有効IDカード取得開始時間
    hh = 23; // 時間（24時間表記）
    mm = 40; // 分
    ss = 00; // 秒

    // 送信先サーバ
    //	const SERVER_URL = "http://192.168.1.1/cakephp/administrators/login";
    // const SERVER_URL = "https://www.touchpay.biz/ydk/pit_touch/proc";
    //	const SERVER_URL = "http://192.168.11.2:88/dining_info/administrators/login";
    const SERVER_URL = "https://demo.touchpay.biz/TouchPay.standard/app/pitTouch/proc";

    // 送信先メソッド
    const SERVER_METHOD = "POST";
    // 送信先タイムアウト
    const SERVER_TIMEOUT = 3000; // 3秒

    // ICカードテーブル名
    const IC_CARD_LICENSE_TABLE_NAME = "ic_card_license";

    // ICカード取込テーブル名
    const T_IC_CARD_LICENSE_TABLE_NAME = "t_ic_card_license";

    // データベース名名
    const SYOKUDO_DATABASE_NAME = "syokudo_database";

    // ICカード取込テーブル取込終了フラグ
    var T_IC_CARD_FINISH = true;

    // 処理区分（ICカード件数取得）
    const IC_CARD_COUNT_DVISION = 2;
    // 処理区分（ICカード取得）
    const IC_CARD_INFO_DVISION = 3;
    // １リクエストに対するICカード取得件数
    const SELECT_CARD_COUNT = 10;

    // ICカード取得開始位置
    var startCardCount = 0;

    // ICカード取得カウント
    var cardCount = 0;

    // ICカード取得クエリ
    var sendCardQuary = null;

    var database = null;

    var op = new ProOperateWrapper();

    window._D = new Date();
    if (_D.getHours() == hh && _D.getMinutes() == mm && _D.getSeconds() == ss) {
        // ここに実行したい処理を書く
        database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, 1024 * 1024 * 2);

        T_IC_CARD_FINISH = true;

        // ネットワーク状態の取得
        var netStat = op.getNetworkStat();

        if (netStat) {
            // サーバ通信中：モーダル
            var obj = document.getElementById("wait");
            new PitTouch_MODAL().modal({
                message: obj,
                css: {
                    width: "440px",
                    height: "232px",
                    top: "20px",
                    left: "20px",
                    textAlign: "left",
                },
            });

            // ICカード件数取得
            getIcCard(IC_CARD_COUNT_DVISION);

            //ICカード取込テーブルレコード削除
            deleteTable(T_IC_CARD_LICENSE_TABLE_NAME);

            // ICカード件数取得
            for (i = 0; i <= cardCount / SELECT_CARD_COUNT; i++) {
                //ICカード取得開始位置設定
                startCardCount = i * SELECT_CARD_COUNT + 1;
                //ICカードを取得し登録
                getIcCard(IC_CARD_INFO_DVISION);
            }

            //ICカード取込テーブルに正常にレコードが追加された場合
            if (T_IC_CARD_FINISH == true) {
                //Cカードテーブルレコード削除
                deleteTable(IC_CARD_LICENSE_TABLE_NAME);
                //CカードテーブルにICカード取込テーブルのレコードを追加
                margeIcCardLicenseTable();
            }

            // 初回selectが重いので一度実行してキャッシュする
            database.readTransaction(function (tx) {
                var sql = "select * from " + IC_CARD_LICENSE_TABLE_NAME + ' where upper(cardID) = ""';

                tx.executeSql(
                    sql,
                    null,
                    function (
                        tx,
                        rs // 成功
                    ) {
                        // サーバ通信中：モーダル解除
                        new PitTouch_MODAL().unmodal();
                    },
                    function (
                        tx,
                        e // 失敗
                    ) {
                        console.error("エラー" + e.message);
                        // サーバ通信中：モーダル解除
                        new PitTouch_MODAL().unmodal();
                    }
                );
            });
        }

        return true;
    } else {
        var _f = arguments.callee;
        window._T = setTimeout(function () {
            _f([hh, mm, ss].join("-"));
        }, 1000);
        return false;
    }

    function getIcCard(ic_card_kbn) {
        sendCardQuary = makeGetCardQuary(ic_card_kbn);

        // サーバ通信
        var ajax = new PitTouch_AJAX_NEW();
        //カウント取得
        if (ic_card_kbn == IC_CARD_COUNT_DVISION) {
            ajax.sendRequest({
                url: SERVER_URL,
                data: sendCardQuary,
                type: SERVER_METHOD,
                dataType: "text",
                timeout: SERVER_TIMEOUT,
                success: function (data) {
                    ajaxGetCardCountSuccess(data);
                },
                error: function (msg) {
                    T_IC_CARD_FINISH = false;
                },
            });
            //カードID取得
        } else {
            ajax.sendRequest({
                url: SERVER_URL,
                data: sendCardQuary,
                type: SERVER_METHOD,
                dataType: "text",
                timeout: SERVER_TIMEOUT,
                success: function (data) {
                    ajaxGetCardSuccess(data);
                },
                error: function (msg) {
                    T_IC_CARD_FINISH = false;
                },
            });
        }
    }

    function makeGetCardQuary(ic_card_kbn) {
        var data = {};

        data.scount = startCardCount; // ICカード取得開始位置
        //		data.ecount = endCardCount;						// ICカード取得終了位置
        data.select = SELECT_CARD_COUNT; // ICカード取得件数

        data.cardkbn = ic_card_kbn; // 処理区分

        return data;
    }

    //
    //	通信：サーバ通信成功のコールバック
    //
    function ajaxGetCardSuccess(data) {
        // レスポンスをパースし、連想配列へ
        var resultAry = getArrayFromResponse(data);

        resultAry.res;
        resultAry.snd;
        var card_info = resultAry.dsp;

        if (card_info.length != 0) {
            //カンマ分割配列格納
            var cardArray = card_info.split(",");

            for (var i = 0; i < cardArray.length; i++) {
                //ICカードDBにレコード作成
                insertCardID(cardArray[i]);
            }
        }
    }

    //ICカード取得件数取得
    function ajaxGetCardCountSuccess(data) {
        // レスポンスをパースし、連想配列へ
        var resultAry = getArrayFromResponse(data);

        cardCount = resultAry.dsp;
    }

    //
    //	ICカードDBにレコード作成
    //
    function insertCardID(card_list) {
        var arr_info = card_list.split("-"); // ICカード番号,社員コード,社員区分を分割

        //ICカードDBにレコード作成
        var sql = "insert into " + T_IC_CARD_LICENSE_TABLE_NAME + "(cardID,employeeId,employeeKbn) values (?,?,?)";
        database.transaction(function (tx) {
            tx.executeSql(
                sql,
                arr_info,
                function (tx, rs) {
                    quary = null;
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
                    quary = null;
                }
            );
        });
    }

    //
    //	DB：対象テーブルのレコード削除
    //
    function deleteTable(table_name) {
        database.transaction(function (tx) {
            var sql = "delete from " + table_name;

            tx.executeSql(
                sql,
                null,
                function (tx, rs) {
                    // 成功
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
                    quary = null;
                }
            );
        });
    }

    //
    //	CカードテーブルにICカード取込テーブルのレコードを追加
    //
    function margeIcCardLicenseTable() {
        database.transaction(function (tx) {
            var sql = "insert into " + IC_CARD_LICENSE_TABLE_NAME + " select * from " + T_IC_CARD_LICENSE_TABLE_NAME;

            tx.executeSql(
                sql,
                null,
                function (tx, rs) {
                    quary = null;
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
                    quary = null;
                }
            );
        });
    }

    //
    //	key=valueのフォーマットを連想配列に変換
    //
    function getArrayFromResponse(text) {
        var lines = text.split("\n");
        var obj = {};

        for (var no in lines) {
            var p;
            var line = lines[no];

            line = line.replace(/\r$/, ""); // 行末のCRは削除
            line = line.replace(/^[ \t]+/, ""); // 先頭の空白は削除

            // 空行は無視
            if (line.match(/^[ \t]*$/)) continue;

            if ((p = line.indexOf("=")) >= 0) {
                var keyname = line.substr(0, p).replace(/[ \t]+$/, "");
                var value = line.substr(p + 1, line.length - p - 1);

                obj[keyname] = value;
            }
        }

        return obj;
    }

    return false;
})();
