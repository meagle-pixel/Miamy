<?php
/** @var array  $resto               */
/** @var int    $id_restaurant       */
/** @var array  $categories          */
/** @var int    $current_category_id */
/** @var string $message_success     */
/** @var string $message_error       */
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
                                                <option value="<?= $cat['id_categorie'] ?>" <?= ($current_category_id == $cat['id_categorie']) ? 'selected' : '' ?>>
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
