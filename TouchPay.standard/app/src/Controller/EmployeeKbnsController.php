<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class EmployeeKbnsController extends AppController {
    public $uses = array('EmployeeKbn', 'Administrator');

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('title_for_layout', '社員区分マスタメンテナンス画面');

        $this->set('menuLink', $this->getMenuLink(10));
    }

    /**
     * 社員区分マスタメンテナンス画面
     */
    public function lists() {
        $dataList = $this->EmployeeKbn->findEX('all', array(
            'order' => array('LPAD(employee_kbn, 2, 0)')
        ));

        // ビューにセット
        $this->set('dataList', $dataList);
    }

    /**
     * 社員区分マスタメンテナンス　登録画面
     */
    public function add() {
        $this->set('title_for_layout', '社員区分マスタメンテナンス　登録画面');
    }

    /**
     * 社員区分マスタメンテナンス　詳細画面
     */
    public function detail($id = null) {
        $this->set('title_for_layout', '社員区分マスタメンテナンス　詳細画面');

        if ($id != null) {
            //データ取得
            $data = $this->EmployeeKbn->findEX('all', array(
                'conditions' => array('id' => $id),
            ));

            // データ未存在の場合は一覧に戻す
            if (empty($data)) {
                return $this->redirect('lists');
            }

            //ビューにセット
            $this->set('data', $data);
        } else {
            return $this->redirect('lists');
        }
    }

    /**
     * 登録処理
     */
    public function insert() {
        $resultMessage = "";
        //入力チェック
        if ($this->inputValidate($this->request->data['EmployeeKbn'], $resultMessage, null)) {
            if ($this->EmployeeKbn->saveEX($this->request->data['EmployeeKbn'])) {
                //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg01'], 'default', array('class' => 'success'));
                $this->Flash->success($this->property['message']['infoMsg01']);
                return $this->redirect(['action' => 'add']);
            } else {
                //$this->getRequest()->getSession()->setFlash($this->property['message']['exceptionMsg01']);
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
        } else {
            //$this->getRequest()->getSession()->setFlash($resultMessage);
            $this->Flash->set($resultMessage);
            $data = array();
            array_push($data, array('EmployeeKbn' => $this->request->data['EmployeeKbn']));
            $this->set('data', $data);
        }
        $this->add();
        $this->render('add');
    }

    /**
     * 更新処理
     */
    public function update() {
        if (empty($this->request->data)) {
            $this->request->data = $this->EmployeeKbn->read();
        } else {
            if (isset($this->request->data['update_proc'])) {
                // 修正
                $resultMessage = "";
                // 入力チェック
                if ($this->inputValidate($this->request->data['EmployeeKbn'], $resultMessage, null)) {
                    if ($this->EmployeeKbn->saveEX($this->request->data['EmployeeKbn'])) {
                        //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg02'], 'default', array('class' => 'success'));
                        $this->Flash->success($this->property['message']['infoMsg02']);
                        return $this->redirect('/employee-kbns/lists');
                    } else {
                        //$this->getRequest()->getSession()->setFlash($this->property['message']['exceptionMsg01']);
                        $this->Flash->set($this->property['message']['exceptionMsg01']);
                    }
                } else {
                    //$this->getRequest()->getSession()->setFlash($resultMessage);
                    $this->Flash->set($resultMessage);
                    $data = array();
                    array_push($data, array('EmployeeKbn' => $this->request->data['EmployeeKbn']));
                    $this->set('data', $data);
                    $this->render('detail');
                    return;
                }
            } elseif (isset($this->request->data['delete_proc'])) {
                // 削除
                $this->EmployeeKbn->id = $this->request->data['EmployeeKbn']['id'];

                if ($this->EmployeeKbn->saveEX(['id' => $this->EmployeeKbn->id, 'delete_flg' => '1'])) {
                    //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg03'], 'default', array('class' => 'success'));
                    $this->Flash->success($this->property['message']['infoMsg03']);
                    return $this->redirect('/employee-kbns/lists');
                } else {
                    //$this->getRequest()->getSession()->setFlash($this->property['message']['exceptionMsg01']);
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            } elseif (isset($this->request->data['undelete_proc'])) {
                // 削除を取り消す
                $this->EmployeeKbn->id = $this->request->data['EmployeeKbn']['id'];

                if ($this->EmployeeKbn->saveEX(['id' => $this->EmployeeKbn->id,'delete_flg' => '0'])) {
                    //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg02'], 'default', array('class' => 'success'));
                    $this->Flash->success($this->property['message']['infoMsg02']);
                    return $this->redirect('/employee-kbns/lists');
                } else {
                    //$this->getRequest()->getSession()->setFlash($this->message['exceptionMsg01']);
                    $this->Flash->set($this->message['exceptionMsg01']);
                }
            }
            $this->request->params['action'] = 'detail';
            $this->detail($this->request->data['EmployeeKbn']['id']);
            $this->render('detail');
        }
    }

    /**
     * バリデーション処理
     */
    private function inputValidate($data, &$resultMessage) {
        $employee_kbn = $this->convertEncode($data['employee_kbn']);
        $employee_kbn_name = $this->convertEncode($data['employee_kbn_name']);

        // 社員区分必須チェック
        if ($employee_kbn == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg29'];
            return false;
        } else {
            //社員区分存在チェック
            if ($this->request->action === 'insert') {
                $resultCount = $this->EmployeeKbn->findEX('count', array(
                    'conditions' => array('employee_kbn' => $employee_kbn),
                ));
                if ($resultCount > 0) {
                    //エラー処理
                    $resultMessage = sprintf($this->property['message']['errorMsg58']);
                    return false;
                }
            }
            // 社員区分半角数字チェック
            if (!preg_match("/^[1-9][0-9]*$/u", $employee_kbn)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg57'];
                return false;
            }
        }

        // 社員区分名
        // 禁則文字チェック
        if ($employee_kbn_name == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], '社員区分名');
            return false;
        } elseif ($this->checkStopWord($employee_kbn_name)) {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg45'], '社員区分名');
            return false;
        }

        return true;
    }
}
