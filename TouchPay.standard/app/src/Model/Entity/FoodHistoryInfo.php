<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FoodHistoryInfo Entity
 *
 * @property int $id
 * @property string $employee_id
 * @property string $employee_kbn
 * @property string $ic_card_number
 * @property int $instrument_division
 * @property int $food_division
 * @property string|null $reason
 * @property \Cake\I18n\FrozenTime $card_recept_time
 * @property string $state_flg
 * @property float $food_cost
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Employee $employee
 */
class FoodHistoryInfo extends Entity
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
        'ic_card_number' => true,
        'instrument_division' => true,
        'food_division' => true,
        'reason' => true,
        'card_recept_time' => true,
        'state_flg' => true,
        'food_cost' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'employee' => true,
    ];
}
