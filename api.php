<?php

$chores = (array)json_decode(file_get_contents("chores.json"));
$age = isset($_GET['age']) ? $_GET['age'] : 10;
$num_chores = isset($_GET['num_chores']) ? $_GET['num_chores'] : -1;
$age_specific_only = isset($_GET['age_specific_only']) && $_GET['age_specific_only']=='true' ?  1 : 0;
$age_keys = array_keys($chores);

function get_age_appropriate_chores(array $chores, $age, $age_specific_only, $num = 1000000) {
	$age_appropriate_chores = array();
  foreach($chores as $age_key=>$age_chores){
    if( ($age_specific_only && ($age == $age_key)) || (!$age_specific_only && ($age >= $age_key))) {
			$age_appropriate_chores = array_merge($age_appropriate_chores,$age_chores);
		}
  }
	shuffle($age_appropriate_chores);
	return array_slice($age_appropriate_chores, 0, $num);
}

$age_appropriate_chores = get_age_appropriate_chores($chores, $age, $age_specific_only, $num_chores);

print json_encode($age_appropriate_chores);
//print "Age $age appropriate chores (".count($age_appropriate_chores)."): ";
//print implode("<li>", $age_appropriate_chores);
?>
