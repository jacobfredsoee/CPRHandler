<?php

$post_dump = print_r($_POST, TRUE);

$fp = fopen('post.log', 'a');
fwrite($fp, date('m/d/Y H:i:s', time()) . ":");
fwrite($fp, "\n");
fwrite($fp, $post_dump);
fclose($fp);

require_once 'vendor/autoload.php';
require_once 'CPRhandler.php';
require_once 'setup.php';

use IU\PHPCap\RedCapProject;

//Generate the API object
try {
    $project = new RedCapProject($apiUrl, $apiToken, false);
} catch (Exception $e) {
    $fp = fopen('error.log', 'a');
	fwrite($fp, date('m/d/Y H:i:s', time()) . ":");
	fwrite($fp, "\n");
	fwrite($fp, print_r($e));
	fclose($fp);
}

$record = $_POST['record'];

try {
    $cprs = $project->exportRecordsAp(['recordIds' => [$record], 'fields' => ['record_id', $CPR_field]]);
} catch (Exception $e) {
    $fp = fopen('error.log', 'a');
	fwrite($fp, date('m/d/Y H:i:s', time()) . ":");
	fwrite($fp, "\n");
	fwrite($fp, print_r($e));
	fclose($fp);
}

//Create header line
$headerArray = array('record_id');
if(!is_null($birthday_field)) array_push($headerArray, $birthday_field);
if(!is_null($gender_field)) array_push($headerArray, $gender_field);
if(!is_null($modulus_field)) array_push($headerArray, $modulus_field);
$header = implode(",", $headerArray)."\n";

//Create input data
$lines = "";
foreach($cprs as $singleCpr) { //Should only be a single one

    $lineArray = array($singleCpr['record_id']);
    
    $cprNumber = new CPRHandler($singleCpr[$CPR_field]);
    
    if(!is_null($birthday_field)) array_push($lineArray, '"'.$cprNumber->getBirthday().'"');
    if(!is_null($modulus_field)) {
        if($cprNumber->passModulus()) {
            array_push($lineArray, 1);
        } else {
            array_push($lineArray, 0);
        }
    } 
    if(!is_null($gender_field)) {
        if($cprNumber->isMale()) {
            array_push($lineArray, 1);
        } else {
            array_push($lineArray, 0);
        }
    }

    print_r($lineArray);
    $lines .= implode(",", $lineArray)."\n";
}

//Import the data
$project->importRecords($header.$lines, 'csv', 'flat');

?>