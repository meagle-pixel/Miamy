<?php

	function get_ip() {
		// IP si internet partagé
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		// IP derrière un proxy
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// Sinon : IP normale
		else {
			return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		}
	}

	function sanitizeString($var)
	{
		$var = str_replace("'",'’',$var);
		$var = stripslashes($var);
		$var = htmlentities($var);
		$var = strip_tags($var);
		
		return $var;
	}

	function array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
				break;
				case SORT_DESC:
					arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	} 
	
	function GenPass($size)
	{
		// Initialisation des caractères utilisables
		$password = '';
		
		$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",",","?",".",";","!","&","-","+","_");

		for($i=0;$i<$size;$i++)
		{
			$password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
		}
			
		return $password;
	}
	
	function splitDateToTimestamp($date)
	{
		$dates = array();
		
		$datestmp = explode('-',$date);
		
		foreach($datestmp as $date)
		{
			$datetmp = trim($date);
			$dates[] = DateTime::createFromFormat('!d/m/Y H:i', $datetmp )->getTimestamp();
		}
		
		return $dates;
	}
	
	function updateValueonColandTable($table,$col,$value,$id)
	{
		$cards = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		
		if($table == 'gradations' && $col == 'actif' && $value == 1)
		{
			$query = "UPDATE `gradations` SET `date_gradation`= NOW() WHERE `id` = '".$id."';";
		
			$result = $mysqli->query($query);
		}
		
		$query = "UPDATE `$table` SET `$col` = '".$value."' WHERE `id` = $id;";
		
		$result = $mysqli->query($query);
		
		return $query;
	}

	if (!function_exists('str_contains')) {
		function str_contains($haystack, $needle) {
			return $needle !== '' && mb_strpos($haystack, $needle) !== false;
		}
	}
	
	function dateTimeFrFromTimestamp($timestamp)
	{
		date_default_timezone_set('Europe/Paris');
		$cdate = getdate(strtotime($timestamp));
		// Date en français
		$semaine = array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");
		$mois = array("","Janvier ","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
		// Avec getdate()
		$date = $semaine[$cdate['wday']].' '.$cdate['mday'].' '.$mois[$cdate['mon']].' '.$cdate['year'].' '.str_pad($cdate['hours'], 2, '0', STR_PAD_LEFT).':'.str_pad($cdate['minutes'], 2, '0', STR_PAD_LEFT).':'.str_pad($cdate['seconds'], 2, '0', STR_PAD_LEFT);

		return $date;
	}
	
	function get_friendly_time_ago($distant_timestamp, $max_units = 1) {
		$distant_timestamp = strtotime($distant_timestamp);
		$i = 0;
		$time = time() - $distant_timestamp; // to get the time since that moment
		$tokens = [
			31536000 => 'an',
			2592000 => 'mois',
			604800 => 'semaine',
			86400 => 'jour',
			3600 => 'heure',
			60 => 'minute',
			1 => 'seconde'
		];

		$responses = [];
		while ($i < $max_units && $time > 0) {
			foreach ($tokens as $unit => $text) {
				if ($time < $unit) {
					continue;
				}
				$i++;
				$numberOfUnits = floor($time / $unit);

				$responses[] = $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
				$time -= ($unit * $numberOfUnits);
				break;
			}
		}

		if (!empty($responses)) {
			return 'il y a '.implode(', ', $responses);
		}

		return 'Maintenant';
	}

?>
