<?php
/** @var array  $categories      */
/** @var string $message_success */
/** @var string $message_error   */
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
                                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.jfif">
                                            <small class="text-muted">Formats acceptés : JPG, PNG, WebP, JFIF (max 5Mo)</small>
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
