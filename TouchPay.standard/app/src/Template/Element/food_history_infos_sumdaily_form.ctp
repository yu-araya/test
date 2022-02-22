<div style="float:left;">

    <div>単位：回数</div>
    <table class="detail-table sum-daily-table">
        <thead>

            <?php
            //曜日テーブル
            $weekArray = array(0 => '日', 1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土');

            //出力
            $headerList = array('日', '曜日');

            foreach ($foodDivisionList as $foodDivision) {
                array_push($headerList, $foodDivision);
            }
            array_push($headerList, '小計');

            echo $this->Html->tableHeaders($headerList);
            $sum = 0;
            ?>
        </thead>
        <tbody>
            <?php
            foreach ($dataList as $record) {
                //曜日名の取得
                $year = substr($record['card_recept_time'], 0, 4);
                $month = substr($record['card_recept_time'], 5, 2);
                $day = substr($record['card_recept_time'], 8, 2);
                $youbi = $weekArray[date("w", mktime(0, 0, 0, $month, $day, $year))];
                // 小計の取得
                $cost = $record['cost'];
                $sum += $cost ?>
            <tr>
                <!-- 日 -->
                <?php echo "<td data-cy=dayNichi$day>" . intval($day) . '</td>' ?>
                <!-- 曜日 -->
                <?php echo "<td data-cy=weekYoubi$day>" . $youbi . '</td>' ?>
                <!-- 食事 -->
                <?php
                foreach ($foodDivisionList as $key => $foodDivision) {
                    echo "<td class='food-count number' data-cy=foodMenu$day$key>". number_format($record['count'.$key]) .'</td>';
                } ?>
                <!-- 小計 -->
                <?php echo "<td class='food-count number' data-cy=subTotal$day>" . number_format($cost) . '</td>' ?>

            </tr>
            <?php
            } ?>
            <tr class="total">
                <td colspan="2">合計</td>
                <?php
                foreach ($foodDivisionList as $key => $foodDivision) {
                    $count = 0;
                    foreach ($dataList as $record) {
                        $count += $record['count'.$key];
                    }
                    echo "<td class='food-count number' data-cy=totalCount$key>".number_format($count).'</td>';
                } ?>
                <td class='food-count number' data-cy="totalCost"><?php echo number_format($sum) ?></td>
            </tr>
        </tbody>
    </table>
</div>
