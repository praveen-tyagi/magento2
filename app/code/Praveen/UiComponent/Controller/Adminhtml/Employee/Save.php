<?php
namespace Praveen\UiComponent\Controller\Adminhtml\Employee;

class Save extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Index';

    protected $resultPageFactory;
    protected $employeeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Praveen\UiComponent\Model\EmployeeFactory $employeeFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->employeeFactory = $employeeFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();// already defined in \Magento\Backend\App\Action
        $data = $this->getRequest()->getPostValue();
        if($data)
        {
            try{
                //$contact = $this->employeeFactory->create()->load($id);
		$employee = $this->employeeFactory->create();
               
                $employee->setData($data);
                $employee->save();
                $this->messageManager->addSuccess(__('Successfully saved the item.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                return $resultRedirect->setPath('*/*/index');
            }
            catch(\Exception $d)
            {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
               // return $resultRedirect->setPath('*/*/edit', ['id' => $employee->getEmployeeId()]);
		return $resultRedirect->setPath('*/*/create');
            }
        }

         return $resultRedirect->setPath('*/*/create');
    }
}



