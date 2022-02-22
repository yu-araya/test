<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DayOffCalendars Model
 *
 * @method \App\Model\Entity\DayOffCalendar get($primaryKey, $options = [])
 * @method \App\Model\Entity\DayOffCalendar newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DayOffCalendar[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DayOffCalendar|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DayOffCalendar saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DayOffCalendar patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DayOffCalendar[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DayOffCalendar findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DayOffCalendarsTable extends AppTable
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

        $this->setTable('day_off_calendars');
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
            ->integer('base_kbn')
            ->requirePresence('base_kbn', 'create')
            ->notEmptyString('base_kbn');

        $validator
            ->dateTime('day_off_datetime')
            ->requirePresence('day_off_datetime', 'create')
            ->notEmptyDateTime('day_off_datetime');

        return $validator;
    }

    /**
     * カレンダーの指定日を削除
     * @param $baseKbn 拠点区分
     * @param $date 日時
     */
    public function deleteDate($baseKbn, $date) {
        $params = array(
            'base_kbn' => $baseKbn,
            'day_off_datetime' => $date,
        );

        $sql  = "DELETE FROM day_off_calendars ";
        $sql .= "WHERE  base_kbn = :base_kbn ";
        $sql .= "AND    DATE_FORMAT(day_off_datetime, '%Y-%m-%d') = :day_off_datetime ";

        return $this->execQuery($sql, $params);
    }
}
