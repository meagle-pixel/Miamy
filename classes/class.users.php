<?php 

	/* ==========================================================================
	   GESTION DES UTILISATEURS & LOGS
	   ========================================================================== */

	// --- FONCTIONS LOGS ---
	if (!function_exists('logUserAction')) {
		function logUserAction($userId, $actionType, $message) {
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			
			$userId = (int)$userId;
			$actionType = $mysqli->real_escape_string($actionType);
			$message = $mysqli->real_escape_string($message);
			$ip = $_SERVER['REMOTE_ADDR'] ?? '';

			$query = "INSERT INTO user_logs (user_id, action_type, message, ip_address) VALUES ('$userId', '$actionType', '$message', '$ip')";
			$mysqli->query($query);
		}
	}

	if (!function_exists('getUserLogs')) {
		function getUserLogs($userId, $limit = 20) {
			$logs = [];
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$userId = (int)$userId;
			
			$query = "SELECT l.*, 
					  COALESCE(a.nom, e.nom, 'Utilisateur') as user_nom,
					  COALESCE(a.prenom, e.prenom, '') as user_prenom
					  FROM user_logs l
					  LEFT JOIN utilisateurs u ON l.user_id = u.id
					  LEFT JOIN administrateurs a ON (u.profil = 1 AND u.profil_id = a.id)
					  LEFT JOIN restaurateurs e ON (u.profil = 2 AND u.profil_id = e.id)
					  LEFT JOIN clients e ON (u.profil = 3 AND u.profil_id = e.id)
					  WHERE l.user_id = '$userId' 
					  ORDER BY l.created_at DESC LIMIT $limit";
					  
			if($res = $mysqli->query($query)) {
				while($row = $res->fetch_assoc()) $logs[] = $row;
			}
			return $logs;
		}
	}

	if (!function_exists('time_elapsed_string')) {
		function time_elapsed_string($datetime, $full = false) {
			$now = new DateTime;
			$ago = new DateTime($datetime);
			$diff = $now->diff($ago);

			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;

			$string = array(
				'y' => 'an', 'm' => 'mois', 'w' => 'semaine',
				'd' => 'jour', 'h' => 'heure', 'i' => 'minute', 's' => 'seconde',
			);
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 && $k != 'm' ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? 'Il y a ' . implode(', ', $string) : 'À l\'instant';
		}
	}
	// --- FIN FONCTIONS LOGS ---


	function insertUtilisateur($utilisateur)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();

		foreach ($utilisateur as $key => $value) {
			$utilisateur[$key] = mysqli_real_escape_string($mysqli, $value);
		}

		$base_salt = $GLOBALS["base_salt"];
		$options = ['cost' => 9];
		$pass = password_hash($utilisateur['motdepasse'].$utilisateur['email'].$base_salt, PASSWORD_BCRYPT, $options);

		$query = "INSERT INTO `utilisateurs` 
		(`id`, `email`, `motdepasse`, `profil`, `profil_id`, `dateinscription`, `dateconnect`, `dateaction`, `token`, `actif`) 
		VALUES 
		(NULL, '".$utilisateur['email']."', '".$pass."', '".$utilisateur['profil']."', '".$utilisateur['profil_id']."', NOW(), NULL, NULL, '', '1');";

		$mysqli->query($query);
		$idu = $mysqli->insert_id;

		if($idu) {
			// LOG : Création compte
			$actorId = $_SESSION['user']['id'] ?? $idu; 
			logUserAction($actorId, 'create_user', "Création du compte pour : " . $utilisateur['email']);
			return $idu;
		}
		return false;
	}

	function getContacts($request)
	{
		$contacts = array();
		
		// Get Administrateurs
		$admins = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT distinct(id),nom,prenom FROM `administrateurs` WHERE `nom` LIKE '%".$request."%' OR `prenom` LIKE '%".$request."%';";
		
		if($result = $mysqli->query($query)){
			while ($admin = $result->fetch_assoc()) { $admins[] = $admin; }
			$result->free();
		}
		
		foreach($admins as $admin)
		{
			$query = "SELECT id FROM `utilisateurs` WHERE `profil` = '".$admin['id']."' AND `profil_id` = '1';";
		
			if($result = $mysqli->query($query)){
				while ($contact = $result->fetch_assoc()) { 
					$contacts[] = array('id' => $contact['id'],'nom' => $admin['prenom'].' '.$admin['nom']);
				}
				$result->free();
			}
		}
		
		// Get Praticiens
		$praticiens = array();
		$query = "SELECT distinct(id),nom,prenom FROM `praticiens` WHERE `nom` LIKE '%".$request."%' OR `prenom` LIKE '%".$request."%';";
		
		if($result = $mysqli->query($query)){
			while ($admin = $result->fetch_assoc()) { $praticiens[] = $admin; }
			$result->free();
		}
		
		foreach($praticiens as $praticien)
		{
			$query = "SELECT * FROM `utilisateurs` WHERE `profil` = '".$praticien['id']."' AND `profil_id` = '2'";
		
			if($result = $mysqli->query($query)){
				while ($contact = $result->fetch_assoc()) { $contacts[] = array('id' => $contact['id'],'nom' => $praticien['prenom'].' '.$praticien['nom']); }
				$result->free();
			}
		}
		
		// Get Cavaliers
		$cavaliers = array();
		$query = "SELECT distinct(id),nom,prenom FROM `cavaliers` WHERE `nom` LIKE '%".$request."%' OR `prenom` LIKE '%".$request."%'";
		
		if($result = $mysqli->query($query)){
			while ($admin = $result->fetch_assoc()) { $cavaliers[] = $admin; }
			$result->free();
		}
		
		foreach($cavaliers as $cavalier)
		{
			$query = "SELECT * FROM `utilisateurs` WHERE `profil` = '".$cavalier['id']."' AND `profil_id` = '3'";
		
			if($result = $mysqli->query($query)){
				while ($contact = $result->fetch_assoc()) { $contacts[] = array('id' => $contact['id'],'nom' => $cavalier['prenom'].' '.$cavalier['nom']); }
				$result->free();
			}
		}
		
		$contacts = deduplicateMultiArray($contacts, 'id');
		return $contacts;
	}


	function connectAs($id)
	{
		$user = getUser($id);
		
		trytoconnect($user['email'],'',true);
		// LOG : Usurpation / Login as
		$adminId = $_SESSION['original_admin_id'] ?? $_SESSION['user']['id'];
		logUserAction($adminId, 'connect_as', "Connexion en tant que l'utilisateur ID $id");
	}
	
	
	function getAllAdmins($onlynb = false)
	{
		$etats = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `administrateurs`";
		
		if($result = $mysqli->query($query)){
			while ($societe = $result->fetch_assoc()) { $etats[] = $societe; }
			$result->free();
		}
		if($onlynb)
			return count($etats);
		return $etats;
	}
	
	function getUserID($id,$profil)
	{
		$user = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "SELECT * FROM `utilisateurs` WHERE `profil` = '".$profil."' AND `profil_id` = '".$id."'";
		$mysqli->query($query);
		
		if($result = $mysqli->query($query))
		{
			while ($tmp = $result->fetch_assoc()) { $user = $tmp; }
			$result->free();
		}
		return $user;
	}
	
	function insertIP($id,$type=1)
	{
		$ip = get_ip();
		$ips = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `ips` WHERE `user` = '".$id."' AND user_type = '".$type."' AND `ip` = '".$ip."' AND date < NOW() + INTERVAL 1 DAY";
		
		if($result = $mysqli->query($query))
		{
			while ($iptmp = $result->fetch_assoc())
			{ 
				$ips[] = $iptmp; 
				$db = Database::getInstance();
				$mysqli = $db->getConnection(); 
				$query = "UPDATE `ips` set `date` = NOW() WHERE `ip` = '".$iptmp['ip']."'";
				$mysqli->query($query);
			}
			$result->free();
		}
		if(empty($ips))
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection(); 
			$query = "INSERT INTO `ips` (`ip`,`user`,`user_type`,`infos`,`date`) 
			 VALUES ('".$ip."', '".$id."','".$type."','".getenv("HTTP_USER_AGENT")."', NOW());";
			
			$mysqli->query($query);
		}
	}
	
	function userIPS($id,$type=1)
	{
		$ips = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `ips` WHERE `user` = '".$id."' AND user_type = '".$type."'";
		
		if($result = $mysqli->query($query))
		{
			while ($iptmp = $result->fetch_assoc()) { $ips[] = $iptmp; }
			$result->free();
		}
		return $ips;
	}
	
	function isClear($profil,$page)
	{
		$aut = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `autorisations` WHERE `page` = '".$page."' AND `profil` = '".$profil."'";
		
		if($result = $mysqli->query($query))
		{
			while ($urltmp = $result->fetch_assoc()) { $aut = $urltmp; }
			$result->free();
		}
		
		if(empty($aut) || $aut['etat'] == 0)
			return false;
		else
			return true;
	}
	
	function editUser($user)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `utilisateurs` SET
		 `email` = '".$user['email']."'";
		
		if($user['motdepasse'] != '')
			$query .= ",`motdepasse` = '".$user['motdepasse']."'";
		 
		$query .= "WHERE `id` = '".$user['id']."';";
		
		$res = $mysqli->query($query);

		if($res) {
			// LOG : Edition profil
			$actorId = $_SESSION['user']['id'] ?? $user['id']; 
			logUserAction($actorId, 'update_profile', "Modification des informations de connexion (Email/Pass)");
		}
		return $res;
	}
	
	function activeUser($key)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "UPDATE `utilisateurs` set `actif` = '1' WHERE `key` = '".$key."'";
		$mysqli->query($query);
	}
	
	function getUser($id)
	{
		$user = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `utilisateurs` WHERE `id` = '".$id."'";
		
		if($result = $mysqli->query($query)){
			while ($users = $result->fetch_assoc()) { $user = $users; }
			$result->free();
		}

		if($user['profil'] == 1)
		{
			$query = "SELECT * FROM `administrateurs` WHERE `id` = '".$user['profil_id']."'";
			if($result = $mysqli->query($query)){
				while ($results = $result->fetch_assoc()) {
					$user['userinfo'] = $results;
				}
				$result->free();
			}
		}
		elseif($user['profil'] == 2)
		{
			$query = "SELECT * FROM `restaurateurs` WHERE `id` = '".$user['profil_id']."'";
			if($result = $mysqli->query($query)){
				while ($results = $result->fetch_assoc()) {
					$user['userinfo'] = $results;
				}
				$result->free();
			}
		}
		elseif($user['profil'] == 3)
		{
			$query = "SELECT * FROM `clients` WHERE `id` = '".$user['profil_id']."'";
			if($result = $mysqli->query($query)){
				while ($results = $result->fetch_assoc()) {
					$user['userinfo'] = $results;
				}
				$result->free();
			}
		}
		
		return $user;
	}
	
	/**
	 * Vérifie si un email est déjà présent dans la table utilisateurs
	 */
	function isRegistered($email)
	{
		$user = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		// Sécurité : toujours échapper les données avant la requête
		$email = mysqli_real_escape_string($mysqli, $email);
		
		$query = "SELECT id FROM `utilisateurs` WHERE `email` = '".$email."'";
		
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$user = $results;
			}
			$result->free();
		}
		
		if(isset($user['id']))
			return true;
		else
			return false;
	}
	
	function trytoconnect($email, $pass, $bypass = false)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		// Sécurité : échappement de l'email
		$email = $mysqli->real_escape_string($email);
		
		// Récupération du grain de sel global
		$base_salt = $GLOBALS["base_salt"] ?? "";
		
		$query = "SELECT * FROM `utilisateurs` WHERE `email` = '".$email."' AND actif = '1'";
		$userFound = false;

		if($result = $mysqli->query($query))
		{
			if($result->num_rows == 1) {
				$userFound = $result->fetch_assoc();
			}
			$result->free();
			
			if($userFound)
			{	
				if(!$bypass)
				{
					// Vérification avec la même logique qu'à l'inscription
					if (!password_verify($pass . $email . $base_salt, $userFound['motdepasse'])) { 
						$_SESSION['connected'] = false;
						$_SESSION['user'] = false;
						
						logUserAction(0, 'login_fail', "Echec connexion pour $email");
						return false;
					}
				}
				
				// On remplit la session avec les données de la table 'utilisateurs'
				$_SESSION['connected'] = true;
				$_SESSION['user'] = $userFound;
				
				$queryUpdate = "UPDATE utilisateurs SET `dateconnect` = NOW() WHERE id = '".$userFound['id']."'";
				$mysqli->query($queryUpdate);

				logUserAction($userFound['id'], 'login', "Connexion au site réussie");

				// --- Chargement des infos complémentaires selon le profil ---
				// On utilise une variable différente ($info) pour ne pas écraser $userFound
				
				$table = "";
				if($userFound['profil'] == 1) $table = "administrateurs";
				elseif($userFound['profil'] == 2) $table = "restaurateurs";
				elseif($userFound['profil'] == 3) $table = "clients";

				if($table != "") {
					$queryInfo = "SELECT * FROM `$table` WHERE `id` = '".$userFound['profil_id']."'";
					if($resInfo = $mysqli->query($queryInfo)) {
						if($info = $resInfo->fetch_assoc()) {
							$_SESSION['user-info'] = $info;
						}
						$resInfo->free();
					}
					// Si profil 1 ou 2, on peut considérer l'accès comme admin/gérant
					if($userFound['profil'] < 3) {
						$_SESSION['admin'] = true;
					}
				}
				
				return true;
			}
		}
		
		$_SESSION['connected'] = false;
		$_SESSION['user'] = false;
		return false;
	}
	
	function updateUserData($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "SELECT * FROM `utilisateurs` WHERE `id` = '".$id."'";
		
		$user = array();
		
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$_SESSION['connected'] = true;
				$_SESSION['user'] = $results;
			}
			$result->free();
		}
		else
		{
			$_SESSION['connected'] = false;
			$_SESSION['user'] = false;
		}
		
		$query = "SELECT * FROM `administrateurs` WHERE `id` = '".$_SESSION['user']['profil_id']."'";
		
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$_SESSION['user']['infos'] = $results;
			}
			$result->free();
		}
		
		$query = "UPDATE `utilisateurs` set `dateaction` = NOW() WHERE `id` = '".$id."'";
		$mysqli->query($query);
		
		//insertIP($id,1);
	}
	
	function getAllProfils($onlynb = false)
	{
		$terrains = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `profils`";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $terrains[] = $message; }
			$result->free();
		}

		if($onlynb)
			return count($terrains);
		return $terrains;
	}
	
	function updateUserDataLite($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "SELECT * FROM `utilisateurs` WHERE `id` = '".$id."'";
		if($result = $mysqli->query($query)){
			while ($results = $result->fetch_assoc()) {
				$_SESSION['connected'] = true;
				$_SESSION['user'] = $results;
			}
			$result->free();
		}
		else
		{
			$_SESSION['connected'] = false;
			$_SESSION['user'] = false;
		}
		
		$query = "UPDATE `utilisateurs` set `dateaction` = NOW() WHERE `id` = '".$id."'";
		$mysqli->query($query);
		
		insertIP($id);
	}
	
	function getAllUsers($onlynb = false)
	{
		$users = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `utilisateurs`";
		
		if($result = $mysqli->query($query)){
			while ($message = $result->fetch_assoc()) { $users[] = $message; }
			$result->free();
		}
		
		if($onlynb)
			return count($users);
		return $users;
	}
	
	function getUsersIdForEmail()
	{
		$users = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `utilisateurs`";
		
		if($result = $mysqli->query($query)){
			while ($user = $result->fetch_assoc()) { $users[] = $user; }
			$result->free();
		}
		return $users;
	}
	
	function getAdmin($id)
	{
		$etats = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT * FROM `administrateurs` WHERE id = '".$id."'";
		
		if($result = $mysqli->query($query)){
			while ($societe = $result->fetch_assoc()) { $etats = $societe; }
			$result->free();
		}

		return $etats;
	}
	
	function getUsersIdForEmailExperts()
	{
		$users = array();
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$mysqli->set_charset( 'utf8');
		$query = "SELECT `utilisateurs`.`id`,
		`utilisateurs`.`nom`,
		`utilisateurs`.`prenom`,
		`utilisateurs`.`email`,
		`societes`.`raisonsociale`
		FROM `utilisateurs`,`societes` 
		WHERE `utilisateurs`.`societe` = `societes`.`id`
		AND `utilisateurs`.`profil` = 2
		ORDER BY `societes`.`raisonsociale` ASC";
		
		if($result = $mysqli->query($query)){
			while ($user = $result->fetch_assoc()) { $users[] = $user; }
			$result->free();
		}
		return $users;
	}
	
	function deleteUser($id,$profil)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		
		// On récupère l'ID utilisateur avant de supprimer pour le log
		$userIdToDelete = 0;
		$q = "SELECT id FROM utilisateurs WHERE profil_id = '$id' AND profil = '$profil'";
		$res = $mysqli->query($q);
		if($res && $row = $res->fetch_assoc()) {
			$userIdToDelete = $row['id'];
		}

		$query = "DELETE FROM `utilisateurs` 
		 WHERE `profil_id` = '".$id."'
		 AND `profil` = '".$profil."';";
		
		if ($mysqli->query($query)) {
			// LOG : Suppression User
			$actorId = $_SESSION['user']['id'] ?? 1;
			logUserAction($actorId, 'delete_user', "Suppression de l'utilisateur ID $userIdToDelete (Profil $profil / $id)");
			return true;
		}
		return false;
	}
	
	function deleteAdmin($id)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		$query = "DELETE FROM `administrateurs` 
		 WHERE `id` = '".$id."';";
		
		return $mysqli->query($query);
	}
	
	/* --- MODIFICATION ICI : REMPLACEMENT DE resetPassword PAR LES 3 FONCTIONS DE TOKEN --- */
	
	// 1. Demande de réinitialisation
	function requestPasswordReset($email)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$email = $mysqli->real_escape_string($email);
		
		// Vérification existence
		$check = $mysqli->query("SELECT id FROM utilisateurs WHERE email = '$email'");
		if ($check->num_rows == 0) return true; // On ne dit rien pour la sécurité
		
		// Création Token
		$token = bin2hex(random_bytes(32));
		$mysqli->query("DELETE FROM password_resets WHERE email = '$email'");
		
		$stmt = $mysqli->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
		$stmt->bind_param("ss", $email, $token);
		
		if ($stmt->execute()) {
			// Envoi Mail (Utilisation de votre nouvelle fonction sendShopMail)
			$link = $GLOBALS['url'] . "/reset-password?token=" . $token;
			$subject = "Réinitialisation de votre mot de passe - Miamy";
			$body = "<p>Bonjour,</p>";
			$body .= "<p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte Miamy.</p>";
			$body .= "<p><a href='$link' style='background:#e74c3c; color:#fff; padding:10px 20px; text-decoration:none; border-radius:4px;'>Réinitialiser mon mot de passe</a></p>";
			$body .= "<p><small>Ce lien est valide pendant 1 heure. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</small></p>";
			
			// Si sendShopMail n'est pas chargé ici, utiliser mail() en fallback
			if (function_exists('sendShopMail')) {
				sendShopMail($email, $subject, "Mot de passe oublié", $body);
			} else {
				mail($email, $subject, strip_tags($body));
			}
			return true;
		}
		return false;
	}
	
	// 2. Vérification du token
	function verifyResetToken($token)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$token = $mysqli->real_escape_string($token);
		
		$query = "SELECT email FROM password_resets WHERE token = '$token' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
		$res = $mysqli->query($query);
		
		if ($res && $res->num_rows > 0) {
			return $res->fetch_assoc()['email'];
		}
		return false;
	}
	
	// 3. Changement effectif du mot de passe
	function resetUserPassword($token, $newPassword)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		
		$email = verifyResetToken($token);
		if (!$email) return false;
		
		// --- HACHAGE SPECIFIQUE (Pass + Email + Salt) ---
		// Comme dans vos fonctions insertUtilisateur et trytoconnect
		$base_salt = $GLOBALS["base_salt"];
		$options = ['cost' => 9];
		$pass = password_hash($newPassword.$email.$base_salt, PASSWORD_BCRYPT, $options);
		
		$stmt = $mysqli->prepare("UPDATE `utilisateurs` SET `motdepasse` = ? WHERE `email` = ?");
		$stmt->bind_param("ss", $pass, $email);
		
		if ($stmt->execute()) {
			$mysqli->query("DELETE FROM password_resets WHERE email = '$email'");
			
			// LOG
			$q = "SELECT id FROM utilisateurs WHERE email = '$email'";
			$res = $mysqli->query($q);
			if($res && $u = $res->fetch_assoc()) {
				logUserAction($u['id'], 'reset_password', "Mot de passe réinitialisé via token");
			}
			return true;
		}
		return false;
	}
	
	/* --- FIN DES MODIFICATIONS MDP --- */

	function updatePassword($cavalier)
	{
		$db = Database::getInstance();
		$mysqli = $db->getConnection();

		if (empty($cavalier['motdepasse'])) {
			return false; 
		}

		$base_salt = $GLOBALS["base_salt"];
		$options = ['cost' => 9];
		$hashedPassword = password_hash($cavalier['motdepasse'].$cavalier['email'].$base_salt, PASSWORD_BCRYPT, $options);
		
		$cavalierId = mysqli_real_escape_string($mysqli, $cavalier['id']);
		$profil = mysqli_real_escape_string($mysqli, $cavalier['profil']);
		$hashedPassword = mysqli_real_escape_string($mysqli, $hashedPassword);

		$query = "UPDATE `utilisateurs` SET `motdepasse` = '$hashedPassword' WHERE `profil_id` = '$cavalierId' AND `profil` = '$profil'"; 

		$result = $mysqli->query($query);

		if($result) {
			// LOG : Changement mot de passe
			$actorId = $_SESSION['user']['id'] ?? 0;
			logUserAction($actorId, 'update_password', "Mot de passe modifié manuellement");
		}

		return $result ? true : false;
	}

?>