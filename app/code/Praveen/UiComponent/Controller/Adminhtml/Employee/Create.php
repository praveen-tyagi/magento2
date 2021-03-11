<?php
namespace Praveen\UiComponent\Controller\Adminhtml\Employee;

use \Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;

class Create extends \Magento\Backend\App\Action
{

    	protected $resultPageFactory = false;      
        public function __construct(Context $context,PageFactory $resultPageFactory)
	{                 
                $this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
         } 
         public function execute()
         {
                 $resultPage = $this->resultPageFactory->create();
                 return $resultPage;
         }
         
}


