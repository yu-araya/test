<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Option Entity
 *
 * @property int $id
 * @property int $option_id
 * @property string $option_key
 * @property int $option_state
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Option[] $options
 */
class Option extends Entity
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
        'option_id' => true,
        'option_key' => true,
        'option_state' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'options' => true,
    ];
}
