<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ReservationInfos Model
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\ReservationInfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\ReservationInfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ReservationInfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ReservationInfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReservationInfo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReservationInfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ReservationInfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ReservationInfo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReservationInfosTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('reservation_infos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Employees', [
//            'foreignKey' => 'employee_id',
//            'joinType' => 'INNER',
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
            ->requirePresence('employee_kbn', 'create')
            ->notEmptyString('employee_kbn');

        $validator
            ->integer('food_division')
            ->requirePresence('food_division', 'create')
            ->notEmptyString('food_division');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 50)
            ->allowEmptyString('reason');

        $validator
            ->dateTime('reservation_date')
            ->requirePresence('reservation_date', 'create')
            ->notEmptyDateTime('reservation_date');

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
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
//    public function buildRules(RulesChecker $rules)
//    {
//        $rules->add($rules->existsIn(['employee_id'], 'Employees'));
//
//        return $rules;
//    }

    /**
     * 予約日別集計件数取得
     * @param $yyyymm 年月
     * @param $foodDivisionArray 食事区分リスト
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getDailySummary($yyyymm, $foodDivisionArray, $employeeKbnArray) {
        $params = array(
            'yyyymm' => $yyyymm,
        );

        $sql  = "SELECT DATE_FORMAT(reservation_date, '%Y-%m-%d') AS reservation_date ";
        $sql .= "      ,COUNT(employee_id) AS count ";
        $sql .= "FROM  reservation_infos ";
        $sql .= "WHERE DATE_FORMAT(reservation_date, '%Y%m') = :yyyymm ";
        $sql .= "AND   food_division IN('".implode("','", $foodDivisionArray)."') ";
        $sql .= "AND   state_flg IN ('0', '1') ";
        $sql .= "AND   delete_flg = 0 ";
        $sql .= "AND   employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "GROUP BY reservation_date ";
        $sql .= "ORDER BY reservation_date ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 予約日別詳細取得
     * @param $yyyymmdd
     * @param $foodDivisionArray 食事区分リスト
     * @param $employeeKbnArray 社員区分リストgroup
     * @param $excludeDelete 削除データ除外フラグ（true：除外）
     */
    public function getReservationDetail($yyyymmdd, $foodDivisionArray, $employeeKbnArray, $excludeDelete = false) {
        $params = array(
            'yyyymmdd' => $yyyymmdd,
        );

        //バーチャルフィールドの定義
//        $this->virtualFields['reservation_date'] = 0;
//        $this->virtualFields['employee_name1'] = 0;
//        $this->virtualFields['food_division_name'] = 0;

        $sql  = "SELECT ReservationInfo.id ";
        $sql .= "      ,ReservationInfo.employee_id ";
        $sql .= "      ,ReservationInfo.reason ";
        $sql .= "      ,DATE_FORMAT(ReservationInfo.reservation_date, '%Y-%m-%d') AS reservation_date ";
        $sql .= "      ,ReservationInfo.state_flg ";
        $sql .= "      ,ReservationInfo.created ";
        $sql .= "      ,EmployeeInfo.employee_name1 AS employee_name1 ";
        $sql .= "      ,ReservationInfo.food_division ";
        $sql .= "      ,FoodDivision.food_division_name AS food_division_name ";
        $sql .= "FROM  reservation_infos AS ReservationInfo ";
        $sql .= "    LEFT JOIN employee_infos AS EmployeeInfo ";
        $sql .= "        ON ReservationInfo.employee_id = EmployeeInfo.employee_id ";
        $sql .= "    LEFT JOIN food_divisions AS FoodDivision ";
        $sql .= "        ON ReservationInfo.food_division = FoodDivision.food_division ";
        $sql .= "WHERE DATE_FORMAT(ReservationInfo.reservation_date, '%Y%m%d') = :yyyymmdd ";
        $sql .= "AND   ReservationInfo.food_division IN('".implode("','", $foodDivisionArray)."') ";
        $sql .= "AND   ReservationInfo.delete_flg = 0 ";
        $sql .= "AND   ReservationInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";

        if ($excludeDelete) {
            $sql .= "AND   state_flg IN ('0', '1') ";
        }

        $sql .= "ORDER BY ReservationInfo.reservation_date, ReservationInfo.employee_id, ReservationInfo.created ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 指定日付の予約情報を削除
     * @param $foodDivision
     * @param $date
     */
    public function deleteReservationDate($foodDivision, $date) {
        $params = array(
            'food_division' => $foodDivision,
            'reservation_date' => $date,
            'modified' => date("YmdHis", strtotime("now")),
        );

        $sql  = "UPDATE	reservation_infos ";
        $sql .= "SET	 state_flg = 2 ";
        $sql .= "		,modified = :modified ";
        $sql .= "WHERE	food_division = :food_division ";
        $sql .= "AND	DATE_FORMAT(reservation_date, '%Y-%m-%d') = :reservation_date ";
        $sql .= "AND	state_flg IN (0, 1) ";
        $sql .= "AND	delete_flg = 0 ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 社員別予約情報取得
     * @param $yyyymm 年月
     * @param $employeeId 社員コード
     */
    public function getEmployeeReservationInfo($yyyymm, $employeeId) {
        $params = array(
            'yyyymm' => $yyyymm,
            'employee_id' => $employeeId,
        );

        //バーチャルフィールドの定義
//        $this->virtualFields['reservation_date'] = 0;
//        $this->virtualFields['food_division'] = 0;

        $sql  = "SELECT DATE_FORMAT(reservation_date, '%Y-%m-%d') AS reservation_date ";
        $sql .= "      ,food_division AS food_division ";
        $sql .= "FROM  reservation_infos ";
        $sql .= "WHERE DATE_FORMAT(reservation_date, '%Y%m') = :yyyymm ";
        $sql .= "AND   employee_id = :employee_id ";
        $sql .= "AND   state_flg IN ('0', '1') ";
        $sql .= "AND   delete_flg = 0 ";
        $sql .= "ORDER BY reservation_date ";

        return $this->execQuery($sql, $params);
    }

    /**
     * 食事手当用実績状況の情報取得（予約）
     * @param $yyyymm 年月
     * @param $foodDivision 食事区分
     * @param $employeeKbnArray 社員区分リスト
     */
    public function getCsvGaEmployeeKbnPerformance($yyyymm, $foodDivision, $employeeKbnArray) {
        $params = array(
            'yyyymm' => $yyyymm,
            'food_division' => $foodDivision,
        );

        //バーチャルフィールドの定義
//        $this->virtualFields['food_count'] = 0;
//        $this->virtualFields['food_cost'] = 0;

        $sql  = "SELECT  ReservationInfo.employee_kbn						AS employee_kbn ";
        $sql .= "		,IFNULL(COUNT(ReservationInfo.food_division), 0)	AS ReservationInfo__food_count ";
        $sql .= "		,IFNULL(SUM(ReservationInfo.food_cost), 0)			AS ReservationInfo__food_cost ";
        $sql .= "FROM	reservation_infos ReservationInfo ";
        $sql .= "		LEFT JOIN food_divisions FoodDivision ";
        $sql .= "			ON  ReservationInfo.food_division = FoodDivision.food_division ";
        $sql .= "			AND FoodDivision.delete_flg = '0' ";
        $sql .= "WHERE	ReservationInfo.food_division = :food_division ";
        $sql .= "AND	DATE_FORMAT(ReservationInfo.reservation_date, '%Y%m') = :yyyymm ";
        $sql .= "AND	ReservationInfo.state_flg IN ('0', '1') ";
        $sql .= "AND	ReservationInfo.delete_flg = '0' ";
        $sql .= "AND	ReservationInfo.employee_kbn IN('".implode("','", $employeeKbnArray)."') ";
        $sql .= "GROUP BY ReservationInfo.employee_kbn ";
        $sql .= "ORDER BY LPAD(ReservationInfo.employee_kbn, 2, 0) ";
        
        return $this->execQuery($sql, $params);
    }

    /**
     * カードタッチ日の予約が存在するか
     */
    public function existsCardReseptDateReservation($employeeId, $foodDivision, $cardReceptTime) {
	$reservation_date = date('Y-m-d', strtotime($cardReceptTime));
        return $this->exists(
            array(
                'employee_id' => $employeeId,
                'food_division IN' => $foodDivision,
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') = '$reservation_date'",
                'state_flg IN' => array('0', '1'),
                'delete_flg' => '0'
            )
        );
    }

    /**
     * 予約状況一括登録画面用
     * 翌週・翌々週の予約状況を取得
     */
    public function getWeeklyReservationData($nextSunday, $next2Sunday) {
        return $this->findEX('all', [
            'fields' => [
		'ReservationInfos.id',
                'ReservationInfos.employee_id',
                'ReservationInfos.employee_kbn',
                'ReservationInfos.food_division',
                'ReservationInfos.reason',
                'ReservationInfos.reservation_date',
                'ReservationInfos.state_flg',
                'ReservationInfos.food_cost',
                'ReservationInfos.created',
                'ReservationInfos.modified',
                'ReservationInfos.delete_flg',
                'EmployeeInfo.employee_name1',
                'FoodDivision.food_division_name',
                'InstrumentDivision.instrument_name'
            ],
            'conditions' => [
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') >= '$nextSunday'",
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') < '$next2Sunday'"
            ],
            'order' => [
                'ReservationInfos.reservation_date'=>'asc',
                'ReservationInfos.employee_id' => 'asc',
            ],
            'joins' => [
                [
                    'table' => 'employee_infos',
                    'alias' => 'EmployeeInfo',
                    'type' => 'LEFT',
                    'conditions' => 'ReservationInfos.employee_id = EmployeeInfo.employee_id'
                ],
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'LEFT',
                    'conditions' => 'ReservationInfos.food_division = FoodDivision.food_division'
                ],
                [
                    'table' => 'instrument_divisions',
                    'alias' => 'InstrumentDivision',
                    'type' => 'LEFT',
                    'conditions' => 'InstrumentDivision.instrument_division = FoodDivision.instrument_division'
                ]
            ],
        ]);
    }
    
    /**
     * 
     */
    public function getReservationDataWithinPeriod($employeeId, $startDate, $endDate) {
//        $this->virtualFields['count'] = 0;
        $list = $this->findEX('all', [
            'fields' => [
                'FoodDivision.instrument_division',
                'ReservationInfos.reservation_date',
                'ReservationInfos.food_division',
                'ReservationInfo__count' => 'count(ReservationInfos.food_division)'
            ],
            'conditions' => [
                'employee_id' => $employeeId,
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') >= '$startDate'",
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') <= '$endDate'",
                'state_flg <>' => 2
            ],
            'joins' => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'LEFT',
                    'conditions' => 'ReservationInfos.food_division = FoodDivision.food_division'
                ]
            ],
            'group' => [
                'FoodDivision.instrument_division',
                'ReservationInfos.food_division',
                'ReservationInfos.reservation_date',
            ]
        ]);
        $result = [];
        foreach($list as $value) {
            array_push($result, [
                'instrumentDivision' => $value['FoodDivision']['instrument_division'],
                'reservationDate' =>date("Y/m/d", strtotime($value['ReservationInfo']['reservation_date'])),
                'foodDivision' => $value['ReservationInfo']['food_division'],
                'count' => $value['ReservationInfo']['ReservationInfo']['count'],
            ]);
        }
        return $result;
    }
    
    /**
     * 個人の１ヶ月の喫食状況を取得
     * 
     * @param employeeId
     * @param month yyyy-mmの形
     */
    public function getUserReservation($employeeId, $startMonth) {
        return $this->findEX("all", [
            "fields" => [
                "ReservationInfos.id",
                "ReservationInfos.employee_id",
                "ReservationInfos.employee_kbn",
                "ReservationInfos.food_division",
                "ReservationInfos.reason",
                "ReservationInfos.reservation_date",
                "ReservationInfos.state_flg",
                "ReservationInfos.food_cost",
                "ReservationInfos.created",
                "ReservationInfos.modified",
                "ReservationInfos.delete_flg",
                "InstrumentDivision.instrument_name",
                "FoodDivision.food_division_name",
                "FoodDivision.instrument_division"],
            "conditions" => [
                "ReservationInfos.employee_id" => $employeeId,
                "ReservationInfos.reservation_date >= " => $startMonth
            ],
            "joins" => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'FoodDivision.food_division = ReservationInfos.food_division'
                    ]
                ],
                [
                    'table' => 'instrument_divisions',
                    'alias' => 'InstrumentDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'InstrumentDivision.instrument_division = FoodDivision.instrument_division'
                    ]
                ]
            ]
        ]);
    }
}
