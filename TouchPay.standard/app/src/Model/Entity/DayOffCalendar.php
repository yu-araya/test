<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DayOffCalendar Entity
 *
 * @property int $id
 * @property int $base_kbn
 * @property \Cake\I18n\FrozenTime $day_off_datetime
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class DayOffCalendar extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'base_kbn' => true,
        'day_off_datetime' => true,
        'created' => true,
        'modified' => true,
    ];
}
