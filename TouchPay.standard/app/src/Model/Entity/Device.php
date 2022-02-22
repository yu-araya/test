<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity
 *
 * @property int $id
 * @property string $deveice_id
 * @property string $client_id
 *
 * @property \App\Model\Entity\Deveice $deveice
 * @property \App\Model\Entity\Client $client
 */
class Device extends Entity
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
        'deveice_id' => true,
        'client_id' => true,
        'deveice' => true,
        'client' => true,
    ];
}
