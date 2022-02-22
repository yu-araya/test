<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReservationInfo Entity
 *
 * @property int $id
 * @property string $employee_id
 * @property string $employee_kbn
 * @property int $food_division
 * @property string|null $reason
 * @property \Cake\I18n\FrozenTime $reservation_date
 * @property string $state_flg
 * @property float $food_cost
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Employee $employee
 */
class ReservationInfo extends Entity
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
        'reason' => true,
        'reservation_date' => true,
        'state_flg' => true,
        'food_cost' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'employee' => true,
    ];
}
