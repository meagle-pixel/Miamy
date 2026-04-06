<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/connexion';</script>";
    exit();
}

$id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

if (!$id_plat || !$id_restaurant) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

// 2. Récupérer le plat
$platClass = new Plat();
$plat      = $platClass->getById($id_plat);

if (!$plat || $plat['id_restaurant'] !== $id_restaurant) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

// 3. Vérifier que le restaurant appartient bien à ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = ? AND id_restaurateur = ?");
$stmt->execute([$id_restaurant, $id_restaurateur]);
$resto = $stmt->fetch();

if (!$resto) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

$message_error = '';

// 4. Traitement de la confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if ($platClass->delete($id_plat)) {
        echo "<script>window.location.href='" . $GLOBALS['url'] . "/gestion-carte?id={$id_restaurant}&success=deleted';</script>";
        exit();
    } else {
        $message_error = "Une erreur est survenue lors de la suppression.";
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Supprimer un plat</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="gestion-carte?id=<?= $id_restaurant ?>">Carte</a></li>
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
                    <p class="text-muted mb-2">
                        Vous êtes sur le point de supprimer le plat :<br>
                        <strong><?= htmlspecialchars($plat['nom']) ?></strong>
                        — <?= number_format($plat['prix'], 2, ',', ' ') ?> €
                    </p>
                    <p class="text-muted mb-4">
                        Restaurant : <strong><?= htmlspecialchars($resto['name']) ?></strong>
                    </p>

                    <?php if (!empty($message_error)): ?>
                        <div class="alert alert-danger"><?= $message_error ?></div>
                    <?php endif; ?>

                    <p class="text-danger fw-bold mb-4">⚠️ Cette action est irréversible.</p>

                    <form method="POST" action="supprimer-plat?id=<?= $id_plat ?>&id_restaurant=<?= $id_restaurant ?>">
                        <div class="row">
                            <div class="col-6">
                                <a href="gestion-carte?id=<?= $id_restaurant ?>" class="btn btn-outline-secondary w-100">
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
