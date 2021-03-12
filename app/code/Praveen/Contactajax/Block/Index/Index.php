<?php
 
namespace Praveen\Contactajax\Block\Index;
 
 
class Index extends \Magento\Framework\View\Element\Template {
 
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }
 
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
 	
	public function getFormAction()
    	{
            // companymodule is given in routes.xml
            // controller_name is folder name inside controller folder
            // action is php file name inside above controller_name folder

        return '/contactajax/index/post';
        // here controller_name is index, action is post
    	}
}
