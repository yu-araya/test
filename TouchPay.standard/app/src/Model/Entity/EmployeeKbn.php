<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmployeeKbn Entity
 *
 * @property int $id
 * @property string $employee_kbn
 * @property string $employee_kbn_name
 * @property string $food_allowance_flg
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 */
class EmployeeKbn extends Entity
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
        'employee_kbn' => true,
        'employee_kbn_name' => true,
        'food_allowance_flg' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
    ];
}
