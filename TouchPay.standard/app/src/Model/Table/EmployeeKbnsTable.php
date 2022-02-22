<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * EmployeeKbns Model
 *
 * @method \App\Model\Entity\EmployeeKbn get($primaryKey, $options = [])
 * @method \App\Model\Entity\EmployeeKbn newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\EmployeeKbn[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeKbn|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmployeeKbn saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\EmployeeKbn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeKbn[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\EmployeeKbn findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EmployeeKbnsTable extends AppTable
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

        $this->setTable('employee_kbns');
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
            ->scalar('employee_kbn')
            ->maxLength('employee_kbn', 2)
            ->requirePresence('employee_kbn', 'create')
            ->notEmptyString('employee_kbn')
            ->add('employee_kbn', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('employee_kbn_name')
            ->maxLength('employee_kbn_name', 50)
            ->requirePresence('employee_kbn_name', 'create')
            ->notEmptyString('employee_kbn_name');

        $validator
            ->scalar('food_allowance_flg')
            ->maxLength('food_allowance_flg', 1)
            ->notEmptyString('food_allowance_flg');

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
        $rules->add($rules->isUnique(['employee_kbn']));

        return $rules;
    }
}
