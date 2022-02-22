<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FoodHistoryReservation Entity
 *
 * @property string $employee_id
 * @property string $employee_kbn
 * @property int $food_division
 * @property \Cake\I18n\FrozenTime $target_date
 * @property string|null $reason
 * @property string $data_type
 * @property float $food_cost
 *
 * @property \App\Model\Entity\Employee $employee
 */
class FoodHistoryReservation extends Entity
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
        'employee_id' => true,
        'employee_kbn' => true,
        'food_division' => true,
        'target_date' => true,
        'reason' => true,
        'data_type' => true,
        'food_cost' => true,
        'employee' => true,
    ];
}
