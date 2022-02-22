<ul id="breadcrumbs">
    <li><a href="<?php echo $this->Url->build('/employee-infos/select', false); ?>"
            style="text-decoration: none" target="_self">
            社員情報
        </a></li>
    <li><a>新規登録</a></li>
</ul>
<br>
<?php
    echo $this->Form->create('EmployeeInfo', array('url' =>['controller' => 'employee-infos', 'action' => 'insert'], 'data-cy' => 'employee-info-add-form'));
    echo $this->Form->hidden("EmployeeInfo[employee_id]");

    // 登録データ配置
    echo $this->element('employee_infos_detail_form', array('mode' => 'add'));
?>
<div>
    <?php //echo $this->Form->end('登録'); ?>
    <?php echo $this->Form->submit('登録', ['label' => false]); ?>
    <?php echo $this->Form->end(); ?>
</div>

<div class="process_box bulk">
    <span class="title">社員情報一括登録</span>
    <div class="comment">アップロード形式を選択してください</div>
    <?php
        echo $this->Form->create('EmployeeInfo', array('url' =>['controller' => 'employee-infos', 'action' => 'uploadLabel'], 'type' => 'file', 'data-cy' => 'uploadFile'));
        echo $this->Form->control("EmployeeInfo[fileType]", [
            'options' => ['CSV', 'EXCEL'],
            'label' => false,
            'data-cy' => 'selectFormat'
        ]); ?>
    <div class="comment">アップロードを行うファイルを選択してください</div>
    <?php
        echo $this->Form->file("EmployeeInfo[result]", array('data-cy' => 'selectFile'));
        //echo $this->Form->end('アップロード');
	echo $this->Form->submit('アップロード', ['label' => false]);
        echo $this->Form->end();
    ?>
</div>
