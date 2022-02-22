<?php
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$standardPlan = boolval($property['plan_config']['standard_plan']);
$dailyMenuFlg = boolval($property['daily_menu_flg']['']);
$bulkReservationFlg = boolval($property['bulk_reservation_flg']['']);
$pittouchbizValid = boolval($property['pittouchbiz_valid']['is_valid']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title><?php echo $title_for_layout; ?></title>
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->script('jquery-3.3.1.min.js');
        echo $this->Html->script('jquery-ui.min.js');
        echo $this->Html->script('jquery.ui.datepicker-ja.js');
        echo $this->Html->css('cake.generic.css?ver=1.1.0');
        echo $this->Html->css('jquery-ui.min.css');
//        echo $scripts_for_layout;
    ?>
</head>

<body>
    <div id="container">
        <div id="header">
            <div id="logo-area" class="logo-area">
                <?php echo $this->Html->image('header_logo.png', ['alt' => $cakeDescription, 'border' => '0', 'class' => 'logo']); ?>
                <span class="nowdate"><?php echo date('Y年m月d日'); ?></span>
            </div>
            <div id="logout-button-area" class="logout-button-area">
            <?php echo $this->Form->control('マニュアル', ['type' => 'button', 'id' => 'manual', 'label' => '',
                    'onclick' => "location.href = '{$this->Url->build('/manual/', false)}';", 'data-cy' => 'manual', ]); ?>
            <?php echo $this->Form->control('ログアウト', ['type' => 'submit', 'id' => 'logout', 'label' => '',
                    'onclick' => "location.href = '{$this->Url->build('/administrators/logout', false)}';", 'data-cy' => 'logOut', ]); ?>
            </div>

        </div>

        <div id="content">
            <?php
                // ロールが"2"の場合食堂業者なのでサイドメニューを表示しない
                $role = $this->getRequest()->getSession()->read('Administrator.role'); ?>
            <div id="main_menu">
                <ul id="nav4">
                    <?php if ($role !== '2'): ?>
                        <?php if (!$standardPlan): ?>
                            <?php foreach ($baseKbnName as $baseKbn => $baseName): ?>
                                <li><a href="<?php echo $this->Url->build('/reservation-infos/index/'.$baseKbn); ?>"
                                        class="tate<?php echo isset($menuLink) ? $menuLink[$baseKbn + $menuBase] : ''; ?>" data-cy="reservation<?php echo $baseKbn; ?>">
                                        予約状況照会<br><?php echo $baseName; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <?php if ($bulkReservationFlg): ?>
                                <li><a href="<?php echo $this->Url->build('/reservation-infos/registration'); ?>"
                                        class="tate<?php echo isset($menuLink) ? $menuLink[2] : ''; ?>" data-cy="bulk_reservation">予約一括登録<br>ＥＸＣＥＬ取込</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li><a href="<?php echo $this->Url->build('/food-history-infos/select'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[3] : ''; ?>" data-cy="food-history-infos">社員別<br>食堂精算</a>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?php echo $this->Url->build('/food-history-infos/sumdaily'); ?>"
                            class="tate<?php echo isset($menuLink) ? $menuLink[4] : ''; ?>" data-cy="food-history-infos-sumdaily">食堂精算<br>集計</a>
                    </li>
                    <?php if ($role !== '2'): ?>
                        <li><a href="<?php echo $this->Url->build('/csvs/select'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[5] : ''; ?>" data-cy="csv-download">ＣＳＶ<br>ファイル出力</a>
                        </li>
                        <li><a href="<?php echo $this->Url->build('/employee-infos/select'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[6] : ''; ?>" data-cy="employee-infos">社員情報<br>メンテナンス</a>
                        </li>
                        <li><a href="<?php echo $this->Url->build('/employee-kbns/lists'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[10] : ''; ?>" data-cy="employee-kbns">社員区分マスタ<br>メンテナンス</a>
                        </li>
                        <?php if (!$standardPlan): ?>
                        <li><a href="<?php echo $this->Url->build('/day-off-calendars/index'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[7] : ''; ?>" data-cy="calendars">カレンダー<br>メンテナンス</a>
                        </li>
                        <?php endif; ?>
                        <?php if ($dailyMenuFlg): ?>
                        <li><a href="<?php echo $this->Url->build('/food-divisions/index'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[13] : ''; ?>" data-cy="food-divisions">食事<br>メンテナンス</a>
                        </li>
                        <?php endif; ?>
                        <?php if ($pittouchbizValid): ?>
                        <li><a href="<?php echo $this->Url->build('/regist-error/index'); ?>"
                                class="tate<?php echo isset($menuLink) ? $menuLink[9] : ''; ?>" data-cy="regist-errors">登録エラー<br>一覧</a>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li><a href="<?php echo $this->Url->build('/administrators/select'); ?>"
                            class="tate<?php echo isset($menuLink) ? $menuLink[8] : ''; ?>" data-cy="password">パスワード<br>変更</a>
                    </li>
                </ul>
            </div>
            <div class="main_category">
                <?php echo $this->Flash->render(); ?>
		<?php echo $this->fetch('content'); ?>
            </div>
        </div>
    </div>
</body>

</html>
