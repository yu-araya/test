<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContentsSetVersion Entity
 *
 * @property int $id
 * @property string $terminal_id
 * @property string $contents_type
 * @property float $version
 * @property string $revision
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $delete_flg
 *
 * @property \App\Model\Entity\Terminal $terminal
 */
class ContentsSetVersion extends Entity
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
        'terminal_id' => true,
        'contents_type' => true,
        'version' => true,
        'revision' => true,
        'created' => true,
        'modified' => true,
        'delete_flg' => true,
        'terminal' => true,
    ];
}
