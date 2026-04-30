<?php

// --- FONCTIONS LOGS ---
if (!function_exists('logUserAction')) {
	function logUserAction($userId, $actionType, $message)
	{
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
	function getUserLogs($userId, $limit = 20)
	{
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
	function time_elapsed_string($datetime, $full = false)
	{
		$now  = new DateTime;
		$ago  = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w   = floor($diff->d / 7);
		$diff->d  -= $diff->w * 7;

		$string = [
			'y' => 'an',
			'm' => 'mois',
			'w' => 'semaine',
			'd' => 'jour',
			'h' => 'heure',
			'i' => 'minute',
			's' => 'seconde',
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


function insertAdministrateur($data)
{
	$pdo  = Database::getInstance()->getConnection();
	$stmt = $pdo->prepare(
		"INSERT INTO `administrateurs` (`nom`, `prenom`, `telephone`)
			 VALUES (:nom, :prenom, :telephone)"
	);
	$stmt->execute([
		'nom'       => $data['nom'],
		'prenom'    => $data['prenom'],
		'telephone' => $data['telephone'] ?? '',
	]);
	return $pdo->lastInsertId();
}

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
		'motdepasse' => $pass,
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

	try {
		$pdo->beginTransaction();

		// Récupère l'id dans utilisateurs pour le log
		$s = $pdo->prepare("SELECT id FROM utilisateurs WHERE profil_id = :profil_id AND profil = :profil");
		$s->execute(['profil_id' => (int)$id, 'profil' => (int)$profil]);
		$row            = $s->fetch();
		$userIdToDelete = $row['id'] ?? 0;

		// Récupère dynamiquement le nom de la table métier depuis profils.type
		// → fonctionne pour tous les rôles actuels et futurs sans rien coder en dur
		$sp = $pdo->prepare("SELECT type FROM `profils` WHERE id = :id");
		$sp->execute(['id' => (int)$profil]);
		$profilRow = $sp->fetch();
		$table     = $profilRow['type'] ?? null;

		if ($table) {
			// Supprime la ligne dans la table métier du profil.
			// Les suppressions en cascade (restaurants → plats, horaires, etc.)
			// sont gérées par les FK ON DELETE CASCADE côté MySQL.
			$pdo->prepare("DELETE FROM `$table` WHERE `id` = :id")
				->execute(['id' => (int)$id]);
		}

		// Supprime le compte utilisateur
		$pdo->prepare("DELETE FROM `utilisateurs` WHERE `profil_id` = :profil_id AND `profil` = :profil")
			->execute(['profil_id' => (int)$id, 'profil' => (int)$profil]);

		$pdo->commit();

		$actorId = $_SESSION['user']['id'] ?? 1;
		logUserAction($actorId, 'delete_user', "Suppression complète de l'utilisateur ID $userIdToDelete (Profil $profil / profil_id $id)");

		return true;
	} catch (Exception $e) {
		if ($pdo->inTransaction()) $pdo->rollBack();
		error_log('[deleteUser] ' . $e->getMessage());
		return false;
	}
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


/**
 * Migre un utilisateur d'un profil à un autre en garantissant la cohérence
 * entre `utilisateurs` et les tables métier (`administrateurs`,
 * `restaurateurs`, `clients`).
 *
 * Tout est encapsulé dans une transaction SQL :
 *   1. Lit la ligne actuelle dans la table métier d'origine
 *   2. Insère dans la table métier de destination en reprenant les champs
 *      compatibles (nom, prenom, telephone…) + $extraData pour les NOT NULL
 *      spécifiques (ex. civilite, codepostal, ville pour `clients`)
 *   3. Met à jour `utilisateurs` (profil ET profil_id)
 *   4. Supprime l'ancienne ligne métier
 *
 * @param int   $userId    ID dans utilisateurs
 * @param int   $newProfil 1 = admin, 2 = restaurateur, 3 = client
 * @param array $extraData Champs supplémentaires (ex. civilite, codepostal, ville)
 * @return bool            true si succès, false si échec (détail dans error_log)
 */
function changeUserProfile($userId, $newProfil, $extraData = [])
{
	$pdo = Database::getInstance()->getConnection();

	$tables = [
		1 => 'administrateurs',
		2 => 'restaurateurs',
		3 => 'clients',
	];

	if (!isset($tables[$newProfil])) {
		error_log("[changeUserProfile] Profil cible inconnu : $newProfil");
		return false;
	}

	$newTable = $tables[$newProfil];

	try {
		$pdo->beginTransaction();

		// 1. Récupère l'utilisateur courant
		$stmt = $pdo->prepare("SELECT profil, profil_id FROM utilisateurs WHERE id = :id");
		$stmt->execute(['id' => (int)$userId]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$user) {
			throw new Exception("Utilisateur ID $userId introuvable");
		}

		$oldProfil   = (int) $user['profil'];
		$oldProfilId = (int) $user['profil_id'];

		// Aucun changement réel : on sort proprement
		if ($oldProfil === (int)$newProfil) {
			$pdo->commit();
			return true;
		}

		if ($oldProfil === 2 && $oldProfilId) {
			$stmt = $pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE id_restaurateur = :id");
			$stmt->execute(['id' => $oldProfilId]);
			$nbRestos = (int) $stmt->fetchColumn();

			if ($nbRestos > 0) {
				throw new Exception(
					"Impossible de migrer ce restaurateur : il possède encore $nbRestos restaurant(s). " . "Réassignez-les ou supprimez-les avant de changer son profil."
				);
			}
		}

		// 2. Lit l'ancienne ligne métier (s'il y en a une)
		$oldTable = $tables[$oldProfil] ?? null;
		$oldData  = [];
		if ($oldTable && $oldProfilId) {
			$stmt = $pdo->prepare("SELECT * FROM `$oldTable` WHERE id = :id");
			$stmt->execute(['id' => $oldProfilId]);
			$oldData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
		}

		// 3. Fusionne ancien + extra (extra prioritaire)
		$merged = array_merge($oldData, $extraData);

		// 4. Filtre selon les colonnes réelles de la table cible
		$cols = $pdo->query("DESCRIBE `$newTable`")->fetchAll(PDO::FETCH_COLUMN);
		$insertData = [];
		foreach ($cols as $col) {
			if ($col === 'id') continue;
			if (array_key_exists($col, $merged)) {
				$insertData[$col] = $merged[$col];
			}
		}

		// 5. INSERT dans la nouvelle table métier
		$colNames     = array_keys($insertData);
		$colsSql      = implode(',', array_map(function ($c) {
			return "`$c`";
		}, $colNames));
		$placeholders = implode(',', array_map(function ($c) {
			return ":$c";
		}, $colNames));
		$stmt = $pdo->prepare("INSERT INTO `$newTable` ($colsSql) VALUES ($placeholders)");
		$stmt->execute($insertData);
		$newProfilId = (int) $pdo->lastInsertId();

		// 6. UPDATE utilisateurs (profil + profil_id)
		$stmt = $pdo->prepare("UPDATE utilisateurs SET profil = :profil, profil_id = :profil_id WHERE id = :id");
		$stmt->execute([
			'profil'    => (int)$newProfil,
			'profil_id' => $newProfilId,
			'id'        => (int)$userId,
		]);

		// 7. DELETE de l'ancienne ligne métier
		if ($oldTable && $oldProfilId) {
			$stmt = $pdo->prepare("DELETE FROM `$oldTable` WHERE id = :id");
			$stmt->execute(['id' => $oldProfilId]);
		}

		$pdo->commit();

		if (function_exists('logUserAction')) {
			$actorId = $_SESSION['user']['id'] ?? $userId;
			logUserAction($actorId, 'update_role', "Migration de profil : utilisateur $userId, $oldProfil -> $newProfil");
		}

		return true;
	} catch (Exception $e) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		error_log('[changeUserProfile] ' . $e->getMessage());
		throw $e;
	}
}
