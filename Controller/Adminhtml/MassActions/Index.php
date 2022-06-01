<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Piantanida\MassActions\Controller\Adminhtml\MassActions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface; //use this injected instead of the objectManager

// use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;


class Index extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const MENU_ID = 'Piantanida_PriceUpdater::massactions_priceupdate';

    protected $resultPageFactory;

    /**
     * Index constructor.
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    

    public function execute()
    {           
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $productId = 1;
        $price = 100;
        $store = 1;

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $post = $this->getRequest()->getParams();

        /*
        try {
            $product->setStoreId($store);
            $product->setPrice($price);
            $product->save();

        } catch (\Exception $e) {
            echo "Error Id : " . $productId;
        }*/
        
        //echo $controllerName = $this->getRequest()->getControllerName() ."<br/>";
        //echo $actionName = $this->getRequest()->getActionName()."<br/>";;
        //echo $routeName = $this->getRequest()->getRouteName()."<br/>";;
        //echo $moduleName = $this->getRequest()->getModuleName()."<br/>";;    
        //echo strval($product->getPrice());
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        
        //Titolo Pagoina
        $resultPage->getConfig()->getTitle()->prepend(__('Mass Price Update') ); 
        
        // retreive block
        $block = $resultPage->getLayout()->getBlock('piantanida.massactions.layout.form');
        
        //$keyValue = $this->getRequest()->getParam('key');
        //$block->setData('key', $keyValue);
        $block->setData('url', $this->getUrl());
            
        // will get you the post data.
        // $this->getRequest()->getParams();        
        // If you want to access a specific parameter you can use  
        // $this->getRequest()->getParam('something');
        // I have tested this on an admin controller and it will work when extending \Magento\Backend\App\Action
        // set data to block        
        
        /*        
        foreach ($block->getPostCollection() as $key=>$post) {
            echo '<tr>
                    <td>'.$post->getPostId().'</td>
                    <td>'.$post->getName().'</td>
                    <td>'.$post->getPostContent().'</td>
                  </tr>';
        }
        */
        
        return $resultPage;
    }
}
