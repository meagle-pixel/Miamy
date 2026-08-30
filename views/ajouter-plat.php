<?php
/** @var array  $resto                */
/** @var int    $id_restaurant        */
/** @var array  $categoriesSuggestions */
/** @var string $message_success       */
/** @var string $message_error         */
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
                                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.jfif">
                                        <small class="text-muted">JPG, PNG, WebP, JFIF — max 5 Mo</small>
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
