<?php

/*
 *    Copyright 2010,2011 Alexander Sadleir 

  Licensed under the Apache License, Version 2.0 (the 'License');
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an 'AS IS' BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
 */

date_default_timezone_set('Australia/ACT');
$debugOkay = Array(
    'session',
    'json',
    'phperror',
    'database',
    'other'
);
$GTFSREnabled = true;
$cloudmadeAPIkey = 'daa03470bb8740298d4b10e3f03d63e6';
$googleMapsAPIkey = 'ABQIAAAA95XYXN0cki3Yj_Sb71CFvBTPaLd08ONybQDjcH_VdYtHHLgZvRTw2INzI_m17_IoOUqH3RNNmlTk1Q';
$otpAPIurl = 'http://cloudnine.lambdacomplex.org:8080/opentripplanner-api-webapp/';

if (isDebug('phperror')) {
    error_reporting(E_ALL ^ E_NOTICE);
}

define('ROOT', pathinfo(__FILE__, PATHINFO_DIRNAME));
if (strpos($_SERVER['PHP_SELF'], "labs/")
        || strpos($_SERVER['PHP_SELF'], "myway/")
        || strpos($_SERVER['PHP_SELF'], "lib/")
        || strpos($_SERVER['PHP_SELF'], "geo/")
        || strpos($_SERVER['PHP_SELF'], "include/")
        || strpos($_SERVER['PHP_SELF'], "rtpis/")) {
    $basePath = "../";
} else {
    $basePath = "";
}

if (stristr(PHP_OS, 'WIN')) {
    // Win
    $tempPath = "c:/tmp/";
} else {
    // Other
    $tempPath = "/tmp/";
}


function isDebugServer() {
    if (isset($_REQUEST['debug']))
        return true;
    return php_sapi_name() == 'cli' || strpos(php_uname('n'), 'actbus') || isset($_SERVER['SERVER_NAME'])
            && ( $_SERVER['SERVER_NAME'] == 'azusa' || $_SERVER['SERVER_NAME'] == 'vanille'
            || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1'
            || $_SERVER['SERVER_NAME'] == '192.168.1.8' || $_SERVER['SERVER_NAME'] == '192.168.178.24');
}

if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'maxious.xen.prgmr.com') {
// Set the exception handler
    require $basePath . "lib/amon-php/amon.php";
Amon::config(array('address' => 'http://amon.disclosurelo.gs:2464',
    'protocol' => 'http',
    'secret_key' => "sSdt1ek79H0aNefmF36cq5SqrJEgtKpyBh1haEMwt4Q"));
Amon::setup_exception_handler();
}

include_once ('common-geo.inc.php');
include_once ('common-net.inc.php');
include_once ('common-transit.inc.php');
if (!strpos($_SERVER['PHP_SELF'], 'feedback')) {
    include_once ('common-db.inc.php');
}

include_once ('common-request.inc.php');
include_once ('common-session.inc.php');
include_once ('common-auth.inc.php');
include_once ('common-template.inc.php');

function isAnalyticsOn() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    return!isDebugServer() && !preg_match('/cloudkick/i', $user_agent) && !preg_match('/googlebot/i', $user_agent) &&
            !preg_match('/baidu/i', $user_agent);
}

function isDebug($debugReason = 'other') {
    global $debugOkay;
    return in_array($debugReason, $debugOkay, false) && isDebugServer();
}

function debug($msg, $debugReason = 'other') {
    if (isDebug($debugReason)) {
        if (is_array($msg))
            $msg = print_r($msg, true);
        echo PHP_EOL . '<!-- ' . date(DATE_RFC822) . PHP_EOL . $msg . PHP_EOL . ' -->' . PHP_EOL;
    }
}

function isIOSDevice() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
}

function isJQueryMobileDevice() {
    // http://forum.jquery.com/topic/what-is-the-best-way-to-detect-all-useragents-which-can-handle-jquery-mobile#14737000002087897
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    return preg_match('/iphone/i', $user_agent) || preg_match('/android/i', $user_agent) || preg_match('/webos/i', $user_agent) || preg_match('/ios/i', $user_agent) || preg_match('/bada/i', $user_agent) || preg_match('/maemo/i', $user_agent) || preg_match('/meego/i', $user_agent) || preg_match('/fennec/i', $user_agent) || (preg_match('/symbian/i', $user_agent) && preg_match('/s60/i', $user_agent) && $browser['majorver'] >= 5) || (preg_match('/symbian/i', $user_agent) && preg_match('/platform/i', $user_agent) && $browser['majorver'] >= 3) || (preg_match('/blackberry/i', $user_agent) && $browser['majorver'] >= 5) || (preg_match('/opera mobile/i', $user_agent) && $browser['majorver'] >= 10) || (preg_match('/opera mini/i', $user_agent) && $browser['majorver'] >= 5);
}

function array_flatten($a, $f = array()) {
    if (!$a || !is_array($a))
        return '';
    foreach ($a as $k => $v) {
        if (is_array($v))
            $f = array_flatten($v, $f);
        else
            $f[$k] = $v;
    }
    return $f;
}

function remove_spaces($string) {
    return str_replace(' ', '', $string);
}

function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

function startsWith($haystack, $needle, $case = true) {
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}

function endsWith($haystack, $needle, $case = true) {
    if ($case) {
        return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
}

function sksort(&$array, $subkey = 'id', $sort_ascending = false) {
    $temp_array = Array();
    if (count($array)) {
        $temp_array[key($array)] = array_shift($array);
    }
    foreach ($array as $key => $val) {
        $offset = 0;
        $found = false;
        foreach ($temp_array as $tmp_key => $tmp_val) {
            if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array(
                    $key => $val
                        ), array_slice($temp_array, $offset));
                $found = true;
            }
            $offset++;
        }
        if (!$found) {
            $temp_array = array_merge($temp_array, array(
                $key => $val
                    ));
        }
    }
    if ($sort_ascending) {
        $array = array_reverse($temp_array);
    } else {
        $array = $temp_array;
    }
}

function sktimesort(&$array, $subkey = 'id', $sort_ascending = false) {
    $temp_array = Array();
    if (count($array)) {
        $temp_array[key($array)] = array_shift($array);
    }
    foreach ($array as $key => $val) {
        $offset = 0;
        $found = false;
        foreach ($temp_array as $tmp_key => $tmp_val) {
            if (!$found and strtotime($val[$subkey]) > strtotime($tmp_val[$subkey])) {
                $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array(
                    $key => $val
                        ), array_slice($temp_array, $offset));
                $found = true;
            }
            $offset++;
        }
        if (!$found) {
            $temp_array = array_merge($temp_array, array(
                $key => $val
                    ));
        }
    }
    if ($sort_ascending && isset($temp_array)) {
        $array = array_reverse($temp_array);
    } else {
        $array = $temp_array;
    }
}

function r_implode($glue, $pieces) {
    foreach ($pieces as $r_pieces) {
        if (is_array($r_pieces)) {
            $retVal[] = r_implode($glue, $r_pieces);
        } else {
            $retVal[] = $r_pieces;
        }
    }
    return implode($glue, $retVal);
}
