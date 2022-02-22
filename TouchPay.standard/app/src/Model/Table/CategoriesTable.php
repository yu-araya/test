<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

/**
 * Categories Model
 *
 * @method \App\Model\Entity\Category get($primaryKey, $options = [])
 * @method \App\Model\Entity\Category newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Category[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Category[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Category findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CategoriesTable extends AppTable
{
    public $name = 'Category';

    private $FoodDivision = NULL;
    private $Category = NULL;
    private $FoodPeriod = NULL;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('categories');
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
            ->integer('category')
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

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

    /**
     * タブレットに表示する最新のメニュー名/価格を表示する
     */
    public function getDisplayTabletMenu(){

        $displayTabletMenus = array();

        // 有効なデータのみ取得
        //$this->FoodDivision = new FoodDivision();
	$foodDivision = $this->FoodDivision ? $this->FoodDivision : TableRegistry::getTableLocator()->get('FoodDivisions');
        $foodDivisionArray = $foodDivision->findEX('all', array('conditions' => array('delete_flg' => '0')));
        $foodDivisionArray = Hash::extract($foodDivisionArray, '{n}.FoodDivision');


        foreach ($foodDivisionArray as $key => $foodDivision) {
            $displayTabletMenu = array();

            // 最新のメニューとデータを取得
            $categoryParam = array(
                'fields' => array('category'),
                'conditions' => array('food_division' => $foodDivision['food_division'])
            );
            //$this->Category = new Category();
            $category = $this->Category ? $this->Category : TableRegistry::getTableLocator()->get('Categories');
            $category = $category->findEX('first', $categoryParam)['Category']['category'];
            // 0は表示しない
            if($category <> 0){
                //$this->FoodPeriod = new FoodPeriod();
                $foodPeriod = $this->FoodPeriod ? $this->FoodPeriod : TableRegistry::getTableLocator()->get('FoodPeriods');
                $displayTabletMenu = array(
                    'food_division' => $foodDivision['food_division'],
                    'food_division_name' => $foodDivision['food_division_name'],
                    'instrument_division' => $foodDivision['instrument_division'],
                    'food_cost' => $foodPeriod->getFoodPrice($foodDivision['food_division'], date('Y-m-d')),
                    'category' => $category
                );
                array_push($displayTabletMenus, $displayTabletMenu);
            }
        }
        return $displayTabletMenus;
    }

}
