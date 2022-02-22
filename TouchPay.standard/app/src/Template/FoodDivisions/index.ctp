<ul id="breadcrumbs">
    <li><a>食事</a></li>
    <li><a>食事区分一覧</a></li>
</ul>
    <table class="detail-table">
        <thead>
            <?php echo $this->Html->tableHeaders(['食事区分', '事業所名', '食事区分名', 'メニュー名', '金額', '登録日', '更新日', '処理']); ?>
        </thead>
        <tbody>
            <?php foreach ($foodDivisions as $key => $record): 
                $foodDivision = $record['FoodDivision']['food_division'];
            ?>
            <tr>
                <td data-cy="foodKbn<?php echo $foodDivision; ?>">
                    <?php echo $foodDivision; ?>
                </td>
                <td data-cy="instrumentName<?php echo $foodDivision; ?>">
                    <?php echo isset($record['FoodDivision']['instrument_division']) ? $instrumentDivisionList[$record['FoodDivision']['instrument_division']] : '' ?>
                </td>
                <td class="food_division" data-cy="foodKbnName<?php echo $foodDivision; ?>">
                    <?php echo htmlentities($record['FoodDivision']['food_division_name']); ?>
                </td>
                <td class="food_division" data-cy="foodMenu<?php echo $foodDivision; ?>">
                    <?php echo isset($record['FoodDivision']['food_period_name']) ? htmlentities($record['FoodDivision']['food_period_name']) : ''; ?>
                </td>
                <td class="number" data-cy="foodCost<?php echo $foodDivision; ?>">
                    <?php echo $record['FoodDivision']['food_cost']; ?>円
                </td>
                <td class="date"><?php echo $this->Date->formatDatetime($record['FoodDivision']['created']); ?></td>
                <td class="date"><?php echo $this->Date->formatDatetime($record['FoodDivision']['modified']); ?></td>
                <td data-cy="foodDetail<?php echo $foodDivision; ?>">
                    <a href="<?php echo $this->Url->build('/food-periods/index/' . $foodDivision . '/'); ?>">詳細</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="process_box bulk">
        <span class="title">メニュー期間一括登録</span>
        <div class="comment">アップロードを行うファイルを選択してください（<?php echo $this->Html->link('サンプルファイル', '/webroot/files/food_period_sample.csv'); ?>）</div>
        <?php
        echo $this->Form->create('FoodDivision', array('url' => ['controller' => 'food-divisions', 'action' => 'uploadLabel'], 'type' => 'file', 'data-cy' => 'uploadFile'));
        echo $this->Form->file("FoodDivision[result]", array('data-cy' => 'selectFile'));
        echo $this->Form->submit('アップロード', ['label' => false]);
	echo $this->Form->end();
        ?>
    </div>
