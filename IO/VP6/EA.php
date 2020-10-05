<?php

class IO_VP6_EA {
    var $eaData;
    var $has_alpha = false;
    var $chunks;
    static function is_ea($eaData) {  // four cc check
        $fourcc = substr($eaData, 0, 4);
        if (($fourcc == "MVhd") || ($fourcc == "AVP6")) {
            return true;
        }
        return false;
    }
    function parse($eaData) {
        $this->eaData = $eaData;
        $bit = new IO_Bit();
        $bit->input($eaData);
        $chunks = [];
        while ($bit->hasNextData(8)) {
            $chunk = $this->parseChunk($bit);
            if ($chunk === false) {
                echo "Error: failed to parse chunk";
                break;
            }
            $chunks []= $chunk;
        }
        $this->chunks = $chunks;
        return true;
    }

    function parseChunk($bit) {
        $name = $bit->getData(4);
        $size = $bit->getUI32LE(4);
        if (! $bit->hasNextData($size - 8)) {
            echo "Error: too long chunk size\n";
            return false;
        }
        $data = $bit->getData($size - 8);
        return ["name" => $name, "data" => $data];
    }
    function dump() {
        foreach ($this->chunks as $chunk) {
            $name = $chunk["name"];
            switch ($name) {
            case "AVP6":
                $this->has_alpha = true;
                echo "AVP6 found\n";
                break;
            case  "MVhd":
            case  "AVhd":
                $dataBit = new IO_Bit();
                $dataBit->input($chunk["data"]);
                $codec = $dataBit->getData(4);
                if ($codec !== "vp60") {
                    throw new Exception("codec:$codec != vp60");
                }
                $width =  $dataBit->getUI16LE();
                $height =  $dataBit->getUI16LE();
                $numFrames =  $dataBit->getUI32LE();
                $largestChunkSize=  $dataBit->getUI32LE();
                $rateDenom = $dataBit->getUI32LE();
                $rateNumer = $dataBit->getUI32LE();
                echo "$name: width:$width height:$height numFrames:$numFrames largest:$largestChunkSize rate=$rateNumer/$rateDenom\n";
                break;
            }
        }
    }
    function getFrames() {
        $frames = [];
        foreach ($this->chunks as $chunk) {
            $name = $chunk["name"];
            switch ($name) {
                //case "AV0K":
                //case "AV0F":
            case "MV0K":
            case "MV0F":
                $frames []= ["Data" => $chunk["data"]];
                break;
            }
        }
        return $frames;
    }
}
