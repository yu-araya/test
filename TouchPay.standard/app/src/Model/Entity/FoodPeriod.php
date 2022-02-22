<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FoodPeriod Entity
 *
 * @property int $id
 * @property int $food_division
 * @property \Cake\I18n\FrozenDate $start_date
 * @property string $food_period_name
 * @property float $food_price
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 */
class FoodPeriod extends Entity
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
        'start_date' => true,
        'food_period_name' => true,
        'food_price' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
    ];
}
