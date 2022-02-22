<ul id="breadcrumbs">
    <li><a href="<?php echo $this->Url->build('/food-history-infos/select', false); ?>" style="text-decoration: none" target="_self">社員食堂精算</a></li>
    <li><a>新規登録</a></li>
</ul>
<br>
<?php
    echo $this->Form->create('FoodHistoryInfo', array('url' => ['controller' => 'food-history-infos', 'action' => 'insert'], 'data-cy' => 'food-history-info-add-form'));
?>

<?php echo $this->Html->script(array('/webroot/js/food_history_infos'), array('inline' => false)); ?>
<?php echo $this->Html->script(array('/webroot/js/food_history_infos_add'), array('inline' => false)); ?>
<?php
    $employee_id = $table->column('employee_id')['default'];
    $food_division = $table->column('food_division')['default'];
    $card_recept_time = $table->column('card_recept_time')['default'];
    $card_recept_time2 = $default_time;
    $instrument_division = $table->column('instrument_division')['default'];
    $reason = $table->column('reason')['default'];

    if(!empty($foodHistoryInfo['FoodHistoryInfo'])){
        $employee_id = $foodHistoryInfo[0]['FoodHistoryInfo']['employee_id'];
        $food_division = $foodHistoryInfo[0]['FoodHistoryInfo']['food_division'];
        $card_recept_time = $foodHistoryInfo[0]['FoodHistoryInfo']['card_recept_time'];
        $card_recept_time2 = $foodHistoryInfo[0]['FoodHistoryInfo']['card_recept_time2'];
        $instrument_division = $foodHistoryInfo[0]['FoodHistoryInfo']['instrument_division'];
        $reason = $foodHistoryInfo[0]['FoodHistoryInfo']['reason'];
    }
?>

<div class="input-table">
<table class="search_area">
    <tr>
        <td>社員コード</td>
        <td class="width_100">
            <?php
                echo $this->Form->control("FoodHistoryInfo[employee_id]",
                    array('type' => 'text',
                        'id' => 'employee_id',
                        'label' => '',
                        'autocomplete' => 'off',
                        'maxlength' => $table->column('employee_id')['length'],
                        'size' => 12,
                        'value' => $employee_id,
                        'data-cy' => 'employee-id',
                        'class' => 'searched-employee-id-input',
                        'placeholder' => '社員コードを入力してください'
                    ));
            ?>
        </td>
        <td>
            <button id ="employee-id-search-button" class="employee-id-search-button" type="button">氏名検索</button>
            <?php
                // 社員検索
                echo $this->element('search_employee');
            ?>
        </td>
        <tr>
            <td>社員名</td>
            <td>
                <?php
                    echo $this->Form->control("FoodHistoryInfo[employee_name]",
                        array('type' => 'text',
                            'id' => 'employee_name',
                            'label' => false,
                            'readonly' => true,
                            'tabindex' => -1,
                            'class' => 'readonly_input',
                            'data-cy' => 'employee-name'
                        ));
                ?>
            </td>
    </tr>
    <tr>
        <td>機器</td>
        <td colspan="2">
            <?php
                if (!empty($this->request->data['FoodHistoryInfo']['instrument_division'])) {
                    $instrument_division = $this->request->data['FoodHistoryInfo']['instrument_division'];
                }
                echo $this->Form->control("FoodHistoryInfo[instrument_division]",
                    array('options' => $instrumentDivisionList,
                        'selected' => $instrument_division,
                        'label' => false,
                        'onchange' => 'changeInstrumentDivision(this.value)',
			'data-cy' => 'instrument-division',
			'id' => 'FoodHistoryInfoInstrumentDivision',
                        'value' => $instrument_division
                    ));
            ?>
        </td>
    </tr>
    <tr>
        <td>メニュー</td>
        <td colspan="2">
            <?php
                $foodDivisionSelectList = $foodDivisionList[key($instrumentDivisionList)];
                if (!empty($this->request->data['FoodHistoryInfo'])) {
                    $foodDivisionSelectList = $foodDivisionList[$this->request->data['FoodHistoryInfo']['instrument_division']];
                }
                echo $this->Form->control("FoodHistoryInfo[food_division]",
                    array('options' => $foodDivisionSelectList,
                        'id' => 'food_division_list',
                        'selected' => $food_division,
                        'label' => false,
			'data-cy' => 'food-division',
			'value' => $food_division

                    ));
            ?>
        </td>
    </tr>
    <tr>
        <td>カード受付時間</td>
        <td colspan="2">
            <div class="date_term">
            <?php
                if (!empty($this->request->data['FoodHistoryInfo']['card_recept_time'])) {
                    $card_recept_time = $this->request->data['FoodHistoryInfo']['card_recept_time'];
                }
                echo $this->Form->control("FoodHistoryInfo[card_recept_time]",
                    array('type' => 'text',
                        'id' => 'card_recept_time',
                        'autocomplete' => 'off',
                        'label' => '',
                        'maxlength' => 10,
                        'size' => 10,
                        'value' => $card_recept_time,
			'data-cy' => 'card-recept-date',
			'id' => 'card_recept_time'
                    ));
                if (!empty($this->request->data['FoodHistoryInfo']['card_recept_time2'])) {
                    $card_recept_time2 = $this->request->data['FoodHistoryInfo']['card_recept_time2'];
                }
                echo $this->Form->control("FoodHistoryInfo[card_recept_time2]",
                    array('type' => 'text',
                        'label' => '',
                        'div' => false,
                        'autocomplete' => 'off',
                        'maxlength' => 5,
                        'size' => 5,
                        'value' => $card_recept_time2,
			'data-cy' => 'card-recept-time',
			'id' => 'FoodHistoryInfoCardReceptTime2'
                    ));
            ?>
            </div>
        </td>
    </tr>    
    <tr>
        <td></td>
        <td colspan="2">&nbsp;※入力形式：2018-01-01 12:00（2018年1月1日 12時00分）</td>
    </tr>
    <tr>
        <td>備考</td>
        <td colspan="2">
            <?php
                if (!empty($this->request->data['FoodHistoryInfo']['reason'])) {
                    $reason = $this->request->data['FoodHistoryInfo']['reason'];
                }
                echo $this->Form->control("FoodHistoryInfo[reason]",
                    array('type' => 'text',
                        'label' => '',
                        'autocomplete' => 'off',
                        'maxlength' => $table->column('reason')['length'],
                        'class' => 'input_reason',
                        'value' => $reason,
                        'data-cy' => 'reason',
			'id' => 'FoodHistoryInfoReason'
                    ));
            ?>
        </td>
    </tr>
</table>
</div>
<br>
<div>
<?php
    //echo $this->Form->end('登録');
    echo $this->Form->submit('登録', ['label' => false]);
    echo $this->Form->end();
?>
</div>

<?php
    foreach($foodDivisionList as $key => $value) {
        echo $this->Form->control("FoodHistoryInfo[food_division_list]".$key,
            array('options' => $value,
                'selected' => $food_division,
                'label' => false,
                'class' => 'display_none',
            ));
    }
?>
