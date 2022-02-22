/*
 * ピットタッチ・プロ サンプルコンテンツ
 * SPIN ライブラリ
 * 
 * COPYRIGHT (C) 2011 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 * このライブラリは、jQuery Spinを参考に作成したものである
 * jQuery Spinの著作権表示・ライセンス表記は、以下の通り
 *
 * Copyright (c) 2009 Naohiko MORI
 * Dual licensed under the MIT and GPL licenses.
 * 
 */


// INPUT要素にSPINボタンを付加するクラス
// ピットタッチ・プロで利用されることを目的としているため
// クロスブラウザ対応には、なっていない


PitTouch_SPIN = function(el,opt)
{
	this.initialize.apply(this, arguments);
}

PitTouch_SPIN.prototype = function()
{
	// static private variables
	var defaultOptions =
	{
		// 画像定義
		imageBasePath: './lib/image/',
		spinButtonImage: 'spin-button.png',
		spinButtonUpImage: 'spin-up.png',
		spinButtonDownImage: 'spin-down.png',
		
		// 最小値・最大値・間隔
		interval: 1,
		max: null,
		min: null,
		
		// 繰り返し時間間隔
		timeInterval: 400,
		timeBlink: 200,
		
		// ボタンCSS
		spinButtonCSS:
		{
			padding: 0,
			margin: 0,
			verticalAlign: 'middle'
		},

		// テキストフィールドCSS
		textFieldCSS:
		{
			marginRight: 0,
			paddingRight: 0
		},
		
		// 値変更前、コールバック
		beforeChange: null,
		// textFieldの値変更コールバック呼出
		changed: null,
		// 上ボタン押下、コールバック
		buttonUp: null,
		// 下ボタン押下、コールバック
		buttonDown: null,
		
		// textFieldとスピンボタンの間隔
		btnoffset: 10,
		// beforeChange Callbackの戻り値でfalseだった場合でも
		// スピンイメージを変更する
		ignoreCallbackRes:false
	}

	// Instance variables
	this.inputElement = null;
	this.options = null;
	
	// "private" method

	return {
		// 初期化
		initialize : function(el,opt)
		{
			this.inputElement = el;
			this.options = opt;
		},
		
		// スピン有効
		spin : function()
		{
			var textField = this.inputElement;
			// オプション展開
			var optionsValue = extend(extend({},defaultOptions), this.options || {});
			
			// 画像展開
			var spinButtonImageSrc = optionsValue.imageBasePath + optionsValue.spinButtonImage;
			var spinButtonImage = new Image();
			spinButtonImage.src = spinButtonImageSrc;
			
			var spinButtonUpImageSrc = optionsValue.imageBasePath + optionsValue.spinButtonUpImage;
			var spinButtonUpImage = new Image();
			spinButtonUpImage.src = spinButtonUpImageSrc;
			
			var spinButtonDownImageSrc = optionsValue.imageBasePath + optionsValue.spinButtonDownImage;
			var spinButtonDownImage = new Image();
			spinButtonDownImage.src = spinButtonDownImageSrc;
			
			var spinButton = document.createElement('img');
			spinButton.setAttribute('src', optionsValue.imageBasePath + optionsValue.spinButtonImage);
			
			// Option:SPINボタンCSS
			if (optionsValue.spinButtonCSS)
			{
				for (var i in optionsValue.spinButtonCSS)
				{
					spinButton.style[i] = optionsValue.spinButtonCSS[i];
				}
			}
			
			// Option:textFieldCSS
			if (optionsValue.textFieldCSS)
			{
				for (var i in optionsValue.textFieldCSS)
				{
					this.inputElement.style[i] = optionsValue.textFieldCSS[i];
				}
			}
			
			// textFieldと同列にスピンボタンの要素を追加
			textField.parentNode.insertBefore(spinButton,textField.nextSibling);
//			textField.parentNode.appendChild(spinButton);
			
			// textFieldから、スピンボタンの配置を決定
			spinButton.style["position"] = getCSS(textField,"position");
			spinButton.style["top"] = getCSS(textField,"top");
			
			var left = eval(getCSS(textField,"left").replace('px','')) + eval(getCSS(textField,"width").replace('px','')) + optionsValue.btnoffset + "px";
			spinButton.style["left"] = left;
			spinButton.style["height"] = getCSS(textField,"height");
			
			// スピン処理のコールバック
			function doSpin(vector)
			{
				
				var textFieldValue = textField.value;
				var textFieldOldValue = textFieldValue;
				
				// 数値のみ
				if (!isNaN(textFieldValue))
				{
					// 間隔に基づき加減算
					textFieldValue = parseInt(textFieldValue, 10) + parseInt((vector * optionsValue.interval),10);
					
					// 最大値・最小値チェック
					if (optionsValue.min!==null && textFieldValue<optionsValue.min)
						textFieldValue=optionsValue.min;
					if (optionsValue.max!==null && textFieldValue>optionsValue.max)
						textFieldValue=optionsValue.max;
					
					// 値が元のものと変わっていたら
					if (textFieldValue != textFieldOldValue)
					{
						// 変わる前に、コールバック呼出
						var result = ((optionsValue.beforeChange) ? optionsValue.beforeChange.apply(textField, [textFieldValue, textFieldOldValue]) : true);
						
						// 変更許可の場合
						if (result !== false)
						{
							// textFieldの値変更
							textField.value = textFieldValue;
							
							// textFieldの値変更コールバック呼出
							if (optionsValue.changed)
								optionsValue.changed.apply(textField, [textFieldValue]);
							
							// textFieldへ、値変更イベント通知
							var event = document.createEvent("HTMLEvents");
							event.initEvent('change', true, false );
							textField.dispatchEvent(event);
							
							// 画像変更
							spinButton.setAttribute('src', (vector > 0 ? spinButtonUpImageSrc : spinButtonDownImageSrc));
							
							// Blink
							if ( optionsValue.timeBlink < optionsValue.timeInterval)
							{
								setTimeout(function()
								{
									// 画像変更
									spinButton.setAttribute('src', spinButtonImageSrc);
								},
								optionsValue.timeBlink
								);
							}
						}
						// Option:beforeChange Callbackの戻り値でfalseだった場合でも
						// スピンイメージを変更する
						else if (optionsValue.ignoreCallbackRes)
						{
							spinButton.setAttribute('src', (vector > 0 ? spinButtonUpImageSrc : spinButtonDownImageSrc));
							
							// Blink
							if (optionsValue.timeBlink < optionsValue.timeInterval)
							{
								setTimeout(function()
								{
									// 画像変更
									spinButton.setAttribute('src', spinButtonImageSrc);
								},
								optionsValue.timeBlink
								);
							}
						}
					}
				}
				
				if (vector > 0)
				{
					// 上ボタン押下、コールバック呼出
					if (optionsValue.buttonUp)
						optionsValue.buttonUp.apply(textField, [textFieldValue]);
				}
				else
				{
					// 下ボタン押下、コールバック呼出
					if (optionsValue.buttonDown)
						optionsValue.buttonDown.apply(textField, [textFieldValue]);
				}
			}
			
			// スピンボタンが押された処理
			spinButton.onmousedown = function (e)
			{
				// 押された場所の算出
				var offset = spinButton.getBoundingClientRect();
				var pos = e.pageY - offset.top;
				var vector = (spinButton.height /2 > pos ? 1 : -1);
				
				// 繰り返し処理のための無名関数
				(function()
				{
					// スピン処理
					doSpin(vector);
					
					// 繰り返し処理
					var timeoutID = setTimeout(arguments.callee, optionsValue.timeInterval);
					
					// 繰り返し処理終了のコールバック
					function clearSpin(event)
					{
						// mouseupイベント受信削除
						document.documentElement.removeEventListener('mouseup', clearSpin, false);
						
						// 繰り返し処理タイムアウトクリア
						clearTimeout(timeoutID);
						
						// 画像変更
						spinButton.setAttribute('src', spinButtonImageSrc)
					}
					
					// mouseupイベント受信
					document.documentElement.addEventListener('mouseup',clearSpin,false);
					
				})();

				return false;
			};
		},
	}
}();

