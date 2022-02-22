<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;


/**
 * FoodPeriods Model
 *
 * @method \App\Model\Entity\FoodPeriod get($primaryKey, $options = [])
 * @method \App\Model\Entity\FoodPeriod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\FoodPeriod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FoodPeriod|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodPeriod saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodPeriod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FoodPeriod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FoodPeriod findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FoodPeriodsTable extends AppTable
{
    // CakePHP2の場合のvalidate
    /*
    public $validate = [
        'start_date' => [
            'rule' => 'date',
            'message' => '開始日を選択してください。',
            'allowEmpty' => false
        ], 'food_period_name' => [
            'rule' => ['lengthBetween', 1, 50],
            'message' => '食事名は1から50文字の間で入力してください',
            'allowEmpty' => false
        ], 'food_price' => [
            'lengthBetween' => [
                'rule' => ['lengthBetween', 1, 7],
                'message' => '価格は1から7桁の間の数値で入力してください',
                'allowEmpty' => false
            ], 'naturalNumber' => [
                'rule' => 'naturalNumber',
                'message' => '価格は1から7桁の間の数値で入力してください',
                'allowEmpty' => false
            ]
        ]
    ];
    */

    private $FoodDivision = NULL;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('food_periods');
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
            ->notEmptyString('food_division');

        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date', '開始日を選択してください。');

        $validator
            ->scalar('food_period_name')
            ->maxLength('food_period_name', 50, '食事名は1から50文字の間で入力してください')
            ->requirePresence('food_period_name', 'create')
            ->notEmptyString('food_period_name', '食事名は1から50文字の間で入力してください');

        $validator
            ->decimal('food_price')
            ->maxLength('food_price', 7, '価格は1から7桁の間の数値で入力してください')
            ->requirePresence('food_price', 'create')
            ->notEmptyString('food_price', '価格は1から7桁の間の数値で入力してください')
            ->naturalNumber('food_price', '価格は1から7桁の間の数値で入力してください');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    /**
     * 該当の食事区分と日付のFoodPeriodのデータを返却
     * 
     * @param division 食事区分
     * @param date 開始日
     */
    public function getFoodPeriodInfo($division, $date) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        return $this->findEX('first', [
            'fields' => [
		'FoodPeriods.id',
                'FoodPeriods.food_division',
                'FoodPeriods.start_date',
                'FoodPeriods.food_period_name',
                'FoodPeriods.food_price',
                'FoodPeriods.created',
                'FoodPeriods.modified',
                'FoodPeriods.delete_flg',
                'FoodDivision.instrument_division'
            ],
            'conditions' => [
                'FoodPeriods.food_division' => $division,
                'FoodPeriods.start_date <=' => $date,
                'FoodPeriods.delete_flg' => 0
            ],
            'order' => 'start_date desc',
            'joins' => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivision',
                    'type' => 'INNER',
                    'conditions' => [
                        'FoodDivision.food_division = FoodPeriods.food_division'
                    ]
                ]
            ]
        ]);
    }

    /**
     * 指定した食事区分の金額が個別に変更されていて、期限内であればその金額を返却
     * 
     * @param division 食事区分
     * @param date 開始日
     */
    public function getFoodPrice($division, $date) {
        $res = $this->getFoodPeriodInfo($division, $date);

        if ($res) {
            return $res['FoodPeriod']['food_price'];
        } else {
            $foodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
       //     $this->FoodDivision = new FoodDivisions();
            $food_cost = $foodDivision->getFoodCost($division);
            return $food_cost ? $food_cost : 0;
        }
    }

    /**
     * 食事期間が重複しているかチェック
     * 
     * @param data
     * @param existData 既存データのチェックを排除するならtrue
     */ 
    public function isFoodTermExisting($data, $existData) {
        $foodDivision = $data['food_division'];
        $startDate = $data['start_date'];
        $conditions = [
            'food_division' => $foodDivision,
            'start_date' => $startDate,
            'delete_flg' => 0
        ];
        if ($existData && isset($data['id'])) {
            $conditions["NOT"] = ["id" => $data['id']];
        }
        $count = $this->findEX('count', ['conditions' => $conditions]);
        return $count > 0;
    }
}
