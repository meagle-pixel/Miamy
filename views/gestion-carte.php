<?php
/** @var array  $resto             */
/** @var array  $plats             */
/** @var array  $platsParCategorie */
/** @var int    $id_restaurant     */
/** @var string $message_success   */
/** @var string $message_error     */
?>

<section id="common_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="common_bannner_text">
                    <h2>Gestion de la carte</h2>
                    <ul>
                        <li><a href="accueil">Accueil</a></li>
                        <li><span><i class="fas fa-circle"></i></span><a href="mon-compte-restaurateur">Mon compte</a></li>
                        <li><span><i class="fas fa-circle"></i></span><?= htmlspecialchars($resto['name']) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="dashboard_main_arae" class="section_padding">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= $GLOBALS['url'] ?>/assets/img/restaurants/<?= !empty($resto['main_image']) ? $resto['main_image'] : 'default-resto.jpg' ?>"
                    alt="img" class="rounded shadow-sm" style="width:70px; height:50px; object-fit:cover;">
                <div>
                    <h3 class="mb-0"><?= htmlspecialchars($resto['name']) ?></h3>
                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($resto['city']) ?></small>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="ajouter-plat?id_restaurant=<?= $id_restaurant ?>" class="btn btn_theme btn_md">
                    <i class="fas fa-plus me-2"></i> Ajouter un plat
                </a>
                <a href="mon-compte-restaurateur" class="btn btn-outline-secondary btn_md">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
            </div>
        </div>

        <?php if ($message_success): ?>
            <div class="alert alert-success shadow-sm">
                <i class="fas fa-check-circle me-2"></i> <?= $message_success ?>
            </div>
        <?php endif; ?>
        <?php if ($message_error): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="fas fa-times-circle me-2"></i> <?= $message_error ?>
            </div>
        <?php endif; ?>

        <?php if (empty($plats)): ?>
            <div class="alert alert-info text-center py-5 shadow-sm">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h4>Votre carte est vide pour l'instant.</h4>
                <p>Ajoutez vos premiers plats pour que vos clients puissent commander.</p>
                <a href="ajouter-plat?id_restaurant=<?= $id_restaurant ?>" class="btn btn_theme mt-2">
                    <i class="fas fa-plus me-2"></i> Ajouter un plat
                </a>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-primary mb-0"><?= count($plats) ?></h2>
                        <small class="text-muted">Plat<?= count($plats) > 1 ? 's' : '' ?> au total</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-success mb-0" id="total-dispo"><?= count(array_filter($plats, fn($p) => $p['disponible'])) ?></h2>
                        <small class="text-muted" id="label-dispo">Disponible<?= count(array_filter($plats, fn($p) => $p['disponible'])) > 1 ? 's' : '' ?></small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="bg-white shadow-sm rounded p-3 text-center border">
                        <h2 class="text-warning mb-0"><?= count($platsParCategorie) ?></h2>
                        <small class="text-muted">Catégorie<?= count($platsParCategorie) > 1 ? 's' : '' ?></small>
                    </div>
                </div>
            </div>

            <?php
            // Toutes les catégories dans l'ordre voulu — toujours affichées même si vides
            $toutesCategories = ['Entrées', 'Plats', 'Desserts', 'Boissons', 'Snacks'];
            // On ajoute les éventuelles catégories personnalisées non standards
            foreach ($platsParCategorie as $cat => $_) {
                if (!in_array($cat, $toutesCategories)) $toutesCategories[] = $cat;
            }
            foreach ($toutesCategories as $categorie):
                $platsCateg = $platsParCategorie[$categorie] ?? [];
            ?>
                <div class="mb-5" data-categorie-section="<?= htmlspecialchars($categorie) ?>">
                    <div class="d-flex align-items-center mb-3">
                        <h4 class="mb-0 me-3">
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-utensils me-2"></i><?= htmlspecialchars($categorie) ?>
                            </span>
                        </h4>
                        <small class="text-muted">
                            <span data-count-number><?= count($platsCateg) ?></span>
                            plat<span data-count-plural><?= count($platsCateg) > 1 ? 's' : '' ?></span>
                        </small>
                    </div>

                    <div class="row sortable-list" data-categorie="<?= htmlspecialchars($categorie) ?>">

                        <!-- Placeholder affiché quand la catégorie est vide -->
                        <div class="empty-placeholder col-12 text-center py-4 text-muted border border-dashed rounded"
                            style="<?= !empty($platsCateg) ? 'display:none;' : '' ?> border-style:dashed !important; background:#f8f9fa;">
                            <i class="fas fa-utensils fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">Aucun plat dans cette catégorie.<br>
                                <small>Glissez-en un ici depuis une autre catégorie.</small>
                            </p>
                        </div>

                        <?php foreach ($platsCateg as $plat): ?>
                            <div class="col-lg-12 mb-3" data-plat-id="<?= $plat['id'] ?>">
                                <div class="d-md-flex align-items-center bg-white shadow-sm rounded overflow-hidden border p-3 gap-3">

                                    <!-- Poignée de glisser-déposer -->
                                    <div class="flex-shrink-0 d-flex align-items-center pe-2" style="cursor:grab;" title="Glisser pour changer de catégorie">
                                        <i class="fas fa-grip-vertical drag-handle text-muted fs-5"></i>
                                    </div>

                                    <div class="flex-shrink-0 mb-3 mb-md-0">
                                        <?php if (!empty($plat['image'])): ?>
                                            <img src="<?= $GLOBALS['url'] ?>/assets/img/plats/<?= htmlspecialchars($plat['image']) ?>"
                                                alt="<?= htmlspecialchars($plat['nom']) ?>"
                                                class="rounded" style="width:110px; height:80px; object-fit:cover;">
                                        <?php else: ?>
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                style="width:110px; height:80px;">
                                                <i class="fas fa-utensils fa-2x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                            <div>
                                                <h5 class="mb-1">
                                                    <?= htmlspecialchars($plat['nom']) ?>
                                                    <!-- data-badge-disponible permet au JS de mettre à jour le badge sans recharger -->
                                                    <?php if (!$plat['disponible']): ?>
                                                        <span class="badge bg-danger ms-2" style="font-size:.7rem;" data-badge-disponible>Indisponible</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success ms-2" style="font-size:.7rem;" data-badge-disponible>Disponible</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <?php if (!empty($plat['description'])): ?>
                                                    <p class="text-muted mb-1" style="font-size:.9rem; max-width:500px;">
                                                        <?= htmlspecialchars(mb_substr($plat['description'], 0, 120)) ?><?= mb_strlen($plat['description']) > 120 ? '…' : '' ?>
                                                    </p>
                                                <?php endif; ?>
                                                <strong class="text-primary"><?= number_format($plat['prix'], 2, ',', ' ') ?> €</strong>
                                            </div>

                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                <!-- Bouton toggle disponibilité -->
                                                <button type="button"
                                                    class="btn btn_sm <?= $plat['disponible'] ? 'btn-outline-warning' : 'btn-outline-success' ?> btn-toggle-dispo"
                                                    data-plat-id="<?= $plat['id'] ?>"
                                                    data-disponible="<?= (int)$plat['disponible'] ?>"
                                                    title="<?= $plat['disponible'] ? 'Marquer comme indisponible' : 'Marquer comme disponible' ?>">
                                                    <i class="fas <?= $plat['disponible'] ? 'fa-eye-slash' : 'fa-eye' ?> me-1"></i>
                                                    <?= $plat['disponible'] ? 'Indispo' : 'Dispo' ?>
                                                </button>
                                                <a href="modifier-plat?id=<?= $plat['id'] ?>"
                                                    class="btn btn-outline-secondary btn_sm" title="Modifier">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                                <a href="supprimer-plat?id=<?= $plat['id'] ?>&id_restaurant=<?= $id_restaurant ?>"
                                                    class="btn btn-outline-danger btn_sm" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>

<?php
$url = $GLOBALS['url'];
$custom_js = <<<HTML
<!-- SortableJS : drag & drop entre catégories -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<style>
    .drag-handle        { cursor: grab !important; }
    .drag-handle:active { cursor: grabbing !important; }
    /* Carte "fantôme" pendant le glisser */
    .drag-ghost         { opacity: 0.4; background: #e8f4ff !important; border: 2px dashed #0d6efd !important; border-radius: 8px; }
    /* Zone de dépôt mise en évidence quand un plat la survole */
    .sortable-list.drag-over { background: #f0f7ff; border-radius: 8px; min-height: 60px; outline: 2px dashed #0d6efd; }
</style>
<script>
(function () {
    const BASE_URL = "{$url}";

    // Met à jour le compteur "X plat(s)" d'une section catégorie
    function updateCount(categorie, delta) {
        const section = document.querySelector('[data-categorie-section="' + categorie + '"]');
        if (!section) return;
        const numEl = section.querySelector('[data-count-number]');
        const plrEl = section.querySelector('[data-count-plural]');
        if (!numEl) return;
        const count = parseInt(numEl.textContent, 10) + delta;
        numEl.textContent = count;
        if (plrEl) plrEl.textContent = count > 1 ? 's' : '';
    }

    // Affiche le placeholder si la liste est vide, le cache sinon
    function checkEmpty(listEl) {
        const placeholder = listEl.querySelector('.empty-placeholder');
        if (!placeholder) return;
        const hasDishes = listEl.querySelectorAll('[data-plat-id]').length > 0;
        placeholder.style.display = hasDishes ? 'none' : '';
    }

    // Initialise SortableJS sur chaque liste de catégorie
    document.querySelectorAll('.sortable-list').forEach(function (el) {
        Sortable.create(el, {
            group:      'plats',     // même groupe = glisser entre sections
            animation:  150,
            handle:     '.drag-handle',
            ghostClass: 'drag-ghost',
            filter:     '.empty-placeholder', // le placeholder n'est pas draggable
            dragoverBubble: true,

            onStart: function (evt) {
                // Indique visuellement les zones de dépôt disponibles
                document.querySelectorAll('.sortable-list').forEach(function (l) {
                    l.classList.add('drag-over');
                });
            },

            onEnd: function (evt) {
                // Retire la mise en évidence
                document.querySelectorAll('.sortable-list').forEach(function (l) {
                    l.classList.remove('drag-over');
                });

                const platId   = evt.item.dataset.platId;
                const newCateg = evt.to.dataset.categorie;
                const oldCateg = evt.from.dataset.categorie;

                if (newCateg === oldCateg) return; // Juste un réordonnancement, rien à faire

                // --- Mise à jour optimiste de l'interface ---
                updateCount(oldCateg, -1);
                updateCount(newCateg, +1);
                checkEmpty(evt.from); // cache le placeholder si la source se vide
                checkEmpty(evt.to);   // cache le placeholder si la destination reçoit un plat

                // --- Appel AJAX pour persister en base ---
                $.post(BASE_URL + '/actions/update-plat-categorie.php', {
                    id_plat:   platId,
                    categorie: newCateg
                }, function (resp) {
                    if (!resp.success) {
                        revert(evt, oldCateg, newCateg);
                        alert('Erreur lors du changement de catégorie. Le plat a été replacé.');
                    }
                }, 'json').fail(function () {
                    revert(evt, oldCateg, newCateg);
                    alert('Erreur réseau. Veuillez réessayer.');
                });
            }
        });
    });

    // Annule visuellement un déplacement si l'AJAX échoue
    function revert(evt, oldCateg, newCateg) {
        const ref = evt.from.children[evt.oldDraggableIndex] || null;
        evt.from.insertBefore(evt.item, ref);
        updateCount(newCateg, -1);
        updateCount(oldCateg, +1);
        checkEmpty(evt.to);
    }

    // --- Toggle disponible / indisponible ---
    $(document).on('click', '.btn-toggle-dispo', function () {
        const btn        = $(this);
        const platId     = btn.data('plat-id');
        const disponible = parseInt(btn.data('disponible'), 10);
        const card       = btn.closest('div[data-plat-id]');
        const badge      = card.find('[data-badge-disponible]');

        // Désactive le bouton le temps de la requête
        btn.prop('disabled', true);

        $.post(BASE_URL + '/actions/toggle-disponible-plat.php', { id_plat: platId },
        function (resp) {
            if (resp.success) {
                const dispo = resp.disponible; // 1 = disponible, 0 = indisponible
                btn.data('disponible', dispo);

                // Mettre à jour le badge
                if (dispo) {
                    badge.removeClass('bg-danger').addClass('bg-success').text('Disponible');
                } else {
                    badge.removeClass('bg-success').addClass('bg-danger').text('Indisponible');
                }

                // Mettre à jour le bouton
                if (dispo) {
                    btn.removeClass('btn-outline-success').addClass('btn-outline-warning')
                       .attr('title', 'Marquer comme indisponible')
                       .html('<i class="fas fa-eye-slash me-1"></i>Indispo');
                } else {
                    btn.removeClass('btn-outline-warning').addClass('btn-outline-success')
                       .attr('title', 'Marquer comme disponible')
                       .html('<i class="fas fa-eye me-1"></i>Dispo');
                }

                // Griser légèrement la carte si indisponible
                card.find('.d-md-flex').toggleClass('opacity-50', !dispo);

                // Mettre à jour le compteur global "Disponibles" en haut de page
                const totalDispoEl = document.getElementById('total-dispo');
                const labelDispoEl = document.getElementById('label-dispo');
                if (totalDispoEl) {
                    const count = parseInt(totalDispoEl.textContent, 10) + (dispo ? 1 : -1);
                    totalDispoEl.textContent = count;
                    if (labelDispoEl) labelDispoEl.textContent = 'Disponible' + (count > 1 ? 's' : '');
                }
            } else {
                alert('Erreur lors de la mise à jour. Veuillez réessayer.');
            }
        }, 'json')
        .fail(function () {
            alert('Erreur réseau. Veuillez réessayer.');
        })
        .always(function () {
            btn.prop('disabled', false);
        });
    });
})();
</script>
HTML;
?>