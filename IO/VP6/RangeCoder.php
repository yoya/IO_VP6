<?php

/*
  IO_VP6 class
  (c) 2020/10/07 yoya@awm.jp
 */

if (is_readable('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require_once 'IO/Bit.php';
}

class IO_VP6_RangeCoder {
    var $code_word;
    function input($bit) {
        $this->code_word = $bit->getUIBits(24);
        return $this->code_word;
    }
    function getBits($n) {
        
    }
}
