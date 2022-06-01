<?php
namespace Piantanida\MassActions\Block;

class Output extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        parent::__construct(
            $context
        );
    }
    
    public function tableIt (string $stringToTable, string $className)
    {
        $line_separator = "\r\n"; 
        $field_separator = "|";
        
        $stringToTable = str_replace( "|","</td><td>", $stringToTable);
        $stringToTable = str_replace( $line_separator,"</td></tr><tr><td>", $stringToTable);
        $stringToTable = "<table class=\"tableit " . $className. " \"><tr><td>" . $stringToTable . "</td></tr></table>";
        
        return ($stringToTable);
    }
}