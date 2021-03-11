<?php
namespace Praveen\UiComponent\Model\ResourceModel\Employee;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'employee_id';
	protected $_eventPrefix = 'employee_details_collection';
	protected $_eventObject = 'Employee_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Praveen\UiComponent\Model\Employee', 'Praveen\UiComponent\Model\ResourceModel\Employee');
	}

}
