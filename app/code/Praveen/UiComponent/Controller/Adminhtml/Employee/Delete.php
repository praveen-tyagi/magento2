<?php
namespace Praveen\UiComponent\Controller\Adminhtml\Employee;

use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Praveen\UiComponent\Model\EmployeeFactory;

class Delete extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Index';

    protected $resultPageFactory;
    protected $employeeFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        EmployeeFactory $employeeFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->employeeFactory = $employeeFactory;
        parent::__construct($context);
    }

    public function execute()
    {
	$resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        $employee = $this->employeeFactory->create()->load($id);

        if(!$employee)
        {
            $this->messageManager->addError(__('Unable to process. please, try again.'));            
            return $resultRedirect->setPath('*/*/', array('_current' => true));
        }

        try{
            $employee->delete();
            $this->messageManager->addSuccess(__('Employee has been deleted !'));
        }
        catch(\Exception $e)
        {
            $this->messageManager->addError(__('Error while trying to delete Employee'));
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }

        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}



