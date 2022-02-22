/*
 * 社員食堂精算管理システム
 * setting 設定画面
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

(function()
{
	window.onload = function(){ready()};

	var op = new ProOperateWrapper();
	var database = null;

	/**
	 *	初期処理
	 */
	function ready()
	{
		database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

		// ネットワーク状態表示
		showNetwork();

		// 設定取得
		getSetting();

		// イベント作成
		makeEvent();

		// ProOperateのコールバック登録
		var callback = {};
		callback.onEvent = updateEvent;		// 状態通知登録

		op.startEventListen(callback);
	};

	/**
	 *	状態更新
	 */
	function updateEvent(eventCode)
	{
		showNetwork();
	}

	/**
	 *	ネットワーク状態表示
	 */
	function showNetwork()
	{
		var netStat = op.getNetworkStat();

		if (netStat == 0)
			changeCSS("network","background-image","url('./css/image/Networkd.png')");
		else
			changeCSS("network","background-image","url('./css/image/Network.png')");
	}

	/**
	 *	DBから機器区分を取得
	 */
	function getSetting()
	{
		database.readTransaction(function (tx)
		{
			tx.executeSql('select * from ' + SETTING_TABLE_NAME, null,
			function (tx,rs)
			{
				// 成功
				var idivision = 1;
				var fno = 1;
				if (rs.rows.length == 0)
				{
					// データがない場合
				}
				else
				{
					// データがある場合
					idivision = rs.rows.item(0).idivision;
					fno = rs.rows.item(0).fno;
				}

				document.getElementById("id_idivision").value = idivision;
				document.getElementById("id_fno").value = fno;

				addClassName(document.getElementById('idivision' + idivision), 'active');
				addClassName(document.getElementById('fno' + fno), 'active');
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
			});
		});
	}

	/**
	 *	DBに機器区分を追加（DELETE→INSERT）
	 */
	function updateSetting(idivision, fno)
	{
		database.transaction(function (tx)
		{
			var sql = 'delete from ' + SETTING_TABLE_NAME;

			tx.executeSql(sql,
			[],
			function (tx,rs)
			{
				// 成功
				var sql2 = 'insert into ' + SETTING_TABLE_NAME + '(idivision, fno) values (?, ?)';

				tx.executeSql(sql2,
				[idivision, fno],
				function (tx,rs)
				{
					// 成功
					setTimeout(function()
					{
						back();
					}, 1000);
				}
				,function (tx,e)
				{
					// 失敗
					console.error('エラー' + e.message);
				});
			}
			,function (tx,e)
			{
				// 失敗
				console.error('エラー' + e.message);
			});
		});
	}

	/**
	 * イベント作成
	 */
	function makeEvent()
	{
		// 戻るボタン
		document.getElementById("back").addEventListener("click", function(e) {
			back();
		});

		// 保存ボタン
		document.getElementById("save").addEventListener("click", function(e) {
			save();
		});

		// ICカード取込ボタン
		document.getElementById("getcard").addEventListener("click", function(e) {
			importIcCard();
		});
	}

	/**
	 * 戻るボタン押下処理
	 */
	function back() {
		location.href = 'index.html';
	}

	/**
	 * 保存ボタン押下処理
	 */
	function save() {
		saveWaitDialog();

		var idivision = 1;

		var fnoObj = document.getElementsByClassName('fnoButton active');
		var fno = fnoObj[0].id;
		fno = fno.replace('fno', '');

		updateSetting(idivision, fno);
	}

	/**
	 *	画面：保存中モーダルダイアログ表示
	 */
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

})();
