<?php

####### SETUP ####### 
####### IMPORTANT, rename this file to setup.php #######

//API settings
$apiUrl = ''; //url to API
$apiToken = ''; //personal API token

//REDCap setttings
$CPR_field = 'patient_cprnum'; //name of the field which holds the CPR number
$birthday_field = 'foedselsdag'; //name of the field which holds the birthday date, null if not used
$gender_field = null; //name of the field which holds the gender, null if not used. Currently assumes male is 1
$modulus_field = 'patient_modulus'; //name of the field which holds the field for modulus check, null if not used. Asumes 1 for pass, 0 for not passed

####### SETUP END #######

?>