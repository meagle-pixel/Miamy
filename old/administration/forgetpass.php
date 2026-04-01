<?php
	session_start();
 
	include('functions.php');
	
	$_SESSION['admin_connected']=false;
	$_SESSION['admin_user']=false;
	
	if(isset($_POST['email']) && isset($_POST['pass']))
	{
		$email = $_POST['email'];
		$pass = $_POST['pass'];
	
		trytoconnect($email,$pass);
		
		if($_SESSION['admin_connected']==true)
			header('Location: index.php');
		else
			$message = 'L\'email ou le mot de passe est erroné !';
	}
	else
	{
		if(isset($_POST['send']))
			$message = 'Veuillez saisir votre email et votre mot de passe !';
	}

	include('template/forgetpass.php'); 
	
?>
