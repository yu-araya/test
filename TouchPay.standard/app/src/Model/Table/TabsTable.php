<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tabs Model
 *
 * @property \App\Model\Table\TabsTable&\Cake\ORM\Association\BelongsTo $Tabs
 * @property \App\Model\Table\TabsTable&\Cake\ORM\Association\HasMany $Tabs
 *
 * @method \App\Model\Entity\Tab get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tab newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tab[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tab|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tab saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tab patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tab[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tab findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TabsTable extends AppTable
{
    public $name = 'Tab';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('tabs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Tabs', [
            'foreignKey' => 'tab_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Tabs', [
            'foreignKey' => 'tab_id',
        ]);
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
            ->scalar('tab_name')
            ->maxLength('tab_name', 50)
            ->requirePresence('tab_name', 'create')
            ->notEmptyString('tab_name');

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
        $rules->add($rules->existsIn(['tab_id'], 'Tabs'));

        return $rules;
    }

    public function getTabLists() {
        $datas = $this->findEX('all', [
            'fields' => ['tab_id', 'tab_name']
        ]);
        $result = [];
        foreach ($datas as $data) {
            array_push($result, [
                'tabNum' => $data['Tab']['tab_id'],
                'tabName' => $data['Tab']['tab_name']
            ]);
        }
        return $result;
    }
}

