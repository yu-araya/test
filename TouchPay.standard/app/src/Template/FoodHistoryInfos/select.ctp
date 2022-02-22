<ul id="breadcrumbs">
	<li><a>社員別食堂精算</a></li>
	<li><a>検索</a></li>
</ul>
<br>
<?php
	// データ入力フォーム配置
	echo $this->element('food_history_infos_select_form',
		array('funcName' => '/food-history-infos/lists', 'buttonValue' => '検索'))
?>
<br>
<button type="button" class="" onclick="location.href='<?php echo $this->Url->build('/food-history-infos/add'); ?>'" data-cy="create-food-history-info">新規登録</button>
