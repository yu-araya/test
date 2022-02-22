<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FoodHistoryReservations Model
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\FoodHistoryReservation get($primaryKey, $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\FoodHistoryReservation findOrCreate($search, callable $callback = null, $options = [])
 */
class FoodHistoryReservationsTable extends AppTable
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

        $this->setTable('food_history_reservations');

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
            ->scalar('employee_kbn')
            ->maxLength('employee_kbn', 2)
            ->notEmptyString('employee_kbn');

        $validator
            ->integer('food_division')
            ->notEmptyString('food_division');

        $validator
            ->dateTime('target_date')
            ->notEmptyDateTime('target_date');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 50)
            ->allowEmptyString('reason');

        $validator
            ->scalar('data_type')
            ->maxLength('data_type', 1)
            ->notEmptyString('data_type');

        $validator
            ->decimal('food_cost')
            ->notEmptyString('food_cost');

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
     * @param $employeeId
     */
    public function getDailyOrderList($employeeId)
    {
        $today = date('Y-m-d');

        $result = $this->findEX('all', [
            'fields' => [
                'FoodHistoryReservations.employee_id',
                'FoodHistoryReservations.employee_kbn',
                'FoodHistoryReservations.food_division',
                'FoodHistoryReservations.target_date',
                'FoodHistoryReservations.reason',
                'FoodHistoryReservations.data_type',
                'FoodHistoryReservations.food_cost',
                'FoodDivisions.food_division_name',
                'FoodDivisions.food_cost',
            ],
            'conditions' => [
                'FoodHistoryReservations.employee_id' => $employeeId,
                // ["DATE_FORMAT(FoodHistoryReservations.target_date, '%Y-%m-%d') = ".$today],
                ["DATE_FORMAT(FoodHistoryReservations.target_date, '%Y-%m-%d') = '".$today."'"],
            ],
            'joins' => [
                [
                    'table' => 'food_divisions',
                    'alias' => 'FoodDivisions',
                    'type' => 'INNER',
                    'conditions' => [
                        'FoodDivisions.food_division = FoodHistoryReservations.food_division',
                    ],
                ],
            ],
        ]);

        return $result;

    }
}
