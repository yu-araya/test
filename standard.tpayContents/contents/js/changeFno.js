(function()
{
	var database = null;

	database = openDatabase(SYOKUDO_DATABASE_NAME, "", SYOKUDO_DATABASE_NAME, DATABASE_SIZE);

	console.log('change fno start');
	var date = null;
	if(CHANGE_FNO_VALID){
		var intervalId = setInterval(function(){
			CHANGE_FNO.forEach(function(element){
				var idivision = element[0];
				var fno = element[1];
				var hh = element[2];
				var mm = element[3];
				date = new Date();
				if(date.getHours() == hh && date.getMinutes() == mm) {
					var op = new ProOperateWrapper();
					try
					{
						var result = op.stopCommunication();
					}
					catch(e)
					{
						console.error("stopCommunication:" + e.name + ":" + e.message);
					}

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
									location.href = 'index.html';
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
				} else {
					console.log('out');
				}
			});
		}, 15000);
	}
})();
