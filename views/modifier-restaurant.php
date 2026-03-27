<?php
// 1. Sécurité : vérifier si connecté et restaurateur
if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/connexion';</script>";
    exit();
}

// 2. Récupérer l'ID du restaurant depuis l'URL
$id_restaurant = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_restaurateur = $_SESSION['user']['profil_id'];

// 3. Connexion BDD
$db = Database::getInstance();
$mysqli = $db->getConnection();

// 4. Récupérer le restaurant ET vérifier qu'il appartient au restaurateur connecté
$query = "SELECT * FROM restaurants WHERE id = '$id_restaurant' AND id_restaurateur = '$id_restaurateur'";
$result = $mysqli->query($query);

if ($result->num_rows === 0) {
    echo "<script>window.location.href='" . $GLOBALS['url'] . "/mon-compte-restaurateur';</script>";
    exit();
}

$resto = $result->fetch_assoc();

// 5. Récupérer les catégories pour le select
$categories = [];
$result_cat = $mysqli->query("SELECT * FROM categories ORDER BY name ASC");
if ($result_cat) {
    while ($row = $result_cat->fetch_assoc()) {
        $categories[] = $row;
    }
}

$message_success = '';
$message_error = '';

// 6. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update'])) {

    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);

    // Validation
    if (empty($name) || empty($city)) {
        $message_error = "Le nom et la ville sont obligatoires.";
    } else {

        // Gestion de l'image (si nouvelle image uploadée)
        $image_name = $resto['main_image']; // Garder l'ancienne par défaut

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5000000) {
                $image_name = $resto['slug'] . '-' . time() . '.' . $ext;
                $upload_path = $_SERVER['DOCUMENT_ROOT'] . '/Miamy/assets/img/restaurants/' . $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
            }
        }

        // Requête UPDATE
        $name = $mysqli->real_escape_string($name);
        $city = $mysqli->real_escape_string($city);
        $description = $mysqli->real_escape_string($description);

        $query = "UPDATE restaurants SET 
                    name = '$name', 
                    city = '$city', 
                    description = '$description', 
                    category_id = '$category_id',
                    main_image = '$image_name'
                  WHERE id = '$id_restaurant' AND id_restaurateur = '$id_restaurateur'";

        if ($mysqli->query($query)) {
            $message_success = "Restaurant modifié avec succès !";
            // Recharger les données
            $result = $mysqli->query("SELECT * FROM restaurants WHERE id = '$id_restaurant'");
            $resto = $result->fetch_assoc();
        } else {
            $message_error = "Erreur lors de la modification : " . $mysqli->error;
        }
    }
}
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Modifier : <?= htmlspecialchars($resto['name']) ?></h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
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
                        <h3>Modifier le restaurant</h3>
                        <h2><?= htmlspecialchars($resto['name']) ?></h2>
                    </div>

                    <?php if ($message_success): ?>
                        <div class="alert alert-success"><?= $message_success ?></div>
                    <?php endif; ?>

                    <?php if ($message_error): ?>
                        <div class="alert alert-danger"><?= $message_error ?></div>
                    <?php endif; ?>

                    <div class="common_author_form">
                        <form action="modifier-restaurant?id=<?= $id_restaurant ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Nom du restaurant *</label>
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($resto['name']) ?>" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Catégorie</label>
                                        <select name="category_id" class="form-control">
                                            <option value="0">-- Sélectionner --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($resto['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label>Ville *</label>
                                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($resto['city']) ?>" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($resto['description']) ?></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label>Photo actuelle</label><br>
                                        <img src="<?= $GLOBALS['url'] ?>/assets/img/restaurants/<?= $resto['main_image'] ?>" alt="Photo actuelle" style="max-width: 200px; border-radius: 8px;">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group mb-4">
                                        <label>Changer la photo</label>
                                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Laissez vide pour garder l'image actuelle</small>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <a href="mon-compte-restaurateur" class="btn btn-outline-secondary w-100">
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
