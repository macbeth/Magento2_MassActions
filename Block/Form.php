<?php
namespace Piantanida\MassActions\Block;

class Form extends \Magento\Framework\View\Element\Template
{
  
   protected $formKey;
   protected $scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        parent::__construct(
            $context
        );
        $this->scopeConfig = $scopeConfig;
        $this->formKey = $formKey;
    }

    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }
}