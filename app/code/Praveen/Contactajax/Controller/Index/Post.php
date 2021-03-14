<?php
 
namespace Praveen\Contactajax\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Praveen\Contactajax\Mail\Template\TransportBuilder;
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
    protected $storeManager;
    
    /*
    @param \Magento\frontend\Block\Template\Context $context


    */
    public function __construct(
        Context $context,
	StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
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
        $this->storeManager = $storeManager;
         
        parent::__construct($context);
    }
    
    public function execute()
    {
	$baseUrl = $this->storeManager->getStore()->getBaseUrl();
	$storeId = $this->storeManager->getStore()->getStoreId();
        $data = $this->getRequest()->getParams();	
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
                    'lname'  => $data['lname'],
		    'mobilenumber'  => $data['mobilenumber'],
		    'address'  => $data['address']
                ])
		//->setFrom("praveen.tyagi15@gmail.com")
		//->addTo("praveen.tyagi@utsavfashion.com")
		->setFrom([
                      'name' => 'Praveen Tyagi',
                      'email' => "praveen.tyagi15@gmail.com",
                  ])
                  ->addTo("praveen.tyagi1504@gmail.com", "Praveen")
		->addAttachment($content, $imageName, $result['type'])
		->getTransport();
 		/*  image attachment logic */
            
                $transport->sendMessage();
                 
                $this->_inlineTranslation->resume();
		$res = 1;
                $this->messageManager->addSuccess('Email sent successfully');
                //$this->_redirect('contactajax/index/index');

		

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }else{
            $res = 0;
        }

        echo json_encode($res);
	
    }
}

