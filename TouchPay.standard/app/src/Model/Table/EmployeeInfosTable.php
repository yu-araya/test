<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EmployeeInfos Model
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\EmployeeInfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmployeeInfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EmployeeInfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeInfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmployeeInfo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmployeeInfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeInfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeInfo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EmployeeInfosTable extends AppTable
{
    const MIN_DATE = '1900-01-01 00:00:00';
    const MAX_DATE = '9999-12-31 23:59:59';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('employee_infos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Employees', [
//            'foreignKey' => 'employee_id',
//        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('employee_kbn')
            ->maxLength('employee_kbn', 2)
            ->allowEmptyString('employee_kbn');

        $validator
            ->scalar('employee_name1')
            ->maxLength('employee_name1', 50)
            ->allowEmptyString('employee_name1');

        $validator
            ->scalar('employee_name2')
            ->maxLength('employee_name2', 50)
            ->allowEmptyString('employee_name2');

        $validator
            ->scalar('password')
            ->maxLength('password', 10)
            ->allowEmptyString('password');

        $validator
            ->scalar('dining_license_flg')
            ->maxLength('dining_license_flg', 1)
            ->allowEmptyString('dining_license_flg');

        $validator
            ->date('dining_licensed_date')
            ->allowEmptyDate('dining_licensed_date');

        $validator
            ->scalar('ic_card_number')
            ->maxLength('ic_card_number', 16)
            ->allowEmptyString('ic_card_number');

        $validator
            ->dateTime('iccard_valid_s_time')
            ->allowEmptyDateTime('iccard_valid_s_time');

        $validator
            ->dateTime('iccard_valid_e_time')
            ->allowEmptyDateTime('iccard_valid_e_time');

        $validator
            ->scalar('ic_card_number2')
            ->maxLength('ic_card_number2', 16)
            ->allowEmptyString('ic_card_number2');

        $validator
            ->dateTime('iccard_valid_s_time2')
            ->allowEmptyDateTime('iccard_valid_s_time2');

        $validator
            ->dateTime('iccard_valid_e_time2')
            ->allowEmptyDateTime('iccard_valid_e_time2');

        $validator
            ->scalar('memo')
            ->maxLength('memo', 40)
            ->allowEmptyString('memo');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
//    public function buildRules(RulesChecker $rules)
//    {
//        $rules->add($rules->existsIn(['employee_id'], 'Employees'));
//
//        return $rules;
//    }

    // 登録されている社員の総件数取得
    public function getAllEmployeeNum() {
        $res = $this->findEX('count', array(
            'conditions' => array(
                'delete_flg' => 0
            )
        ));
        return $res ? $res: 0;
    }

    public function userLoginConfirm($employeeID, $password) {
        $result = $this->findEX('first', [
            'conditions' => [
                'employee_id' => $employeeID, 
                'password' => $password,
                'delete_flg <>' => 1,
            ]
        ]);
        return $result;
    }

    public function getCheckEmployeeId($employeeId) {
        $conditions = array(
            'employee_id' => $employeeId,
        );

        // 登録済み社員IDの件数取得
        $resultCount = $this->findEX('count', array(
            'conditions' => $conditions,
        ));

        return $resultCount;
    }

    public function getEmployeeInfo($employeeId) {
        $list = $this->findEX('first', [
            'conditions' => [
                'employee_id ' => $employeeId,
            ]
        ]);
        return [
            'id' => $list['EmployeeInfo']['employee_id'],
            'name' => $list['EmployeeInfo']['employee_name1'],
            'password' => $list['EmployeeInfo']['password']
        ];
    }

    public function getEmpoyeeKbn($employee_id) {
        $result = $this->findEX('first', [
            'fields' => ['employee_kbn'],
            'conditions' => [
                'employee_id' => $employee_id
            ]
        ]);
        return $result['EmployeeInfo']['employee_kbn'];
    }

    public function getCheckIcCardNumber($employee_id, $card_id) {
        $conditions = array(
            'employee_id !=' => $employee_id,
            'delete_flg' => 0,
            'OR' => array(
                array('UCASE(ic_card_number)' => strtoupper($card_id)),
                array('UCASE(ic_card_number2)' => strtoupper($card_id))
            ),
        );

        // 登録済み社員IDの件数取得
        $resultCount = $this->findEX('count', array(
            'conditions' => $conditions,
        ));

        return $resultCount;
    }

    /**
     * 食事別日別実績状況の情報取得
     * @param $yyyymm 年月
     * @param $foodDivision 食事区分
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvHrFoodDaily($yyyymm, $foodDivision, $employeeKbnArray) {
        $params = array(
            'yyyymm' => $yyyymm,
            'food_division' => $foodDivision,
        );

        //バーチャルフィールドの定義
//        $this->virtualFields['employee_kbn_name'] = 0;
//        $this->virtualFields['target_date'] = 0;
//        $this->virtualFields['food_count'] = 0;

        $sql  = "SELECT  EmployeeInfo.employee_id				AS employee_id ";
        $sql .= "		,EmployeeInfo.employee_kbn				AS employee_kbn ";
        $sql .= "		,EmployeeInfo.employee_name1			AS employee_name1 ";
        $sql .= "		,EmployeeKbn.employee_kbn_name			AS employee_kbn_name ";
        $sql .= "		,FoodInfo.target_date					AS target_date ";
        $sql .= "		,IFNULL(FoodInfo.food_count, 0)			AS food_count ";
        $sql .= "FROM	employee_infos AS EmployeeInfo ";

        switch ($foodDivision) {
            case '1':	// 滋賀昼
                $sql .= " 		LEFT JOIN ( ";
                $sql .= " 				SELECT   employee_id ";
                $sql .= "						,employee_kbn ";
                $sql .= "						,DATE_FORMAT(card_recept_time, '%Y%m%d') AS target_date ";
                $sql .= "						,IFNULL(COUNT(food_division), 0) AS food_count ";
                $sql .= "				FROM	food_history_infos  ";
                $sql .= "				WHERE	food_division = :food_division ";
                $sql .= "				AND		DATE_FORMAT(card_recept_time, '%Y%m') = :yyyymm ";
                $sql .= "				AND		state_flg IN ('0', '1') ";
                $sql .= "				AND		delete_flg = '0' ";
                $sql .= "				AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= "				GROUP BY employee_id, employee_kbn, DATE_FORMAT(card_recept_time, '%Y%m%d') ";
                $sql .= " 		) AS FoodInfo ";
                $sql .= "			ON EmployeeInfo.employee_id = FoodInfo.employee_id ";
                break;
            case '2':	// 滋賀夜
            case '3':	// 京都昼
                $sql .= " 		LEFT JOIN ( ";
                $sql .= " 				SELECT   employee_id ";
                $sql .= "						,employee_kbn ";
                $sql .= "						,DATE_FORMAT(reservation_date, '%Y%m%d') AS target_date ";
                $sql .= "						,IFNULL(COUNT(food_division), 0) AS food_count ";
                $sql .= "				FROM	reservation_infos  ";
                $sql .= "				WHERE	food_division = :food_division ";
                $sql .= "				AND		DATE_FORMAT(reservation_date, '%Y%m') = :yyyymm ";
                $sql .= "				AND		state_flg IN ('0', '1') ";
                $sql .= "				AND		delete_flg = '0' ";
                $sql .= "				AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= "				GROUP BY employee_id, employee_kbn, DATE_FORMAT(reservation_date, '%Y%m%d') ";
                $sql .= " 		) AS FoodInfo ";
                $sql .= "			ON EmployeeInfo.employee_id = FoodInfo.employee_id ";
                break;
        }

        $sql .= "		LEFT JOIN employee_kbns AS EmployeeKbn ";
        $sql .= "			ON EmployeeInfo.employee_kbn = EmployeeKbn.employee_kbn ";

        $sql .= "WHERE	EmployeeInfo.delete_flg = '0' ";
        $sql .= "AND	EmployeeInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "ORDER BY LPAD(EmployeeInfo.employee_kbn, 2, 0), EmployeeInfo.employee_id, FoodInfo.target_date ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 社員別月間実績状況の情報取得
     * @param $yyyymm 年月
     * @param $employeeKbnArray 社員区分リスト
     * @param array $foodDivisions 食事区分
     */
    public function getCsvHrEmployeePerformance($startYmd, $endYmd, $employeeKbnArray, $foodDivisions) {
        $params = array(
            'startYmd' => $startYmd,
            'endYmd' => $endYmd
        );

        $size = count($foodDivisions);

        $sql  = "SELECT  EmployeeInfo.employee_id					AS employee_id ";
        $sql .= "		,EmployeeInfo.employee_kbn					AS employee_kbn ";
        $sql .= "		,EmployeeInfo.employee_name1				AS employee_name1 ";
        $sql .= "		,EmployeeKbn.employee_kbn_name				AS employee_kbn_name ";

        for ($i = 0; $i < $size; $i++) {
            if ($this->isReserve($foodDivisions[$i])) {
                $sql .= "		,IFNULL(ReservationInfo".($i + 1).".food_count,0)	AS food_count".($i + 1)." ";
                $sql .= "		,IFNULL(ReservationInfo".($i + 1).".food_cost ,0)	AS food_cost".($i + 1)." ";
            } else {
                $sql .= "		,IFNULL(FoodHistoryInfo".($i + 1).".food_count,0)	AS food_count".($i + 1)." ";
                $sql .= "		,IFNULL(FoodHistoryInfo".($i + 1).".food_cost ,0)	AS food_cost".($i + 1)." ";
            }
        }

        $sql .= "FROM	employee_infos AS EmployeeInfo ";

        for ($i = 0; $i < $size; $i++) {
            $foodDivision = $foodDivisions[$i];
            if ($this->isReserve($foodDivision)) {
                $sql .= " 		LEFT JOIN ( ";
                $sql .= " 				SELECT	 a.employee_id ";
                $sql .= "						,IFNULL(COUNT(a.food_division), 0) AS food_count ";
                $sql .= "						,IFNULL(SUM(a.food_cost), 0) AS food_cost ";
                $sql .= "				FROM	reservation_infos AS a ";
                $sql .= "				WHERE	a.food_division = $foodDivision";
                $sql .= "				AND		DATE_FORMAT(a.reservation_date, '%Y-%m-%d') BETWEEN :startYmd AND :endYmd ";
                $sql .= "				AND		a.state_flg IN ('0', '1') ";
                $sql .= "				AND		a.delete_flg = '0' ";
                $sql .= "				AND		a.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= "			  	GROUP BY a.employee_id ";
                $sql .= " 		) AS ReservationInfo".($i + 1)." ";
                $sql .= "			ON EmployeeInfo.employee_id = ReservationInfo".($i + 1).".employee_id ";
            } else {
                $sql .= " 		LEFT JOIN ( ";
                $sql .= " 				SELECT	 a.employee_id ";
                $sql .= "						,IFNULL(COUNT(a.food_division), 0) AS food_count ";
                $sql .= "						,IFNULL(SUM(a.food_cost), 0) AS food_cost ";
                $sql .= "				FROM	food_history_infos AS a ";
                $sql .= "				WHERE	a.food_division = $foodDivision";
                $sql .= "				AND		DATE_FORMAT(a.card_recept_time, '%Y-%m-%d') BETWEEN :startYmd AND :endYmd ";
                $sql .= "				AND		a.state_flg IN ('0', '1') ";
                $sql .= "				AND		a.delete_flg = '0' ";
                $sql .= "				AND		a.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= "			  	GROUP BY a.employee_id ";
                $sql .= " 		) AS FoodHistoryInfo".($i + 1)." ";
                $sql .= "			ON EmployeeInfo.employee_id = FoodHistoryInfo".($i + 1).".employee_id ";
            }
        }

        $sql .= "		LEFT JOIN employee_kbns AS EmployeeKbn ";
        $sql .= "			ON EmployeeInfo.employee_kbn = EmployeeKbn.employee_kbn ";

        $sql .= "WHERE	EmployeeInfo.delete_flg = '0' ";
        $sql .= "AND	EmployeeInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "ORDER BY LPAD(EmployeeInfo.employee_kbn, 2, 0), EmployeeInfo.employee_id ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 食事手当用実績状況の情報取得
     * @param $yyyymm 年月
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvHrFoodCostDaily($yyyymm, $employeeKbnArray) {
        $params = array(
            'yyyymm' => $yyyymm,
        );

        $sql  = "SELECT  EmployeeInfo.employee_id				AS employee_id ";
        $sql .= "		,EmployeeInfo.employee_kbn				AS employee_kbn ";
        $sql .= "		,EmployeeInfo.employee_name1			AS employee_name1 ";
        $sql .= "		,EmployeeKbn.employee_kbn_name			AS employee_kbn_name ";
        $sql .= "		,FoodInfo.target_date					AS target_date ";
        $sql .= "		,IFNULL(FoodInfo.food_cost, 0)			AS food_cost ";
        $sql .= "FROM	employee_infos AS EmployeeInfo ";

        $sql .= " 		LEFT JOIN ( ";
        $sql .= " 				SELECT   a.employee_id ";
        $sql .= "						,a.employee_kbn ";
        $sql .= "						,DATE_FORMAT(a.target_date, '%Y%m%d') AS target_date ";
        $sql .= "						,IFNULL(MAX(b.food_cost), 0) AS food_cost ";
        $sql .= "				FROM	food_history_reservations AS a ";
        $sql .= "						LEFT JOIN food_divisions b ";
        $sql .= "							ON  a.food_division = b.food_division ";
        $sql .= "							AND b.delete_flg = '0' ";
        $sql .= "				WHERE	DATE_FORMAT(a.target_date, '%Y%m') = :yyyymm ";
        $sql .= "				AND		a.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "				GROUP BY a.employee_id, a.employee_kbn, DATE_FORMAT(a.target_date, '%Y%m%d') ";
        $sql .= " 		) AS FoodInfo ";
        $sql .= "			ON EmployeeInfo.employee_id = FoodInfo.employee_id ";

        $sql .= "		LEFT JOIN employee_kbns AS EmployeeKbn ";
        $sql .= "			ON EmployeeInfo.employee_kbn = EmployeeKbn.employee_kbn ";

        $sql .= "WHERE	EmployeeInfo.delete_flg = '0' ";
        $sql .= "AND	EmployeeInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "ORDER BY LPAD(EmployeeInfo.employee_kbn, 2, 0), EmployeeInfo.employee_id, FoodInfo.target_date ";

        return $this->execQuery($sql, $params);
    }

    public function getUsefulCardIdCount() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:GET,POST, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("text/plain;charset=UTF-8");

        $fields = array(
            'employee_id',
        );
        $conditions = array(
            'delete_flg' => 0,
            'dining_license_flg' => 0,
        );
        // 有効ICカードのレコード検索
        $result = $this->findEX('count', array(
            'conditions' => $conditions,
        ));
        printf("dsp=".$result);
        printf("\r\nsnd=1002");
    }

    public function getUsefulCardIdInfo($start_count, $select_count) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:GET,POST, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("text/plain;charset=UTF-8");

        $now = date('Y-m-d H:i:s');
        if (date('H') >= '22') {
            // 22時を超えている場合は翌日分として扱う
            $now = date('Y-m-d 00:00:00', strtotime("1 day"));
        }
        $card_id = "";

        $fields = array(
            'employee_id',
            'employee_kbn',
            'ic_card_number',
            'iccard_valid_s_time' => 'IFNULL(iccard_valid_s_time, \''. self::MIN_DATE .'\')',
            'iccard_valid_e_time' => 'IFNULL(iccard_valid_e_time, \''. self::MAX_DATE .'\')',
            'ic_card_number2',
            'iccard_valid_s_time2' => 'IFNULL(iccard_valid_s_time2, \''. self::MIN_DATE .'\')',
            'iccard_valid_e_time2' => 'IFNULL(iccard_valid_e_time2, \''. self::MAX_DATE .'\')',
        );
        $conditions = array(
            'delete_flg' => 0,
            'dining_license_flg' => 0,
        );
        // 登録済み情報を全件取得
	if ($start_count > 0) {
            $start_count = $start_count - 1;
	}
        $result = $this->findEX('all', array(
            'fields' => $fields,
            'conditions' => $conditions,
            'offset' => $start_count,
            'limit' => $select_count,
        ));

        $count = 0;
        for ($i=0; $i < count($result); $i++) {
            $record = $result[$i]['EmployeeInfo'];
            // ICカード番号
            if ($record['ic_card_number'] != null && $record['ic_card_number'] != '') {
                // 期間内のICカード番号のみ抽出
                if ($record['iccard_valid_s_time'] <= $now && $now <= $record['iccard_valid_e_time']) {
                    if ($card_id != '') {
                        $card_id .= ",";
                    }
                    $card_id .= $record['ic_card_number']. '-' .$record['employee_id']. '-' .$record['employee_kbn'];
                    $count++;
                }
            }
            // ICカード番号２
            if ($record['ic_card_number2'] != null && $record['ic_card_number2'] != '') {
                // 期間内のICカード番号のみ抽出
                if ($record['iccard_valid_s_time2'] <= $now && $now <= $record['iccard_valid_e_time2']) {
                    if ($card_id != '') {
                        $card_id .= ",";
                    }
                    $card_id .= $record['ic_card_number2']. '-' .$record['employee_id']. '-' .$record['employee_kbn'];
                    $count++;
                }
            }
        }

        // 機器区分
        $idivision = isset($_POST["idivision"]) ? $_POST["idivision"] : null;
        // 食事区分
        $fno = isset($_POST["fno"]) ? $_POST["fno"] : null;

        if (empty($idivision)) {
            \Cake\Log\Log::info('ic_card export:'. $count);
        } else {
            \Cake\Log\Log::info('ic_card export:'. $count .' [idiv:'. $idivision .', fno:'. $fno .']');
        }

        printf("dsp=".$card_id);
        printf("\r\nsnd=1002");
    }

    public function getUsefulCardIdReservationInfo($start_count, $select_count) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:GET,POST, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("text/plain;charset=UTF-8");

        $now = date('Y-m-d H:i:s');
        if (date('H') >= '22') {
            // 22時を超えている場合は翌日分として扱う
            $now = date('Y-m-d 00:00:00', strtotime("1 day"));
        }
        $card_id = "";

        //バーチャルフィールドの定義
//        $this->virtualFields['iccard_valid_s_time'] = 0;
//        $this->virtualFields['iccard_valid_e_time'] = 0;
//        $this->virtualFields['iccard_valid_s_time2'] = 0;
//        $this->virtualFields['iccard_valid_e_time2'] = 0;
//        $this->virtualFields['food_division'] = 0;

        $fields = array(
            'ic_card_number',
            'iccard_valid_s_time' => 'IFNULL(iccard_valid_s_time, \''. self::MIN_DATE .'\')',
            'iccard_valid_e_time' => 'IFNULL(iccard_valid_e_time, \''. self::MAX_DATE .'\')',
            'ic_card_number2',
            'iccard_valid_s_time2' => 'IFNULL(iccard_valid_s_time2, \''. self::MIN_DATE .'\')',
            'iccard_valid_e_time2' => 'IFNULL(iccard_valid_e_time2, \''. self::MAX_DATE .'\')',
            'food_division' => 'ReservationInfo.food_division'
        );
        $conditions = array(
            'EmployeeInfos.delete_flg' => 0,
            'dining_license_flg' => 0,
        );
        // 登録済み情報を全件取得
        if($start_count > 0) {
             $start_count = $start_count - 1;
        }
        $result = $this->findEX('all', array(
            'fields' => $fields,
            'conditions' => $conditions,
            'offset' => $start_count,
            'limit' => $select_count,
            'joins' => array(
                array(
                    'table' => 'reservation_infos',
                    'alias' => 'ReservationInfo',
                    'type' => 'INNER',
                    'conditions' => array(
                        'EmployeeInfos.employee_id = ReservationInfo.employee_id',
                        'ReservationInfo.state_flg IN (0, 1)',
                        'DATE_FORMAT(reservation_date, \'%Y%m%d\') = \'' .date('Ymd', strtotime($now)). '\'',
                    )
                ),
            ),
        ));

        $count = 0;
        for ($i=0; $i < count($result); $i++) {
            $record = $result[$i]['EmployeeInfo'];
            // ICカード番号
            if ($record['ic_card_number'] != null && $record['ic_card_number'] != '') {
                // 期間内のICカード番号のみ抽出
                if ($record['iccard_valid_s_time'] <= $now && $now <= $record['iccard_valid_e_time']) {
                    if ($card_id != '') {
                        $card_id .= ",";
                    }
                    $card_id .= $record['ic_card_number']. '-' .$record['food_division'];
                    $count++;
                }
            }
            // ICカード番号２
            if ($record['ic_card_number2'] != null && $record['ic_card_number2'] != '') {
                // 期間内のICカード番号のみ抽出
                if ($record['iccard_valid_s_time2'] <= $now && $now <= $record['iccard_valid_e_time2']) {
                    if ($card_id != '') {
                        $card_id .= ",";
                    }
                    $card_id .= $record['ic_card_number2']. '-' .$record['food_division'];
                    $count++;
                }
            }
        }

        // 機器区分
        $idivision = isset($_POST["idivision"]) ? $_POST["idivision"] : null;
        // 食事区分
        $fno = isset($_POST["fno"]) ? $_POST["fno"] : null;

        if (empty($fno)) {
            \Cake\Log\Log::info('ic_card reservation export:'. $count);
        } else {
            \Cake\Log\Log::info('ic_card reservation export:'. $count .' [fno:'. $fno .']');
        }

        printf("dsp=".$card_id);
        printf("\r\nsnd=1002");
    }

    public function checkIcCard() {
        $now = date('Y-m-d H:i:s');

        // ICカード番号
        $ic_card_number = isset($_POST["cid"]) ? $_POST["cid"] : null;

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:GET,POST, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("text/plain;charset=UTF-8");

        $conditions = array(
            'delete_flg' => 0,
            'dining_license_flg' => 0,
            'OR' => array(
                array(
                    'UCASE(ic_card_number)' => strtoupper($ic_card_number),
                    'IFNULL(iccard_valid_s_time, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time, \'9999-12-31 23:59:59\') >=' => $now,
                ),
                array(
                    'UCASE(ic_card_number2)' => strtoupper($ic_card_number),
                    'IFNULL(iccard_valid_s_time2, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time2, \'9999-12-31 23:59:59\') >=' => $now,
                )
            ),
        );

        $result = $this->findEX('all', array(
            'conditions' => $conditions,
        ));

        if (empty($result)) {
            printf("dsp=");
            printf("\r\nsnd=1003");
        } else {
            $json = array('employee_id' => $result[0]['EmployeeInfo']['employee_id']);
            printf("dsp=". json_encode($json));
            printf("\r\nsnd=1002");
        }
    }

    /**
     * 有効な社員情報をICカード番号から取得する
     */
    public function findValidEmpInfoByIcCardNumber($ic_card_number, $card_recept_time) {
        $now = date('Y-m-d H:i:s', strtotime($card_recept_time));

        $conditions = array(
            'delete_flg' => 0,
            'dining_license_flg' => 0,
            'OR' => array(
                array(
                    'UCASE(ic_card_number)' => strtoupper($ic_card_number),
                    'IFNULL(iccard_valid_s_time, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time, \'9999-12-31 23:59:59\') >=' => $now,
                ),
                array(
                    'UCASE(ic_card_number2)' => strtoupper($ic_card_number),
                    'IFNULL(iccard_valid_s_time2, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time2, \'9999-12-31 23:59:59\') >=' => $now,
                )
            ),
        );

        $res = $this->findEX('first', array(
            'conditions' => $conditions,
        ));
	if ($res) {
	    return $res;
	} else {
	    return array();
	}
    }

    /**
     * 有効な全ての社員情報を取得する
     */
    public function findAllValidEmpInfo($card_recept_time) {
        $now = date('Y-m-d H:i:s', strtotime($card_recept_time));

        $conditions = array(
            'delete_flg' => 0,
            'OR' => array(
                array(
                    'NOT' => array(
                        'ic_card_number' => null,
                        'ic_card_number' => ''
                    ),
                    'IFNULL(iccard_valid_s_time, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time, \'9999-12-31 23:59:59\') >=' => $now,
                ),
                array(
                    'NOT' => array(
                        'ic_card_number2' => null,
                        'ic_card_number2' => ''
                    ),
                    'IFNULL(iccard_valid_s_time2, \'0000-01-01 00:00:00\') <=' => $now,
                    'IFNULL(iccard_valid_e_time2, \'9999-12-31 23:59:59\') >=' => $now,
                ),
            ),
            'NOT' => array(
                'dining_license_flg' => '1'
            )
        );

        return $this->findEX('all', array(
            'conditions' => $conditions,
        ));
    }

    /**
     * パスワード更新
     */
    public function updatePassword($employeeId, $password){
        return $this->updateAll([
                'password' => $password,
                'modified' => date("YmdHis", strtotime("now")),
            ],
            [
                'employee_id' => $employeeId,
            ]
        );
    }
}
