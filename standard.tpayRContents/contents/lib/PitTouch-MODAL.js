/*
 * ピットタッチ・プロ サンプルコンテンツ
 * モーダル ライブラリ
 * 
 * COPYRIGHT (C) 2011 B.U.G., INC.  ALL RIGHTS RESERVED.
 * 
 * @author B.U.G., INC.
 * @version 1.0
 * 
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 * 
 * このライブラリは、jQuery blockUI pluginを参考に作成したものである
 * jQuery blockUI pluginの著作権表示・ライセンス表記は、以下の通り
 *
 * Copyright (c) 2007-2010 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 * 
 */

// モーダルをCSSで実現するクラス
// ピットタッチ・プロで利用されることを目的としているため
// クロスブラウザ対応には、なっていない


PitTouch_MODAL = function()
{
}

PitTouch_MODAL.prototype = function()
{
	// static private variables
	var modalClassName = "PitTouch_MODAL";
	
	// Blockしているページ
	var pageBlock = null;
	// Option
	var optionsValue = {};
	
	var defaultOptions =
	{
		title: null,
		//
		blockMsgClass: 'blockMsg',
		
		// 
		css:
		{
			padding:	0,
			margin:		0,
			width:		'30%',
			top:		'40%',
			left:		'35%',
			textAlign:	'center',
			color:		'#000',
			border:		'3px solid #aaa',
			backgroundColor:'#fff',
		},
		
		// 
		overlayCSS:
		{
			backgroundColor: '#000',
			opacity:	  	 0.6,
		}
	};
	
	// private method
	
	// 画面のクリア
	var clearScreen = function (elements)
	{
		// 追加した要素の削除
		for (var i = elements.length - 1; i >= 0; i--)
		{
			var childs = elements[i].childNodes;

			// 退避
			for (var j = 0; j < childs.length; j++)
			{
				childs[j].style.display = 'none';
				document.body.appendChild(childs[j]);
			}
			
			document.body.removeChild(elements[i]);
		}
	}
	
	// イベントをバインドする
	var bind = function (isBind)
	{
		if (!isBind && !pageBlock)
			return;
		
		if (isBind)
		{
			document.addEventListener('mousedown', handler, false);
			document.addEventListener('mouseup', handler, false);
		}
		else
		{
			document.removeEventListener('mousedown', handler, false);
			document.removeEventListener('mouseup', handler, false);
		}
	}
	
	// バインドするイベントハンドラ
	var handler = function (e)
	{
		// メッセージを表示するレイヤーdivは、イベントを取得する
		if ((e.target.className.indexOf(modalClassName + " content")) >= 0)
		{
			// 何もしない
		}
		// その他 PitTouch_MODALのクラスは、イベントを阻害
		else if ((e.target.className.indexOf(modalClassName)) >= 0)
		{
			e.stopPropagation();
		}
		
	}

	return {
		modal : function(options)
		{
			// オプション操作
			optionsValue = extend(extend({},defaultOptions), options || {});
			optionsValue.overlayCSS = extend(extend({},defaultOptions.overlayCSS), optionsValue.overlayCSS || {});
			var css = extend(extend({},defaultOptions.css), optionsValue.css || {});

			var msg = optionsValue.message;

			// 現在モーダル状態であれば、解除
			if (pageBlock)
				this.unmodal();

			var z = 90000;

			// 影の部分を表示するレイヤーdiv
			var layer1 = document.createElement('DIV');
			// OPTION:影の部分を表示するレイヤーdivにスタイルを付加
			for (var i in optionsValue.overlayCSS)
			{
				layer1.style[i] = optionsValue.overlayCSS[i];
			}
			//
			layer1.className = modalClassName + " shadow";
			layer1.style["z-index"] = z++;
			layer1.style.display = 'none';
			layer1.style.border = 'none';
			layer1.style.margin = '0';
			layer1.style.padding = '0';
			layer1.style.width = '100%';
			layer1.style.height = '100%';
			layer1.style.top = '0';
			layer1.style.left = '0';
			layer1.style.position = 'fixed';

			
			// メッセージを表示するレイヤーdiv
			var layer2 = document.createElement('DIV');

			// OPTION:メッセージを表示するレイヤーdivにスタイルを付加			
			if (msg)
			{
				for (var i in css)
				{
					layer2.style[i] = css[i];
				}
			}
			// 
			layer2.className = modalClassName + " content";
			layer2.style["z-index"] = (z+10);
			layer2.style.display = 'none';
			layer2.style.position = 'fixed';


			layer2.style.position = 'fixed';

			// 各レイヤー要素の追加
			document.body.appendChild(layer1);
			document.body.appendChild(layer2);
			
			// 表示
			if (msg)
			{
				layer2.appendChild(msg);

				msg.style.display = 'block';
				layer1.style.display = 'block';

				if (msg)
					layer2.style.display = 'block';

			}

			// バインド
			bind(true);
			
			// ページを保存
			pageBlock = layer2;
		},

		unmodal : function()
		{
			// 
			bind(false);
			
			// 保存していたページのクリア
			pageBlock = pageBlockEls = null;
			
			// 画面のクリア
			var elements = document.getElementsByClassName(modalClassName);
			clearScreen(elements);
		},
		
		
		
		modalDialog : function (opt,css,overlaycss)
		{
			var defaultcss =
			{
				width:'440px',
				height:'232px',
				top:'20px',
				left:'20px',
				textAlign:'left',
			}

			var defaultOverlaycss =
			{
				opacity:0.6
			}
			
			var cssValue = extend(extend({},defaultcss), css || {});
			var overlaycssValue = extend(extend({},defaultOverlaycss), overlaycss || {});
			
			var el = document.getElementById(opt.elementName);
			var modal = this;
			
			for (var i in opt)
			{
				var el2 = document.getElementById(i);
				
				if (typeof opt[i] === "function")
				{
					// Callback登録
					el2.onclick = function()
					{
						var f = opt[this.id];
						
						if (f() == true)
							modal.unmodal();
						
						return true;
					}
				}
				else if (typeof opt[i] === "string")
				{
					if (i != "elementName")
					{
						// Message
						el2.innerHTML = opt[i];
					}
				}
			}
			
			modal.modal(
			{
				 message: el,
				 css : cssValue,
				 overlayCSS : overlaycssValue
			});
			
		},
		
	}
}();

