<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmployeeInfosController extends AppController {
    public $uses = array('EmployeeInfo', 'Administrator', 'EmployeeKbn');
    public $pageLine = 20;
    private $params = NULL;

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('menuLink', $this->getMenuLink(6));
    }

    public function add() {
        $this->set('title_for_layout', '社員情報　登録画面');
        $this->set('table', $this->EmployeeInfo->schema());	//テーブル情報取得
        $this->setEmployeeKbnArray();
    }

    public function select() {
        $this->set('title_for_layout', '社員情報　検索画面');

        //セッション情報削除
        $this->getRequest()->getSession()->delete('search_employeeId');
        $this->getRequest()->getSession()->delete('search_employee_name1');
        $this->getRequest()->getSession()->delete('search_page');
    }

    /**
     * 氏名、社員IDで検索
     */
    public function lists() {
        //POST+session利用時のブラウザバックでのキャッシュ有効期間切れ対応
        $this->response = $this->response->withCache('-1 minute', '+1 day');

        $this->set('title_for_layout', '社員情報　検索一覧画面');

        //セッション情報があるかチェック
        if ($this->getRequest()->getSession()->check('search_employeeId')) {
            //ある場合はセッションから設定
            $employeeId = $this->getRequest()->getSession()->read('search_employeeId');
            $employeeName1 = $this->getRequest()->getSession()->read('search_employee_name1');
        } else {
            //ない場合は前画面の情報から設定
            $employeeId = $this->request->data['EmployeeInfo']['employee_id'];
            $employeeName1 = $this->request->data['EmployeeInfo']['employee_name1'];

            //セッション情報書込
            $this->getRequest()->getSession()->write('search_employeeId', $employeeId);
            $this->getRequest()->getSession()->write('search_employee_name1', $employeeName1);
        }

        $conditions = 'WHERE 1 = 1';
        $sqlParams = array();
        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();
        if (!empty($employeeKbnArray)) {
//            $conditions[] = array('EmployeeInfo.employee_kbn ' => $employeeKbnArray);
	      $conditions .= ' AND EmployeeInfos.employee_kbn IN (';
	      $cnt = count($employeeKbnArray);
              for ($i = 0; $i < $cnt; $i++) {
		      $conditions .= $employeeKbnArray[$i];
		      if($i < ($cnt - 1)) {
			      $conditions .=  ',';
		      }
	      } 
	      $conditions .= ') ';
        }

        if (!empty($employeeId)) {
//            $conditions[] = array('EmployeeInfo.employee_id = ?' => $employeeId);
	      $conditions .= ' AND EmployeeInfos.employee_id = ?';            
	      $sqlParams[] = $employeeId;
        }
        if (!empty($employeeName1)) {
//            $conditions[] = array('EmployeeInfo.employee_name1 LIKE ?' => '%'.$employeeName1.'%');
	       $conditions .= ' AND  EmployeeInfos.employee_name1 LIKE ?';        
	      $sqlParams[] = '%'.$employeeName1.'%';
        }

        // 現在ページの取得
        $page = isset($this->params['named']['page']) ? $this->params['named']['page'] : '1';
        if (!empty($page)) {
            // セッション情報書込
            $this->getRequest()->getSession()->write('search_page', $page);
        } else {
            // セッションに情報がある場合は、セッションから読み込み
            if ($this->getRequest()->getSession()->check('search_page')) {
                $page = $this->getRequest()->getSession()->read('search_page');
            }
        }

	$sql  = 'SELECT ';
	$sql .= '    EmployeeInfos.*, ';
	$sql .= '    EmployeeKbn.employee_kbn_name ';
	$sql .= 'FROM ';
	$sql .= '    employee_infos EmployeeInfos ';
	$sql .= 'LEFT JOIN employee_kbns EmployeeKbn '; 
	$sql .= '    ON EmployeeInfos.employee_kbn = EmployeeKbn.employee_kbn ';
	$sql .= $conditions;

	$options = ['limit' => $this->pageLine,
		'order' => [
			'EmployeeInfos.employee_id' => 'asc'
		],
		'offset' => $page
	];

        // データ取得
//        $this->paginate = array(
//            'EmployeeInfos' => array(
//                'fields' => 'EmployeeInfos.*, EmployeeKbn.*',
//                'page' => $page,
//                'conditions' => $conditions,
//                'order' => array('EmployeeInfos.employee_id'=>'asc'),
//                'limit' => $this->pageLine,
//                'joins' => array(
//                    array(
//                        'table' => 'employee_kbns',
//                        'alias' => 'EmployeeKbn',
//                        'type' => 'LEFT',
//                        'conditions' => 'EmployeeInfos.employee_kbn = EmployeeKbn.employee_kbn'
//                    ),
//                ),
//            )
//        );
        $this->PaginatorForPdo->setSortColumns(['EmployeeInfos.employee_id']);
        $dataList = $this->PaginatorForPdo->paginateForPdo($sql, $sqlParams, $options);

        // ビューに情報をセット
        $this->set('dataList', $dataList);
        $this->set('employeeId', $employeeId);
        $this->set('employee_name1', $employeeName1);

        // ビューの表示
        $this->render('list');
    }

    /**
     * 詳細画面の表示
     */
    public function detail($id = null) {
        $this->set('title_for_layout', '社員情報　詳細画面');

        if ($id != null) {
            // 絞り込み条件の設定
            $conditions = array('conditions' => array('id' => $id));

            //データ取得
            $employeeInfo = $this->EmployeeInfo->findEX('all', $conditions);

            // データ未存在の場合は一覧に戻す
            if (empty($employeeInfo)) {
                return $this->redirect(['action' => 'lists']);
            }

            $employee_kbn = $employeeInfo[0]['EmployeeInfo']['employee_kbn'];

            // 取扱できない社員区分の場合は一覧に戻す
            $employeeKbnCheckList = $this->getConditionEmployeeKbnList();
            if (!empty($employeeKbnCheckList)) {
                if (!in_array($employee_kbn, $employeeKbnCheckList, true)) {
                    return $this->redirect(['action' => 'lists']);
                }
            }

            //ビューにセット
            $this->set('employeeId', $employeeInfo[0]['EmployeeInfo']['employee_id']);
            $this->set('employeeInfo', $employeeInfo);
        }

        //ビューにセット
        $this->set('table', $this->EmployeeInfo->schema());	//テーブル情報取得
        $this->setEmployeeKbnArray();
    }

    public function insert() {
        $resultMessage = "";

        // 登録人数が最大登録人数を超えている場合、メッセージを表示して処理終了
        if ($this->maxEmployeeCheck()) {
            $msg = sprintf($this->property['message']['errorMsg59'], '新規登録');
            $this->Flash->set($msg);
        } 
        // 入力チェック
        elseif ($this->inputValidate($this->request->data['EmployeeInfo'], $resultMessage, null, null)) {
            // パスワード（初期）
            $this->request->data['EmployeeInfo']['password'] = $this->property['init_password'][''];
            if (empty($this->request->data['EmployeeInfo']['iccard_valid_s_time'])) {
	        $this->request->data['EmployeeInfo']['iccard_valid_s_time'] = null;
	    }
	    if (empty($this->request->data['EmployeeInfo']['iccard_valid_e_time'])) {
	        $this->request->data['EmployeeInfo']['iccard_valid_e_time'] = null;
	    }
	    if (empty($this->request->data['EmployeeInfo']['iccard_valid_s_time2'])) {
        	$this->request->data['EmployeeInfo']['iccard_valid_s_time2'] = null;
	    }
	    if (empty($this->request->data['EmployeeInfo']['iccard_valid_e_time2'])) {
	        $this->request->data['EmployeeInfo']['iccard_valid_e_time2'] = null;
	    }	
            if ($this->EmployeeInfo->saveEX($this->request->data['EmployeeInfo'])) {
                $this->Flash->success($this->property['message']['infoMsg01']);
                return $this->redirect('/employee-infos/add');
            } else {
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
        } else {
            $this->Flash->set($resultMessage);
            $employeeInfo = array();
            array_push($employeeInfo, array('EmployeeInfo' => $this->request->data['EmployeeInfo']));
            $this->set('employeeInfo', $employeeInfo);
        }
        $this->add();
        $this->render('add');
    }

    public function update() {
        if (empty($this->request->data)) {
            $this->request->data = $this->EmployeeInfo->read();
        } else {
            //修正
            if (isset($this->request->data['update_proc'])) {
                $resultMessage = "";
                //入力チェック
                if ($this->inputValidate($this->request->data['EmployeeInfo'], $resultMessage, null, null)) {
                    //社員食堂使用許可フラグＯＮ時、社員食堂使用不可設定日を設定
                    if ($this->request->data['EmployeeInfo']['dining_license_flg'] == '1') {
                        $this->request->data['EmployeeInfo']['dining_licensed_date'] = date("Y-m-d");
                    } else {
                        $this->request->data['EmployeeInfo']['dining_licensed_date'] = null;
                    }


                    if (empty($this->request->data['EmployeeInfo']['iccard_valid_s_time'])) {
                        $this->request->data['EmployeeInfo']['iccard_valid_s_time'] = null;
                    }
                     if (empty($this->request->data['EmployeeInfo']['iccard_valid_e_time'])) {
                        $this->request->data['EmployeeInfo']['iccard_valid_e_time'] = null;
                    }
                    if (empty($this->request->data['EmployeeInfo']['iccard_valid_s_time2'])) {
                        $this->request->data['EmployeeInfo']['iccard_valid_s_time2'] = null;
                    }
                    if (empty($this->request->data['EmployeeInfo']['iccard_valid_e_time2'])) {
                        $this->request->data['EmployeeInfo']['iccard_valid_e_time2'] = null;
                    }

                    if ($this->EmployeeInfo->saveEX($this->request->data['EmployeeInfo'])) {
                        $this->Flash->success($this->property['message']['infoMsg02']);
                        return $this->redirect($this->getRedirectListUrl());
                    } else {
                        $this->Flash->set($this->property['message']['exceptionMsg01']);
                    }
                } else {
                    $this->Flash->set($resultMessage);
                    $employeeInfo = array();
                    array_push($employeeInfo, array('EmployeeInfo' => $this->request->data['EmployeeInfo']));
                    $this->set('employeeInfo', $employeeInfo);
                    $this->detail($this->request->data['EmployeeInfo']['id']);
                    $this->render('detail');
                    return;
                }
                //削除
            } elseif (isset($this->request->data['delete_proc'])) {
                $this->EmployeeInfo->id = $this->request->data['EmployeeInfo']['id'];

                if ($this->EmployeeInfo->saveEX(['id' => $this->EmployeeInfo->id, 'delete_flg' => '1'])) {
                    $this->Flash->success($this->property['message']['infoMsg03']);
                    return $this->redirect($this->getRedirectListUrl());
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
                //削除を取り消す
            } elseif (isset($this->request->data['undelete_proc'])) {
                // 登録人数が最大登録人数を超えている場合、メッセージを表示して処理終了
                if ($this->maxEmployeeCheck()) {
                    $msg = sprintf($this->property['message']['errorMsg59'], '削除の取消しが');
                    $this->Flash->set($msg);
                }  else {
                    $this->EmployeeInfo->id = $this->request->data['EmployeeInfo']['id'];

                    if ($this->EmployeeInfo->saveEX(['id' => $this->EmployeeInfo->id, 'delete_flg' => '0'])) {
                        $this->Flash->success($this->property['message']['infoMsg02']);
                        return $this->redirect($this->getRedirectListUrl());
                    } else {
                        $this->Flash->set($this->message['exceptionMsg01']);
                    }
                }
                //パスワードリセット
            } elseif (isset($this->request->data['reset_password'])) {
                $this->EmployeeInfo->id = $this->request->data['EmployeeInfo']['id'];

		foreach ($this->property['init_password'] as $key => $value) {
			$initpassword = $value;
	        }	
                if ($this->EmployeeInfo->saveEX(['id' => $this->EmployeeInfo->id, 'password' => $initpassword])) {
                    $this->Flash->success($this->property['message']['infoMsg05']);
                    return $this->redirect($this->getRedirectListUrl());
                } else {
                    $this->Flash->set($this->message['exceptionMsg01']);
                }
            }
            $this->request->params['action'] = 'detail';
            $this->detail($this->request->data['EmployeeInfo']['id']);
            $this->render('detail');
        }
    }

    /**
    * 登録されている社員数が登録最大人数に達しているかチェック
    */
    private function maxEmployeeCheck() {
        $count = $this->EmployeeInfo->getAllEmployeeNum();
        return $count >= $this->property['max_employee_num'][''];
    }

    /**
     * 社員情報の取得
     */
    public function getEmployee() {
        $this->autoRender = false;
        $this->response->type('json');

        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }

        if (isset($this->request->data['search_employee'])) {
            // 検索（サジェスト機能）
            $conditions = array(
                'delete_flg' => 0,
                'OR' => array(
                    array('employee_id LIKE ' => $this->request->data['search_employee'].'%'),
                    array('employee_name1 LIKE ' => '%'.$this->request->data['search_employee'].'%'),
                )
            );
        } elseif (isset($this->request->data['search_employee_id'])) {
            // 社員コード検索（完全一致）
            $conditions = array(
                'delete_flg' => 0,
                'employee_id' => $this->request->data['search_employee_id'],
            );
        }
        $employeeInfo = $this->EmployeeInfo->findEX('all', array(
            'fields' => array('employee_id', 'employee_name1'),
            'conditions' => $conditions,
            'limit' => 50,
            'order' => array('employee_id'),
        ));
	$this->response->body(json_encode($employeeInfo));
	$this->response->send();
	exit;
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
            $up_file = $this->data['EmployeeInfo']['result']['tmp_name'];
            $fileName = $this->data['EmployeeInfo']['result']['name'];
            if (is_uploaded_file($up_file)) {
                $fileType = $this->data['EmployeeInfo']['fileType'];
                // ファイルの拡張子チェック
                if ($fileType === '0' && $this->isCsv($fileName)) {
                    $fileName = "../tmp/csv/"."upload.csv";
                    move_uploaded_file($up_file, $fileName);

                    // CSVファイルの内容をDBにインポート
                    if ($this->uploadFromCSV($fileName, $resultMessage, $resultCount, $fileType)) {
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
                } elseif ($fileType === '1' && $this->isExcel($fileName)) {
                    $fileName = "../tmp/excel/"."upload.excel";
                    move_uploaded_file($up_file, $fileName);
                    // EXCELファイルの内容をDBにインポート
                    if ($this->uploadFromExcel($fileName, $resultMessage, $resultCount, $fileType)) {
                        if (empty($resultMessage)) {
                            $resultMessage = 'EXCELファイルのアップロードに成功しました。';
                        } else {
                            if ($resultCount > 0) {
                                // 1件でも成功していれば文言付加
                                $resultMessage = 'EXCELファイルのアップロードに成功しました。'.'<br><br>'.$resultMessage;
                            }
                            $errorFlag = true;
                        }
                    } else {
                        $errorFlag = true;
                    }
                } else {
                    // CSVファイル以外が選択された場合
                    $resultMessage = "アップロードできるファイルはCSVまたはEXCELファイルとなります。";
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
        return $this->redirect(array('action'=>'add'));
    }

    /**
     * アップロードしたCSVのデータを取得
     * @param ファイル名
     */
    private function uploadFromCSV($fileName, &$resultMessage, &$resultCount, $fileType) {
        try {
            $csvData = file($fileName, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            // データがない場合はエラー
            if (count($csvData) <= 1) {
                $resultMessage = $this->property['message']['errorMsg37'];
                return false;
            }
            $this->updateDBFromUpdateFile($csvData, $resultMessage, $resultCount, $fileType);
            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }
        return false;
    }

    /**
     * アップロードしたEXCELのデータを取得
     * @param ファイル名
     */
    private function uploadFromExcel($fileName, &$resultMessage, &$resultCount, $fileType) {
        try {
            $excelData = $this->readXlsx($fileName);
            
            if (count($excelData) <= 1) {
                $resultMessage = $this->property['message']['errorMsg37'];
                return false;
            }
            $this->updateDBFromUpdateFile($excelData, $resultMessage, $resultCount, $fileType);
            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }
        return false;
    }

    /**
     * アップロードしたファイルから取得してデータをDBに書き込む
     *
     * @param inputData データ
     * @param fileType ファイル種別　0:Csv 1:Excelか
     */
    private function updateDBFromUpdateFile($inputData, &$resultMessage, &$resultCount, $fileType) {
        $employeeIdList = [];
        foreach ($inputData as $key => $line) {
            if ($key === 0) {
                continue;
            }
            if ($fileType === '0') {
                $record = explode(",", $line);
            } else {
                $record = ($inputData[$key]);
            }
            // 社員コードを配列で保持
            $employeeIdList[] = strval($record[1]);
        
            // カラム数チェック
            if (count($record) != 11) {
                if (!empty($resultMessage)) {
                    $resultMessage .= "<br>";
                }
                $resultMessage .= $this->property['message']['errorMsg31']."（" . ($key) . "レコード目）";
                continue;
            }

            $data = array(
                    'employee_kbn' => $this->convertEncode(strval($record[0])),							//社員区分
                    'employee_id' => $this->convertEncode(strval($record[1])),							//社員コード
                    'employee_name1' => $this->convertEncode($record[2]),								//氏名
                    'employee_name2' => $this->convertEncode($record[3]),								//所属
                    'ic_card_number' => $this->convertEncode(strval($record[4])),						//ICカード番号
                    'iccard_valid_s_time' => $record[5] ? $this->convertEncode($this->formatToDateStr($record[5])) : null,	//有効期間（開始）
                    'iccard_valid_e_time' => $record[6] ? $this->convertEncode($this->formatToDateStr($record[6])) : null,	//有効期間（終了）
                    'ic_card_number2' => $this->convertEncode(strval($record[7])),						//ICカード番号２
                    'iccard_valid_s_time2' => $record[8] ? $this->convertEncode($this->formatToDateStr($record[8])) : null,	//有効期間（開始）２
                    'iccard_valid_e_time2' => $record[9] ? $this->convertEncode($this->formatToDateStr($record[9])) : null,	//有効期間（終了）２
                    'delete_flg' => $record[10] ? $this->convertEncode($record[10]) : '0'				        //削除フラグ
                );

            $filter = array_filter($data);
            //空のレコードの場合何もしない 
            if(empty($filter)) {
                continue;
            }
            
            // バリデーションの実施
            $errorMessage = "";
            if (!$this->inputValidate($data, $errorMessage, $key, $employeeIdList)) {
                if (!empty($resultMessage)) {
                    $resultMessage .= "<br>";
                }
                $resultMessage .= $errorMessage;
                continue;
            }

            // パスワード（初期）
            $data['password'] = $this->property['init_password'][''];


            //社員コード存在チェック
            $employeeCount = $this->EmployeeInfo->getCheckEmployeeId(strval($record[1]));
            $result = 0;
            if ($employeeCount > 0) {
                // 更新処理実行
	        try {
                    $result = $this->EmployeeInfo->updateAll(
                        [
                            'employee_kbn' => $this->convertEncode(strval($record[0])),							//社員区分
                            'employee_name1' => $this->convertEncode($record[2]),								//氏名
                            'employee_name2' => $this->convertEncode($record[3]),								//所属
                            'ic_card_number' => $this->convertEncode(strval($record[4])),						//ICカード番号
                            'iccard_valid_s_time' => $record[5] ? $this->convertEncode($this->formatToDateStr($record[5])) : null,	//有効期間（開始）
                            'iccard_valid_e_time' => $record[6] ? $this->convertEncode($this->formatToDateStr($record[6])) : null,	//有効期間（終了）
                            'ic_card_number2' => $this->convertEncode(strval($record[7])),						//ICカード番号２
                            'iccard_valid_s_time2' => $record[8] ? $this->convertEncode($this->formatToDateStr($record[8])) : null,	//有効期間（開始）２
			    'iccard_valid_e_time2' => $record[9] ? $this->convertEncode($this->formatToDateStr($record[9])) : null,	//有効期間（終了）２
                            'delete_flg' => $record[10] ? $this->convertEncode($record[10]) : '0'					//削除フラグ
                        ],
                        [ 'employee_id' => $this->convertEncode(strval($record[1])) ]
                    );
                  } catch (Exception $e) {
                      $this->log($e->getMessage());
                  }
            } else {
                $this->EmployeeInfo->set($data);
                $this->EmployeeInfo->saveEX();
            }
            if ($this->EmployeeInfo->getAffectedRows() > 0) {
                $resultCount++;
            } else {
//              $this->log($e->getMessage());
            }
        }
    }

    /**
     * バリデーション処理
     * 発行日と有効期限は除く
     */
    private function inputValidate($data, &$resultMessage, $key = null, $employeeIdList) {
        $err_record = "";
        if (!is_null($key)) {
            $err_record = "（" . ($key) . "レコード目）";
        }

        $employee_kbn = $this->convertEncode($data['employee_kbn']);
        $employee_id = $this->convertEncode($data['employee_id']);
        $employee_name1 = $this->convertEncode($data['employee_name1']);
        $employee_name2 = $this->convertEncode($data['employee_name2']);
        $ic_card_number = $this->convertEncode($data['ic_card_number']);
        $expirationDateStart = $this->convertEncode($data['iccard_valid_s_time']);
        $expirationDateEnd = $this->convertEncode($data['iccard_valid_e_time']);
        $ic_card_number2 = $this->convertEncode($data['ic_card_number2']);
        $expirationDateStart2 = $this->convertEncode($data['iccard_valid_s_time2']);
        $expirationDateEnd2 = $this->convertEncode($data['iccard_valid_e_time2']);
	if (isset($data['delete_flg'])) {
            $delete_flg = $this->convertEncode($data['delete_flg']);
	}

        //社員区分必須チェック
        if ($employee_kbn == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg29'].$err_record;
            return false;
        } else {
            //社員区分存在チェック
            $employeeKbnCheckList = $this->getConditionEmployeeKbnList();
            if (!in_array($employee_kbn, $employeeKbnCheckList, true)) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg30'], implode(",", $employeeKbnCheckList)).$err_record;
                return false;
            }
        }

        //社員コード必須チェック
        if ($employee_id == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg26'].$err_record;
            return false;
        } else {
            //半角英数字チェック
            if (!preg_match("/^([a-zA-Z0-9])*$/u", $employee_id)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg13'].$err_record;
                return false;
            }
            //文字数チェック
            if (strlen($employee_id) > 10) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg34'], '社員コード', '10').$err_record;
                return false;
            }
            
            if (!empty($employeeIdList)) {
                // 社員リストが存在する場合はファイル内での重複確認を行う
                if (array_count_values($employeeIdList)[$employee_id] > 1) {
                    //エラー処理
                    $resultMessage = sprintf($this->property['message']['errorMsg56']).$err_record;
                    return false;
                }
            } else {
                // 社員リストが存在しない場合はDBとの重複確認
                if ($this->request->action === "insert" || !is_null($key)) {
                    $resultCount = $this->EmployeeInfo->getCheckEmployeeId($employee_id);
                    if ($resultCount > 0) {
                        //エラー処理
                        $resultMessage = $this->property['message']['errorMsg12'].$err_record;
                        return false;
                    }
                }
            }
        }

        //氏名
        //禁則文字チェック
        if ($this->checkStopWord($employee_name1)) {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg21'].$err_record;
            return false;
        }

        //所属
        //禁則文字チェック
        if ($this->checkStopWord($employee_name2)) {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg22'].$err_record;
            return false;
        }

        //ICカード番号
        if ($ic_card_number != '') {
            //半角英数字チェック
            if (!preg_match("/^([a-zA-Z0-9])*$/u", $ic_card_number)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg27'].$err_record;
                return false;
            }
            //文字数チェック
            if (strlen($ic_card_number) > 16) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg34'], 'ICカード番号（正）', '16').$err_record;
                return false;
            }
            //存在チェック
            $resultCount = $this->EmployeeInfo->getCheckIcCardNumber($employee_id, $ic_card_number);
            if ($resultCount > 0) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg35'], 'ICカード番号（正）').$err_record;
                return false;
            }
        }

        //有効期間（開始）の日付妥当性チェック
        if ($expirationDateStart != '') {
            if (!$this->isDate($expirationDateStart)) {
                $resultMessage = $this->property['message']['errorMsg14'].$err_record;
                return false;
            }
        }
        //有効期間（終了）の日付妥当性チェック
        if ($expirationDateEnd != '') {
            if (!$this->isDate($expirationDateEnd)) {
                $resultMessage = $this->property['message']['errorMsg15'].$err_record;
                return false;
            }
        }

        //開始日付と終了日付の比較
        if ($expirationDateStart != '' && $expirationDateEnd != '') {
            if ($expirationDateStart > $expirationDateEnd) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg16'].$err_record;
                return false;
            }
        }

        //ICカード番号2
        if ($ic_card_number2 != '') {
            //半角英数字チェック
            if (!preg_match("/^([a-zA-Z0-9])*$/u", $ic_card_number2)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg28'].$err_record;
                return false;
            }
            //文字数チェック
            if (strlen($ic_card_number2) > 16) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg34'], 'ICカード番号（副）', '16').$err_record;
                return false;
            }
            //存在チェック
            $resultCount = $this->EmployeeInfo->getCheckIcCardNumber($employee_id, $ic_card_number2);
            if ($resultCount > 0) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg35'], 'ICカード番号（副）').$err_record;
                return false;
            }
        }

        //ICカード番号（正）とICカード番号（副）の同値チェック
        if ($ic_card_number != '' && $ic_card_number2 != '') {
            if ($ic_card_number == $ic_card_number2) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg36'].$err_record;
                return false;
            }
        }

        //有効期間（開始）の日付妥当性チェック
        if ($expirationDateStart2 != '') {
            if (!$this->isDate($expirationDateStart2)) {
                $resultMessage = $this->property['message']['errorMsg23'].$err_record;
                return false;
            }
        }
        //有効期間（終了）の日付妥当性チェック
        if ($expirationDateEnd2 != '') {
            if (!$this->isDate($expirationDateEnd2)) {
                $resultMessage = $this->property['message']['errorMsg24'].$err_record;
                return false;
            }
        }
        //開始日付と終了日付の比較
        if ($expirationDateStart2 != '' && $expirationDateEnd2 != '') {
            if ($expirationDateStart2 > $expirationDateEnd2) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg25'].$err_record;
                return false;
            }
        }

        // 削除フラグが0,1以外
        if (isset($delete_flg) && !in_array($delete_flg, ['0', '1'])) {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg54'].$err_record;
            return false;
        }

        return true;
    }

    /**
     * 戻り先の一覧画面をget.
     */
    private function getRedirectListUrl() {
        return '/employee-infos/lists';
    }

    /**
     * 設定可能な社員区分を抽出してビューに情報をセット
     */
    private function setEmployeeKbnArray() {
        $employeeKbnList = $this->getEmployeeKbnList();
        $employeeKbnArray = $this->getConditionEmployeeKbnList();
        
        $employeeKbnInputList = array();
        foreach ($employeeKbnArray as $kbn) {
            $employeeKbnInputList += array($kbn => $employeeKbnList[$kbn]);
        }
        $this->set('employeeKbnInputList', $employeeKbnInputList);
    }
}
