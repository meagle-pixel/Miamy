<?php
/** @var array  $resto                */
/** @var array  $plat                 */
/** @var int    $id_plat              */
/** @var int    $id_restaurant        */
/** @var array  $categoriesSuggestions */
/** @var array  $errors               */
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
                                    <li><i class="fas fa-times-circle me-2"></i><?= htmlspecialchars($error) ?></li>
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
