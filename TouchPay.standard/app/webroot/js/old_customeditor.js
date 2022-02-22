myEditor = new YAHOO.widget.Editor('msgpost', {
    height: '450px',
    width: '800px',
    dompath: false,
    animate: true,
    extracss: '.yui-toolbar-large span.yui-toolbar-icon{background: url(editor-sprite.gif) no-repeat 30px 30px;} body {font-family: ヒラギノ角ゴ ProN W3, Hiragino Kaku Gothic ProN; font-size: 16px}',
    toolbar: {
        titlebar: '文書ファイルの内容編集',
        buttons: [
            { group: 'textstyle', label: 'スタイル',
                buttons: [
                    { type: 'push', label: '太字', value: 'bold' },
                    { type: 'push', label: '斜字', value: 'italic' },
                    { type: 'push', label: '下線', value: 'underline' },
                    { type: 'separator' },
                    { type: 'select', label: '標準', value: 'fontsize2',
                        menu: [
                            { text: '標準' },
                            { text: '小' },
                            { text: '中' },
                            { text: '大' }
                        ]
                    },
					{ type: 'separator' },
                    { type: 'color', label: 'フォントの色', value: 'forecolor'},
                    { type: 'color', label: '背景色', value: 'backcolor'},
                    { type: 'separator' },
                    { type: 'push', label: '左揃え CTRL + SHIFT + [', value: 'justifyleft' },
                    { type: 'push', label: '中央揃え Center CTRL + SHIFT + |', value: 'justifycenter' },
                    { type: 'push', label: '右揃え CTRL + SHIFT + ]', value: 'justifyright' },
                    { type: 'separator' },
					{ type: 'push', label: 'スタイルの解除', value: 'removeformat', disabled: true }
                ]
            }
        ]
    }
});

myEditor.on('editorContentLoaded', function() {
    this.toolbar.on('fontsize2Click', function(o) {
		var button = this.toolbar.getButtonById(o.button.id); 
		var size, label;
	    switch(o.button.value) {
	        case '大':
				size = '44px';
				label = '大';
	            break;
	        case '中':
				size = '38px';
				label = '中';
	            break;
	        case '小':
				size = '32px';
				label = '小';
	            break;
	        case '標準':
				size = '16px';
				label = '標準';
	            break;
	    };
		this.execCommand('fontsize', size);
		button.set('label', label); 
		this._updateMenuChecked('fontsize2', o.button.value); 
		this.STOP_EXEC_COMMAND = true; 
    }, this, true);
}, myEditor, true);

myEditor._disabled[myEditor._disabled.length] = 'fontsize2'; 

myEditor.on('afterNodeChange', function() { 
    var elm = this._getSelectedElement();

    if (!this._isElement(elm, 'body') && !this._isElement(elm, 'img')) { 
        this.toolbar.enableButton('fontsize2'); 
    } 
}, myEditor, true); 

myEditor.render();
