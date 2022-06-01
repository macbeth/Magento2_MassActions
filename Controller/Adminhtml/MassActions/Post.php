<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Piantanida\MassActions\Controller\Adminhtml\MassActions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

use \Magento\Catalog\Api\ProductRepositoryInterface;  // service for products

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\Product;


/**
 * Class Post
 */
class Post extends \Magento\Backend\App\Action implements HttpPostActionInterface
{    
    const MENU_ID = 'Piantanida_PriceUpdater::massactions_priceupdate';

    protected $productRepository;
    protected $request;
    protected $resultPageFactory;
    protected $errorDataFormat;
    
    /**
     * Post constructor.
     */
    
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Webapi\Rest\Request $request,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);        
        $this->productRepository = $productRepository;     
        $this->_request = $request;
        $this->resultPageFactory = $resultPageFactory;
    }
    
    
    protected function _extractSkuPriceData()
    {
        $field_separator = "\t"; //tab character
    
/*
        Windows: '\r\n'
        Mac (OS 9-): '\r'
        Mac (OS 10+): '\n'
        Unix/Linux: '\n'
*/
        $line_sperator_windows = "\r\n";        
        $line_sperator_mac9 = "\r";        
        $line_sperator_mac10_linux = "\n";
        $skuPrice = array();
        
        //check if the text area is populated
        if ($this->getRequest()->getPost('skuprice')) {
                    
            $skuPriceForm = $this->getRequest()->getPost('skuprice');   //get textarea
            $skuPriceForm = trim($skuPriceForm);
            
            //replace newlines chars with tabs, every SO
            $skuPriceForm = str_replace($line_sperator_windows, $field_separator, $skuPriceForm); 
            $skuPriceForm = str_replace($line_sperator_mac9, $field_separator, $skuPriceForm); 
            $skuPriceForm = str_replace($line_sperator_mac10_linux, $field_separator, $skuPriceForm); 
            
            $skuPriceForm = explode($field_separator, $skuPriceForm); //create the array

            //check data for consistency
//            $errorDataFormat = "";
            for ($i=0; $i<count($skuPriceForm); $i++) {
                
                //TODO add more check to be sure that the combo sku prices are correct.        
                //echo " " . $i . " " . $skuPriceForm[$i];
                if (empty($skuPriceForm[$i]))
                {
                    $this->errorDataFormat = "Either one SKU or price is empty. Please check your data";
                    return;
                }

                //if is a price 
                if ($i % 2 != 0) {
                    //check if the number has thousand separators
                    if (strpos($skuPriceForm[$i], ",") and strpos($skuPriceForm[$i], "."))
                    {
                        $this->errorDataFormat = "Do not specify thousand separators. Please reformat your data";
                        return;
                    }
                }
                
            }
            
            //populate a multidimensional array
            for ($i=0; $i<count($skuPriceForm); $i++) {
                //the key is the sku, the value is the price
                $skuPrice[$skuPriceForm[$i]] = str_replace(",",".",$skuPriceForm[$i+1]); //replace price decimal separator  [,] ->  [.] 
                $i++;// = $i+2;
            }
            
            //echo  $line_sperator . "multi dim array created" . $line_sperator;
            //var_dump($skuPrice);
            //return $skuPrice;
        }
        return $skuPrice;
    }

    
    
    public function execute()
    {
        $line_sperator = "\r\n";
        $skuPriceError = "" ;
        $skuPriceSuccess = "" ;        
        $skuPrice = $this->_extractSkuPriceData();
        
        if ($skuPrice)
        {
            foreach ($skuPrice as $sku => $price)
            {
                $product;

                //get product
                try {
                    $product = $this->productRepository->get($sku);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $product = false;
                }

                //if the product exists update with the new price
                if ($product)
                {
                    //update price
                    try {
                        //$product->setStoreId($store);
                        $product->setPrice($price);            
                        $product->save();                
                        $skuPriceSuccess = $skuPriceSuccess . $sku . "|" . $price . $line_sperator;

                    } catch(Exception $e) {
                        $skuPriceError = $skuPriceError . $sku . "|" . $price .  "|" . $e  . $line_sperator;
                    }
                }
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);

        // retreive block
        $block = $resultPage->getLayout()->getBlock('piantanida.massactions.layout.output');

        //save data to the block
        $block->setData('success', $skuPriceSuccess);
        $block->setData('errordataformat', $this->errorDataFormat);
        $block->setData('error', $skuPriceError);
        $block->setData('form_url', $block->getUrl('massactions/massactions/index') );
        
        return $resultPage;
        
        //$block->setData('key', $keyValue);

        //echo strval($product->getPrice());
        //$resultPage = $this->resultPageFactory->create();
        //$resultPage->setActiveMenu(static::MENU_ID);
        //$resultPage->getConfig()->getTitle()->prepend(__('Hello World'.strval($product->getPrice())));         
        // retreive block

        //$block->setData('error', $skuPriceError[$sku]);
        //$block->setData('success', $skuPriceSuccess[$sku]);

        /* redirect stuff
            $resultRedirect = $this->resultRedirectFactory->create()->setHeader("X-Requested-With", "XMLHttpRequest");    
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect -> setPath('massactions/massactions/index');
            return $resultRedirect;    
        */


        // $resultRedirect->setStatusHeader("X-Requested-With", "XMLHttpRequest");                    
        // $this->_redirect('massactions/index');
        // exit("before redirect ");
    }
}