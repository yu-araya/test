/*
 * ピットタッチ・プロ サンプルコンテンツ
 * ファームウェアバージョンチェック
 * 
 * COPYRIGHT (C) 2012 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */


function CheckVersion()
{
	/* ファームバージョンを取得 */
	try {
		this.firmver = new ProOperateWrapper().getFirmwareVersion();
	}
	catch (e)
	{
		console.info("CheckVersion: getFirmwareVersion exception");
		this.firmver = "";
	}
}

CheckVersion.prototype = {
	verCmp: function(ver1, ver2) {
		/*
		 * バージョン番号を比較しver1が新しければ正数、古ければ負数、
		 * 同じならば0、比較不能ならundefinedを返す。
		 * メジャーバージョン番号、マイナーバージョン番号のみ比較し、
		 * ベータ番号、リビジョン番号などは無視する。
		 */
		var ver = [ver1, ver2];
		var verNum = new Array();
		for (var i in ver) {
			verNum[i] = ver[i].match(/^[0-9]{1,2}.[0-9]{2}/);
			if (verNum[i] == null) {
				return; // undefined
			}
		}
		return (verNum[0] - verNum[1]);
	},
	
	isFirmNewer: function(version) {
		/* 
		 * ファームのバージョンが指定したものと同じか新しければ true を返す。
		 * 古ければfalse、比較不能の場合はundefined。
		 */
		var result = this.verCmp(this.firmver, version);
		if (result == undefined) {
			return; // undefined
		} else if (result >= 0) {
			return true;
		}
		return false;
	}	
};
