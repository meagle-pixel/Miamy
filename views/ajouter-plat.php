<?php
// 1. Sécurité
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$id_restaurant   = isset($_GET['id_restaurant']) ? (int)$_GET['id_restaurant'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

if (!$id_restaurant) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 2. Vérifier que le restaurant appartient bien à ce restaurateur
$pdo  = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = :id AND id_restaurateur = :id_restaurateur");
$stmt->execute([
    'id'             => $id_restaurant,
    'id_restaurateur' => $id_restaurateur,
]);
$resto = $stmt->fetch();

if (!$resto) {
    header('Location: ' . $GLOBALS['url'] . '/mon-compte-restaurateur');
    exit();
}

// 3. Récupérer les catégories déjà utilisées pour ce restaurant (suggestions)
$platClass            = new Plat();
$categoriesExistantes = $platClass->getCategoriesByRestaurant($id_restaurant);

$message_success = '';
$message_error   = '';

// 4. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plat'])) {

    $nom         = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix        = str_replace(',', '.', trim($_POST['prix'] ?? '0'));
    $categorie   = trim($_POST['categorie'] ?? 'Plats');
    $disponible  = isset($_POST['disponible']) ? 1 : 0;

    if (empty($nom)) {
        $message_error = "Le nom du plat est obligatoire.";
    } elseif (!is_numeric($prix) || $prix < 0) {
        $message_error = "Le prix doit être un nombre valide.";
    } else {
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                $slug_plat  = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $nom));
                $image_name = $slug_plat . '-' . time() . '.' . $ext;
                $upload_path = $GLOBALS['dev']
                    ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/plats/' . $image_name
                    : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/plats/' . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_name    = null;
                    $message_error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $message_error = "Image invalide (formats acceptés : JPG, PNG, WebP — max 5 Mo).";
            }
        }

        if (empty($message_error)) {
            $data = [
                'nom'           => $nom,
                'description'   => $description,
                'prix'          => (float)$prix,
                'image'         => $image_name,
                'categorie'     => $categorie,
                'id_restaurant' => $id_restaurant,
                'disponible'    => $disponible,
            ];

            if ($platClass->insert($data)) {
                header('Location: ' . $GLOBALS['url'] . '/gestion-carte?id=' . $id_restaurant . '&success=added');
                exit();
            } else {
                $message_error = "Erreur lors de l'ajout du plat.";
            }
        }
    }
}

$categoriesBase        = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
$categoriesSuggestions = array_values(array_unique(array_merge(
    $categoriesBase,
    array_filter($categoriesExistantes, fn($v) => is_string($v) && $v !== '')
)));
sort($categoriesSuggestions);
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Ajouter un plat</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="gestion-carte?id=<?= $id_restaurant ?>">Carte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Ajouter un plat</li>
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
                        <h2>Nouveau plat</h2>
                    </div>

                    <?php if ($message_error): ?>
                        <div class="alert alert-danger"><?= $message_error ?></div>
                    <?php endif; ?>

                    <div class="common_author_form">
                        <form action="ajouter-plat?id_restaurant=<?= $id_restaurant ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Nom du plat *</label>
                                        <input type="text" name="nom" class="form-control"
                                            placeholder="Ex: Entrecôte grillée" required
                                            value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Catégorie</label>
                                        <select name="categorie" class="form-control">
                                            <?php foreach ($categoriesSuggestions as $cat): ?>
                                                <option value="<?= htmlspecialchars($cat) ?>"
                                                    <?= (($_POST['categorie'] ?? 'Plats') === $cat) ? 'selected' : '' ?>>
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
                                            step="0.01" min="0" placeholder="Ex: 12.50" required
                                            value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="3"
                                            placeholder="Ingrédients, allergènes, mode de cuisson…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Photo du plat</label>
                                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">JPG, PNG, WebP — max 5 Mo</small>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" name="disponible"
                                            id="disponible" <?= (!isset($_POST['submit_plat']) || isset($_POST['disponible'])) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="disponible">
                                            <p>Plat disponible à la commande</p>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <a href="gestion-carte?id=<?= $id_restaurant ?>" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-arrow-left me-2"></i> Annuler
                                    </a>
                                </div>
                                <div class="col-lg-6">
                                    <button type="submit" name="submit_plat" class="btn btn_theme btn_md w-100">
                                        <i class="fas fa-plus me-2"></i> Ajouter le plat
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
