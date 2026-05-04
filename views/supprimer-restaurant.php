<?php
/** @var array  $resto         */
/** @var int    $id_restaurant */
/** @var string $message_error */
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Supprimer un restaurant</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span>Supprimer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="common_author_boxed text-center p-4">

                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                    </div>

                    <h3 class="mb-2">Confirmer la suppression</h3>
                    <p class="text-muted mb-4">
                        Vous êtes sur le point de supprimer définitivement le restaurant :<br>
                        <strong><?= htmlspecialchars($resto['name']) ?></strong> — <?= htmlspecialchars($resto['city']) ?>
                    </p>

                    <?php if (!empty($message_error)): ?>
                        <div class="alert alert-danger"><?= $message_error ?></div>
                    <?php endif; ?>

                    <p class="text-danger fw-bold mb-4">⚠️ Cette action est irréversible.</p>

                    <form method="POST" action="supprimer-restaurant?id=<?= $id_restaurant ?>">
                        <div class="row">
                            <div class="col-6">
                                <a href="mon-compte-restaurateur" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i> Annuler
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="confirm_delete" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
