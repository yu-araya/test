<?php

namespace App\Controller;

use Cake\Event\Event;

class DayOffCalendarsController extends AppController
{
    public $uses = ['DayOffCalendar', 'ReservationInfo'];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->set('title_for_layout', 'カレンダーメンテナンス画面');
        $this->set('menuLink', $this->getMenuLink(7));
    }

    /**
     * カレンダー登録・修正初期画面.
     */
    public function index($pBaseKbn = null, $pYm = null)
    {
        // パラメータが設定されている場合（更新後のリダイレクト等）はリクエスト情報を上書き設定
        if (!empty($pBaseKbn)) {
            $this->request->data = $this->_setParam($pBaseKbn, $pYm);
        }

        if (isset($this->request->data['DayOffCalendar'])) {
            $baseKbn = $this->request->data['DayOffCalendar']['base_kbn'];
            $yyyymm = $this->request->data['DayOffCalendar']['target_date'];
        } else {
            $baseKbn = '';
            foreach ($this->getInstrumentDivisionList() as $key => $value) {
                $baseKbn = $key;
                break;
            }
            $yyyymm = [
                'year' => date('Y'),
                'month' => date('m'),
            ];
        }

        // カレンダーを取得
        $calendar = $this->_makeCalendar($baseKbn, $yyyymm['year'], $yyyymm['month']);

        //ビューにセット
        $this->set('baseKbnList', $this->getInstrumentDivisionList());
        $this->set('baseKbn', $baseKbn);
        $this->set('yyyymm', $yyyymm);
        $this->set('calendar', $calendar);
    }

    /**
     * 更新処理.
     */
    public function update()
    {
        if (!isset($this->request->data['day_off_datetime'])) {
            // リダイレクト時の検索条件設定
            return $this->redirect(['action' => 'index']);
        }

        try {
            $baseKbn = $this->request->data['base_kbn'];
            $dayOffDatetime = $this->request->data['day_off_datetime'];
            $dayOffYm = str_replace('-', '', substr($this->request->data['day_off_datetime'], 0, 7));

            if ($this->request->data['day_off_flag'] == '0') {
                // 休日の解除
                $result = $this->DayOffCalendar->deleteDate($baseKbn, $dayOffDatetime);

                $this->Flash->success($this->property['message']['infoMsg02']);

                // リダイレクト時の検索条件設定
                return $this->redirect(['action' => 'index'.'/'.$baseKbn.'/'.$dayOffYm]);
            } else {
                // 休日の設定
                if ($this->DayOffCalendar->saveEX($this->request->data)) {
                    // 予約データがある場合は削除する
                    $foodDivision = $this->getFoodDivision($baseKbn);
                    $this->ReservationInfo->deleteReservationDate($foodDivision, $dayOffDatetime);

                    $this->Flash->success($this->property['message']['infoMsg02']);

                    // リダイレクト時の検索条件設定
                    return $this->redirect(['action' => 'index'.'/'.$baseKbn.'/'.$dayOffYm]);
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());

            $this->Flash->set($this->property['message']['exceptionMsg01']);
            $this->index();
            $this->render('index');
        }
    }

    /**
     * CSVファイルのアップロード.
     */
    public function uploadDayOffCalendar()
    {
        // 結果
        $resultMessage = '';
        $resultCount = 0;
        $errorFlag = false;

        // ファイルが選択されているかどうか
        if (!empty($this->request->data)) {
            $upFile = $this->request->data['DayOffCalendar']['result']['tmp_name'];
            $fileName = $this->request->data['DayOffCalendar']['result']['name'];
            $baseKbn = $this->request->data['DayOffCalendar']['base_kbn'];
            $targetDate = $this->request->data['DayOffCalendar']['target_date'];

            if (is_uploaded_file($upFile)) {
                // ファイルの拡張子チェック
                if ($this->isCsv($fileName)) {
                    $fileName = '../tmp/csv/'.'upload_calendar_'.$baseKbn.'_'.date('YmdHis').'.csv';
                    move_uploaded_file($upFile, $fileName);

                    // CSVファイルの内容をDBにインポート
                    if ($this->uploadFromCSV($baseKbn, $fileName, $resultMessage, $resultCount)) {
                        if (empty($resultMessage)) {
                            $resultMessage = 'CSVファイルのアップロードに成功しました。';
                        } else {
                            if ($resultCount > 0) {
                                // 1件でも成功していれば文言付加
                                $resultMessage = 'CSVファイルのアップロードに成功しました。'.'<br><br>'.$resultMessage;
                            }
                            $errorFlag = true;
                        }
                    } else {
                        $errorFlag = true;
                    }
                } else {
                    // CSVファイル以外が選択された場合
                    $resultMessage = 'アップロードできるファイルはCSVファイルのみとなります。';
                    $errorFlag = true;
                }
            } else {
                $resultMessage = 'アップロードするファイルを選択してください。';
                $errorFlag = true;
            }
        } else {
            $resultMessage = 'エラーが発生致しました。';
            $errorFlag = true;
        }
        // 画面表示
        $msgMethod = 'set';
        if (!$errorFlag) {
            // エラーがない場合は青字
            $msgMethod = 'success';
        }
        $this->log('$resultMessage : '.$resultMessage, 'debug');
        $this->Flash->$msgMethod($resultMessage);

        return $this->redirect(['action' => 'index'.'/'.$baseKbn.'/'.$targetDate]);
    }

    /**
     * アップロードしたCSVの内容をDBにインポートする.
     */
    private function uploadFromCSV($baseKbn, $fileName, &$resultMessage, &$resultCount)
    {
        $this->log('uploadFromCSV');
        try {
            $csvData = file($fileName, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            // データがない場合はエラー
            if (count($csvData) == 0) {
                $resultMessage = $this->property['message']['errorMsg37'];

                return false;
            }

            foreach ($csvData as $key => $line) {
                $record = explode(',', $line);

                // カラム数チェック
                if (count($record) != 1) {
                    if (!empty($resultMessage)) {
                        $resultMessage .= '<br>';
                    }
                    $resultMessage .= $this->property['message']['errorMsg31'].'（'.($key + 1).'レコード目）';
                    continue;
                }

                $data = [
                    'base_kbn' => $baseKbn,												//事業所
                    'day_off_datetime' => $this->convertEncode($record[0]),				//休日
                ];
                // バリデーションの実施
                $errorMessage = '';
                if (!$this->inputValidate($data, $errorMessage, $key + 1)) {
                    if (!empty($errorMessage)) {
                        if (!empty($resultMessage)) {
                            $resultMessage .= '<br>';
                        }
                        $resultMessage .= $errorMessage;
                    }
                    continue;
                }
                $this->DayOffCalendar->set($data);
                $this->DayOffCalendar->saveEX();
                if ($this->DayOffCalendar->getAffectedRows() > 0) {
                    ++$resultCount;
                } else {
                    $this->log($e->getMessage());
                }
            }

            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        return false;
    }

    /**
     * バリデーション処理
     * 発行日と有効期限は除く.
     */
    private function inputValidate($data, &$resultMessage, $key = null)
    {
        $err_record = '（'.($key).'レコード目）';

        $baseKbn = $this->convertEncode($data['base_kbn']);
        $dayOffDatetime = $this->convertEncode($data['day_off_datetime']);

        // 休日の日付妥当性チェック
        if (!$this->isDate($dayOffDatetime)) {
            $resultMessage = sprintf($this->property['message']['errorMsg40'], '休日', '日付').$err_record;

            return false;
        }

        // 休日存在チェック
        $resultCount = $this->DayOffCalendar->findEX('count', [
            'conditions' => ['base_kbn' => $baseKbn, 'day_off_datetime LIKE ' => $dayOffDatetime.'%'],
        ]);
        if ($resultCount > 0) {
            // エラーにはせずスキップ
            return false;
        }

        return true;
    }

    /**
     * カレンダーの配列を作成（休日フラグ付き）.
     */
    private function _makeCalendar($baseKbn, $year, $month)
    {
        // カレンダー作成のベースとなるリスト取得
        $calendar = $this->getCalendar($year, $month);

        // 休日を取得
        $dayOffList = $this->DayOffCalendar->findEX('all', [
                'fields' => ['day_off_datetime'],
                'conditions' => ['base_kbn' => $baseKbn, 'day_off_datetime LIKE ' => $year.'-'.$month.'%'],
                'order' => ['day_off_datetime'],
        ]);

        for ($i = 0; $i < count($calendar); ++$i) {
            if (empty($calendar[$i]['day'])) {
                $calendar[$i]['day_off'] = '';
                continue;
            }

            $calendar[$i]['day_off'] = '0';
            foreach ((array) $dayOffList as $dayOff) {
                if ($calendar[$i]['day'] == intval(substr($dayOff['DayOffCalendar']['day_off_datetime'], 8, 2))) {
                    $calendar[$i]['day_off'] = '1';
                    break;
                }
            }
        }

        return $calendar;
    }

    private function _setParam($baseKbn, $ym)
    {
        if (empty($ym)) {
            $yyyymm = [
                'year' => date('Y'),
                'month' => date('m'),
            ];
        } else {
            $yyyymm = [
                'year' => substr($ym, 0, 4),
                'month' => substr($ym, 4, 2),
            ];
        }

        $param = [
            'DayOffCalendar' => [
                'base_kbn' => $baseKbn,
                'target_date' => $yyyymm,
            ],
        ];

        return $param;
    }
}
