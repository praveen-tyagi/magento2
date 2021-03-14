<?php
namespace Praveen\ProfilePic\Block\Customer\Account;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Customer\Model\Session;
use \Magento\Customer\Model\Customer;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem;
use \Magento\MediaStorage\Helper\File\Storage\Database;
use \Magento\MediaStorage\Helper\File\Storage;
use \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use \Magento\Framework\App\ResourceConnection;
use \Magento\Store\Model\StoreManagerInterface;

class ProfilePic extends \Magento\Framework\View\Element\Template
{	
	protected $customerid;
	protected $customer;
	protected $session;
	protected $filesystem;
	protected $database;
	protected $storage;
	protected $notprotectedextension;
	protected $resourceconnection;
	protected $storemanager;
	protected $allowedExtensions = ['png','jpeg','jpg','gif','svg'];

	public function __construct(Context $context,Session $session,Customer $customer,Filesystem $filesystem,Database $database,
		Storage $storage,NotProtectedExtension $notprotectedextension,ResourceConnection $resourceconnection,
		StoreManagerInterface $storemanager,array $data = []
	){
		$this->session = $session;
		$this->customer = $customer;
		$this->filesystem = $filesystem;
		$this->database = $database;
		$this->storage = $storage;
		$this->notprotectedextension = $notprotectedextension;
		$this->resourceconnection = $resourceconnection;
		$this->storemanager = $storemanager;
		parent::__construct($context, $data);
	}
	
	public function getCustomer($id = false){
		
		if($id){
			$this->customer->load($id);
		}
		elseif($this->session && $this->session->getData('customer_id')){
			$this->customer->load($this->session->getData('customer_id'));
		}
		
		return $this->customer;
	}
	
	public function getCustomerId(){
		
		$this->customerid = $this->getCustomer()->getId();
		
		return $this->customerid;
	}
	
	public function getSession(){
		return $this->session;
	}
	
	public function getProfilePicUrl(){
		$url = $this->getViewFileUrl('Praveen_ProfilePic/media/default.jpg');
		if($this->getCustomer()->getData('profile_pic')){
			
			$mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath().'pub/media/';
			if(file_exists($mediaDir.'profilePic/'.$this->getCustomer()->getData('profile_pic'))){				
				$url = $this->storemanager->getStore()->getBaseUrl().'pub/media/profilePic/'.
					$this->getCustomer()->getData('profile_pic');
			}
		}
		return $url;
	}

	public function saveProfilePic($file){
		$uploader = new \Magento\MediaStorage\Model\File\Uploader(
			$file,
			$this->database,
			$this->storage,
			$this->notprotectedextension
			
		);
		$uploader->setAllowCreateFolders(true);
		$uploader->setAllowedExtensions($this->allowedExtensions);
		$mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath().'pub/media/';
		if($this->getCustomer()->getData('profile_pic')){
			@unlink($mediaDir.'profilePic/'.$this->getCustomer()->getData('profile_pic'));
			@rmdir($mediaDir.'profilePic/'.$this->getCustomerId());
		}
		if ($uploader->save($mediaDir.'profilePic/'.$this->getCustomerId())) {
			$resource = $this->resourceconnection;
			$table = $resource->getTableName('customer_entity');
			$write = $resource->getConnection($resource::DEFAULT_CONNECTION);
			
			$uploadedFileNameAndPath = $this->getCustomerId().'/'.$uploader->getUploadedFileName();
			$write->query("UPDATE `{$table}` SET `profile_pic`='{$uploadedFileNameAndPath}' WHERE `entity_id`='{$this->getCustomerId()}'");
		}
	}
}
