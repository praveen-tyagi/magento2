<?php
namespace Praveen\UiComponent\Model;
class Employee extends \Magento\Framework\Model\AbstractModel
{
	protected function _construct()
	{
		$this->_init('Praveen\UiComponent\Model\ResourceModel\Employee');
	}
}

