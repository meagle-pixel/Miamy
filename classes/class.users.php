<?php

	// --- FONCTIONS LOGS ---
	if (!function_exists('logUserAction')) {
		function logUserAction($userId, $actionType, $message) {
			$pdo = Database::getInstance()->getConnection();
			$ip  = $_SERVER['REMOTE_ADDR'] ?? '';

			$stmt = $pdo->prepare(
				"INSERT INTO user_logs (user_id, action_type, message, ip_address) VALUES (:user_id, :action_type, :message, :ip_address)"
			);
			$stmt->execute([
				'user_id'     => (int)$userId,
				'action_type' => $actionType,
				'message'     => $message,
				'ip_address'  => $ip,
			]);
		}
	}

	if (!function_exists('getUserLogs')) {
		function getUserLogs($userId, $limit = 20) {
			$pdo = Database::getInstance()->getConnection();

			$stmt = $pdo->prepare(
				"SELECT l.*,
				 COALESCE(a.nom, r.nom, c.nom, 'Utilisateur') as user_nom,
				 COALESCE(a.prenom, r.prenom, c.prenom, '') as user_prenom
				 FROM user_logs l
				 LEFT JOIN utilisateurs u ON l.user_id = u.id
				 LEFT JOIN administrateurs a ON (u.profil = 1 AND u.profil_id = a.id)
				 LEFT JOIN restaurateurs r ON (u.profil = 2 AND u.profil_id = r.id)
				 LEFT JOIN clients c ON (u.profil = 3 AND u.profil_id = c.id)
				 WHERE l.user_id = :user_id
				 ORDER BY l.created_at DESC LIMIT $limit"
			);
			$stmt->execute(['user_id' => (int)$userId]);
			return $stmt->fetchAll();
		}
	}

	if (!function_exists('time_elapsed_string')) {
		function time_elapsed_string($datetime, $full = false) {
			$now  = new DateTime;
			$ago  = new DateTime($datetime);
			$diff = $now->diff($ago);

			$diff->w   = floor($diff->d / 7);
			$diff->d  -= $diff->w * 7;

			$string = [
				'y' => 'an', 'm' => 'mois', 'w' => 'semaine',
				'd' => 'jour', 'h' => 'heure', 'i' => 'minute', 's' => 'seconde',
			];
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
		$pdo       = Database::getInstance()->getConnection();
		$base_salt = $GLOBALS["base_salt"];
		$options   = ['cost' => 9];
		$pass      = password_hash($utilisateur['motdepasse'] . $utilisateur['email'] . $base_salt, PASSWORD_BCRYPT, $options);

		$stmt = $pdo->prepare(
			"INSERT INTO `utilisateurs`
			(`id`, `email`, `motdepasse`, `profil`, `profil_id`, `dateinscription`, `dateconnect`, `dateaction`, `token`, `actif`)
			VALUES
			(NULL, :email, :motdepasse, :profil, :profil_id, NOW(), NULL, NULL, '', '1')"
		);
		$stmt->execute([
			'email'     => $utilisateur['email'],
			'motdepasse'=> $pass,
			'profil'    => $utilisateur['profil'],
			'profil_id' => $utilisateur['profil_id'],
		]);
		$idu = $pdo->lastInsertId();

		if ($idu) {
			$actorId = $_SESSION['user']['id'] ?? $idu;
			logUserAction($actorId, 'create_user', "Création du compte pour : " . $utilisateur['email']);
			return $idu;
		}
		return false;
	}

	function getContacts($request)
	{
		$contacts = [];
		$pdo      = Database::getInstance()->getConnection();

		// Administrateurs
		$stmt = $pdo->prepare(
			"SELECT DISTINCT id, nom, prenom FROM `administrateurs`
			 WHERE `nom` LIKE :like OR `prenom` LIKE :like"
		);
		$like = '%' . $request . '%';
		$stmt->execute(['like' => $like]);
		$admins = $stmt->fetchAll();

		foreach ($admins as $admin) {
			$s = $pdo->prepare("SELECT id FROM `utilisateurs` WHERE `profil` = 1 AND `profil_id` = :profil_id");
			$s->execute(['profil_id' => (int)$admin['id']]);
			foreach ($s->fetchAll() as $contact) {
				$contacts[] = ['id' => $contact['id'], 'nom' => $admin['prenom'] . ' ' . $admin['nom']];
			}
		}

		return $contacts;
	}


	function connectAs($id)
	{
		$user = getUser($id);
		trytoconnect($user['email'], '', true);
		$adminId = $_SESSION['original_admin_id'] ?? $_SESSION['user']['id'];
		logUserAction($adminId, 'connect_as', "Connexion en tant que l'utilisateur ID $id");
	}


	function getAllAdmins($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->query("SELECT * FROM `administrateurs`");
		$data = $stmt->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function getUserID($id, $profil)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `utilisateurs` WHERE `profil` = :profil AND `profil_id` = :profil_id");
		$stmt->execute(['profil' => (int)$profil, 'profil_id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function insertIP($id, $type = 1)
	{
		$ip  = get_ip();
		$pdo = Database::getInstance()->getConnection();

		$stmt = $pdo->prepare(
			"SELECT * FROM `ips` WHERE `user` = :user AND user_type = :user_type AND `ip` = :ip AND date < NOW() + INTERVAL 1 DAY"
		);
		$stmt->execute(['user' => (int)$id, 'user_type' => (int)$type, 'ip' => $ip]);
		$existing = $stmt->fetchAll();

		if (!empty($existing)) {
			$upd = $pdo->prepare("UPDATE `ips` SET `date` = NOW() WHERE `ip` = :ip");
			$upd->execute(['ip' => $ip]);
		} else {
			$ins = $pdo->prepare(
				"INSERT INTO `ips` (`ip`, `user`, `user_type`, `infos`, `date`) VALUES (:ip, :user, :user_type, :infos, NOW())"
			);
			$ins->execute([
				'ip'        => $ip,
				'user'      => (int)$id,
				'user_type' => (int)$type,
				'infos'     => getenv("HTTP_USER_AGENT"),
			]);
		}
	}

	function userIPS($id, $type = 1)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `ips` WHERE `user` = :user AND user_type = :user_type");
		$stmt->execute(['user' => (int)$id, 'user_type' => (int)$type]);
		return $stmt->fetchAll();
	}

	function isClear($profil, $page)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `autorisations` WHERE `page` = :page AND `profil` = :profil");
		$stmt->execute(['page' => (int)$page, 'profil' => (int)$profil]);
		$aut = $stmt->fetch();

		if (empty($aut) || $aut['etat'] == 0)
			return false;
		return true;
	}

	function editUser($user)
	{
		$pdo   = Database::getInstance()->getConnection();
		$query = "UPDATE `utilisateurs` SET `email` = :email";
		$params = ['email' => $user['email']];

		if ($user['motdepasse'] != '') {
			$query   .= ", `motdepasse` = :motdepasse";
			$params['motdepasse'] = $user['motdepasse'];
		}

		$query        .= " WHERE `id` = :id";
		$params['id']  = (int)$user['id'];

		$stmt = $pdo->prepare($query);
		$res  = $stmt->execute($params);

		if ($res) {
			$actorId = $_SESSION['user']['id'] ?? $user['id'];
			logUserAction($actorId, 'update_profile', "Modification des informations de connexion (Email/Pass)");
		}
		return $res;
	}

	function activeUser($key)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("UPDATE `utilisateurs` SET `actif` = '1' WHERE `key` = :key");
		$stmt->execute(['key' => $key]);
	}

	function getUser($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `utilisateurs` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		$user = $stmt->fetch();

		if (!$user) return [];

		if ($user['profil'] == 1) {
			$s = $pdo->prepare("SELECT * FROM `administrateurs` WHERE `id` = :id");
			$s->execute(['id' => (int)$user['profil_id']]);
			$user['userinfo'] = $s->fetch();
		} elseif ($user['profil'] == 2) {
			$s = $pdo->prepare("SELECT * FROM `restaurateurs` WHERE `id` = :id");
			$s->execute(['id' => (int)$user['profil_id']]);
			$user['userinfo'] = $s->fetch();
		} elseif ($user['profil'] == 3) {
			$s = $pdo->prepare("SELECT * FROM `clients` WHERE `id` = :id");
			$s->execute(['id' => (int)$user['profil_id']]);
			$user['userinfo'] = $s->fetch();
		}

		return $user;
	}

	function isRegistered($email)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT id FROM `utilisateurs` WHERE `email` = :email");
		$stmt->execute(['email' => $email]);
		return $stmt->fetch() !== false;
	}

	function trytoconnect($email, $pass, $bypass = false)
	{
		$pdo       = Database::getInstance()->getConnection();
		$base_salt = $GLOBALS["base_salt"] ?? "";

		$stmt = $pdo->prepare("SELECT * FROM `utilisateurs` WHERE `email` = :email AND actif = '1'");
		$stmt->execute(['email' => $email]);
		$userFound = $stmt->fetch();

		if ($userFound) {
			if (!$bypass) {
				if (!password_verify($pass . $email . $base_salt, $userFound['motdepasse'])) {
					$_SESSION['connected'] = false;
					$_SESSION['user']      = false;
					logUserAction(0, 'login_fail', "Echec connexion pour $email");
					return false;
				}
			}

			$_SESSION['connected'] = true;
			$_SESSION['user']      = $userFound;

			$upd = $pdo->prepare("UPDATE utilisateurs SET `dateconnect` = NOW() WHERE id = :id");
			$upd->execute(['id' => (int)$userFound['id']]);

			logUserAction($userFound['id'], 'login', "Connexion au site réussie");

			$table = '';
			if ($userFound['profil'] == 1) $table = "administrateurs";
			elseif ($userFound['profil'] == 2) $table = "restaurateurs";
			elseif ($userFound['profil'] == 3) $table = "clients";

			$_SESSION['user-info'] = null;
			if ($table !== '') {
				$s = $pdo->prepare("SELECT * FROM `$table` WHERE `id` = :id");
				$s->execute(['id' => (int)$userFound['profil_id']]);
				$_SESSION['user-info'] = $s->fetch() ?: null;

				if ($userFound['profil'] < 3) {
					$_SESSION['admin'] = true;
				}
			}

			return true;
		}

		$_SESSION['connected'] = false;
		$_SESSION['user']      = false;
		return false;
	}

	function updateUserData($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `utilisateurs` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		$result = $stmt->fetch();

		if ($result) {
			$_SESSION['connected'] = true;
			$_SESSION['user']      = $result;
		} else {
			$_SESSION['connected'] = false;
			$_SESSION['user']      = false;
		}

		$s = $pdo->prepare("SELECT * FROM `administrateurs` WHERE `id` = :id");
		$s->execute(['id' => (int)($_SESSION['user']['profil_id'] ?? 0)]);
		$_SESSION['user']['infos'] = $s->fetch();

		$upd = $pdo->prepare("UPDATE `utilisateurs` SET `dateaction` = NOW() WHERE `id` = :id");
		$upd->execute(['id' => (int)$id]);
	}

	function getAllProfils($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `profils`")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function updateUserDataLite($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `utilisateurs` WHERE `id` = :id");
		$stmt->execute(['id' => (int)$id]);
		$result = $stmt->fetch();

		if ($result) {
			$_SESSION['connected'] = true;
			$_SESSION['user']      = $result;
		} else {
			$_SESSION['connected'] = false;
			$_SESSION['user']      = false;
		}

		$upd = $pdo->prepare("UPDATE `utilisateurs` SET `dateaction` = NOW() WHERE `id` = :id");
		$upd->execute(['id' => (int)$id]);

		insertIP($id);
	}

	function getAllUsers($onlynb = false)
	{
		$pdo  = Database::getInstance()->getConnection();
		$data = $pdo->query("SELECT * FROM `utilisateurs`")->fetchAll();
		return $onlynb ? count($data) : $data;
	}

	function getUsersIdForEmail()
	{
		$pdo = Database::getInstance()->getConnection();
		return $pdo->query("SELECT * FROM `utilisateurs`")->fetchAll();
	}

	function getAdmin($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("SELECT * FROM `administrateurs` WHERE id = :id");
		$stmt->execute(['id' => (int)$id]);
		return $stmt->fetch() ?: [];
	}

	function deleteUser($id, $profil)
	{
		$pdo = Database::getInstance()->getConnection();

		$s = $pdo->prepare("SELECT id FROM utilisateurs WHERE profil_id = :profil_id AND profil = :profil");
		$s->execute(['profil_id' => (int)$id, 'profil' => (int)$profil]);
		$row            = $s->fetch();
		$userIdToDelete = $row['id'] ?? 0;

		$stmt = $pdo->prepare("DELETE FROM `utilisateurs` WHERE `profil_id` = :profil_id AND `profil` = :profil");
		$ok   = $stmt->execute(['profil_id' => (int)$id, 'profil' => (int)$profil]);

		if ($ok) {
			$actorId = $_SESSION['user']['id'] ?? 1;
			logUserAction($actorId, 'delete_user', "Suppression de l'utilisateur ID $userIdToDelete (Profil $profil / $id)");
			return true;
		}
		return false;
	}

	function deleteAdmin($id)
	{
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare("DELETE FROM `administrateurs` WHERE `id` = :id");
		return $stmt->execute(['id' => (int)$id]);
	}

	// 1. Demande de réinitialisation
	function requestPasswordReset($email)
	{
		$pdo  = Database::getInstance()->getConnection();

		$check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
		$check->execute(['email' => $email]);
		if (!$check->fetch()) return true;

		$token = bin2hex(random_bytes(32));

		$del = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
		$del->execute(['email' => $email]);

		$ins = $pdo->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())");
		if ($ins->execute(['email' => $email, 'token' => $token])) {
			$link    = $GLOBALS['url'] . "/reset-password?token=" . $token;
			$subject = "Réinitialisation de votre mot de passe - Miamy";
			$body    = "<p>Bonjour,</p>";
			$body   .= "<p>Une demande de réinitialisation de mot de passe a été effectuée pour votre compte Miamy.</p>";
			$body   .= "<p><a href='$link' style='background:#e74c3c; color:#fff; padding:10px 20px; text-decoration:none; border-radius:4px;'>Réinitialiser mon mot de passe</a></p>";
			$body   .= "<p><small>Ce lien est valide pendant 1 heure. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</small></p>";

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
		$pdo  = Database::getInstance()->getConnection();
		$stmt = $pdo->prepare(
			"SELECT email FROM password_resets WHERE token = :token AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
		);
		$stmt->execute(['token' => $token]);
		$row = $stmt->fetch();
		return $row ? $row['email'] : false;
	}

	// 3. Changement effectif du mot de passe
	function resetUserPassword($token, $newPassword)
	{
		$pdo   = Database::getInstance()->getConnection();
		$email = verifyResetToken($token);
		if (!$email) return false;

		$base_salt = $GLOBALS["base_salt"];
		$options   = ['cost' => 9];
		$pass      = password_hash($newPassword . $email . $base_salt, PASSWORD_BCRYPT, $options);

		$stmt = $pdo->prepare("UPDATE `utilisateurs` SET `motdepasse` = :motdepasse WHERE `email` = :email");
		if ($stmt->execute(['motdepasse' => $pass, 'email' => $email])) {
			$del = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
			$del->execute(['email' => $email]);

			$s = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
			$s->execute(['email' => $email]);
			if ($u = $s->fetch()) {
				logUserAction($u['id'], 'reset_password', "Mot de passe réinitialisé via token");
			}
			return true;
		}
		return false;
	}

	function updatePassword($data)
	{
		$pdo = Database::getInstance()->getConnection();

		if (empty($data['motdepasse'])) return false;

		$base_salt      = $GLOBALS["base_salt"];
		$options        = ['cost' => 9];
		$hashedPassword = password_hash($data['motdepasse'] . $data['email'] . $base_salt, PASSWORD_BCRYPT, $options);

		$stmt = $pdo->prepare(
			"UPDATE `utilisateurs` SET `motdepasse` = :motdepasse WHERE `profil_id` = :profil_id AND `profil` = :profil"
		);
		$result = $stmt->execute([
			'motdepasse' => $hashedPassword,
			'profil_id'  => (int)$data['id'],
			'profil'     => (int)$data['profil'],
		]);

		if ($result) {
			$actorId = $_SESSION['user']['id'] ?? 0;
			logUserAction($actorId, 'update_password', "Mot de passe modifié manuellement");
		}

		return $result;
	}

?>
