<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RegistError Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $occurrence_datetime
 * @property string $function_name
 * @property string $error_level
 * @property string $reason
 * @property string|null $employee_id
 * @property string|null $ic_card_number
 * @property int|null $instrument_division
 * @property int|null $food_division
 * @property \Cake\I18n\FrozenTime|null $card_recept_time
 *
 * @property \App\Model\Entity\Employee $employee
 */
class RegistError extends Entity
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
        'occurrence_datetime' => true,
        'function_name' => true,
        'error_level' => true,
        'reason' => true,
        'employee_id' => true,
        'ic_card_number' => true,
        'instrument_division' => true,
        'food_division' => true,
        'card_recept_time' => true,
        'employee' => true,
    ];
}
