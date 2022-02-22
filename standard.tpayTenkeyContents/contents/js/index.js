/*
 * 社員食堂精算管理システム
 * index トップ（金額入力）画面
 * キーパッド入力タイプ用
 *
 * COPYRIGHT (C) 2012 AGILECORE, INC.  ALL RIGHTS RESERVED.
 *
 * @author AGILECORE, INC.
 * @version 1.0
 *
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 */

(function () {
    window.onload = function () {
        ready();
    };
    window.onunload = function () {
        unload();
    };

    // static private variables
    // 送信先サーバ
    const SERVER_URL = "https://demo.touchpay.biz/TouchPay.standard/app/pitTouch/proc";

    // 送信先メソッド
    const SERVER_METHOD = "POST";
    // 送信先タイムアウト
    const SERVER_TIMEOUT = 3000; // 3秒
    // メッセージ表示時間
    const SHOW_MESSAGE_TIME = 10000; // 10秒
    // 再送間隔
    const RESEND_INTERVAL = 1000; // 1秒
    // 再送件数/再送検知間隔
    const RESEND_DETECT_INTERVAL = 5000; // 5秒
    // ログの最大件数
    const MAX_LOG = 10000;
    // 結果表示タイムアウト
    const SHOW_RESULT_TIMEOUT = 3000; // 3秒

    // 結果表示通信エラータイムアウト
    const SHOW_RESULT_SEND_ERR_TIMEOUT = 3000; // 3秒
    // 結果表示チェックエラータイムアウト
    const SHOW_RESULT_CHECK_ERR_TIMEOUT = 3000; // 3秒

    // エラー表示
    const STATUS_ERROR_BASE = 100; // 使用しない
    const STATUS_ERROR_KEYPAD = 101; // keypad抜けエラー

    // 金額入力桁数
    const INPUT_LIMIT_MONEY_LENGTH = 4; // 4桁入力

    // 0...入力画面表示
    const STATUS_INPUT_MONEY = 0;

    // 1...確認表示
    const STATUS_CONFIRM_NO_ID = 1;

    // 2...結果表示
    const STATUS_RESULT = 2;

    // 3...通信エラー表示
    const STATUS_ERR = 3;

    // 4...通信中
    const STATUS_COM = 4;

    // 5...チェックエラー表示
    const CHECK_ERR = 5;

    // 現在の入力値
    var inputValue = 0;

    // データベース名名
    const SYOKUDO_DATABASE_NAME = "syokudo_database";

    // ログテーブル名
    const LOG_TABLE_NAME = "log_menu_lunch";

    // ICカードテーブル名
    const IC_CARD_LICENSE_TABLE_NAME = "ic_card_license";

    // ICカード取込テーブル名
    const T_IC_CARD_LICENSE_TABLE_NAME = "t_ic_card_license";

    // 食事区分
    const FOOD_DVISION_C2 = "13";

    // 処理区分（ICカード件数取得）
    const IC_CARD_COUNT_DVISION = 2;
    // 処理区分（ICカード取得）
    const IC_CARD_INFO_DVISION = 3;
    // １リクエストに対するICカード取得件数
    const SELECT_CARD_COUNT = 100;

    // ICカード取得開始位置
    var startCardCount = 0;

    // ICカード取得カウント
    var cardCount = 0;

    // ICカード取込テーブル取込終了フラグ
    var T_IC_CARD_FINISH = true;

    var op = new ProOperateWrapper();
    var database = null;
    var division = -1;

    // クエリ情報
    var quary = null;
    // 送信クエリ
    var sendQuary = null;
    // 再送信クエリ
    var resendQuary = null;

    //有効カードIDエラーフラグ
    var check_error_flg = 0;

    // 登録有効ICカード
    var insCardid = null;

    // 再送タイムアウトID
    var reSendTimeoutID = -1;

    // 表示クリアタイムアウトID
    var displayClearTimeoutID = -1;

    // 消失検知フラグ
    var vanished = false;

    // Ajax終了フラグ
    var returnAjax = false;

    var currentValue = 0;

    var displayStatus = 0;

    //
    //	初期処理
    ///
    function ready() {
        // データベースの初期化
        initDatabase();

        changeCSS("log", "background-image", "url('./css/image/LogImage.gif')");

        // ネットワーク状態表示
        showNetwork();
        // keypad接続状態表示
        showKeypadIcon();

        // 時刻表示
        showTime();

        // ログ表示
        showLog();

        // 未送信データ件数の取得
        getUnsendDataCount();

        makeEvent();

        // ProOperateのコールバック登録
        var callback = {};
        callback.onEvent = updateEvent; // 状態通知登録

        op.startEventListen(callback);

        // ProOperateのコールバック登録
        var keypadCallback = {};
        keypadCallback.onKeyDown = keypadKeyDown; // keypad:キーが押された
        keypadCallback.onEvent = keypadEvent; // keypad:状態通知

        // keypad状態通知開始
        op.startKeypadListen(keypadCallback);

        // keypad接続状態チェック
        if (keypadCheck()) {
            // ステータス変更：エラー表示中
            changeDisplayStatus(STATUS_ERROR_KEYPAD);
            return;
        }

        //金額入力画面
        changeDisplayStatus(STATUS_INPUT_MONEY);
    }

    function makeEvent() {
        // ICカード取込ボタン
        document.getElementById("getcard").addEventListener("click", function (e) {
            initIcCardTable();
        });
    }

    function initIcCardTable() {
        // ステータス変更
        changeDisplayStatus(STATUS_COM);
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
        // ネットワーク状態の取得
        var netStat = op.getNetworkStat();

        if (netStat) {
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
                    outputInfoLog("キャッシュreadTransaction後");
                    // サーバ通信中：モーダル解除
                    new PitTouch_MODAL().unmodal();
                    //金額入力画面
                    changeDisplayStatus(STATUS_INPUT_MONEY);
                },
                function (
                    tx,
                    e // 失敗
                ) {
                    console.error("エラー" + e.message);
                    // サーバ通信中：モーダル解除
                    new PitTouch_MODAL().unmodal();
                    //金額入力画面
                    changeDisplayStatus(STATUS_INPUT_MONEY);
                }
            );
        });
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

                    returnAjax = true;
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

                    returnAjax = true;
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
    //	終了処理
    //
    function unload() {
        // kaypad：画面クリア
        clearKeyDisplay();
    }

    //
    //	時刻表示
    //
    function showTime() {
        var now = new Date();

        var dateFormat = new DateFormat("yyyy/MM/dd");
        var timeFormat = new DateFormat("HH:mm:ss");

        changeHTML("date", dateFormat.format(now));
        changeHTML("time", timeFormat.format(now));

        setTimeout(function () {
            showTime();
        }, 1000);
    }

    //
    //	ログ表示
    //
    function showLog() {
        changeCSS("log", "background-image", "url('./css/image/LogImage.gif')");
    }

    //
    //	打刻区分ボタン押下
    //
    function inputValue(obj) {
        // 押されたボタン要素
        var index = getElementIndexByClassName("button_down", obj);

        if (division == index) return;
        // ステータス変更
        changeDisplayStatus(STATUS_CONFIRM_NO_ID);
    }

    //
    //	状態更新
    //
    function updateEvent(eventCode) {
        showNetwork();
    }

    //
    //	ネットワーク状態表示
    ///
    function showNetwork() {
        var netStat = op.getNetworkStat();

        if (netStat == 0) changeCSS("network", "background-image", "url('./css/image/Networkd.png')");
        else changeCSS("network", "background-image", "url('./css/image/Network.png')");
    }

    //
    //	メッセージクリア
    ///
    function clearDisplayMessage() {
        changeHTML("message", "");
    }

    //
    //	非接触IC開始
    ///
    function startCom() {
        // 消失フラグと、Ajax終了フラグの初期化
        vanished = false;
        returnAjax = false;
        // FeliCaパラメータオブジェクト
        var felicaParam = {};
        felicaParam.systemCode = "FFFF"; // 読み出し対象のシステムコードを指定する

        var felicaAry = [felicaParam];

        // MIFAREパラメータオブジェクト
        var mifare1k = {};
        mifare1k.type = 1; // 検出するMIFARE種別。1:standerd 1k

        var mifare4k = {};
        mifare4k.type = 2; // 検出するMIFARE種別。2:standerd 4k

        var mifareUL = {};
        mifareUL.type = 3; // 検出するMIFARE種別。3:standerd UL

        var mifareAry = [mifare1k, mifare4k, mifareUL];

        // オプションパラメータ
        var param = {};

        // 成功音と失敗音はデフォルト音
        param.successLamp = "BB0N"; // 成功ランプ
        param.failLamp = "RR0S"; // 失敗ランプ
        param.waitLamp = "WW1L"; // 待ちうけランプ
        param.felica = felicaAry;
        param.mifare = mifareAry;
        param.onetime = true; // 1度だけ読む
        param.onEvent = onEventCommunication; // コールバック登録

        try {
            var result = op.startCommunication(param);
        } catch (e) {
            console.error("startCommunication:" + e.name + ":" + e.message);
        }
    }
    /**
     *	非接触IC通知
     */
    function onEventCommunication(eventCode, responseObject) {
        // 非接触ICを一旦停止
        try {
            var result = op.stopCommunication();
        } catch (e) {
            console.error("stopCommunication:" + e.name + ":" + e.message);
        }
        // 消失
        if (eventCode == 0) {
            vanished = true;
        }
        // 検出
        else if (eventCode == 1) {
            // メッセージ表示のクリア
            clearDisplayMessage();

            var idm = responseObject.idm;

            /*
             *未送信としてログをDBに作成、
             *ログが作成できたらAjax通信を開始、
             *未送信ログが上限に達していたらAjax通信は行わない
             */

            // 有効カードIDを取得
            database.readTransaction(function (tx) {
                var sql =
                    "select * from " +
                    IC_CARD_LICENSE_TABLE_NAME +
                    ' where upper(cardID) = "' +
                    idm.toUpperCase() +
                    '"';

                tx.executeSql(
                    sql,
                    null,
                    function (
                        tx,
                        rs // 成功
                    ) {
                        // 該当行がない場合
                        if (rs.rows.length == 0) {
                            outputInfoLog("未登録ICカード：" + idm.toUpperCase());
                            changeDisplayStatus(CHECK_ERR);
                            //エラー音出力
                            errVoice();
                        }
                        // 有効カードIDが存在する場合
                        else {
                            quary = makeQuary(idm, rs.rows.item(0).employeeId, rs.rows.item(0).employeeKbn);
                            deleteAndAddLog();
                        }
                    },
                    function (
                        tx,
                        e // 失敗
                    ) {
                        outputInfoLog("未登録ICカード：" + idm.toUpperCase());
                        changeDisplayStatus(CHECK_ERR);
                        //エラー音出力
                        errVoice();
                    }
                );
            });
        }
    }

    //
    //	DBのレコードチェックと作成
    //
    function deleteAndAddLog() {
        // ステータス変更
        changeDisplayStatus(STATUS_COM);

        // 最大件数をチェックし、最古のレコードを取得
        database.readTransaction(function (tx) {
            var sql =
                "select * from " +
                LOG_TABLE_NAME +
                " where (select count(id) from " +
                LOG_TABLE_NAME +
                " ) >= " +
                MAX_LOG +
                " and id = (select min(id) FROM " +
                LOG_TABLE_NAME +
                ");";

            tx.executeSql(
                sql,
                null,
                function (
                    tx,
                    rs // 成功
                ) {
                    // 該当行がない
                    if (rs.rows.length == 0) {
                        // ログの追加
                        addLog();
                    }
                    // 未送信
                    else if (rs.rows.item(0).status == 1) {
                        // alert：モーダル
                        new PitTouch_MODAL().modalDialog({
                            elementName: "confirm",
                            // 確認ボタン
                            yes: function () {
                                returnAjax = true;
                                // 非接触IC再開
                                if (vanished && returnAjax) {
                                    startCom();
                                }
                                return true;
                            },
                        });

                        quary = null;
                    }
                    // 送信済み
                    else {
                        // 削除へ
                        deleteLog(rs.rows.item(0).id);
                    }
                },
                function (
                    tx,
                    e // 失敗
                ) {
                    console.error("エラー" + e.message);

                    // サーバ通信中：モーダル解除
                    new PitTouch_MODAL().unmodal();

                    returnAjax = true;
                    // 非接触IC再開
                    if (vanished && returnAjax) {
                        startCom();
                    }
                }
            );
        });
    }

    //
    //	DB：レコード削除
    //
    function deleteLog(id) {
        database.transaction(function (tx) {
            var sql = "delete from " + LOG_TABLE_NAME + " where id = ?";

            tx.executeSql(
                sql,
                [id],
                function (tx, rs) {
                    // 成功
                    addLog();
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
                    quary = null;

                    returnAjax = true;
                    // 非接触IC再開
                    if (vanished && returnAjax) {
                        startCom();
                    }
                }
            );
        });
    }

    //
    //	DBにレコード作成
    //
    function addLog() {
        database.transaction(function (tx) {
            var sql =
                "insert into " +
                LOG_TABLE_NAME +
                "(createDate,modifyDate,cardID,division,cost,status) values (?,?,?,?,?,?)";

            tx.executeSql(
                sql,
                [quary.datetime, quary.datetime, quary.cid, quary.fdivision, quary.cost, quary.sendstat],
                function (tx, rs) {
                    //作成したレコードのid取得
                    quary.id = rs.insertId;

                    // 送信するクエリ情報
                    sendQuary = extend({}, quary);
                    //				delete sendQuary.division;
                    delete sendQuary.id;

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
                        // サーバ通信
                        var ajax = new PitTouch_AJAX();
                        ajax.sendRequest({
                            url: SERVER_URL,
                            data: sendQuary,
                            type: SERVER_METHOD,
                            dataType: "text",
                            timeout: SERVER_TIMEOUT,
                            success: function (data) {
                                // レスポンスをパースし、連想配列へ
                                var ary = getArrayFromResponse(data);
                                if (ary.dsp == "0") {
                                    ajaxSuccess(ary);
                                } else {
                                    changeDisplayStatus(CHECK_ERR);
                                    //エラー音出力
                                    errVoice();
                                }
                            },
                            error: function (msg) {
                                ajaxFail("送信に失敗");
                                //通信結果の表示
                                changeDisplayStatus(STATUS_ERR);
                            },
                        });
                    } else {
                        ajaxFail("offline");
                        changeDisplayStatus(STATUS_ERR);
                    }
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
                    quary = null;

                    returnAjax = true;
                    // 非接触IC再開
                    if (vanished && returnAjax) {
                        startCom();
                    }
                }
            );
        });
    }

    //
    //	ログを送信済みから未送信に変換
    //
    function sendFail() {
        // ログに記録、送信状態から未送信状態へ
        database.transaction(function (tx) {
            var now = new Date();
            var sql = "update " + LOG_TABLE_NAME + " set modifyDate=?,status=1 where id=?";

            tx.executeSql(
                sql,
                [now, quary.id],
                function (tx, rs) {
                    //成功
                    quary = null;
                },
                function (tx, e) {
                    // 失敗
                    quary = null;
                }
            );
        });
    }

    //
    //	サーバ通信成功
    //
    function ajaxSuccess(ary) {
        // c.f. BFR-421 サーバ送受信仕様書
        // レスポンスは、key=valueで送られてくる

        // res=XX					結果：2バイト固定 ASCII 16進数字 00：成功
        // snd=XXXX					音声：4バイト固定 ASCII 10進数字
        // lmp=XXXX					ランプ：2バイト固定 ASCII 10進数字、今回は送られない
        // sts=XX					ステータス変更：2バイト固定 ASCII 16進数字、今回は送られない
        // fnc=XX					機能実行：2バイト固定 ASCII 16進数字、今回は送られない

        // 独自拡張
        msg = "testエラー"; //	表示するメッセージ

        // 音声
        // snd=XXXX					音声：4バイト固定 ASCII 10進数字
        // XXXX+「.wav」のファイルを再生
        // 指定された音声ファイルパスを取得
        var soundPath = "./sound/" + ary.snd + ".wav";

        var soundparam = {};
        soundparam.filePath = soundPath;
        soundparam.loop = false;

        // 指定された音声を再生
        op.playSound(soundparam);

        // メッセージ表示
        changeHTML("message", ary.msg);

        if (displayClearTimeoutID != -1) clearTimeout(displayClearTimeoutID);
        displayClearTimeoutID = setTimeout(function () {
            clearDisplayMessage();
            displayClearTimeoutID = -1;
        }, SHOW_MESSAGE_TIME);

        quary = null;

        // サーバ通信中：モーダル解除
        new PitTouch_MODAL().unmodal();
        returnAjax = true;

        //通信結果の表示
        changeDisplayStatus(STATUS_RESULT);
    }

    //
    //	画面：状態の変更
    //
    function changeDisplayStatus(newStatus) {
        displayStatus = newStatus;

        // keypad表示更新
        updateKeyDisplay();
        // 画面更新
        updateMessage();
    }

    //
    //	画面：更新
    //
    function updateMessage() {
        var resultValue = currentValue + parseInt(inputValue);
        currentValue = resultValue > 9999 ? currentValue : resultValue;
        var showValue = "    " + currentValue + " 円";
        if (displayStatus == STATUS_INPUT_MONEY) {
            // 0...金額入力画面
            changeHTML("point2", showValue);
            changeCSS("mes2", "visibility", "visible");
            changeCSS("point2", "visibility", "visible");
        } else if (displayStatus == STATUS_CONFIRM_NO_ID) {
            // 1...確認画面
            confirmDialogUnder("reconfirm2");
        } else if (displayStatus == STATUS_RESULT) {
            // 2...結果表示
            changeCSS("mes3", "visibility", "visible");

            // 指定時間経過後トップ画面に戻る
            timerID = setTimeout(function () {
                location.href = "index.html";
            }, SHOW_RESULT_TIMEOUT);
        } else if (displayStatus == STATUS_ERR) {
            // 3...通信エラー画面
            confirmDialogUnder("requesterr");
            // 指定時間経過後トップ画面に戻る
            timerID = setTimeout(function () {
                location.href = "index.html";
            }, SHOW_RESULT_SEND_ERR_TIMEOUT);
        } else if (displayStatus == CHECK_ERR) {
            // 5...チェックエラー画面
            confirmDialogUnder("checkerr");
            // 指定時間経過後トップ画面に戻る
            timerID = setTimeout(function () {
                location.href = "index.html";
            }, SHOW_RESULT_CHECK_ERR_TIMEOUT);
        }
    }

    //
    //	サーバ通信失敗
    //
    function ajaxFail(msg) {
        //ログを送信済みから未送信に変換
        sendFail();

        // サーバ通信中：モーダル解除
        new PitTouch_MODAL().unmodal();

        returnAjax = true;
        // 非接触IC通信再開
        if (vanished && returnAjax) {
            startCom();
        }
    }

    //
    //	DBの初期化
    //
    function initDatabase() {
        database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, 1024 * 1024 * 2);

        if (database != null) {
            const sql1 =
                "create table if not exists " +
                LOG_TABLE_NAME +
                "(id integer primary key autoincrement,createDate integer not null,modifyDate integer not null,cardID text not null,division integer not null,cost integer not null,status integer not null)";

            const sql2 =
                "create table if not exists " +
                IC_CARD_LICENSE_TABLE_NAME +
                "(" +
                "cardID text not null," +
                "employeeId text not null," +
                "employeeKbn text not null" +
                ")";

            const sql3 =
                "create table if not exists " +
                T_IC_CARD_LICENSE_TABLE_NAME +
                "(" +
                "cardID text not null," +
                "employeeId text not null," +
                "employeeKbn text not null" +
                ")";

            database.transaction(function (tx) {
                executeSql(tx, sql1);
                executeSql(tx, sql2);
                executeSql(tx, sql3);
            });
        }
    }

    function executeSql(tx, sql) {
        tx.executeSql(
            sql,
            null,
            function (t, rs) {
                return true;
            },
            function (t, e) {
                console.error("エラー" + e.message);
                return false;
            }
        );
    }

    //
    //	クエリ作成
    //
    function makeQuary(idm) {
        // tid=[TID]&cid=[CID]&tim=[TIME]&typ=[TYPE]&sts=[STS]&sendstat=[TX]

        // [TID] 端末ID
        // [CID] 非接触IC ID
        // [TIME] 時刻情報 YYYYMMDDhhmmss、14バイト固定 ASCII 10進数字
        // [TYPE] 非接触IC種別
        // [STS] ステータス 2バイト固定 ASCII 16進数字 01-04
        // [TX] 送信状態 1バイト固定 ASCII 10進数字 0:通常送信 1:再送通信

        var now = new Date();
        var format = new DateFormat("yyyyMMddHHmmss");
        var tid = op.getTerminalID();

        var data = {};
        data.datetime = now.getTime(); // 打刻時刻 DB用

        data.tid = tid; // 端末ID
        data.cid = idm; // カードID
        data.tim = format.format(now); // 打刻時刻 YYYYMMDDhhmmss
        data.sts = ("0" + (division + 1)).slice(-2); // 打刻区分 01-04
        data.typ = "00"; // カードタイプ
        data.sendstat = 0; // 再送ステータス
        data.fdivision = FOOD_DVISION_C2; // 食事区分 DB用
        data.cost = currentValue; // 金額
        data.cardkbn = 8;

        return data;
    }

    //
    //	未送信データ件数の取得
    //
    function getUnsendDataCount() {
        database.readTransaction(function (tx) {
            var sql = "select count(*) from " + LOG_TABLE_NAME + " where status = 1";

            tx.executeSql(
                sql,
                null,
                function (tx, rs) {
                    // 成功
                    var row = rs.rows.item(0);
                    var count = row["count(*)"];

                    changeHTML("senddata", "未送信データ：" + count + "件");

                    setTimeout(function () {
                        getUnsendDataCount();
                    }, RESEND_DETECT_INTERVAL);

                    // 再送の処理を起動
                    if (count > 0 && reSendTimeoutID == -1)
                        reSendTimeoutID = setTimeout(function () {
                            reSend();
                        }, RESEND_INTERVAL);
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);
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

    //
    //	再送処理
    //
    function reSend() {
        database.readTransaction(function (tx) {
            tx.executeSql(
                "select * from " + LOG_TABLE_NAME + " where status = 1 order by createDate asc limit 1;",
                null,
                function (tx, rs) {
                    if (rs.rows.length == 0) {
                        // 未送信がない場合
                        reSendTimeoutID = -1;
                        // getUnsendDataCountで、未送信を検知
                    } else {
                        // 未送信がある場合
                        reSendData(rs.rows.item(0));
                    }
                },
                function (tx, e) {
                    // 失敗
                    console.error("エラー" + e.message);

                    reSendTimeoutID = -1;
                }
            );
        });
    }

    //
    //	再送処理
    //
    function reSendData(item) {
        resendQuary = makeReSendQuary(item);

        // サーバ通信
        var ajax = new PitTouch_AJAX();
        ajax.sendRequest({
            url: SERVER_URL,
            data: resendQuary,
            type: SERVER_METHOD,
            dataType: "text",
            timeout: SERVER_TIMEOUT,
            success: function (data) {
                ajaxreSendSuccess(data);
            },
            error: function (msg) {
                ajaxreSendFail(msg);
            },
        });
    }

    //
    //	再送信：クエリ作成
    ///
    function makeReSendQuary(item) {
        // tid=[TID]&cid=[CID]&tim=[TIME]&typ=[TYPE]&sts=[STS]&sendstat=[TX]

        // [TID] 端末ID
        // [CID] 非接触IC ID
        // [TIME] 時刻情報 YYYYMMDDhhmmss、14バイト固定 ASCII 10進数字
        // [TYPE] 非接触IC種別
        // [STS] ステータス 2バイト固定 ASCII 16進数字 01-04
        // [TX] 送信状態 1バイト固定 ASCII 10進数字 0:通常送信 1:再送通信

        var date = new Date();
        date.setTime(item.createDate);
        var format = new DateFormat("yyyyMMddHHmmss");
        var tid = op.getTerminalID();

        var data = {};
        data.id = item.id; // シーケンスID DB用

        data.tid = tid; // 端末ID
        data.cid = item.cardID; // カードID
        data.tim = format.format(date); // 打刻時刻 YYYYMMDDhhmmss
        data.sts = ("0" + (item.division + 1)).slice(-2); // 打刻区分 01-04
        data.typ = "00"; // カードタイプ
        data.sendstat = 1; // 再送ステータス
        data.fdivision = item.division; // 食事区分 DB用
        data.cost = item.cost; // 金額
        data.cardkbn = 8;

        return data;
    }

    //
    //	再送信：サーバ通信成功
    //
    function ajaxreSendSuccess(data) {
        // ログに記録
        updateLog();
    }

    //
    //	再送信：サーバ通信失敗
    //
    function ajaxreSendFail(msg) {
        //		console.error("ajaxreSendFail error: " + msg);

        // 何もしない
        // getUnsendDataCountで、未送信を再検知
        resendQuary = null;
        reSendTimeoutID = -1;
    }

    //
    //	DBのレコードを更新
    //
    function updateLog() {
        if (database != null) {
            database.transaction(function (tx) {
                var now = new Date();
                var sql = "update " + LOG_TABLE_NAME + " set modifyDate=?,status=0 where id=?";

                tx.executeSql(
                    sql,
                    [now, resendQuary.id],
                    function (tx, rs) {
                        resendQuary = null;

                        // 再送を起動
                        setTimeout(function () {
                            reSend();
                        }, RESEND_INTERVAL);
                    },
                    function (tx, e) {
                        // 失敗
                        // 更新に失敗しているので、いつまでも再送し続ける
                        resendQuary = null;
                        reSendTimeoutID = -1;
                    }
                );
            });
        }
    }

    //
    //	ネットワーク状態表示
    ///
    function showNetwork() {
        var netStat = op.getNetworkStat();

        if (netStat == 0) changeCSS("network", "background-image", "url('./css/image/Networkd.png')");
        else changeCSS("network", "background-image", "url('./css/image/Network.png')");
    }

    //
    //	keypad:表示状態の更新
    //
    function updateKeyDisplay() {
        var displayParam = {};
        var lightParam = {};

        if (displayStatus == STATUS_INPUT_MONEY) {
            // 0...入力画面
            //			var showValue = ("        " + inputValue).slice(-8);
            var showValue = ("    " + inputValue).slice(-4);

            displayParam.firstLine = "                ";
            //			displayParam.secondLine = "ﾘﾖｳ\\?   " + showValue;
            displayParam.secondLine = "ﾘﾖｳ\\?       " + showValue;
            displayParam.backLight = true;

            if (currentValue == 0 && inputValue == 0) lightParam.light = "000000";
            else lightParam.light = "000010"; // 次へ
        } else if (displayStatus == STATUS_CONFIRM_NO_ID) {
            // 1...確認画面
            displayParam.firstLine = "ｶｻﾞｼﾃｸﾀﾞｻｲ      ";
            displayParam.secondLine = "                ";
            displayParam.backLight = false;

            lightParam.light = "000000";
        } else if (displayStatus == STATUS_COM) {
            // 4...通信中
            displayParam.firstLine = "ﾂｳｼﾝｼﾃｲﾏｽ       ";
            displayParam.secondLine = "ｵﾏﾁｸﾀﾞｻｲ        ";
            displayParam.backLight = false;

            lightParam.light = "000000";
        } else if (displayStatus == CHECK_ERR) {
            // 5...チェックエラー
            displayParam.firstLine = "ﾘﾖｳﾃﾞｷﾅｲｶｰﾄﾞﾃﾞｽ ";
            displayParam.secondLine = "ｶｸﾆﾝｼﾃｸﾀﾞｻｲ     ";
            displayParam.backLight = false;

            lightParam.light = "000000";
        }

        // keypad:display更新
        op.setKeypadDisplay(displayParam);
        // keypad:LED更新
        op.setKeypadLed(lightParam);
    }

    //
    //	keypad:キーが押された
    //
    function keypadKeyDown(keyCode) {
        switch (keyCode) {
            case 111: // 終了
                // 終了は、通信中以外有効
                if (displayStatus != STATUS_COM) location.href = "../index.html";
                break;
            case 107: // 次へ
                updateMessage();
                inputValue = 0;
                updateKeyDisplay();
                break;
            case 13: // 決定
                if (currentValue != 0 || inputValue != 0) {
                    // 決定は、入力中のみ有効
                    updateMessage();

                    if (displayStatus == STATUS_INPUT_MONEY) {
                        inputValue = 0;
                        // 照会がない場合
                        // ステータス変更
                        changeDisplayStatus(STATUS_CONFIRM_NO_ID);

                        // 非接触IC開始
                        startCom();
                    }
                }
                break;
            case 109: // 戻る
                // 戻るは、確認画面のみ有効
                if (displayStatus == STATUS_CONFIRM_NO_ID) {
                    inputValue = 0;
                    // 確認画面：モーダル解除
                    new PitTouch_MODAL().unmodal();

                    //非接触IC通信の停止
                    op.stopCommunication();

                    if (displayStatus == STATUS_CONFIRM_NO_ID) {
                        changeDisplayStatus(STATUS_INPUT_MONEY);
                    }
                }
                break;
            case 106: // クリア
                // クリアは、入力中のみ有効
                if (displayStatus == STATUS_INPUT_MONEY) {
                    // 入力値をクリア
                    inputValue = 0;

                    // 更新
                    updateMessage();
                    updateKeyDisplay();
                }
                break;
            case 58: // 00
            // 00は、入力中のみ有効
            default:
                // 数字
                // 数字は、入力中のみ有効
                if ((keyCode - 48 >= 0 && keyCode - 48 <= 9) || keyCode == 58)
                    if (displayStatus == STATUS_INPUT_MONEY) {
                        if (keyCode == 58) {
                            // 00の場合
                            if (inputValue != 0 && String(inputValue).length < INPUT_LIMIT_MONEY_LENGTH - 1) {
                                inputValue = inputValue + "00";
                            }
                        } else {
                            var check_inputValue = String(inputValue);
                            // 4桁数値入力
                            if (check_inputValue.length < INPUT_LIMIT_MONEY_LENGTH) {
                                // 0-9の数値の場合
                                inputValue = parseInt((inputValue === 0 ? "" : inputValue) + String(keyCode - 48));
                            }
                        }

                        // 更新
                        updateKeyDisplay();
                    }

                break;
        }
    }

    //
    //	keypad:状態通知
    //
    function keypadEvent(eventCode) {
        // 既にエラー
        if (displayStatus > STATUS_ERROR_BASE) {
            // 再接続
            if (eventCode == 1 && displayStatus == STATUS_ERROR_KEYPAD) {
                // Top画面に戻る
                location.href = "index.html";
            }
        }
        // keypad接続状態チェック
        else if (keypadCheck()) {
            changeDisplayStatus(STATUS_ERROR_KEYPAD);
        }
    }

    function errVoice() {
        // 指定された音声ファイルパスを取得
        var soundPath = "./sound/pipipipi.wav";

        var soundparam = {};
        soundparam.filePath = soundPath;
        soundparam.loop = false;

        op.playSound(soundparam);
    }

    /**
     * インフォメーションログをコンソールに出力
     */
    function outputInfoLog(message) {
        var now = new Date();
        var dateFormat = new DateFormat("yyyy/MM/dd");
        var timeFormat = new DateFormat("HH:mm:ss");

        console.log(dateFormat.format(now) + " " + timeFormat.format(now) + " [INFO]" + message);
    }
})();
