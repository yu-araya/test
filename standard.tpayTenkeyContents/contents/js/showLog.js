/*
 * 社員食堂精算管理システム
 * showLog ログ画面
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

(function()
{
	window.onload = function(){ready()};
	window.onunload = function(){ unload() };

	// static private variables
	var database = null;		// database object

	var page = 1;				// 現在の表示ページ
	var maxPage = 1;			// 最大ページ数
	var maxCount = -1;			// 件数

	const TABLE_ROW = 7;		// 1つのテーブルに表示する行数
	
	var timerId = 0;			// 書き込みリトライ用タイマーID
	var writeRetry = 0;			// 書き込みリトライ回数
	const writeRetryMax = 15;	// 書き込みリトライ最大回数

	// データベース名名
	const SYOKUDO_DATABASE_NAME	= "syokudo_database";
	// ログテーブル名
	const LOG_TABLE_NAME	= "log_menu_lunch";

	//
	//	初期処理
	///
	function ready()
	{	
		database = openDatabase(SYOKUDO_DATABASE_NAME,"",SYOKUDO_DATABASE_NAME, 1024*1024*2);
		
		// ページ計算/表示
		getLogCount(showPageCount);
				
		// ボタンのクリック動作
		
		// 右ボタン
		var obj = document.getElementById("right");

		obj.onclick = function (e)
		{
			nextPage();
		}

		// 左ボタン
		obj = document.getElementById("left");
		
		obj.onclick = function (e)
		{
			previousPage();
		}
		
		// 全削除ボタン
		obj = document.getElementById("clear");
		obj.onclick = function (e)
		{
			confirmDialog();
		}
		
		// 保存ボタン
		obj = document.getElementById("save");
		obj.onclick = function (e)
		{
			saveConfirmDialog();
		}
	}	

	//
	//	終了処理
	//
	function unload()
	{
		// 何もしない
	}

	//
	//	画面：ページを進める
	//
	function nextPage()
	{		
		page++;
		
		if (page > maxPage)
			page = 1;
		
		// 画面更新
		showPageCount();
	}

	//
	//	画面：ページを戻す
	//
	function previousPage()
	{

		page--;

		if (page < 1)
			page = maxPage;
		
		// 画面更新
		showPageCount();
	}

	//
	//	画面：ページ表示の更新
	//
	function showPageCount(count)
	{
		if (typeof count !== "undefined")
			maxCount = count;
		
		// 最大ページ数を計算
		maxPage = Math.ceil(maxCount / TABLE_ROW);
		
		if (maxPage == 0)
			maxPage = 1;
				
		changeHTML("screenTitle","ログ表示 " + maxCount + "件  （" + page + "/" + maxPage + "）");
		
		// ページを表示
		var from = (page - 1) * TABLE_ROW;
		getLogs(from,TABLE_ROW,showPage);
	}

	//
	//	画面：内容の更新
	///
	function showPage(rows)
	{
		var devisionList = ["その他"];
		var statusList = ["送信済み","未送信"];
	
		
		for (var i = 0; i < TABLE_ROW; i++)
		{
			if (i >= rows.length)
			{
				changeCSS("r" + i + "1","display","none");
				changeHTML("r" + i + "1"," ");

				changeCSS("r" + i + "2","display","none");
				changeHTML("r" + i + "2"," ");

				changeCSS("r" + i + "3","display","none");
				changeHTML("r" + i + "3"," ");

				changeCSS("r" + i + "4","display","none");
				changeHTML("r" + i + "4"," ");
			}
			else
			{
				var date = new Date();
				date.setTime(rows.item(i).createDate);
				var dateFormat = new DateFormat("yyyy/MM/dd HH:mm:ss");
				
				changeCSS("r" + i + "1","display","table-cell");
				changeHTML("r" + i + "1",dateFormat.format(date));

				changeCSS("r" + i + "2","display","table-cell");
				changeHTML("r" + i + "2",rows.item(i).cardID);

				changeCSS("r" + i + "3","display","table-cell");
				changeHTML("r" + i + "3",devisionList[0]);
//				changeHTML("r" + i + "3",devisionList[rows.item(i).division -1]);
				
				changeCSS("r" + i + "4","display","table-cell");
				changeHTML("r" + i + "4",statusList[rows.item(i).status]);
			}
		}
		changeCSS("logTable","visibility","visible");
	}

	//
	//	画面：モーダルダイアログ表示
	//
	function confirmDialog()
	{
		new PitTouch_MODAL().modalDialog(
		{
			elementName : "confirm",
			// 確認ダイアログ - はいボタン
			yes : function()
			{
				deleteAllColum(function ()
				{
					page = 1;
					getLogCount(showPageCount);
				});
				
				return true;
			},
			// 確認ダイアログ - いいえボタン
			no : function()
			{
				// 何もしない
				return true;
			},
		});
	}


	//
	//	ＤＢ：指定された行のログ内容を取得し、コールバックに通知する
	//
	function getLogs(from,to,update)
	{
		database.readTransaction(function (tx)
		{
			var sql = 'select * from ' + LOG_TABLE_NAME + ' order by createDate desc limit ' + from + ',' + to;
		
			tx.executeSql(sql,
			null,
			function (tx,rs)
			{
				// 成功
				update(rs.rows);
			}
			,function (tx,e)
			{
				// 失敗
				// その後の遷移は考慮しない
				console.error('エラー' + e.message);
			});
			
		});
	}

	//
	//	ＤＢ：現在の件数を取得し、件数をコールバックに通知する
	//
	function getLogCount(update)
	{		
		database.readTransaction(function (tx)
		{
			var sql = 'select count(id) from ' + LOG_TABLE_NAME + ';';

			tx.executeSql(sql,
			null,
			function (tx,rs)
			{
				// 成功
				var row = rs.rows.item(0);
				maxCount = row["count(id)"];
				
				update();
			}
			,function (tx,e)
			{
				// 失敗
				// その後の遷移は考慮しない
				console.error('エラー' + e.message);
			});
			
		});
		
	}

	//
	//	DBからすべての行を削除する
	//
	function deleteAllColum(update)
	{
		database.transaction(function (tx)
		{
			var sql = 'delete from ' + LOG_TABLE_NAME;

			tx.executeSql(sql,
			null,
			function (tx,rs)
			{
				// 成功
				changeCSS("logTable","visibility","hidden");
				update();
			}
			,function (tx,e)
			{
				// 失敗
				// その後の遷移は考慮しない
				console.error('エラー' + e.message);
			});
			
		});
	}


	//
	//	画面：保存確認モーダルダイアログ表示
	//
	function saveConfirmDialog()
	{
		new PitTouch_MODAL().modalDialog(
		{
			elementName : "saveconfirm",
			// 確認ダイアログ - はいボタン
			syes : function()
			{
				saveWaitDialog();
				saveLog();
				//return true;
			},
			// 確認ダイアログ - いいえボタン
			sno : function()
			{
				// 何もしない
				return true;
			},
		});
	}

	//
	//	画面：保存完了モーダルダイアログ表示
	//
	function saveFinishDialog(filename)
	{
		changeHTML("savedfilename","ログをUSBメモリに保存しました。<br><br>ファイル名：" + filename);

		new PitTouch_MODAL().modalDialog(
		{
			elementName : "savefinish",
			// 確認ボタン
			fok : function()
			{
				// 何もしない
				return true;
			},
		});
	}

	//
	//	画面：保存失敗モーダルダイアログ表示
	//
	function saveFailedDialog(err)
	{
		changeHTML("errormessage","ログの保存に失敗しました。<br><br>" + err);

		new PitTouch_MODAL().modalDialog(
		{
			elementName : "savefailed",
			// 確認ボタン
			eok : function()
			{
				// 何もしない
				return true;
			},
		});
	}

	//
	//	画面：ログ保存中モーダルダイアログ表示
	//
	function saveWaitDialog()
	{
		var obj = document.getElementById("savewait");
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
	}

	//
	// データをUSBメモリへ書き込む
	//
	function writeData(param)
	{
		var op = new ProFileOperateWrapper();
		try {
			var result = op.write(param);
		} catch(e) {
			if (e.name == 'USB memory is not mounted') {
				writeRetry++;
				if (writeRetry < writeRetryMax) {
					timerId = setTimeout(function() {writeData(param);}, 1000);
					return;
				}
			}
			console.error("write:" + e.name + e.message);
			saveFailedDialog(e.name + e.message);
			return;
		}
		if (result < 0) {
			console.error("write: detect error(" + result + ")");
			saveFailedDialog("");
		} else {
			// 書き込み完了ダイアログ表示
			saveFinishDialog(param.fileName);
		}
		
	}
	
	//
	//  ログ書き込み
	//
	function saveLog()
	{
		database.readTransaction(function (tx)
		{
			var sql = 'select * from ' + LOG_TABLE_NAME + ' order by createDate desc';

			// コンテンツセットUSB読み書きが使えるのは ver.2.30 から
			requireVer = "2.30";
			result = new CheckVersion().isFirmNewer(requireVer);
			if (result == undefined) {
				console.info('failed to check firmware version');
			} else if (!result) {
				// 未サポートファームウェア
				console.error('unsupported function: need newer than ver.' + requireVer);
				saveFailedDialog('このファームウェアでは未サポートです。<br>ver.' + requireVer + '以降のファームウェアが必要です。');
				return -1;
			}
			tx.executeSql(sql,
			null,
			function (tx,rs)
			{
				// 成功
				
				// パラメータ設定
				var param = {};
				
				// 書き込みモード：常に上書きモード
				param.isAppend = false;
				
				// 書き込みデータ
				param.data = "時刻, ID, 食事, 料金, ステータス" + '\r\n';
				
				for (var i = 0; i < rs.rows.length; i++) {
				
					var date = new Date();
					date.setTime(rs.rows.item(i).createDate);
					var dateFormat = new DateFormat("yyyy/MM/dd HH:mm:ss");
					var row = rs.rows.item(i);
					
					param.data += dateFormat.format(date) + ',';
					param.data += row.cardID + ',';
					param.data += row.division + ',';
					param.data += row.cost + ',';
					param.data += row.status + '\r\n';

				}
				//ファイル名生成：yyyyMMddHHmmss.log
				var current = new Date();
				var filenameFormat = new DateFormat("yyyyMMddHHmmss");
				param.fileName = filenameFormat.format(current) + '.log';
		
				/* データ書き込み(マウントされていないときは1秒ごとにリトライ、15回まで) */
				writeRetry = 0;
				writeData(param);
			}
			,function (tx,e)
			{
				// 失敗
				// その後の遷移は考慮しない
				console.error('エラー' + e.message);
				saveFailedDialog(e.message);
			});
			
		});
	}
})();


