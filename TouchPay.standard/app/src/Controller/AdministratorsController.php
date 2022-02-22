<?php
namespace App\Controller;

//use App\Controller\AppController;

class AdministratorsController extends AppController
{
    //    public $uses = array('Administrator', 'LoginHistory');
    
    public function login($passChange = false)
    {
        $this->set('title_for_layout', 'ログイン画面');
        $this->Session->destroy();
        $standardPlan                              = boolval($this->property['plan_config']['standard_plan']);
        $this->data['Administrator']['login_name'] = isset($this->data['login_name']) ? $this->data['login_name'] : null;
        $this->data['Administrator']['password']   = isset($this->data['password']) ? $this->data['password'] : null;
        if (isset($this->data['Administrator']['login_name']) || isset($this->data['Administrator']['password'])) {
            $authIdentify = $this->Auth->identify();
            //            if ($this->Auth->login()) {
            if ($authIdentify) {
                $this->Auth->setUser($authIdentify);
                // ログイン履歴に登録
                $loginHistoryArray = array(
                    'login_name' => $this->data['Administrator']['login_name'],
                    'login_datetime' => date('Y-m-d H:i:s')
                );
                $this->LoginHistory->saveEX($loginHistoryArray);
                
                // ロール取得
                $role = $this->Administrator->getRole($this->data['Administrator']['login_name']);
                $this->Session->write('Administrator.role', $role);
                
                if ($passChange) {
                    return $this->redirect('/administrators/select');
                } else {
                    if ($role === '2') {
                        // 食堂業者用
                        return $this->redirect('/food-history-infos/sumdaily');
                    } else if ($standardPlan) {
                        return $this->redirect('/food-history-infos/select');
                    } else {
                        return $this->redirect('/reservation-infos/index/1');
                    }
                }
            } else {
                if (!empty($this->data['Administrator'])) {
                    $login_name = mb_convert_encoding($this->data['Administrator']['login_name'], 'utf-8', 'sjis');
                    $password   = mb_convert_encoding($this->data['Administrator']['password'], 'utf-8', 'sjis');
                    
                    $message = "";
                    if ($login_name == '' && $password == '') {
                        $message = $this->property['message']['errorMsg20'];
                    } elseif ($login_name != '' && $password == '') {
                        $message = $this->property['message']['errorMsg10'];
                    } elseif ($login_name == '' && $password != '') {
                        $message = $this->property['message']['errorMsg32'];
                    } else {
                        $message = $this->property['message']['errorMsg33'];
                    }
                    //$this->getRequest()->getSession()->setFlash(__($message));
                    $this->Flash->error(__($message));
                }
            }
        }
    }
    
    public function logout()
    {
        $this->Session->delete('property');
        return $this->redirect($this->Auth->logout());
    }
    
    public function index()
    {
        return $this->redirect('login');
    }
    
    public function select()
    {
        $this->set('title_for_layout', 'パスワード変更画面');
        $this->set('menuLink', $this->getMenuLink(8));
    }
    
    public function savePassword()
    {
        $newPassword1 = $this->request->data['Administrator']['new_password1'];
        $newPasswrod2 = $this->request->data['Administrator']['new_password2'];
	if ($this->checkPassword($newPassword1, $newPasswrod2)) {
	    $this->Administrator->id = $this->Auth->user('id');
            if (!$this->Administrator->exists(['id' => $this->Administrator->id])) {
                throw new NotFoundException(__('Invalid user'));
            }
            // 更新処理時
            $this->request->data['Administrator']['password'] = $this->request->data['Administrator']['new_password1'];

            if ($this->Administrator->saveEX($this->request->data['Administrator'])) {
                // 更新成功
                $this->Flash->success($this->property['message']['infoMsg02']);
                // 2つのパスワード入力フォームを空で表示するために、配列から破棄
                unset($this->request->data['Administrator']['new_password1'], $this->request->data['Administrator']['new_password2']);
            } else {
                // 更新失敗
                $this->Flash->set(__('The user could not be saved. Please, try again.'));
            }
        }
        return $this->redirect('/administrators/select');
    }
    
    private function checkPassword($password1 = null, $password2 = null)
    {
        if ($this->request->data['Administrator']['new_password1'] == '' && $this->request->data['Administrator']['new_password2'] == '') {
            $this->Flash->set($this->property['message']['errorMsg10']);
            return false;
        } elseif ($this->request->data['Administrator']['new_password1'] !== $this->request->data['Administrator']['new_password2']) {
            $this->Flash->set($this->property['message']['errorMsg11']);
            return false;
        }
        return true;
    }
}
