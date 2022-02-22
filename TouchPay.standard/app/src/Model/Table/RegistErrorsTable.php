<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RegistErrors Model
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 *
 * @method \App\Model\Entity\RegistError get($primaryKey, $options = [])
 * @method \App\Model\Entity\RegistError newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RegistError[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RegistError|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RegistError saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RegistError patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RegistError[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RegistError findOrCreate($search, callable $callback = null, $options = [])
 */
class RegistErrorsTable extends AppTable
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

        $this->setTable('regist_errors');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->dateTime('occurrence_datetime')
            ->requirePresence('occurrence_datetime', 'create')
            ->notEmptyDateTime('occurrence_datetime');

        $validator
            ->scalar('function_name')
            ->maxLength('function_name', 10)
            ->requirePresence('function_name', 'create')
            ->notEmptyString('function_name');

        $validator
            ->scalar('error_level')
            ->maxLength('error_level', 50)
            ->requirePresence('error_level', 'create')
            ->notEmptyString('error_level');

        $validator
            ->scalar('reason')
            ->maxLength('reason', 50)
            ->requirePresence('reason', 'create')
            ->notEmptyString('reason');

        $validator
            ->scalar('ic_card_number')
            ->maxLength('ic_card_number', 16)
            ->allowEmptyString('ic_card_number');

        $validator
            ->integer('instrument_division')
            ->allowEmptyString('instrument_division');

        $validator
            ->integer('food_division')
            ->allowEmptyString('food_division');

        $validator
            ->dateTime('card_recept_time')
            ->allowEmptyDateTime('card_recept_time');

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
}
