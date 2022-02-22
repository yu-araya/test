<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * FoodDivisions Model
 *
 * @method \App\Model\Entity\FoodDivision get($primaryKey, $options = [])
 * @method \App\Model\Entity\FoodDivision newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\FoodDivision[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FoodDivision|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodDivision saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodDivision patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FoodDivision[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FoodDivision findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FoodDivisionsTable extends AppTable
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

        $this->setTable('food_divisions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->integer('food_division')
            ->requirePresence('food_division', 'create')
            ->notEmptyString('food_division')
            ->add('food_division', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('food_division_name')
            ->maxLength('food_division_name', 50)
            ->requirePresence('food_division_name', 'create')
            ->notEmptyString('food_division_name');

        $validator
            ->integer('instrument_division')
            ->requirePresence('instrument_division', 'create')
            ->notEmptyString('instrument_division');

        $validator
            ->decimal('food_cost')
            ->requirePresence('food_cost', 'create')
            ->notEmptyString('food_cost');

        $validator
            ->integer('reserve_food_division')
            ->notEmptyString('reserve_food_division');

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
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['food_division']));

        return $rules;
    }

    // 食事区分を取得
    public function getFoodDivision() {
        $res = $this->findEX('all', [
            'fields' => 'food_division'
        ]);
        $list = [];
        for ($i=0; $i<count($res); $i++) {
            array_push($list, $res[$i]['FoodDivision']['food_division']);
        }
        return $list;
    }

    /**
     * 予約の食事区分のリストを取得
     */
    public function getReserveFoodDivisionList() {
//        $this->virtualFields['instrument_name'] = '';
        $result = $this->findEX('all', [
            'fields' => [
                'FoodDivisions.food_division',
                'FoodDivision__instrument_name' => 'InstrumentDivision.instrument_name',
                'FoodDivisions.food_division_name'
            ],
            'conditions' => [
                'FoodDivisions.reserve_food_division <>' => 0
            ],
            'order' => 'FoodDivisions.food_division',
            'joins' => [
                [
                    'table' => 'instrument_divisions',
                    'alias' => 'InstrumentDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'InstrumentDivision.instrument_division = FoodDivisions.instrument_division'
                    ]
                ]
            ]
        ]);
        return $result;
    }

    /**
     * 予約の食事区分のみを取得
     */
    public function getReserveFoodDivision() {
        $list = [];
        $res = $this->getReserveFoodDivisionList();
        foreach($res as $value) {
            array_push($list, $value['FoodDivision']['food_division']);
        }
        return $list;
    }

    /**
     * API用予約の食事区分を取得
     * [{instrumentDivision,division,name}]
     */
    public function getReserveFoodDivisionListForApi() {
        $list = $this->findEX('all', [
            'fields' => [
                'instrumentDivision' => 'instrument_division',
                'division' => 'food_division', 
                'name' => 'food_division_name'
            ],
            'conditions' => [
                'reserve_food_division <>' => 0,
                'delete_flg' => 0
            ]
        ]);
        $result = [];
        foreach($list as $value) {
            array_push($result, $value['FoodDivision']);
        }
        return $result;
    }

    // food_divisionsテーブルよりコストを取得する
    public function getFoodCost($foodDivision) {
        $res = $this->findEX('first', array(
            'conditions' => array(
                'food_division' => $foodDivision
            )
        ));
        return $res ? $res['FoodDivision']['food_cost'] : null;
    }

    /**
     * food_divisionsより食事区分を取得
     * @param string instrumentDivision 
     * @param bool isReserve 予約を取得したい場合true
     */
    public function getFoodDivisionList($instrumentDivision, $isReserve) {
        $conditions = [
            'FoodDivisions.instrument_division' => $instrumentDivision
        ];
        if($isReserve) {
//            $conditions['FoodDivisions.reserve_food_division <>'] = '0';
            $conditions += ['FoodDivisions.reserve_food_division <>' => 0];
                
        } else {
            $conditions += ['FoodDivisions.reserve_food_division' => 0];
        }
        $result = [];
        // 食事区分を取得
        $foodDivision =  $this->findEX('all', array(
            'fields' => ['FoodDivisions.food_division'],
            'conditions' => $conditions
        ));
        foreach((array)$foodDivision as $value){
            array_push($result, $value['FoodDivision']['food_division']);
        }
        return $result;
    }

    /**
     * 喫食の食事区分リストを取得
     * セレクトボックスに表示するため、事業所区分毎に配列にする
     * @param 事業所区分リスト
     */
    public function getFoodDivisionSelectList($instrumentDivisionList) {
        $result = [];
        foreach($instrumentDivisionList as $key => $value) {
            $conditions = [ 
                'FoodDivisions.reserve_food_division' => '0',
                'FoodDivisions.instrument_division' => $key
            ];
            $list = $this->findEX('all', [ 'conditions' => $conditions ]);
            $result[$key] = Hash::Combine($list, '{n}.FoodDivision.food_division', '{n}.FoodDivision.food_division_name');
        }
        return $result;
    }
}
