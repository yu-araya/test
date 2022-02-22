/*
 * ピットタッチ・プロ サンプルコンテンツ
 * CROSS FADE2 ライブラリ
 * 
 * COPYRIGHT (C) 2012 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under a Creative Commons License : http://creativecommons.org/licenses/by-sa/2.5/
 * 
 * このライブラリは、Crossfaderを参考に作成したものである
 * Crossfaderの著作権表示・ライセンス表記は、以下の通り
 *
 *  author:		Timothy Groves - http://www.brandspankingnew.net
 *
 * Licensed under a Creative Commons License : http://creativecommons.org/licenses/by-sa/2.5/
 *
 */

// DIV要素をクロスフェードするクラス
// ピットタッチ・プロで利用されることを目的としているため
// クロスブラウザ対応には、なっていない

PitTouch_CROSSFADE2 = function(className, fadetime)
{
	this.initialize.apply(this, arguments);
}

PitTouch_CROSSFADE2.prototype = function()
{
	// static private variables
	var my = null;
	
	// Instance variables
	// Div要素リスト
	this.divElements = null;
	// Fadeする時間
	this.fadeTime = 500;
	
	// FadeタイムアウトID
	this.fadeTimeoutID = -1;
	
	// 表示している画像Index
	this.state = -1;
	this.oldState = -1;
	
	// 
	this.passedTime = 0;
	this.intervalTime = 0;

	// private method
	var easeInOut = function(t,b,c,d)
	{
		return c/2 * (1 - Math.cos(Math.PI*t/d)) + b;
	}

	return {
		initialize : function(className, fadetime)
		{ 
			// 初期化
			this.divElements = document.getElementsByClassName(className);
			
			if (fadetime)
				this.fadeTime = fadetime;
			
			this.state = -1;
			
			// DIVにStyle設定
			if (!my)
			{
				for (var i=0;i<this.divElements.length;i++)
				{
					this.divElements[i].style.opacity = 0;
					this.divElements[i].style.position = "absolute";
					this.divElements[i].style.filter = "alpha(opacity=0)";
					this.divElements[i].style.visibility = "hidden";
				}
			}
		},
		// 次の画像を表示
		next : function()
		{
			var mySelf = this;

			// Indexのインクリメント
			mySelf.oldState = mySelf.state;
			mySelf.state++;
			
			// 最大値を超えたら繰り返す
			if (mySelf.divElements[mySelf.state] == null)
				mySelf.state = 0;
			// 1枚しかない
			if (mySelf.state == mySelf.oldState)
				return false;
				
			// DIV表示
			mySelf.divElements[mySelf.state].style.visibility = "visible";

			// 
			mySelf.intervalTime = 50;
			mySelf.passedTime = 0;
			
			
			// Fade登録
			mySelf.fadeTimeoutID = setInterval(function()
			{
				PitTouch_CROSSFADE2.prototype.fade(mySelf);
			},mySelf.intervalTime);
		},
		// Fade開始
		fade : function(_mySelf)
		{
			var mySelf = _mySelf;
			mySelf.passedTime += mySelf.intervalTime;
	
			var ieop = Math.round( easeInOut(mySelf.passedTime, 0, 1, mySelf.fadeTime) * 100 );
			var op = ieop / 100;
	
			// これからの画像をFadein
			mySelf.divElements[mySelf.state].style.opacity = op;
			mySelf.divElements[mySelf.state].style.filter = "alpha(opacity="+ieop+")";
			
			// 前の画像をFadeout
			if (mySelf.oldState > -1)
			{
				mySelf.divElements[mySelf.oldState].style.opacity = 1 - op;
				mySelf.divElements[mySelf.oldState].style.filter = "alpha(opacity="+(100 - ieop)+")";
			}
	
			if (mySelf.passedTime == mySelf.fadeTime)
			{
				// タイムアウトクリア
				clearInterval( mySelf.fadeTimeoutID );
				
				// 前の画像を隠す
				if (mySelf.oldState > -1)
					mySelf.divElements[mySelf.oldState].style.visibility = "hidden";
			}
		},
		// 次に表示されているdivオブジェクトの取得
		getNextShowImage : function()
		{
			var mySelf = this;
			var state = mySelf.state + 1;

			// 最大値を超えたら繰り返す
			if (mySelf.divElements[state] == null)
				state = 0;

			return mySelf.divElements[state];
		},
	}
}();

