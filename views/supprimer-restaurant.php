<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$id_restaurant   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

if (!$id_restaurant) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 2. Récupérer le restaurant et vérifier qu'il appartient au restaurateur connecté
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id_restaurant = :id AND id_restaurateur = :id_restaurateur");
$stmt->execute([
    'id'              => $id_restaurant,
    'id_restaurateur' => $id_restaurateur,
]);
$resto = $stmt->fetch();

if (!$resto) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 3. Traitement de la confirmation de suppression
$message_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    try {
        $restaurant = new Restaurant();
        if ($restaurant->delete($id_restaurant)) {
            header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur?success=deleted');
            exit();
        } else {
            $message_error = "Une erreur est survenue lors de la suppression.";
        }
    } catch (PDOException $e) {
        error_log('[supprimer-restaurant] ' . $e->getMessage());
        $message_error = "Erreur lors de la suppression. Détail technique : " . $e->getMessage();
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Supprimer un restaurant</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Supprimer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="common_author_boxed text-center p-4">

                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                    </div>

                    <h3 class="mb-2">Confirmer la suppression</h3>
                    <p class="text-muted mb-4">
                        Vous êtes sur le point de supprimer définitivement le restaurant :<br>
                        <strong><?= htmlspecialchars($resto['name']) ?></strong> — <?= htmlspecialchars($resto['city']) ?>
                    </p>

                    <?php if (!empty($message_error)): ?>
                        <div class="alert alert-danger"><?= $message_error ?></div>
                    <?php endif; ?>

                    <p class="text-danger fw-bold mb-4">⚠️ Cette action est irréversible.</p>

                    <form method="POST" action="supprimer-restaurant?id=<?= $id_restaurant ?>">
                        <div class="row">
                            <div class="col-6">
                                <a href="mon-compte-restaurateur" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i> Annuler
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="confirm_delete" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
