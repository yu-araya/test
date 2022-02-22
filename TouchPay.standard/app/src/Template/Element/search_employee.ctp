<?php echo $this->Html->script(['/webroot/js/search_employee.js'], ['inline' => false]); ?>

<div class="position_relative">
    <div class="employee-search display_none">
        <div class="search_title">社員検索</div>
        <?php
            echo $this->Form->control('search_employee',
                ['type' => 'text',
                    'id' => 'search_employee',
                    'label' => false,
                    'class' => 'search-employee-id-input',
                    'placeholder' => '氏名を入力してください',
                    'data-cy' => 'inputEmployeeName',
                ]);
        ?>
    </div>
    <input type="hidden" id="url_search_employee" value="<?php echo $this->Url->build('/employee-infos/', ['_ssh' => true]); ?>">
</div>
