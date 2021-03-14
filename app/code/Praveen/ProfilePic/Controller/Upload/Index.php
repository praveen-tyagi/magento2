<?php
namespace Praveen\ProfilePic\Controller\Upload;
use \Magento\Framework\App\Action\Context;
use \Praveen\ProfilePic\Block\Customer\Account\ProfilePic;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_profilePic;
	protected $fileId = 'profile_picture';

	public function __construct(Context $context,	ProfilePic $profilePic){
		$this->_profilePic = $profilePic;
		parent::__construct($context);
	}

	public function execute(){
		$customer = $this->_profilePic->getCustomer();
		$image=$this->getRequest()->getParams('profile_picture');
		if($customerId = $customer->getId()){			
			
			try {
				$this->_profilePic->saveProfilePic($this->fileId);
				$this->messageManager->addSuccess(__('Successfully uploaded.'));
			
			} catch (\Exception $e) {

				$this->messageManager->addError($e->getMessage());
			}
		}
		
		$this->_redirect('customer/account');
	}
}
