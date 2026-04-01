<?php

	function getMessageFromTemplate($template,$params)
	{
		$templatefile = file_get_contents('administration/mailtemplate/'.$template.'.tpl');
		
		foreach($params as $key => $param)
		{
			$templatefile  = str_replace('{$'.$key.'$}', $param, $templatefile);
		}
		
		return $templatefile;
	}
	
	function getMessageFromTemplateAdmin($template,$params)
	{
		$templatefile = file_get_contents('mailtemplate/'.$template.'.tpl');
		
		foreach($params as $key => $param)
		{
			$templatefile  = str_replace('{$'.$key.'$}', $param, $templatefile);
		}
		
		return $templatefile;
	}
	
	function getMessageFromTemplateAdminAction($template,$params)
	{
		$templatefile = file_get_contents('../mailtemplate/'.$template.'.tpl');
		
		foreach($params as $key => $param)
		{
			$templatefile  = str_replace('{$'.$key.'$}', $param, $templatefile);
		}
		
		return $templatefile;
	}
	
	function sendMessage($from,$to,$subject,$template,$params)
	{
		$message = getMessageFromTemplate($template,$params);
 
		$headers = 'From: ' .$from . "\r\n" .
		'Reply-To: ' .$from. "\r\n" .
		'X-Mailer: PHP/' . phpversion()."\r\n";
		$headers .= 'MIME-Version: 1.0'."\r\n" ;
		$headers .= "Content-Type: text/html; charset=\"utf-8\"";
		
		return mail($to,$subject,$message, $headers);
	}
	
	function sendMessageAdminAction($from,$to,$subject,$template,$params)
	{
		$message = getMessageFromTemplateAdminAction($template,$params);
 
		$headers = 'From: ' .$from . "\r\n" .
		'Reply-To: ' .$from. "\r\n" .
		'X-Mailer: PHP/' . phpversion()."\r\n";
		$headers .= 'MIME-Version: 1.0'."\r\n" ;
		$headers .= "Content-Type: text/html; charset=\"utf-8\"";
		
		return mail($to,$subject,$message, $headers);
	}
	
	function sendMessageAdmin($from,$to,$subject,$template,$params)
	{
		$message = getMessageFromTemplateAdmin($template,$params);
 
		$headers = 'From: ' .$from . "\r\n" .
		'Reply-To: ' .$from. "\r\n" .
		'X-Mailer: PHP/' . phpversion()."\r\n";
		$headers .= 'MIME-Version: 1.0'."\r\n" ;
		$headers .= "Content-Type: text/html; charset=\"utf-8\"";
		
		return mail($to,$subject,$message, $headers);
	}
	
?>