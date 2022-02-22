<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LoginHistory Entity
 *
 * @property int $id
 * @property string $login_name
 * @property \Cake\I18n\FrozenTime $login_datetime
 */
class LoginHistory extends Entity
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
        'login_name' => true,
        'login_datetime' => true,
    ];
}
