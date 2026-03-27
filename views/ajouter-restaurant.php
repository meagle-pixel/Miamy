<?php
// 1. Sécurité : vérifier si connecté et restaurateur
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/connexion';</script>";
    exit();
}

$message_success = '';
$message_error = '';

// 2. Récupérer les catégories pour le select
$categories = [];
$db = Database::getInstance();
$mysqli = $db->getConnection();
$result = $mysqli->query("SELECT * FROM categories ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// 3. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_restaurant'])) {

    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $id_restaurateur = $_SESSION['user']['profil_id'];

    // Validation
    if (empty($name) || empty($city)) {
        $message_error = "Le nom et la ville sont obligatoires.";
    } else {
        // Création du slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = $slug . '-' . uniqid(); // Rendre unique

        // Gestion de l'image
        $image_name = 'default-resto.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                $image_name = $slug . '.' . $ext;
                $upload_path = $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/restaurants/' . $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            }
        }

        // Insertion en BDD
        $name = $mysqli->real_escape_string($name);
        $slug = $mysqli->real_escape_string($slug);
        $city = $mysqli->real_escape_string($city);
        $description = $mysqli->real_escape_string($description);

        $query = "INSERT INTO restaurants (name, slug, description, city, main_image, category_id, id_restaurateur, created_at) 
                  VALUES ('$name', '$slug', '$description', '$city', '$image_name', '$category_id', '$id_restaurateur', NOW())";

        if ($mysqli->query($query)) {
            $message_success = "Restaurant ajouté avec succès !";
        } else {
            $message_error = "Erreur lors de l'ajout : " . $mysqli->error;
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
                                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
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