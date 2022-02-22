<ul id="breadcrumbs">
	<li><a>登録エラー一覧</a></li>
</ul>
<br>
<br>
<div style='padding:0 1%;'>
<?php
if(0 < count($registError)){
	$this->log($registError, 'debug');
	// 登録データ一覧テーブル配置
	echo $this->element('regist_error_list',
		array('registError' => $registError));
}else{
	echo '<div style="text-align:center"><font size 3>登録内容はありません</font></div>';
}
?>
</div>
