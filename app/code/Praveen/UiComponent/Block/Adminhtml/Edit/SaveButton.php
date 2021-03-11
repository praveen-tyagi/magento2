<?php
namespace Praveen\UiComponent\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Employee'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
	    'on_click' => sprintf("location.href= '%s';", $this->getSaveUrl()),
            'sort_order' => 90,
        ];
    }
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', []) ;
    }
}


