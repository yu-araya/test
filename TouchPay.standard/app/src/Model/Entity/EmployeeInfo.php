<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EmployeeInfo Entity
 *
 * @property int $id
 * @property string|null $employee_id
 * @property string|null $employee_kbn
 * @property string|null $employee_name1
 * @property string|null $employee_name2
 * @property string|null $password
 * @property string|null $dining_license_flg
 * @property \Cake\I18n\FrozenDate|null $dining_licensed_date
 * @property string|null $ic_card_number
 * @property \Cake\I18n\FrozenTime|null $iccard_valid_s_time
 * @property \Cake\I18n\FrozenTime|null $iccard_valid_e_time
 * @property string|null $ic_card_number2
 * @property \Cake\I18n\FrozenTime|null $iccard_valid_s_time2
 * @property \Cake\I18n\FrozenTime|null $iccard_valid_e_time2
 * @property string|null $memo
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Employee $employee
 */
class EmployeeInfo extends Entity
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
        'employee_name1' => true,
        'employee_name2' => true,
        'password' => true,
        'dining_license_flg' => true,
        'dining_licensed_date' => true,
        'ic_card_number' => true,
        'iccard_valid_s_time' => true,
        'iccard_valid_e_time' => true,
        'ic_card_number2' => true,
        'iccard_valid_s_time2' => true,
        'iccard_valid_e_time2' => true,
        'memo' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'employee' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
//    protected $_hidden = [
//        'password',
//    ];
}
