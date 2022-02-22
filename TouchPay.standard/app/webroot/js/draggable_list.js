
/*

	draggable_list 1.0

	マウスで並べ替え可能なテーブルをulタグ+liタグで生成するためのライブラリ
	
	特徴
		・マウスのドラッグ・ドロップで，行を並べ替えできる(scriptaculousのSortable)
		・任意の行を「固定行」にすることが可能
		・行の追加・削除が可能
		・ドラッグ・ドロップ終了時に，行が光る

*/

var DraggableList = function( options )
{
	// オプションを解析
	this.ul_id                 = options.target_ul_id;
	this.sortable_li_class     = options.sortable_li_class;
	this.li_original_color     = options.li_original_color;
	this.li_highlight_color    = options.li_highlight_color;
	this.li_highlight_duration = options.li_highlight_duration;
	if( options.li_appear_duration != null )
	{
		this.li_appear_duration = options.li_appear_duration;
	}
	if( options.li_disappear_duration != null )
	{
		this.li_disappear_duration = options.li_disappear_duration;
	}
	if( options.onDragFinish != null )
	{
		this.onDragFinish = options.onDragFinish;
	}

	// リスト作成
	this._create();
	
	// 初回のデータセットアップ
	this._init_data_setup();
};
DraggableList.prototype = {


	/* ---------- 設定事項 ---------- */


	// 適用したいul要素のID
	ul_id : null,

	// ソート可能なliのCSSクラス
	sortable_li_class : null,
	
	// li要素の地の色（ハイライト後に戻す色）
	li_original_color : null,
	
	// li要素のハイライト時の色
	li_highlight_color : null, 
	
	// li要素のハイライトに要する時間
	li_highlight_duration : null,
	
	// li要素の新規追加のフェードに要する時間
	li_appear_duration : 0.2,
	
	// li要素の削除のフェードに要する時間
	li_disappear_duration : 0.2,
	
	// ドラッグドロップが終了したときのコールバック関数
	onDragFinish : null,
	

	/* ---------- 内部変数 ---------- */


	// 前回実行されたエフェクト
	_change_effect : null,
	
	// 現在エフェクトが発生中のli要素
	_current_effecting_li : null,
	
	// 初回読み込み時の構成要素の行数
	_static_max_id_number : 0,
	
	// エフェクトのフレームレート
	_default_fps : 25,


	/* ---------- メソッド：エフェクト関連 ---------- */

	
	// 前回発生したhighlightをキャンセル
	_cancel_previous_highlight : function()
	{
		if( this.change_effect != null )
		{
			// 停止させる
			this._cancel_previous_effect();
			
			// 前回移動した行の色を即戻す
			new Effect.Highlight(
				this.current_effecting_li, 
				{
					restorecolor : this.li_original_color,
					duration     : 0 
				}
			);
		}
	},

	// 前回発生したエフェクトをキャンセル
	_cancel_previous_effect : function()
	{
		if( this.change_effect != null )
		{
			this.change_effect.cancel();
			this.change_effect = null;
		}
	},

	// li要素をハイライト
	_highlight_item : function( li_item )
	{
		// エフェクトを発生させ，登録
		this.change_effect = new Effect.Highlight(
			li_item, 
			{
				startcolor : this.li_highlight_color, 
				duration   : this.li_highlight_duration 
			}
		);
		// 発生中の要素を登録
		this.current_effecting_li = li_item;
	},


	/* ---------- メソッド：行要素関連 ---------- */


	// リストの構成要素から，ID末尾の番号を取得します。
	// 取得できない場合はnullを返します。
	_get_id_number : function( elem )
	{
		if( elem.id.match(/.*_([0-9]*)$/) != null )
		{
			return parseInt( RegExp.$1, 10 );
		}
		else
		{
			return null;
		}
	}
	,

	// リストの構成要素（並べ替え可能要素＋並べ替え不可能要素）かどうか判定します。
	// 一番先頭の見出し行は，通常含まれません。
	_is_listed_item : function( elem )
	{
		// idから並び順を取り出すことができれば対象に含める
		return ( this._get_id_number( elem ) != null );
	}
	,
	
	// ul内の全li要素を返します。
	_get_all_li : function()
	{
		var lis = $$( "#" + this.ul_id + " > li" );
		return lis;

	}
	,
	
	// リストの全構成要素を返します。
	_get_all_listed_item : function()
	{
		var lis = this._get_all_li();
		var c_this = this;
		var all_listed_item = lis.inject( [], function( res, item, idx ){
			// 全liに対して判定対して
			if( c_this._is_listed_item( item ) )
			{
				res.push( item );
			}
			return res;
		});
		return all_listed_item;
	}
	,
	
	// リストの構成要素の数を返します。
	_how_many_listed_items : function()
	{
		return this._get_all_listed_item().length;
	}
	,
	
	// リストの全構成要素の中から，位置固定のものをすべて返します。
	_get_all_fixed_item : function()
	{
		return this._get_all_listed_item().select(function( item ){
			return ( item.getAttribute("fixed_order") != null ) ? true : false;
		});
	}
	,

	// ID末尾の数字を指定して，それにマッチした構成要素を1つ取得
	_get_elem_by_id_number : function( id_number )
	{
		var temp_arr = this._get_all_listed_item().select(function( elem ){
			if( elem.id.match( new RegExp( "_" + id_number + "$" ) ) ){
				return true;
			}
			else
			{
				return false;
			}
		});
		var next_element = temp_arr[0];
		
		return next_element;
	}
	,
	
	// 現存する構成要素のID末尾の数字の中から，最大のものを取得。
	// 行が無ければ，
	//   初回読み込み時は0を返却。
	//   初回以降は，初回に存在した最大の数字を返却。
	// (新規行追加時の便宜のために)
	_get_max_id_number : function()
	{
		var all_sequence = this._get_all_sequence();
		if( all_sequence.length > 0 )
		{
			return all_sequence.max();
		}
		else
		{
			return this._static_max_id_number;
		}
	}
	,


	/* ---------- メソッド：並び順関連 ---------- */


	// 移動不可要素を含めたリスト全構成要素の現在の並び順を取得
	// ※Sortable.sequence(ul_id)では移動可能要素しか出てこないので
	_get_all_sequence : function()
	{
		var c_this = this;
		var all_sequence = this._get_all_listed_item().map(function(item){
			// IDから並び順を取り出す
			return c_this._get_id_number( item );
		});
		return all_sequence;
	}
	,
	
	// 要素の（本来あるべき）固定順を取得
	_get_expected_fix_position : function( elem )
	{
		return parseInt( elem.getAttribute("fixed_order"), 10 ) - 1;
	}
	,
	
	// 要素の現実の位置を取得
	_get_real_position : function( elem )
	{
		var li_id_number = this._get_id_number( elem );
		var all_sequence = this._get_all_sequence();
		var real_position = all_sequence.indexOf( li_id_number );
		
		return real_position;
	}
	,

	// 配列を渡すことにより，現在の並び順を検証します。（テスト用）
	// 使い方：dl._check_sequence([100,200,300,400,500,600])
	_check_sequence : function( expected )
	{
		var real = this._get_all_sequence();
		if( expected.length != real.length )
		{
			return false;
		}
		for( var i = 0; i < real.length; ++i )
		{
			if( real[i] != expected[i] )
			{
				return false;
			}
		}
		
		return true;
	}
	,


	/* ---------- メソッド：固定要素の位置修正関連 ---------- */


	// 固定要素を直前に挿入すべき要素を返します
	_get_next_elem_of_fixed_elem : function( expected_position, real_position )
	{
		// 位置を取得
		var next_element_position = expected_position;
			//alert(next_element_position + "番目の要素の手前に挿入しなおせばよいと判断（※元の固定要素は削除済み）");
		
		// その位置にある要素のID末尾の数字を取得
		var all_sequence = this._get_all_sequence();
		var next_element_id_number = all_sequence[ next_element_position ];
		
		// その要素自体を取得
		var next_element = this._get_elem_by_id_number( next_element_id_number );
		
		return next_element;
	}
	,

	// 固定要素の位置を修正します
	_restore_fixed_position : function( item, expected_position, real_position )
	{
		// 末尾の場合は特別視する
		var current_last_index = this._how_many_listed_items() - 1;
	
		// 削除
		var clone = item;
		item.parentNode.removeChild( item );
			//alert(item.id + "の位置を修正すべく，元要素を削除");

		// 複製物を挿入
		if( expected_position == current_last_index )// 末尾要素の場合
		{
			// 末尾に挿入
				//alert("末尾に挿入します");
			Insertion.Bottom( this.ul_id, clone );
		}
		else
		{
			// 固定要素の次にくるべき要素を探す
			var next_element = this._get_next_elem_of_fixed_elem( expected_position, real_position );
				//alert(next_element.id + "の直前に" + clone.id + "を挿入");
				
			Insertion.Before( next_element.id, clone );
		}
	}
	,

	// 1つの固定要素の位置がずれていたら修正
	_adjust_fixed_item : function( elem )
	{
		// この要素の本来あるべき位置
		var expected_position = this._get_expected_fix_position( elem );

		// この要素の現実の位置
		var real_position = this._get_real_position( elem );
			//alert( elem.id + "は固定要素。本来の位置" + expected_position + ", 現在" + real_position );
		
		// 固定されているべき要素が移動したか？
		if( expected_position != real_position )
		{
			// これから移動しようとしている位置を補正(行が削除されてしまった場合など)
			var max_position = this._how_many_listed_items() - 1;
			if( expected_position > max_position )
			{
					//alert( "移動先を" + expected_position + "から" + max_position + "に調整" );
				expected_position = max_position;
			}
		
			// 位置を補正
			this._restore_fixed_position( elem, expected_position, real_position );
		}
	}
	,

	// 全固定要素の位置がずれていたら補正
	_adjust_all_fixed_items : function()
	{
		var c_this = this;
		// 全固定要素に対して
		this._get_all_fixed_item().each(function( item ){
			c_this._adjust_fixed_item( item );
		});
	},


	/* ---------- メソッド：全体の初期化 ---------- */


	// リストを作成
	_create : function()
	{
		with({ c_this : this })
		{
			Sortable.create( c_this.ul_id, {
				tag      : "li",
				only     : c_this.sortable_li_class,
				ghosting : false, // trueだとdrop終了が滑らかにならない
				overlap  : "vertical",
				constraint  : "vertical",
				
				// ドラッグ開始時
				starteffect: function() {
					// デフォルトの半透明のエフェクトは発生させない
					
					// 前回発生したエフェクトがまだ残っているかもしれないのでそれをキャンセル
					c_this._cancel_previous_highlight();
					
				},
				
				// ドラッグ終了が認識されてすぐ，並び順に変更があったとき
				onUpdate : function( ul ){
						//alert( ul.innerHTML );
				},

				// ドラッグ終了時
				endeffect : function( li_item ){
					// 移動不可能な要素の位置がずれたのではないか？その補正
					c_this._adjust_all_fixed_items();

					// カスタムコールバック
					if( c_this.onDragFinish != null )
					{
						c_this.onDragFinish();
					}

					// この行をハイライト
					c_this._highlight_item( li_item );
				}
			} );
		}
	}
	,
	
	
	// 終了
	destroy : function()
	{
		Sortable.destroy( this.ul_id );
	}
	,
	
	
	// 再度初期化（新規行が追加された時など）
	_restart : function()
	{
		// いったん破棄
		this.destroy();
		
		// また生成
		this._create();
	}
	,
	
	// 初回データセットアップ
	_init_data_setup : function()
	{
		this._static_max_id_number = this._get_max_id_number();
	}
	,
	

	/* ---------- メソッド：行の追加・削除 ---------- */
	
	
	// 行を追加
	add_new_li : function( new_li_html, new_li_id )
	{
		var c_this = this;
		new Insertion.Bottom( this.ul_id, new_li_html );
		new Effect.Appear( new_li_id, {
			duration : this.li_appear_duration,
			fps      : this._default_fps
		} );
		
		// 全体をSortableにしなおす
		this._restart();
		this._adjust_all_fixed_items()
	}
	,
	
	
	// 行を削除
	delete_li : function( li_id )
	{
		// 削除エフェクトとかぶるとエラーになる
		this._cancel_previous_highlight();

		// いったんマウス受付停止（削除エフェクト中にドラッグドロップされると困る）
		this.destroy();

		var elem = $( li_id );
		with({
			c_elem : elem, 
			c_this : this 
		})
		{
			// 消えていくエフェクト
			new Effect.Parallel(
				[
					new Effect.Fade( c_elem, {
						from  : 1,
						to    : 0,
						fps   : c_this._default_fps,
						delay : 0.2
					})
					,
					new Effect.Scale( c_elem, 0, {
						fps    : c_this._default_fps,
						scaleX : false,
						scaleY : true
					} )
				], 
				{
					duration : c_this.li_disappear_duration,
					afterFinishInternal : function( effect ){
						// 透明になった時点でDOM削除
						c_elem.parentNode.removeChild( c_elem );
						
						// また生成
						c_this._create();
						
						// 固定要素の順序を補正
						c_this._adjust_all_fixed_items();
					}
				}
			);
		}
		
		// ※削除エフェクト発生前に，ドラッグエフェクトは終了させる。
		// ※削除エフェクト終了までは，ドラッグを受け付けない。
	}

};



