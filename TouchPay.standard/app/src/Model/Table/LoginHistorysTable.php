<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LoginHistorys Model
 *
 * @method \App\Model\Entity\LoginHistory get($primaryKey, $options = [])
 * @method \App\Model\Entity\LoginHistory newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\LoginHistory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LoginHistory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoginHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\LoginHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\LoginHistory[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\LoginHistory findOrCreate($search, callable $callback = null, $options = [])
 */
class LoginHistorysTable extends AppTable
{
    public $useTable = 'login_historys';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('login_historys');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->scalar('login_name')
            ->maxLength('login_name', 10)
            ->requirePresence('login_name', 'create')
            ->notEmptyString('login_name');

        $validator
            ->dateTime('login_datetime')
            ->requirePresence('login_datetime', 'create')
            ->notEmptyDateTime('login_datetime');

        return $validator;
    }
}
