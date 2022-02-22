/*
 * ピットタッチ・プロ サンプルコンテンツ
 * ProFileOperate Wrapper
 * ProFileOperateをPCで動作させるためのラッパークラス
 * ReferenceErrorが発生するので、代わりの値を決めている
 * 
 * COPYRIGHT (C) 2012 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 */

function ProFileOperateWrapper()
{	
}

ProFileOperateWrapper.prototype.write = function(param)
{
	/*
	 * profileoperate.jsがない環境(PC上など)で動作させる場合、
	 * resultの初期値がそのまま戻り値(書き込みデータ長)となります。
	 * 必要に応じて値を変更してください。
	 */
	var result = 0;
	
	try
	{
		var op = new ProFileOperate();
		result =  op.write(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:ProFileOperate.write");
		}
		else
			throw e;
	}
	
	return result;
};

ProOperateWrapper.prototype.read = function()
{
	/*
	 * profileoperate.jsがない環境(PC上など)で動作させる場合、
	 * resultの初期値がそのまま戻り値(読み込んだデータ)となります。
	 * 必要に応じて値を変更してください。
	 */
	var result = "";
	
	try
	{
		var op = new ProFileOperate();
		result =  op.read(param);
	}
	catch (e)
	{
		if (e.name == "ReferenceError")
		{
			console.info("ReferenceError:ProFileOperate.read");
		}
		else
			throw e;
	}
	
	return result;
};
