<?php
// 1. Sécurité : vérifier si connecté ET restaurateur (profil 2 uniquement)
// Les admins (profil 1) ont leur profil_id pointant vers `administrateurs`,
// pas vers `restaurateurs` → la FK échouerait. Cette page est réservée au profil 2.
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || (int)$_SESSION['user']['profil'] !== 2) {
    header('Location: ' . $GLOBALS['url'] . '/connexion');
    exit();
}

$message_success = '';
$message_error   = '';

// 2. Récupérer les catégories pour le select
$pdo        = Database::getInstance()->getConnection();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// 3. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_restaurant'])) {

    $name            = trim($_POST['name'] ?? '');
    $city            = trim($_POST['city'] ?? '');
    $description     = trim($_POST['description'] ?? '');
    $category_id     = (int)($_POST['category_id'] ?? 0);
    $id_restaurateur = (int)($_SESSION['user']['profil_id'] ?? 0);

    if ($id_restaurateur <= 0) {
        $message_error = "Compte restaurateur introuvable. Veuillez contacter l'administrateur.";
    } elseif (empty($name) || empty($city)) {
        $message_error = "Le nom et la ville sont obligatoires.";
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = $slug . '-' . uniqid();

        // Gestion de l'image
        $image_name = 'default-resto.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                $image_name  = $slug . '.' . $ext;
                $upload_path = $GLOBALS['dev']
                    ? $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/restaurants/' . $image_name
                    : $_SERVER['DOCUMENT_ROOT'] . '/assets/img/restaurants/' . $image_name;

                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            }
        }

        // Insertion en BDD avec requête préparée (sous transaction + try/catch
        // pour qu'une exception SQL n'aille jamais casser la sortie HTML).
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO restaurants (name, slug, description, city, main_image, id_restaurateur, created_at)
                 VALUES (:name, :slug, :description, :city, :main_image, :id_restaurateur, NOW())"
            );

            $stmt->execute([
                'name'            => $name,
                'slug'            => $slug,
                'description'     => $description,
                'city'            => $city,
                'main_image'      => $image_name,
                'id_restaurateur' => $id_restaurateur,
            ]);

            $new_id = $pdo->lastInsertId();

            // Liaison avec la catégorie via la table intermédiaire
            if ($category_id > 0) {
                $stmt_cat = $pdo->prepare(
                    "INSERT INTO restaurant_categories (id_restaurant, id_categorie) VALUES (:id_restaurant, :id_categorie)"
                );
                $stmt_cat->execute([
                    'id_restaurant' => $new_id,
                    'id_categorie'  => $category_id,
                ]);
            }

            $message_success = "Restaurant ajouté avec succès !";

        } catch (PDOException $e) {
            error_log('[ajouter-restaurant] ' . $e->getMessage());
            $message_error = "Erreur lors de l'ajout du restaurant. Détail technique : " . $e->getMessage();
        }
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Ajouter un restaurant</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Ajouter</li>
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
                        <h3>Nouveau restaurant</h3>
                        <h2>Informations de l'établissement</h2>
                    </div>

                    <?php if ($message_success): ?>
                        <div class="alert alert-success"><?= $message_success ?></div>
                        <div class="text-center mt-3">
                            <a href="mon-compte-restaurateur" class="btn btn_theme">Retour au tableau de bord</a>
                        </div>
                    <?php else: ?>

                        <?php if ($message_error): ?>
                            <div class="alert alert-danger"><?= $message_error ?></div>
                        <?php endif; ?>

                        <div class="common_author_form">
                            <form action="ajouter-restaurant" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label>Nom du restaurant *</label>
                                            <input type="text" name="name" class="form-control" placeholder="Ex: Le Petit Bistrot" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Catégorie</label>
                                            <select name="category_id" class="form-control">
                                                <option value="0">-- Sélectionner --</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label>Ville *</label>
                                            <input type="text" name="city" class="form-control" placeholder="Ex: Paris" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="4" placeholder="Décrivez votre établissement..."></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group mb-4">
                                            <label>Photo principale</label>
                                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                            <small class="text-muted">Formats acceptés : JPG, PNG, WebP (max 5Mo)</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <button type="submit" name="submit_restaurant" class="btn btn_theme btn_md w-100">
                                            <i class="fas fa-plus me-2"></i> Créer mon restaurant
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>