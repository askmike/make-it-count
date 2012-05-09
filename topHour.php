<?php


	require 'db.php';
	
	$t = getTopHour($link);
	$start = $t['date'];
	$end = $start + 3600;
	
	
	$tweets = array();
	
	$result = $link->query('SELECT * FROM `tweets` WHERE `date` > ' . $start . ' AND `date` < ' . $end);
	
	while($tweet = $result->fetch_array(MYSQLI_ASSOC)){
		$tweets[] = $tweet;
	}
	
	dbClose($link);

	echo json_encode($tweets);

?>