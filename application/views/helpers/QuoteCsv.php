<?php

class Zend_View_Helper_QuoteCsv extends Zend_View_Helper_Abstract
{

    public function quoteCsv($value, $delimiter = ',', $enclosure = '"')
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $delimiter_esc = preg_quote($delimiter, '/');
            $enclosure_esc = preg_quote($enclosure, '/');
            $escaped = str_replace($enclosure, $enclosure.$enclosure, $value);
            $output = $enclosure . $escaped . $enclosure;
            return $output;
        }
    }
}