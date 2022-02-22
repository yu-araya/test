<?php

namespace App\Model\Table;

use Cake\I18n\Number;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * FoodHistoryInfos Model.
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\FoodHistoryInfo       get($primaryKey, $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo       newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo[]     newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo       saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo       patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo[]     patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryInfo       findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FoodHistoryInfosTable extends AppTable
{
    private $FoodPeriod = null;

    /**
     * Initialize method.
     *
     * @param array $config the configuration for the Table
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('food_history_infos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Employees', [
//            'foreignKey' => 'employee_id',
//           'joinType' => 'INNER',
//       ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator validator instance
     *
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
            ->requirePresence('employee_kbn', 'create')
            ->notEmptyString('employee_kbn');

        $validator
            ->scalar('ic_card_number')
            ->maxLength('ic_card_number', 16)
            ->requirePresence('ic_card_number', 'create')
            ->notEmptyString('ic_card_number');

        $validator
            ->integer('instrument_division')
            ->requirePresence('instrument_division', 'create')
            ->notEmptyString('instrument_division');

        $validator
            ->integer('food_division')
            ->requirePresence('food_division', 'create')
            ->notEmptyString('food_division');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 50)
            ->allowEmptyString('reason');

        $validator
            ->dateTime('card_recept_time')
            ->requirePresence('card_recept_time', 'create')
            ->notEmptyDateTime('card_recept_time');

        $validator
            ->scalar('state_flg')
            ->maxLength('state_flg', 1)
            ->notEmptyString('state_flg');

        $validator
            ->decimal('food_cost')
            ->requirePresence('food_cost', 'create')
            ->notEmptyString('food_cost');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @return \Cake\ORM\RulesChecker
     */
//    public function buildRules(RulesChecker $rules)
//    {
//        $rules->add($rules->existsIn(['employee_id'], 'EmployeeInfos'));
//
//       return $rules;
//    }

    /**
     * 社員精算確認 ファイル出力情報を取得します。
     *
     * @param $yyyymm 年月
     * @param $employeeId 社員ID
     * @param $employeeName1 社員名
     * @param $dining_license_flg 社員食堂利用許可フラグ
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getFoodHistorysSql($yyyymm = null, $employeeId = null, $employeeName1 = null, $dining_license_flg = null, $employeeKbnArray)
    {
        //バーチャルフィールドの定義
        $baseKbnArray = $this->getInstrumentDivisionList();
        /*
                $count = 0;
                foreach (array_keys($baseKbnArray) as $baseKbn) {
                    $count += 1;
        //            $this->virtualFields["sum_food_division$count"] = 0;
                }
                foreach (array_keys($baseKbnArray) as $baseKbn) {
                    $foodDivisionsForReservation = $this->getFoodDivisionArrayFrom($baseKbn, true);
                    if (count($foodDivisionsForReservation) > 0) {
                        $count += 1;
        //                $this->virtualFields["sum_food_division$count"] = 0;
                    }
                }
        //        $this->virtualFields['employee_kbn_name'] = 0;
        */
        $sql = 'SELECT  EmployeeInfo.employee_id						AS employee_id ';
        $sql .= '		,EmployeeInfo.employee_kbn					AS employee_kbn ';
        $sql .= '		,EmployeeInfo.employee_name1					AS employee_name1 ';
        $sql .= '		,EmployeeInfo.dining_license_flg				AS dining_license_flg ';
        $sql .= '		,EmployeeKbn.employee_kbn_name					AS employee_kbn_name ';

        $count = 0;
        foreach (array_keys($baseKbnArray) as $baseKbn) {
            ++$count;
            $sql .= "		,IFNULL(FoodHistoryInfo$baseKbn.sum_food_division,0)	AS sum_food_division$count ";
        }
        foreach (array_keys($baseKbnArray) as $baseKbn) {
            $foodDivisionsForReservation = $this->getFoodDivisionArrayFrom($baseKbn, true);
            if (count($foodDivisionsForReservation) > 0) {
                ++$count;
                $sql .= "		,IFNULL(ReservationInfo$baseKbn.sum_food_division,0)	AS sum_food_division$count ";
            }
        }

        $sql .= 'FROM	employee_infos AS EmployeeInfo ';
        $sql .= ' 		LEFT JOIN ( ';
        $sql .= ' 				SELECT  employee_kbn, ';
        $sql .= '						employee_kbn_name ';
        $sql .= '				FROM	employee_kbns  ';
        $sql .= "				WHERE	delete_flg = '0' ";
        $sql .= ' 		) AS EmployeeKbn ';
        $sql .= '			ON EmployeeInfo.employee_kbn = EmployeeKbn.employee_kbn ';

        foreach (array_keys($baseKbnArray) as $baseKbn) {
            $foodDivisions = $this->getFoodDivisionArrayFrom($baseKbn);
            $sql .= ' 		LEFT JOIN ( ';
            if (!empty($foodDivisions)) {
                $sql .= ' 				SELECT  employee_id, ';
                $sql .= '						IFNULL(COUNT(food_division),0) AS sum_food_division  ';
                $sql .= '				FROM	food_history_infos  ';
                $sql .= '				WHERE	';
                $sql .= '						food_division IN ('.implode(', ', $foodDivisions).')  AND ';
                $sql .= "						DATE_FORMAT(card_recept_time, '%Y%m') = '".$yyyymm."' ";
                $sql .= "				AND		state_flg IN ('0', '1') ";
                $sql .= "				AND		delete_flg = '0' ";
                $sql .= "				AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= '			  	GROUP BY employee_id ';
            } else {
                $sql .= ' SELECT employee_id, 0 AS sum_food_division from food_history_infos ';
            }
            $sql .= " 		) AS FoodHistoryInfo$baseKbn  ";
            $sql .= "			ON EmployeeInfo.employee_id = FoodHistoryInfo$baseKbn.employee_id ";
        }

        foreach (array_keys($baseKbnArray) as $baseKbn) {
            $foodDivisionsForReservation = $this->getFoodDivisionArrayFrom($baseKbn, true);
            $sql .= ' 		LEFT JOIN ( ';
            if (!empty($foodDivisionsForReservation)) {
                $sql .= ' 				SELECT  employee_id, ';
                $sql .= '						IFNULL(COUNT(food_division),0) AS sum_food_division  ';
                $sql .= '				FROM	reservation_infos  ';
                $sql .= '				WHERE	';
                $sql .= '						food_division IN ('.implode(', ', $foodDivisionsForReservation).')  AND ';
                $sql .= "						DATE_FORMAT(reservation_date, '%Y%m') = '".$yyyymm."' ";
                $sql .= "				AND		state_flg IN ('0', '1') ";
                $sql .= "				AND		delete_flg = '0' ";
                $sql .= "				AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
                $sql .= '			  	GROUP BY employee_id ';
            } else {
                $sql .= ' SELECT employee_id, 0 AS sum_food_division from reservation_infos ';
            }
            $sql .= " 		) AS ReservationInfo$baseKbn  ";
            $sql .= "			ON EmployeeInfo.employee_id = ReservationInfo$baseKbn.employee_id ";
        }

        $sql .= "WHERE	EmployeeInfo.delete_flg = '0' ";
        $sql .= "AND	EmployeeInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";

        if ($employeeId != null) {
            $sql .= "AND	EmployeeInfo.employee_id = '".$employeeId."'";
        }
        if ($employeeName1 != null) {
            $sql .= "AND	EmployeeInfo.employee_name1 LIKE '%".$employeeName1."%'";
        }
        if ($dining_license_flg == null) {
            $sql .= "AND	(EmployeeInfo.dining_license_flg = '0' ";
            $sql .= "OR	    (EmployeeInfo.dining_license_flg = '1' AND  DATE_FORMAT(EmployeeInfo.dining_licensed_date, '%Y%m') != '".$yyyymm."')) ";
        }

        $sql .= 'GROUP BY EmployeeInfo.employee_kbn, EmployeeInfo.employee_id, EmployeeInfo.employee_name1, EmployeeInfo.dining_license_flg ';
        $sql .= 'ORDER BY LPAD(EmployeeInfo.employee_kbn, 2, 0), EmployeeInfo.employee_id asc ';

        return $sql;
    }

    /**
     * 社員精算確認 精算金額訂正情報を取得します。
     *
     * @param $yyyymm 年月
     * @param $employeeId 社員ID
     */
    public function getEmployeeCostInfoList($yyyymm = null, $employeeId = null)
    {
        $sql = 'SELECT ';
        $sql .= ' FoodHistoryInfo.id AS FoodHistoryInfo__id, ';
        $sql .= ' FoodHistoryInfo.employee_id AS FoodHistoryInfo__employee_id, ';
        $sql .= ' FoodHistoryInfo.food_division AS FoodHistoryInfo__food_division, ';
        $sql .= ' FoodHistoryInfo.reason AS FoodHistoryInfo__reason, ';
        $sql .= ' FoodHistoryInfo.target_date AS FoodHistoryInfo__target_date, ';
        $sql .= ' FoodHistoryInfo.state_flg AS FoodHistoryInfo__state_flg, ';
        $sql .= ' FoodHistoryInfo.created AS FoodHistoryInfo__created, ';
        $sql .= ' FoodHistoryInfo.data_type AS FoodHistoryInfo__data_type, ';
        $sql .= ' FoodDivision.id AS FoodDivision__id, ';
        $sql .= ' FoodDivision.food_division AS FoodDivision__food_division, ';
        $sql .= ' FoodDivision.food_division_name AS FoodDivision__food_division_name, ';
        $sql .= ' FoodDivision.instrument_division AS FoodDivision__instrument_division, ';
        $sql .= ' FoodDivision.food_cost AS FoodDivision__food_cost, ';
        $sql .= ' FoodDivision.created AS FoodDivision__created, ';
        $sql .= ' FoodDivision.modified AS FoodDivision__modified, ';
        $sql .= ' FoodDivision.reserve_food_division AS FoodDivision__reserve_food_division, ';
        $sql .= ' FoodDivision.delete_flg AS FoodDivision__delete_flg ';
        $sql .= 'FROM ';
        $sql .= '	( ';

        // 実績情報の取得
        $sql .= '		SELECT	 id ';
        $sql .= '				,employee_id ';
        $sql .= '				,food_division ';
        $sql .= '				,reason ';
        $sql .= '				,card_recept_time AS target_date ';
        $sql .= '				,state_flg ';
        $sql .= '				,created ';
        $sql .= "				,'1' AS data_type ";
        $sql .= '		FROM	food_history_infos ';
        $sql .= "		WHERE	DATE_FORMAT(card_recept_time, '%Y%m') = '".$yyyymm."' ";
        $sql .= "		AND		employee_id = '".$employeeId."' ";
        $sql .= "		AND		delete_flg = '0' ";

        $sql .= '		UNION ALL ';

        // 予約情報の取得
        $sql .= '		SELECT	 id ';
        $sql .= '				,employee_id ';
        $sql .= '				,food_division ';
        $sql .= '				,reason ';
        $sql .= '				,reservation_date AS target_date ';
        $sql .= '				,state_flg ';
        $sql .= '				,created ';
        $sql .= "				,'2' AS data_type ";
        $sql .= '		FROM	reservation_infos ';
        $sql .= "		WHERE	DATE_FORMAT(reservation_date, '%Y%m') = '".$yyyymm."' ";
        $sql .= "		AND		employee_id = '".$employeeId."' ";
        $sql .= "		AND		delete_flg = '0' ";
        $sql .= '	) AS FoodHistoryInfo ';
        $sql .= '		LEFT JOIN food_divisions AS FoodDivision ';
        $sql .= '			ON FoodHistoryInfo.food_division = FoodDivision.food_division ';

        $sql .= 'ORDER BY FoodHistoryInfo.target_date ASC, FoodHistoryInfo.created ';

        return $this->execQuery($sql);
    }

    /**
     * 食堂精算 集計情報を取得します。
     *
     * @param $yyyymm 年月
     * @param $foodDivisionList 食事区分リスト
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getFoodHistorySummarySql($yyyymm, $foodDivisionList, $employeeKbnArray)
    {
        //バーチャルフィールドの定義
//        $this->virtualFields['card_recept_time'] = 0;

//        foreach ($foodDivisionList as $key => $foodDivision) {
//            $this->virtualFields['count'.$key] = 0;
//        }
//        $this->virtualFields['cost'] = 0;

        $sql = 'SELECT union_tbl.card_recept_time AS card_recept_time ';

        foreach ($foodDivisionList as $key => $foodDivision) {
            $sql .= ' ,SUM(union_tbl.count'.$key.') AS count'.$key.' ';
        }
        $sql .= ' ,SUM(union_tbl.food_cost) AS cost ';

        $sql .= 'FROM ';
        $sql .= '( ';

        // 食事区分が増える場合はUNIONで増やす
        $union = '';
        foreach ($foodDivisionList as $key => $foodDivision) {
            if (!empty($union)) {
                $union .= ' UNION ALL ';
            }

            $union .= " SELECT DATE_FORMAT(target_date, '%Y-%m-%d') AS card_recept_time ";

            foreach ($foodDivisionList as $key2 => $foodDivision2) {
                if ($key == $key2) {
                    $union .= ' ,1 AS count'.$key2;
                } else {
                    $union .= ' ,0 AS count'.$key2;
                }
            }
            $union .= ' ,food_cost';

            $union .= '    FROM  food_history_reservations ';
            $union .= '    WHERE food_division = '.$key;
            $union .= "    AND   DATE_FORMAT(target_date, '%Y%m') = '".$yyyymm."' ";
            $union .= "	   AND   employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        }
        $sql .= $union;

        $sql .= ') AS union_tbl ';
        $sql .= 'GROUP BY union_tbl.card_recept_time ';
        $sql .= 'ORDER BY union_tbl.card_recept_time ';

        $result = $this->execQuery($sql);

        return $result;
    }

    /**
     * 食事手当用実績状況の情報取得（実績）.
     *
     * @param $yyyymm 年月
     * @param $foodDivision 食事区分
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvGaEmployeeKbnPerformance($yyyymm, $foodDivision, $employeeKbnArray)
    {
        $params = [
            'yyyymm' => $yyyymm,
            'food_division' => $foodDivision,
        ];

        //バーチャルフィールドの定義
//        $this->virtualFields['food_count'] = 0;
//        $this->virtualFields['food_cost'] = 0;

        $sql = 'SELECT  FoodHistoryInfo.employee_kbn						AS employee_kbn ';
        $sql .= '		,IFNULL(COUNT(FoodHistoryInfo.food_division), 0)	AS food_count ';
        $sql .= '		,IFNULL(SUM(FoodHistoryInfo.food_cost), 0)			AS food_cost ';
        $sql .= 'FROM	food_history_infos FoodHistoryInfo ';
        $sql .= '		LEFT JOIN food_divisions FoodDivision ';
        $sql .= '			ON  FoodHistoryInfo.food_division = FoodDivision.food_division ';
        $sql .= "			AND FoodDivision.delete_flg = '0' ";
        $sql .= 'WHERE	FoodHistoryInfo.food_division = :food_division ';
        $sql .= "AND	DATE_FORMAT(FoodHistoryInfo.card_recept_time, '%Y%m') = :yyyymm ";
        $sql .= "AND	FoodHistoryInfo.state_flg IN ('0', '1') ";
        $sql .= "AND	FoodHistoryInfo.delete_flg = '0' ";
        $sql .= "AND	FoodHistoryInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= 'GROUP BY FoodHistoryInfo.employee_kbn ';
        $sql .= 'ORDER BY LPAD(FoodHistoryInfo.employee_kbn, 2, 0) ';

        return $this->execQuery($sql, $params);
    }

    /**
     * 会議費等、福利厚生費、現金支払実績状況の情報取得.
     *
     * @param $yyyymm 年月
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvGaEmployeeOtherPerformance($yyyymm, $employeeKbnArray)
    {
        $sql = 'SELECT	 FoodHistoryInfo.employee_kbn ';
        $sql .= '		,FoodHistoryInfo.employee_id ';
        $sql .= '		,FoodHistoryInfo.food_division ';
        $sql .= '		,FoodHistoryInfo.reason ';
        $sql .= '		,FoodHistoryInfo.target_date ';
        $sql .= '		,FoodHistoryInfo.data_type ';
        $sql .= '		,EmployeeInfo.employee_name1 ';
        $sql .= '		,FoodDivision.food_division_name ';
        $sql .= '		,EmployeeKbn.employee_kbn_name ';
        $sql .= 'FROM	food_history_reservations AS FoodHistoryInfo ';
        $sql .= '		LEFT JOIN employee_infos AS EmployeeInfo ';
        $sql .= '			ON EmployeeInfo.employee_id = FoodHistoryInfo.employee_id ';
        $sql .= '		LEFT JOIN food_divisions AS FoodDivision ';
        $sql .= '			ON FoodHistoryInfo.food_division = FoodDivision.food_division ';
        $sql .= '		LEFT JOIN employee_kbns AS EmployeeKbn ';
        $sql .= '			ON FoodHistoryInfo.employee_kbn = EmployeeKbn.employee_kbn ';
        $sql .= "WHERE	DATE_FORMAT(FoodHistoryInfo.target_date, '%Y%m') = '".$yyyymm."' ";
        $sql .= "AND	FoodHistoryInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= 'ORDER BY LPAD(FoodHistoryInfo.employee_kbn, 2, 0), FoodHistoryInfo.target_date, FoodHistoryInfo.employee_id ';

        return $this->execQuery($sql);
    }

    /**
     * 月別個別予約・実績状況の情報取得.
     *
     * @param $foodDivisionList 食事区分リスト
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvGaPerformance($startYmd, $endYmd, $foodDivisionList, $employeeKbnArray)
    {
        //バーチャルフィールドの定義
//        $this->virtualFields['employee_name1'] = 0;
//        $this->virtualFields['employee_ic_card_number'] = 0;
//        $this->virtualFields['food_division_name'] = 0;
//        $this->virtualFields['employee_kbn_name'] = '';

        $params = [
            'startYmd' => $startYmd,
            'endYmd' => $endYmd,
        ];

        $sql = 'SELECT  FoodHistoryInfo.* ';
        $sql .= '		,EmployeeInfo.employee_name1     AS employee_name1 ';
        $sql .= '		,EmployeeInfo.ic_card_number     AS employee_ic_card_number '; // 予約データ表示用
        $sql .= '		,FoodDivision.food_division_name AS food_division_name ';
        $sql .= '		,EmployeeKbn.employee_kbn_name   AS employee_kbn_name ';
        $sql .= 'FROM ';
        $sql .= '	( ';

        // 実績情報の取得
        $sql .= '		SELECT	 employee_id ';
        $sql .= '				,employee_kbn ';
        $sql .= '				,ic_card_number ';
        $sql .= '				,food_division ';
        $sql .= '				,reason ';
        $sql .= '				,card_recept_time AS target_date ';
        $sql .= '				,state_flg ';
        $sql .= '		FROM	food_history_infos ';
        $sql .= "		WHERE	DATE_FORMAT(card_recept_time, '%Y%-%m-%d') BETWEEN :startYmd AND :endYmd ";
        $sql .= "		AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "		AND		food_division IN('".implode("','", $foodDivisionList)."') ";

        $sql .= '		UNION ALL ';

        // 予約情報の取得
        $sql .= '		SELECT	 employee_id ';
        $sql .= '				,employee_kbn ';
        $sql .= "				,'' AS ic_card_number ";
        $sql .= '				,food_division ';
        $sql .= '				,reason ';
        $sql .= '				,reservation_date AS target_date ';
        $sql .= '				,state_flg ';
        $sql .= '		FROM	reservation_infos ';
        $sql .= "		WHERE	DATE_FORMAT(reservation_date, '%Y-%m-%d') BETWEEN :startYmd AND :endYmd ";
        $sql .= "		AND		employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "		AND		food_division IN('".implode("','", $foodDivisionList)."') ";

        $sql .= '	) AS FoodHistoryInfo ';
        $sql .= '		LEFT JOIN employee_infos AS EmployeeInfo ';
        $sql .= '			ON FoodHistoryInfo.employee_id = EmployeeInfo.employee_id ';
        $sql .= '		LEFT JOIN food_divisions AS FoodDivision ';
        $sql .= '			ON FoodHistoryInfo.food_division = FoodDivision.food_division ';
        $sql .= '		LEFT JOIN employee_kbns AS EmployeeKbn ';
        $sql .= '			ON FoodHistoryInfo.employee_kbn = EmployeeKbn.employee_kbn ';

        $sql .= 'ORDER BY FoodHistoryInfo.target_date ASC, FoodHistoryInfo.employee_id ASC ';

        return $this->execQuery($sql, $params);
    }

    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = [])
    {
        if ($page == 0) {
            $page = 1;
        }
        $recursive = -1;
        $sql = $extra['originalSql'].' LIMIT '.(($page - 1) * $limit).','.$limit;

        return $this->execQuery($sql);
    }

    public function paginateCount($conditions = null, $recursive = 0, $extra = [])
    {
        $sql = $extra['originalSql'];
        $this->recursive = $recursive;
        $results = $this->execQuery($sql);

        return count($results);
    }

    /**
     * 社員精算確認 精算実績情報を登録します。
     */
    public function registFoodHistoryInfo()
    {
        $status = isset($_POST['sts']) ? $_POST['sts'] : null;

        //ICカード番号
        $ic_card_number = isset($_POST['cid']) ? $_POST['cid'] : null;
        //社員コード
        $employee_id = isset($_POST['empid']) ? $_POST['empid'] : null;
        //社員区分
        $employee_kbn = isset($_POST['empkbn']) ? $_POST['empkbn'] : null;
        //機器区分
        $instrument_division = isset($_POST['idivision']) ? $_POST['idivision'] : null;
        //食事区分
        $food_division = isset($_POST['fno']) ? $_POST['fno'] : null;
        //カード受付時間
        $card_recept_time = isset($_POST['tim']) ? $_POST['tim'] : null;

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods:GET,POST, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('text/plain;charset=UTF-8');

        if ($ic_card_number != null) {
            printf('res=00');
            printf("\r\nsnd=1002");

            $conditions = [
                'employee_id' => $employee_id,
                'ic_card_number' => $ic_card_number,
                'card_recept_time' => $card_recept_time,
            ];
            // 登録済情報の取得（二重登録の回避の為）
            $result = $this->findEX('count', [
                'conditions' => $conditions,
            ]);

            if ($result == 0) {
                //$foodPeriod = new FoodPeriod();
                $foodPeriod = $this->FoodPeriod ? $this->FoodPeriod : TableRegistry::getTableLocator()->get('FoodPeriods');
                $food_cost = $foodPeriod->getFoodPrice($food_division, $card_recept_time);

                $params = [
                    'employee_id' => $employee_id,
                    'employee_kbn' => $employee_kbn,
                    'ic_card_number' => $ic_card_number,
                    'instrument_division' => $instrument_division,
                    'food_division' => $food_division,
                    'card_recept_time' => $card_recept_time,
                    'created' => date('YmdHis', strtotime('now')),
                    'modified' => date('YmdHis', strtotime('now')),
                    'food_cost' => $food_cost,
                ];

                $sql = 'INSERT INTO food_history_infos (';
                $sql .= 'employee_id, ';
                $sql .= 'employee_kbn, ';
                $sql .= 'ic_card_number, ';
                $sql .= 'instrument_division, ';
                $sql .= 'food_division, ';
                $sql .= 'card_recept_time, ';
                $sql .= 'created, ';
                $sql .= 'modified, ';
                $sql .= 'food_cost ';
                $sql .= ') values ( ';
                $sql .= ':employee_id, ';
                $sql .= ':employee_kbn, ';
                $sql .= ':ic_card_number, ';
                $sql .= ':instrument_division, ';
                $sql .= ':food_division, ';
                $sql .= ':card_recept_time, ';
                $sql .= ':created, ';
                $sql .= ':modified, ';
                $sql .= ':food_cost ';
                $sql .= ')';

                $this->execQuery($sql, $params);
            }
        } else {
            printf('res=99');
            printf("\r\nsnd=1003");
            printf("\r\ndsp=エラーが発生しました。");
        }
    }

    /**
     * 個人の１ヶ月の喫食状況を取得.
     *
     * @param employeeId
     * @param month yyyy-mmの形
     */
    public function getUserFoodHistory($employeeId, $startMonth)
    {
        return $this->findEX('all', [
            'fields' => [
                'FoodHistoryInfos.employee_id',
                'FoodHistoryInfos.employee_kbn',
                'FoodHistoryInfos.ic_card_number',
                'FoodHistoryInfos.instrument_division',
                'FoodHistoryInfos.food_division',
                'FoodHistoryInfos.reason',
                'FoodHistoryInfos.card_recept_time',
                'FoodHistoryInfos.state_flg',
                'FoodHistoryInfos.food_cost',
                'FoodHistoryInfos.created',
                'FoodHistoryInfos.modified',
                'FoodHistoryInfos.delete_flg',
                'InstrumentDivision.instrument_name',
        'FoodDivision.food_division_name',
        ],
            'conditions' => [
                'employee_id' => $employeeId,
                'card_recept_time >= ' => $startMonth,
            ],
            'joins' => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'FoodDivision.food_division = FoodHistoryInfos.food_division',
                    ],
                ],
                [
                    'table' => 'instrument_divisions',
                    'alias' => 'InstrumentDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'InstrumentDivision.instrument_division = FoodDivision.instrument_division',
                    ],
                ],
            ],
        ]);
    }

    /**
     * 当日の注文履歴を取得.
     *
     * @param [type] $instrumentDivision
     *
     * @return void
     */
    public function getDailyOrder($requestAll)
    {
        if ($requestAll === 'true') {
            $conditions = [
                'DATE_FORMAT(CURRENT_TIMESTAMP, "%Y%m%d") = DATE_FORMAT(FoodHistoryInfos.card_recept_time, "%Y%m%d")',
            ];
        } else {
            $before1Minute = date('Y-m-d H:i:s', strtotime('-1 minute'));
            $conditions = [
                "card_recept_time > '$before1Minute'",
            ];
        }

        $result = $this->findEX('all', [
            'fields' => [
                'FoodHistoryInfos.id',
                'FoodHistoryInfos__cardReceptTime' => 'FoodHistoryInfos.card_recept_time',
                'FoodHistoryInfos__foodDivision' => 'FoodDivision.food_division',
                'FoodHistoryInfos__foodName' => 'FoodDivision.food_division_name',
                'FoodHistoryInfos__employeeName' => 'EmployeeInfo.employee_name1',
            ],
            'conditions' => $conditions,
            'joins' => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'FoodDivision.food_division = FoodHistoryInfos.food_division',
                    ],
                ],
                [
                    'table' => 'employee_infos',
                    'alias' => 'EmployeeInfo',
                    'type' => 'INNER',
                    'conditions' => [
                        'EmployeeInfo.employee_id = FoodHistoryInfos.employee_id',
                    ],
                ],
            ],
            'order' => ['card_recept_time DESC', 'FoodHistoryInfos.id DESC'],
        ]);

        if (empty($result)) {
            return $result;
        }

        return Hash::extract($result, '{n}.FoodHistoryInfo');
    }

    /**
     * 売店用金額登録.
     *
     * @param [type] $cardId
     * @param [type] $cost
     *
     * @return void
     */
    public function registShopData($emplyeeInfo, $cardId, $time, $cost, $foodDivision)
    {
        $conditions = [
                'employee_id' => $emplyeeInfo['EmployeeInfo']['employee_id'],
                'employee_kbn' => $emplyeeInfo['EmployeeInfo']['employee_kbn'],
                'ic_card_number' => $cardId,
                'instrument_division' => 1,
                'food_division' => $foodDivision,
                'card_recept_time' => $time,
                'food_cost' => $cost,
            ];

        return $this->saveEX($conditions);
    }
}
