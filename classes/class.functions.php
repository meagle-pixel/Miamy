<?php
require_once('classes/class.horaires.php');


	function sanitizeString($let)
	{
		$let = str_replace("'", "\u{2019}", $let);
		$let = stripslashes($let);
		$let = htmlentities($let);
		$let = strip_tags($let);

		return $let;
	}

?>
