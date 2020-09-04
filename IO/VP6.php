<?php

/*
  IO_VP6 class
  (c) 2020/09/05 yoya@awm.jp
 */

if (is_readable('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require_once 'IO/Bit.php';
}


class IO_VP6 {
    var $_vp6Data = null;
    function parse($vp6Data, $opts = array()) {
        $this->_vp6Data = $vp6Data;
        $bit = new IO_Bit();
        $bit->input($vp6Data);
        $keyframe        = 1 - $bit->getUIBit();
        $quantizer       = $bit->getUIBits(6);
        $separated_coeff = $bit->getUIBit();
        echo "keyframe:$keyframe quantizer:$quantizer separated_coeff:$separated_coeff\n";
        if ($keyframe) {
            $sub_version = $bit->getUIBits(5);
            if ($sub_version > 8) {
                fprintf(stderr, "sub_version:$sub_version > 8.\n");
                return ;
            }
            $filter_header = $bit->getUIBits(2);
            $interlace = $bit->getUIBit();
            if ($interlace) {
                fprintf(stderr, "interlace no support\n");
                return ;
            }
            echo "sub_version:$sub_version filter_header:$filter_header interlace:$interlace\n";
            if ($separated_coeff || (! $filter_header)) {
                $coeff_offset = $bit->getUI16BE() - 2;
                echo "coeff_offset:$coeff_offset\n";
            }
            $rows = $bit->getUI8();
            $cols = $bit->getUI8();
            $disp_rows = $bit->getUI8();
            $disp_cols = $bit->getUI8();
            echo "rows:$rows cols:$cols disp_rows:$disp_rows disp_cols:$disp_cols\n";
            if (($rows === 0) || ($cols === 0)) {
                fprintf(stderr, "(rows:$rows === 0) || (cols:$cols === 0)\n");
                return ;
            }
            $code_word = $bit->getUIBits(24);
            printf("range_decoder code_word:0x%06x\n", $code_word);
        } else {
            fprintf(stderr, "not implemented yet.\n");
            return ;
        }
    }
    function dump($opts = array()) {
        
    }
    function build($opts = array()) {
        fprintf(STDERR, "build: not implemented yet.");
    }
}
