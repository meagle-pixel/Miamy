<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/connexion';</script>";
    exit();
}

$id_plat         = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

if (!$id_plat) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

// 2. Récupérer le plat
$platClass = new Plat();
$plat      = $platClass->getById($id_plat);

if (!$plat) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

$id_restaurant = $plat['id_restaurant'];

// 3. Vérifier que le restaurant appartient bien à ce restaurateur
$db     = Database::getInstance();
$mysqli = $db->getConnection();
$stmt   = $mysqli->prepare("SELECT * FROM restaurants WHERE id = ? AND id_restaurateur = ?");
$stmt->bind_param("ii", $id_restaurant, $id_restaurateur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}
$resto = $result->fetch_assoc();
$stmt->close();

// 4. Catégories existantes
$categoriesExistantes = $platClass->getCategoriesByRestaurant($id_restaurant);
$categoriesBase = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
$categoriesSuggestions = array_values(array_unique(array_merge(
    $categoriesBase,
    array_filter($categoriesExistantes, fn($v) => is_string($v) && $v !== '')
)));

$errors = []; 

// 5. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update'])) {

    $nom         = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix        = str_replace(',', '.', trim($_POST['prix'] ?? '0'));
    $categorie   = trim($_POST['categorie'] ?? 'Plats');
    $disponible  = isset($_POST['disponible']) ? 1 : 0;

    // Validation des champs
    if (empty($nom)) {
        $errors[] = "Le nom du plat est obligatoire.";
    }

    if (!is_numeric($prix) || $prix < 0) {
        $errors[] = "Le prix doit être un nombre valide et positif.";
    }

    // Gestion de l'image
    $image_name = $plat['image']; // Par défaut on garde l'ancienne

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format d'image non supporté (JPG, PNG, WebP uniquement).";
        } elseif ($_FILES['image']['size'] > 5000000) {
            $errors[] = "L'image est trop volumineuse (maximum 5 Mo).";
        } else {
            $slug_plat  = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
            $new_image  = $slug_plat . '-' . time() . '.' . $ext;
            $upload_dir = $GLOBALS['dev'] ? '/Miamy/assets/img/plats/' : '/assets/img/plats/';
            $upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir . $new_image;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_name = $new_image;
            } else {
                $errors[] = "Erreur technique lors du téléchargement de l'image.";
            }
        }
    }

    // Si aucune erreur, on procède à l'update
    if (empty($errors)) {
        $data = [
            'nom'         => $nom,
            'description' => $description,
            'prix'        => (float)$prix,
            'image'       => $image_name,
            'categorie'   => $categorie,
            'disponible'  => $disponible,
        ];

        if ($platClass->update($id_plat, $data)) {
            echo "<script>window.location.href='" . $GLOBALS['url'] . "/gestion-carte?id={$id_restaurant}&success=updated';</script>";
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la mise à jour en base de données.";
        }
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Modifier un plat</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="gestion-carte?id=<?= $id_restaurant ?>">Carte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Modifier</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="common_author_boxed">
                    <div class="common_author_heading">
                        <h3><?= htmlspecialchars($resto['name']) ?></h3>
                        <h2>Modifier : <?= htmlspecialchars($plat['nom']) ?></h2>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><i class="fas fa-times-circle me-2"></i><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="common_author_form">
                        <form action="modifier-plat?id=<?= $id_plat ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Nom du plat *</label>
                                        <input type="text" name="nom" class="form-control" required
                                            value="<?= htmlspecialchars($plat['nom']) ?>">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Catégorie</label>
                                        <select name="categorie" class="form-control">
                                            <?php foreach ($categoriesSuggestions as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat) ?>"
                                                    <?= ($plat['categorie'] === $cat) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Prix (€) *</label>
                                        <input type="number" name="prix" class="form-control"
                                            step="0.01" min="0" required
                                            value="<?= htmlspecialchars($plat['prix']) ?>">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($plat['description'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Photo actuelle</label><br>
                                        <?php if (!empty($plat['image'])): ?>
                                            <img src="<?= $GLOBALS['url'] ?>/assets/img/plats/<?= htmlspecialchars($plat['image']) ?>"
                                                alt="Photo du plat" style="max-width:200px; border-radius:8px;" class="shadow-sm">
                                        <?php else: ?>
                                            <div class="rounded bg-light d-inline-flex align-items-center justify-content-center p-4 border text-muted">
                                                <i class="fas fa-utensils fa-2x me-2"></i> Aucune image
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Changer la photo</label>
                                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Laissez vide pour garder l'image actuelle</small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" name="disponible"
                                            id="disponible" <?= $plat['disponible'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="disponible">
                                            Plat disponible à la commande
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <a href="gestion-carte?id=<?= $id_restaurant ?>" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-arrow-left me-2"></i> Retour
                                    </a>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" name="submit_update" class="btn btn_theme btn_md w-100">
                                        <i class="fas fa-save me-2"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>