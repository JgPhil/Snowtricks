<?php

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;




    class Project_Twig_Extension extends \Twig\Extension\AbstractExtension {

    public function getFunctions(){
        return [
            new \Twig\TwigFunction('unset', [$this, 'unset'], [ 'needs_context' => true, ]),
        ];
    }
    /**
    * $context is a special array which hold all know variables inside 
    * If $key is not defined unset the whole variable inside context
    * If $key is set test if $context[$variable] is defined if so unset $key inside multidimensional array
    **/
    public function unset(&$context, $variable, $key = null) {
        if ($key === null) unset($context[$variable]);
        else{
            if (isset($context[$variable])) unset($context[$variable][$key]);
        }
    }
}