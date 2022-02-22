<?php echo $this->Html->script(['/webroot/js/food_period.js'], ['inline' => false]); ?>

<ul id="breadcrumbs">
    <li><a>食事</a></li>
    <li><a
            href="<?php echo $this->Url->build('/food-divisions/index'); ?>">食事区分一覧</a>
    </li>
    <li><a>食事期間一覧</a></li>
</ul>
    <div class="input-table" style="float:left;">
        <table>
            <tbody>
                <tr>
                    <td">食事区分</td>
                    <td><?php echo $foodDivision['0']['food_division']; ?>
                    </td>
                </tr>
                <tr>
                    <td>食事区分名</td>
                    <td><?php echo $foodDivision['0']['food_division_name']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="float:left;">
        <?php echo($this->Form->create('FoodPeriod', ['action' => 'add'])); ?>
        <table class="detail-table food_period_detail">
            <thead>
                <?php echo($this->Html->tableHeaders(['開始日', '食事名', '価格', '登録日', '更新日', ['' => ['colspan' => '3']]])); ?>
            </thead>
            <tbody>
        <?php if (!empty($foodPeriods)) { ?>
                <?php foreach ($foodPeriods as $i => $record): ?>
                <?php
                    $key = $record['FoodPeriod']['id'];
                    $isUpdate = isset($targetFoodPeriod) && isset($targetFoodPeriod['id']) && ($targetFoodPeriod['id'] == $record['FoodPeriod']['id']); ?>
                <tr class="<?php echo($isUpdate ? '' : 'unchecked') ?> <?php echo($record['FoodPeriod']['delete_flg'] == 0 ? '' : 'delete') ?>"
                    data-index="<?php echo($key) ?>">
                    <td>
                        <div class="date_term">
                            <?php
                                $options = ['type' => 'text', 'value' => $this->Date->formatDate($record['FoodPeriod']['start_date']), 'label' => '', 'maxlength' => 10, 'disabled' => true, 'required' => false, 'readonly' => 'readonly', 'data-cy' => 'foodDateFix' . $i];
                                if ($isUpdate) {
                                    $options['value'] = $targetFoodPeriod['start_date'];
                                }
			        $this->Form->templates([
				    'textContainer' => '<div class="input text">{{content}}</div>'
			        ]);
                                echo($this->Form->control("FoodPeriod.$key.start_date", $options)); ?>
                        </div>
                    </td>
                    <td>
                        <?php
                            $options = ['type' => 'text', 'value' => $record['FoodPeriod']['food_period_name'], 'label' => '', 'maxlength' => 50, 'disabled' => true, 'required' => false, 'data-cy' => 'foodNameFix' . $i];
                            if ($isUpdate) {
                                $options['value'] = $targetFoodPeriod['food_period_name'];
                            }
			    $this->Form->templates([
				'textContainer' => '<div class="input text">{{content}}</div>'
			    ]);
                            echo($this->Form->control("FoodPeriod.$key.food_period_name", $options)); ?>
                    </td>
                    <td>
                        <?php
                            $options = ['type' => 'text', 'value' => $record['FoodPeriod']['food_price'], 'label' => '', 'maxlength' => 7, 'disabled' => true, 'required' => false, 'data-cy' => 'foodValueFix' . $i];
                            if ($isUpdate) {
                                $options['value'] = $targetFoodPeriod['food_price'];
                            }
			    $this->Form->templates([
				'textContainer' => '<div class="input text">{{content}} 円</div>'
			    ]);
                            echo($this->Form->control("FoodPeriod.$key.food_price", $options)); ?>
                    </td>
                    <td class="date"><?php echo $this->Date->formatDatetime($record['FoodPeriod']['created']); ?>
                    </td>
                    <td class="date"><?php echo $this->Date->formatDatetime($record['FoodPeriod']['modified']); ?>
                    </td>
                    <td>
                        <?php
                            echo($this->Form->control("FoodPeriod.$key.update_check", ['type' => 'checkbox', 'id' => "update_check".$key, 'label' => ' 修正', 'required' => false, 'data-action' => 'update', 'data-cy' => 'foodMainteFix' . $i])); ?>
                    </td>
                    <td>
                        <?php
                            if ($record['FoodPeriod']['delete_flg'] == 0) {
                                echo($this->Form->control("FoodPeriod.$key.delete_check", ['type' => 'checkbox', 'id' => "delete_check".$key, 'label' => ' 削除', 'required' => false, 'data-action' => 'delete', 'data-cy' => 'foodMainteDelete' . $i]));
                            } ?>
                    </td>
                    <td>
                        <div class="submit"><?php echo($this->Form->button('反映', ['type' => 'submit', 'name' => "FoodPeriod[$key][id]", 'value' => $record['FoodPeriod']['id'], 'disabled' => true, 'data-cy' => 'foodMainteRevision' . $i])); ?>
                        </div>
                        <?php echo($this->Form->control("FoodPeriod.$key.food_division", ['type' => 'hidden', 'value' => $record['FoodPeriod']['food_division']])); ?>
                        <?php echo($this->Form->control("FoodPeriod.$key.created", ['type' => 'hidden', 'value' => $record['FoodPeriod']['created']])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
        <?php } ?>
                <tr>
                    <td>
                        <div class="date_term">
                            <?php
                                $isAdd = isset($targetFoodPeriod) && !isset($targetFoodPeriod['id']);

                                $options = ['type' => 'text', 'id' => 'new_start_date', 'autocomplete' => 'off', 'label' => '', 'maxlength' => 10, 'value' => '', 'required' => false, 'readonly' => 'readonly', 'data-cy' => 'foodMainteDate'];
                                if ($isAdd && $targetFoodPeriod['start_date']) {
                                    $options['value'] = $targetFoodPeriod['start_date'];
                                }
			        $this->Form->templates([
				    'textContainer' => '<div class="input text">{{content}}</div>'
			        ]);
                                echo($this->Form->control("FoodPeriod.FoodPeriod.start_date", $options)); ?>
                        </div>
                    </td>
                    <td>
                        <?php
                            $options = ['type' => 'text', 'id' => 'food_period_name', 'autocomplete' => 'off', 'label' => '', 'maxlength' => 50, 'value' => '', 'required' => false, 'data-cy' => 'foodMainteName'];
                            if ($isAdd && $targetFoodPeriod['food_period_name']) {
                                $options['value'] = $targetFoodPeriod['food_period_name'];
                            }
			    $this->Form->templates([
				'textContainer' => '<div class="input text">{{content}}</div>'
			    ]);
                            echo($this->Form->control("FoodPeriod.FoodPeriod.food_period_name", $options)); ?>
                    </td>
                    <td>
                        <?php
                            $options = ['type' => 'text', 'id' => 'food_price', 'autocomplete' => 'off', 'label' => '', 'maxlength' => 7, 'value' => '', 'required' => false, 'data-cy' => 'foodMainteValue'];
                            if ($isAdd && $targetFoodPeriod['food_price']) {
                                $options['value'] = $targetFoodPeriod['food_price'];
			    }
			    $this->Form->templates([
				'textContainer' => '<div class="input text">{{content}} 円</div>'
			    ]);
                            echo($this->Form->control("FoodPeriod.FoodPeriod.food_price", $options)); ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3">
                        <div class="submit"><input id="submit" type="submit" value="登録" data-cy="foodMainteAdd"></div>
                            <?php echo($this->Form->hidden("FoodPeriod.FoodPeriod.food_division", ['value' => $foodDivision['0']['food_division']])); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#new_start_date").datepicker({
            showOn: 'focus',
            buttonText: 'カレンダー',
            showButtonPanel: true
        });
    });
</script>
