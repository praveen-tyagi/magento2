<?php
 
namespace Praveen\Contactajax\Controller\Index;
 
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
 

use Magento\Framework\App\Action\Context;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\Log\Filter\Timestamp;
use Magento\Store\Model\StoreManagerInterface;
class Post extends \Magento\Framework\App\Action\Action
{
    
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
    const EMAIL_IDENTIFIER_TEMPLATE = 'customemail_email_template';
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
    protected $storeManager;
    protected $messageManager;
    /*
    @param \Magento\frontend\Block\Template\Context $context


    */
    public function __construct(
        Context $context,
	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        StoreManagerInterface $storeManager,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
	array $data = []

    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
	$this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->storeManager = $storeManager;
         
        parent::__construct($context);
        //parent::__construct($context,$contactsConfig,$mail,$dataPersistor,$logger);
    }
    
    public function execute()
    {
	$baseUrl = $this->storeManager->getStore()->getBaseUrl();
	$storeId = $this->storeManager->getStore()->getStoreId();
        $data = $this->getRequest()->getParams();
	/*$templateName = $this->scopeConfig->getValue(
            self::EMAIL_IDENTIFIER_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );*/	
        if(isset($_FILES['filepath']['name']) && $_FILES['filepath']['tmp_name'] != '') {
            try{
		$uploaderFactories = $this->uploaderFactory->create(['fileId' => 'filepath']);
                $uploaderFactories->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactories->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                $uploaderFactories->setAllowRenameFiles(true);
                $uploaderFactories->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
		$destinationPath = $mediaDirectory->getAbsolutePath('customer');
                $result = $uploaderFactories->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
	
	    $data['filepath'] = $mediaPath = $result['path'].$result['file'];
            $content = file_get_contents($mediaPath);
            $imageName = pathinfo($result['name'],PATHINFO_BASENAME);
		// Send Mail
            $this->_inlineTranslation->suspend();
                         
            //$sender = ['name' => "Praveen Tyagi",'email' => "praveen.tyagi15@gmail.com"];
             
            $transport=$this->_transportBuilder
            ->setTemplateIdentifier('customemail_email_template')
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => $this->storeManager->getStore()->getId()
                ]
                )
                ->setTemplateVars([
                    'name'  => $data['fname'],
                    'url'  => $mediaPath
                ])
		//->setFrom("praveen.tyagi15@gmail.com")
		//->addTo("praveen.tyagi@utsavfashion.com")
		->setFrom([
                      'name' => 'Praveen Tyagi',
                      'email' => "praveen.tyagi15@gmail.com",
                  ])
                  ->addTo("praveen.tyagi1504@gmail.com", "Prabhat")
		->addAttachment($content, $imageName, $result['type'])
		->getTransport();
 /*  image attachment logic */
            
                $transport->sendMessage();
                 
                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess('Email sent successfully');
                //$this->_redirect('contactajax/index/index');

		

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }else{
            $res = "File not found!";
        }

        echo json_encode($mediaPath);
	
    }
}

