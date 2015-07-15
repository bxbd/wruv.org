<?php

	$months = array(
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Okt',
      'Nov',
      'Dec'
    );
    $weekday = array(
      'Maandag',
      'Dinsdag',
      'Woensdag',
      'Donderdag',
      'Vrijdag',
      'Zaterdag',
      'Zondag'
    );
    $id_months = intval(strftime("%m", strtotime($data_event))) - 1;
	$id_weekday = intval(strftime("%u", strtotime($data_event))) - 1;
	$id_months_finish = intval(strftime("%m", strtotime($data_finish))) - 1;
	$id_weekday_finish = intval(strftime("%u", strtotime($data_finish))) - 1;
    $trans_months = htmlentities(utf8_decode($months[$id_months]));
	$trans_weekday = htmlentities(utf8_decode($weekday[$id_weekday]));
	$trans_months_finish = htmlentities(utf8_decode($months[$id_months_finish]));
	$trans_weekday_finish = htmlentities(utf8_decode($weekday[$id_weekday_finish]));
    $date_M = $trans_months;
	$date_w = $trans_weekday;
	$date_M_finish = $trans_months_finish;
	$date_w_finish = $trans_weekday_finish;
	
?>