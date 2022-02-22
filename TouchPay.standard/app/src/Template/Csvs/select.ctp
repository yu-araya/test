<?php
$standardPlan = boolval($property['plan_config']['standard_plan']);
?>
<?php echo $this->Html->script(array('/webroot/js/csvs.js'), array('inline' => false)); ?>
<ul id="breadcrumbs">
    <li><a>ＣＳＶファイル出力</a></li>
</ul>
<br>

<div class="process_box csv_box hr">
    <div class="employee_info_download">
        <span class="title">社員情報</span>
        <?php
            echo $this->Form->create('Csvs', array('url' => ['controller' => 'csvs', 'action' => 'employeeInfos']));
        ?>
        <input type="hidden" name="Csvs[select_kbn]" value="<?php echo $selectKbn; ?>">
        <?php
            echo $this->Form->submit('ＣＳＶ出力', ['label' => false, 'class' => 'csv-export-submit-button']);
            echo $this->Form->end();
        ?>

        <?php
            echo $this->Form->create('Csvs', array('url' => ['controller' => 'csvs', 'action' => 'employeeInfosExcel']));
        ?>
        <input type="hidden" name="Csvs[select_kbn]" value="<?php echo $selectKbn; ?>">
        <?php
    	    echo $this->Form->submit('ＥＸＣＥＬ出力', ['label' => false, 'class' => 'csv-export-submit-button']);
            echo $this->Form->end();
        ?>
    </div>
</div>

<div class="process_box csv_box ga">
    <?php
        echo $this->Form->create('Csvs', array('url' => ['controller' => 'csvs', 'action' => 'gaAllPerformance']));
    ?>
    <span class="title">月別全体実績状況</span>
    <div class="input-table condition">
    <table>
        <tr>
            <td>
                <label for="summary_start_date">検索年月日</label>
            </td>
            <td>
                <div class="date_term">
                    <?php
                    $summary_start_date = NULL;
                    $summary_end_date= NULL;
                        echo $this->Form->control(
                        "Csvs[summary_start_date]",
                        array('type' => 'text',
                                            'id' => 'summary_start_date',
                                            'autocomplete' => 'off',
                                            'label' => false,
                                            'maxlength' => 10,
                                            'size' => 12,
                                            'value' => substr($summary_start_date, 0, 10),
                                            'placeholder' => '開始年月日'
                                        )
            );
                    ?>
                    ～
                    <?php
                        echo $this->Form->control(
                        "Csvs[summary_end_date]",
                        array('type' => 'text',
                                'id' => 'summary_end_date',
                                'autocomplete' => 'off',
                                'label' => false,
                                'maxlength' => 10,
                                'size' => 12,
                                'value' => substr($summary_end_date, 0, 10),
                                'placeholder' => '終了年月日'
                            )
                    );
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><label for="CsvsBaseKbnGa">事業所区分</label></td>
        <td>
            <?php
            if (!empty($this->request->data['Csvs']['base_kbn_ga'])) {
                $baseKbnGa = $this->request->data['Csvs']['base_kbn_ga'];
            } else {
                $baseKbnGa = 0;
            }
                echo $this->Form->control(
                    "Csvs[base_kbn_ga]",
                    array('options' => $baseKbnList,
                        'label' => false,
                        'selected' => $baseKbnGa,
                        'id' => 'baseKbnListAll',
                        'value' => $baseKbnGa
                    )
                );
            ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="CsvsEmployeeKbnAll">社員区分</label>
            </td>
            <td>
            <?php
            if (!empty($this->request->data['Csvs']['employee_kbn_all'])) {
                $employeeKbn = $this->request->data['Csvs']['employee_kbn_all'];
            }
                echo $this->Form->control(
                    "Csvs[employee_kbn_all]",
                    array('options' => $employeeKbns,
                        'label' => false,
                        'selected' => $employeeKbn,
                        'id' => 'CsvsEmployeeKbnAll',
                        'value' => $employeeKbn
                    )
                );
            ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="exportMenu">メニューごとの集計を表示する</label>
            </td>
            <td>
            <?php
                echo $this->Form->controle(
                    "Csvs[exportMenu]",
                    array('type' => 'checkbox',
                        'checked' => true,
                        'label' => false,
                        'id' => 'exportMenu',
                    )
                );
            ?>
            </td>
        </tr>
    </table>
    </div>
    <input type="hidden" name="select_kbn" value="<?php echo $selectKbn; ?>">
    <?php
        //echo $this->Form->end('ＣＳＶ出力');
        echo $this->Form->submit('ＣＳＶ出力', ['label' => false, 'class' => 'csv-export-submit-button']);
        echo $this->Form->end();

    ?>
</div>

<div class="process_box csv_box ga">
    <?php
        echo $this->Form->create('Csvs', array('url' => ['controller' => 'csvs', 'action' => 'gaPerformance']));
    ?>
    <span class="title">月別個別予約・実績状況</span>
    <div class="input-table condition">
    <table>
        <tr>
            <td><label for="detail_start_date">検索年月日</td>
            <td>
            <div class="date_term">
                <?php
                        $detail_start_date = NULL;
                        $detail_end_date = NULL;
                        echo $this->Form->control(
                        "Csvs[detail_start_date]",
                        array('type' => 'text',
                                            'id' => 'detail_start_date',
                                            'autocomplete' => 'off',
                                            'label' => false,
                                            'maxlength' => 10,
                                            'size' => 12,
                                            'value' => substr($detail_start_date, 0, 10),
                                            'placeholder' => '開始年月日'
                        )
                    );
                ?>
            ～
            <?php
                echo $this->Form->control(
                "Csvs[detail_end_date]",
                array('type' => 'text',
                        'id' => 'detail_end_date',
                        'autocomplete' => 'off',
                        'label' => false,
                        'maxlength' => 10,
                        'size' => 12,
                        'value' => substr($detail_end_date, 0, 10),
                        'placeholder' => '終了年月日'
                    )
            );
            ?>
            </div>
            </td>
        </tr>
        <tr>
            <td><label for="CsvsBaseKbn">事業所区分</label></td>
        <td>
            <?php
            if (!empty($this->request->data['Csvs']['base_kbn'])) {
                $baseKbn = $this->request->data['Csvs']['base_kbn'];
            }
                echo $this->Form->control(
                    "Csvs[base_kbn]",
                    array('options' => $baseKbnList,
                        'label' => false,
                        'selected' => $baseKbn,
                        'id' => 'CsvsBaseKbn',
                        'value' => $baseKbn
                    )
                );
            ?>
        </td>
        </tr>
        <tr>
            <td><label for="CsvsEmployeeKbn">社員区分</label></td>
            <td>
            <?php
            if (!empty($this->request->data['Csvs']['employee_kbn'])) {
                $employeeKbn = $this->request->data['Csvs']['employee_kbn'];
            }
                echo $this->Form->control(
                    "Csvs[employee_kbn]",
                    array('options' => $employeeKbns,
                        'label' => false,
                        'selected' => $employeeKbn,
                        'id' => 'CsvsEmployeeKbn',
                        'value' => $employeeKbn
                    )
                );
            ?>
        </td>
    </tr>
    </table>
    <input type="hidden" name="select_kbn" value="<?php echo $selectKbn; ?>">
    <?php
       // echo $this->Form->end('ＣＳＶ出力');
	  echo $this->Form->submit('ＣＳＶ出力', ['label' => false, 'class' => 'csv-export-submit-button']);
          echo $this->Form->end();

    ?>
    </div>
</div>
