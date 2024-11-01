<?php

function _set($var, $key, $def = '') {
    if (!$var)
        return false;
    if (is_object($var) && isset($var->$key)) {
        return $var->$key;
    } elseif (is_array($var) && isset($var[$key])) {
        return $var[$key];
    } elseif ($def) {
        return $def;
    } else {
        return false;
    }
}

function pr($data) {
    echo '<pre>';
    print_r($data);
    exit;
}
