<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class FoodDivisionsController extends AppController {
    public $uses = array('FoodDivision', 'FoodPeriod');

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('menuLink', $this->getMenuLink(13));
        $this->set('title_for_layout', '食事区分一覧');
    }

    public function index() {
        $foodDivisions = $this->FoodDivision->findEX('all');

        for ($i = 0; $i < count($foodDivisions); $i++) {
            $res = $this->FoodPeriod->getFoodPeriodInfo($foodDivisions[$i]['FoodDivision']['food_division'], null);
	    if ($res) {
                $foodDivisions[$i]['FoodDivision']['food_period_name'] = $res['FoodPeriod']['food_period_name'];
                $foodDivisions[$i]['FoodDivision']['instrument_division'] = $res['FoodDivision']['instrument_division'];
                $foodDivisions[$i]['FoodDivision']['food_cost'] = $res['FoodPeriod']['food_price'];
                $foodDivisions[$i]['FoodDivision']['created'] = $res['FoodPeriod']['created'];
                $foodDivisions[$i]['FoodDivision']['modified'] = $res['FoodPeriod']['modified'];
            }
        }

        $this->set('instrumentDivisionList', $this->getInstrumentDivisionList());
        $this->set('foodDivisions', $foodDivisions);
    }

    /**
     * CSVファイルのアップロード
     */
    public function uploadLabel() {
        // 結果
        $resultMessage = "";
        $resultCount = 0;
        $errorFlag = false;

        // ファイルが選択されているかどうか
        if (!empty($this->data)) {
            $up_file = $this->data['FoodDivision']['result']['tmp_name'];
            $fileName = $this->data['FoodDivision']['result']['name'];
            if (is_uploaded_file($up_file)) {
                // ファイルの拡張子チェック
                if ($this->isCsv($fileName)) {
                    $fileName = "../tmp/csv/"."upload.csv";
                    move_uploaded_file($up_file, $fileName);

                    // CSVファイルの内容をDBにインポート
                    if ($this->uploadFromCSV($fileName, $resultMessage, $resultCount)) {
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
                    $resultMessage = "アップロードできるファイルはCSVファイルのみとなります。";
                    $errorFlag = true;
                }
            } else {
                $resultMessage = "アップロードするファイルを選択してください。";
                $errorFlag = true;
            }
        } else {
            $resultMessage = 'エラーが発生致しました。';
            $errorFlag = true;
        }
        // 画面表示
        if (!$errorFlag) {
            // エラーがない場合は青字
            $this->Flash->success($resultMessage);
	} else {
            $this->Flash->set($resultMessage);
	}
        return $this->redirect(array('action'=>'index'));
    }

    /**
     * アップロードしたCSVの内容をDBにインポートする
     * @param ファイル名
     */
    private function uploadFromCSV($fileName, &$resultMessage, &$resultCount) {
        try {
            $csvData = file($fileName, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            // データがない場合はエラー
            if (count($csvData) <= 1) {
                $resultMessage = $this->property['message']['errorMsg37'];
                return false;
            }

            foreach ($csvData as $key => $line) {
                if ($key == 0) { // １行目はヘッダー部分なので飛ばす
                    continue;
                }

                $record = explode(",", $line);

                // カラム数チェック
                if (count($record) != 4) {
                    if (!empty($resultMessage)) {
                        $resultMessage .= "<br>";
                    }
                    $resultMessage .= $this->property['message']['errorMsg31']."（" . ($key) . "レコード目）";
                    continue;
                }

                $data = array(
                    'food_division' => $this->convertEncode($record[0]),        // 食事区分
                    'start_date' => $this->convertEncode($record[1]),           // 開始日
                    'food_period_name' => $this->convertEncode($record[2]),     // 食事名
                    'food_price' => $this->convertEncode($record[3]),           // 価格
                    'delete_flg' => '0'                                         // 削除フラグ
                );

                // バリデーションの実施
                $errorMessage = "";
                if (!$this->inputValidate($data, $errorMessage, $key)) {
                    if (!empty($resultMessage)) {
                        $resultMessage .= "<br>";
                    }
                    $resultMessage .= $errorMessage;
                    continue;
                }

                // パスワード（初期）
                $data['password'] = $this->property['init_password'][''];

                $this->FoodPeriod->set($data);
                $this->FoodPeriod->saveEX();
                if ($this->FoodPeriod->getAffectedRows() > 0) {
                    $resultCount++;
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
     * 発行日と有効期限は除く
     */
    private function inputValidate($data, &$resultMessage, $key = null) {
        $err_record = "";
        if (!is_null($key)) {
            $err_record = "（" . ($key) . "レコード目）";
        }

        $food_division = (int)$this->convertEncode($data['food_division']);
        $start_date = $this->convertEncode($data['start_date']);
        $food_period_name = $this->convertEncode($data['food_period_name']);
        $food_price = $this->convertEncode($data['food_price']);

        //食事区分必須チェック
        if ($food_division == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], '食事区分').$err_record;
            return false;
        } else {
            //食事区分存在チェック
            $foodDivisionList = $this->FoodDivision->getFoodDivision();
            if (!in_array($food_division, $foodDivisionList, true)) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg52'], implode(",", $foodDivisionList)).$err_record;
                return false;
            }
        }

        //開始日の日付妥当性チェック
        if ($start_date == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], '開始日').$err_record;
            return false;
        } elseif (!$this->isDate($start_date)) {
            $resultMessage = $this->property['message']['errorMsg53'].$err_record;
            return false;
        } elseif ($this->FoodPeriod->isFoodTermExisting($data, false)) {
            $resultMessage = $this->property['message']['errorMsg55'].$err_record;
            return false;
        }

        //食事名
        //禁則文字チェック
        if ($food_period_name == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], '食事名').$err_record;
            return false;
        } elseif ($this->checkStopWord($food_period_name)) {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg21'].$err_record;
            return false;
        }

        //価格
        if ($food_price == null) {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], '価格').$err_record;
            return false;
        } elseif (!preg_match("/^([0-9])*$/u", $food_price)) {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg06'].$err_record;
            return false;
        }
        
        return true;
    }
}
