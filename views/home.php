<?php
/** @var array  $allCategories  */
/** @var array  $featuredRestos */
/** @var string $error_message  */

// Compatibilité : si la vue est appelée sans passer par HomeController
$allCategories  = $allCategories  ?? [];
$featuredRestos = $featuredRestos ?? [];
$error_message  = $error_message  ?? '';
?>

<?php if (!empty($error_message)): ?>
    <div class="container mt-5 pt-5">
        <div class="alert alert-danger shadow">
            <strong>Oups !</strong> <?= htmlspecialchars($error_message) ?><br>
            Vérifiez vos identifiants dans <code>class.database.php</code> et assurez-vous que vos tables SQL existent.
        </div>
    </div>
<?php endif; ?>

<section id="home_one_banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="banner_one_text">
                    <h1><span>Le menu interactif de votre restaurant</span></h1>
                    <p>Scannez, commandez, savourez. Trouvez les meilleures tables équipées de la technologie Miamy.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/img/banner/bg1.png" alt="img" class="responsive">
            </div>
        </div>
    </div>
</section>

<section class="section_padding_top">
    <div class="container">
        <div class="row">
            <?php foreach ($allCategories as $cat): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card_custom service_card text-center mb-4">
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <a href="liste-restaurants.php?cat=<?= $cat['id_categorie'] ?>" class="btn btn_theme btn_sm">Voir</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">
        <div class="section_heading_center"><h2>Nos restaurants partenaires</h2></div>
        <div class="row">
            <?php if (empty($featuredRestos) && empty($error_message)): ?>
                <p class="text-center">Aucun restaurant à afficher pour le moment.</p>
            <?php endif; ?>

            <?php foreach ($featuredRestos as $resto): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="theme_common_box_two">
                    <div class="theme_two_box_img">
                        <img src="assets/img/restaurants/<?= $resto['main_image'] ?>" alt="<?= $resto['name'] ?>">
                    </div>
                    <div class="theme_two_box_content p-3">
                        <h4><?= htmlspecialchars($resto['name']) ?></h4>
                        <p><?= htmlspecialchars($resto['city']) ?></p>
                        <a href="restaurant-profile.php?slug=<?= $resto['slug'] ?>" class="btn btn_theme btn_sm">Voir la carte</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
echo '<pre>'; // La balise <pre> permet de garder le formatage (retours à la ligne)
var_dump($_SESSION);
echo '</pre>';
?>
