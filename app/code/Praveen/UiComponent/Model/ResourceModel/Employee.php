<?php
namespace Praveen\UiComponent\Model\ResourceModel;
class Employee extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('employee_details', 'employee_id');
	}
	
}

