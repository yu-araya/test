<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Administrators Model
 *
 * @method \App\Model\Entity\Administrator get($primaryKey, $options = [])
 * @method \App\Model\Entity\Administrator newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Administrator[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Administrator|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Administrator saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Administrator patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Administrator findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AdministratorsTable extends AppTable
{
    public $name = 'Administrator';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('administrators');
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
            ->scalar('login_name')
            ->maxLength('login_name', 10)
            ->requirePresence('login_name', 'create')
            ->notEmptyString('login_name');

        $validator
            ->scalar('password')
            ->maxLength('password', 10)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('role')
            ->maxLength('role', 1)
            ->requirePresence('role', 'create')
            ->notEmptyString('role');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    public function getRole($login_name) {
        return  $this->findEX('first', [
            'fields' => ['role'],
            'conditions' => [
                'login_name' => $login_name
            ]
        ])['Administrator']['role'];
    }

}
