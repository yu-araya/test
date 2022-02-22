<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FoodDivision Entity
 *
 * @property int $id
 * @property int $food_division
 * @property string $food_division_name
 * @property int $instrument_division
 * @property float $food_cost
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $reserve_food_division
 * @property int $delete_flg
 */
class FoodDivision extends Entity
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
        'food_division' => true,
        'food_division_name' => true,
        'instrument_division' => true,
        'food_cost' => true,
        'created' => true,
        'modified' => true,
        'reserve_food_division' => true,
        'delete_flg' => true,
    ];
}
