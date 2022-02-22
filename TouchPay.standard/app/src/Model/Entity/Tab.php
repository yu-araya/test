<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tab Entity
 *
 * @property int $id
 * @property int $tab_id
 * @property string $tab_name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Tab[] $tabs
 */
class Tab extends Entity
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
        'tab_id' => true,
        'tab_name' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'tabs' => true,
    ];
}
