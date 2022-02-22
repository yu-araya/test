<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ContentsSetVersions Model.
 *
 * @property \App\Model\Table\TerminalsTable&\Cake\ORM\Association\BelongsTo $Terminals
 *
 * @method \App\Model\Entity\ContentsSetVersion       get($primaryKey, $options = [])
 * @method \App\Model\Entity\ContentsSetVersion       newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ContentsSetVersion[]     newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ContentsSetVersion|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContentsSetVersion       saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ContentsSetVersion       patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ContentsSetVersion[]     patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ContentsSetVersion       findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContentsSetVersionsTable extends AppTable
{
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

        $this->setTable('contents_set_versions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

//        $this->belongsTo('Terminals', [
//            'foreignKey' => 'terminal_id',
//            'joinType' => 'INNER',
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
            ->scalar('contents_type')
            ->maxLength('contents_type', 10)
            ->requirePresence('contents_type', 'create')
            ->notEmptyString('contents_type');

        $validator
            ->decimal('version')
            ->requirePresence('version', 'create')
            ->notEmptyString('version');

        $validator
            ->scalar('revision')
            ->maxLength('revision', 5)
            ->requirePresence('revision', 'create')
            ->notEmptyString('revision');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules the rules object to be modified
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
//        $rules->add($rules->existsIn(['terminal_id'], 'Terminals'));

        return $rules;
    }

    /**
     * タブレット・個人画面のコンテンツのバージョンを取得.
     *
     * @param [type] $contents 'tablet' or 'user'
     *
     * @return string 'version' + v + 'revision'
     */
    public function getContentsVersion($contents)
    {
        $result = $this->findEX('first', [
            'conditions' => [
                'contents_type' => $contents,
            ],
        ]);
        if (!empty($result)) {
            $version = doubleval($result['ContentsSetVersion']['version']);
            $revision = $result['ContentsSetVersion']['revision'];

            return (is_null($version) || is_null($revision) || $revision === '') ? '' : number_format($version, 2).'.'.strval($revision);
        } else {
            return '';
        }
    }
}
