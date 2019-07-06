<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ItemCategory Entity
 *
 * @property int $id
 * @property string $name
 *
 * @property \App\Model\Entity\Item[] $items
 */
class ItemCategory extends Entity
{
		protected $_virtual = [
			'image_fullpath'
		];

		protected function _getImageFullpath()
		{
			if(!empty($this->_properties['image'])){
				return 'http://healthymaster.in/healthymaster/img/itemcategories/'.$this->_properties['image'];
			}
			else
			{
				return '';
			}
		} 
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
        '*' => true,
        'id' => false
    ];
}
