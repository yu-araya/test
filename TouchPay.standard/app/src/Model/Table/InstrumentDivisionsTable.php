<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * InstrumentDivisions Model
 *
 * @method \App\Model\Entity\InstrumentDivision get($primaryKey, $options = [])
 * @method \App\Model\Entity\InstrumentDivision newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\InstrumentDivision[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\InstrumentDivision|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InstrumentDivision saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\InstrumentDivision patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\InstrumentDivision[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\InstrumentDivision findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class InstrumentDivisionsTable extends AppTable
{
    public $name = 'InstrumentDivisions';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('instrument_divisions');
        $this->setDisplayField('instrument_division');
        $this->setPrimaryKey('instrument_division');

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
            ->integer('instrument_division')
            ->allowEmptyString('instrument_division', null, 'create');

        $validator
            ->scalar('instrument_name')
            ->maxLength('instrument_name', 50)
            ->requirePresence('instrument_name', 'create')
            ->notEmptyString('instrument_name');

        $validator
            ->integer('delete_flg')
            ->notEmptyString('delete_flg');

        return $validator;
    }

    /**
     * 全ての事業所区分を取得
     * @return [['事業所区分', '事業所名'][][]...]
     */
    public function getInstrumentDivisionList() {
        $list = $this->findEX('all', [
            'fields' => ['instrument_division', 'instrument_name']
        ]);
        $result = [];
        foreach($list as $key => $value) {
            array_push($result, [
                'instrument_division' => $value['InstrumentDivision']['instrument_division'],
                'instrument_name' => $value['InstrumentDivision']['instrument_name']
            ]);
        }
        return $result;
    }

    /**
     * 喫食メニューが存在する事業所区分のみ取得
     * @return [事業所区分=>事業所名]
     */
    public function getEatingInstrumentDivisionList() {
        $query = $this->find('list', [
            'keyField' => 'instrument_division',
            'valueField' => 'instrument_name',
        ]);
	$result = $query->join([
		'table' => 'food_divisions',
                'alias' => 'FoodDivisions',
                'type' => 'INNER',
                'conditions' => [
                    'InstrumentDivisions.instrument_division = FoodDivisions.instrument_division'
                ]
	])->where(['FoodDivisions.reserve_food_division' => '0'])->group(['InstrumentDivisions.instrument_division']);
	return $result->toArray();
    }
}
