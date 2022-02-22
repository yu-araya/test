<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use App\Controller\AppController;

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppTable extends Table {
    // プロパティファイルデータ
//    public $property = array();

    private $FoodDivision = NULL;   
    private $InstrumentDivision = NULL;   

    public function __construct() {
        parent::__construct();

//       if (empty($this->property)) {
//            $appController = new AppController();
//            $this->property = $appController->getPropertyArray();
//        }
    }

    /**
     * 食事区分リストを返します。
     * @param string $base 拠点
     * @param bool $isReserve 予約
     */
    protected function getFoodDivisionArrayFrom($base, $isReserve = false) {
        $foodDivsion = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
        return $foodDivsion->getFoodDivisionList($base, $isReserve);
    }

    /**
     * 食事区分が予約かどうかを返します。
     * @param string $foodDivsion 食事区分
     * @return boolean 予約の場合、trueを返します。
     */
    protected function isReserve($foodDivsion) {
        foreach (array_keys($this->getInstrumentDivisionList()) as $baseKbn) {
            if (in_array($foodDivsion, $this->getFoodDivisionArrayFrom($baseKbn, true))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 機器区分リストを取得
     */
    public function getInstrumentDivisionList() {
        $instrumentDivision = $this->InstrumentDivision ? $this->InstrumentDivision : TableRegistry::getTableLocator()->get('InstrumentDivisions');
        $instrumentDivisionsList = $instrumentDivision->findEX('all', array(
            'conditions' => array('delete_flg' => '0'),
            'order' => 'instrument_division'
        ));
        return Hash::Combine($instrumentDivisionsList, '{n}.InstrumentDivision.instrument_division', '{n}.InstrumentDivision.instrument_name');
    }

    /**
     * CakePHP2 ⇒ CakePHP3.9.3 バージョンアップ対応 wrap
     */
    public function findEX($type = 'all', $options = [], $useOriginalMethod = false) {
	$result_model = null;
        if ($useOriginalMethod) {
            return parent::find($type, $options);
        }
        // CakePHP3系の場合、joins → joinに変更
        if(isset($options['joins'])){
            $options['join'] = $options['joins'];
            unset($options['joins']);
        }
        if ($type == 'first') {
            $query = self::findEX('all', $options);
            $result = $query->first();
        }elseif ($type == 'count') {
            $query = self::findEX('all', $options);
            $result = $query->count();
        }else{
            $result = parent::find($type, $options);
	}
//print_r($result->sql());
 
        // find('first')からの呼び出しの場合は、first()を呼ぶ必要があるので、1回目はスキップ
        // find('count')からの呼び出しの場合は、count()を呼ぶ必要があるので、1回目はスキップ
        // paginateからの呼び出しの場合は、AppControllerのpaginateで成型するので、ここではスキップ
        // existsからの呼び出しの場合は、オブジェクトを返却する必要があるので、ここではスキップ
	if(!(isset(debug_backtrace()[1]['args'][0]) && debug_backtrace()[1]['args'][0] === 'first'
		|| isset(debug_backtrace()[1]['args'][0]) && debug_backtrace()[1]['args'][0] === 'count'
		|| isset(debug_backtrace()[1]['function']) && debug_backtrace()[1]['function'] === 'paginate'
		|| isset(debug_backtrace()[1]['function']) && debug_backtrace()[1]['function'] === 'exists')
        ){
            if(isset($result)){
		if(is_object($result)){
		    $result = $this->objectToArray($result);
		}
                // モデル名を連想配列のキーに追加（戻り値をCakePHP2系と合わせるための対応）
                $model_name = \Cake\Utility\Inflector::classify($this->_table);
                // joinの場合、配列が一個下の改装に入ってしまうので、それをフラットにする
                if ($type == 'first') {
                    if(isset($options['join'])){
                        foreach($options['join'] as $join){
                           $alias = \Cake\Utility\Inflector::classify($join['table']);
                           if(isset($result[$alias])){
                               $result_model[$alias] = $result[$alias];
                               unset($result[$alias]);
                           }
                        }
                    }
                    $result_model[$model_name] = $result;
		}elseif ($type == 'count') {
                    if($result >= 1){
                        $result_model = $result;
		    }else{
		        $result_model = 0;
		    }
                }else{
                     foreach($result as $key => &$res){
                         if(isset($options['join'])){
                             foreach($options['join'] as $join){
                                 if (!is_string($join['table'])) {continue;} // table にサブクエリを渡した場合のwarning回避。とりあえずスキップ
                                 $alias = \Cake\Utility\Inflector::classify($join['table']);
                                 if(isset($res[$alias])){
                                     $result_model[$key][$alias] = $res[$alias];
                                     unset($res[$alias]);
                                 }
                             }
                         }
                         $result_model[$key][$model_name] = $res;
                     }
		}
            }
	}else{
	    $result_model = $result;
	}
	return $result_model;
    }

    /**
     * オブジェクトを配列に変換する
     * @param object $data オブジェクト
     * return 配列の値
     */
    public function objectToArray($obj) {
        return json_decode(json_encode($obj->toArray()), true);
    }

    /**
     * set（CakePHP2系の処理をそのまま生かすための対応）
     */
    function set($data) {
        $this->setData = $data;
    }

    /**
     * save オーバーライド（CakePHP2系の処理をそのまま生かすための対応）
     */
    function saveEX($data = null, $validate = true, $fieldList = []) {
        $this->setRowCount(0);
        // TODO バリデーション処理
        $this->insertId = null;
        $res = false;

        $new_model_name = \Cake\Utility\Inflector::camelize($this->_table);
        // 引数がnullの場合は、setから取得
        $data = !isset($data) ? $this->setData : $data;
        if(isset($data)){
            $tableRegistry = \Cake\ORM\TableRegistry::getTableLocator()->get($new_model_name);
            $entity = $tableRegistry->newEntity();
            foreach($data as $key => $val){
                $entity->$key = $val;
            }
            // idをプライマリーキーに指定
            if(isset($this->id)){
                $entity->{$tableRegistry->getPrimaryKey()} = $this->id;
                unset($this->id);
            }
	    $res = $tableRegistry->save($entity);
            if($res){
                $this->setInsertID($res->{$tableRegistry->getPrimaryKey()});
                $this->setRowCount(1);
            }
        }
        return $res;
    }

    /**
     * updateAll オーバーライド（処理件数をセットするための対応）
     */
    function updateAll($fields, $conditions) {
        $rowCnt = parent::updateAll($fields, $conditions);
        $this->setRowCount($rowCnt);
        return $rowCnt;
    }

    /**
    * DB接続処理
    */
    function getDbo() {
            return \Cake\Datasource\ConnectionManager::get('default');
    }

    /**
    * SQL直接実行処理
    */
    function execQuery($sql, $params = null) {
        $this->setRowCount(0);
        $first_char = strtolower(substr(trim(preg_replace('/^\(/', '', $sql)), 0, 1));
        switch ($first_char) {
            // select
            case 's':
                // パラメータありの場合はexecute
                if(isset($params)){
                    $result = $this->getDbo()->execute($sql, $params)->fetchAll('assoc');
                // パラメータなしの場合はquery
                }else{
                    $result = $this->getDbo()->query($sql)->fetchAll('assoc');
                }
                $this->setRowCount(count($result));
                return $result;
            // insert、update、delete
            default:
                // パラメータありの場合はexecute
                if(isset($params)){
                    $result = $this->getDbo()->execute($sql, $params);
                // パラメータなしの場合はquery
                }else{
                    $result = $this->getDbo()->query($sql);
                }
                
                $this->setInsertID($result->lastInsertId());
                $this->setRowCount($result->rowCount());
                return $result;
        }
    }

    /**
     * AppTableから処理件数をセット
     */
    function setRowCount($cnt){
        $this->_rowCount = $cnt;
        return;
    }

    /**
     * 処理件数を取得（CakePHP2系の処理をそのまま生かすための対応）
     */
    function getAffectedRows(){
        return $this->_rowCount;
    }

    /**
     * 最後に発行されたIDをセット
     */
    function setInsertID($id){
        $this->_insertId = $id;
        return;
    }

    /**
     * 最後に発行されたIDを取得（CakePHP2系の処理をそのまま生かすための対応）
     */
    function getInsertID(){
        return $this->_insertId;
    }
}
