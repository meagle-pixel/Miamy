<?php include('head.php'); ?>
<?php include('header.php'); ?>

    <!-- Common Banner Area -->
    <section id="common_banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="common_bannner_text">
                        <h2>Historique des commandes</h2>
                        <ul>
                            <li><a href="index.php">Accueil</a></li>
                            <li><span><i class="fas fa-circle"></i></span>Historique des commandes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Area -->
    <section class="section_padding">
        <div class="container">
            <div class="row">
                <?php include ('menu-compte.php'); ?>
                <div class="col-lg-8">
                    <div class="dashboard_common_table">
                        <h3>Mes commandes</h3>
                        <div class="table-responsive-lg table_common_area">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Ref. Commande</th>
                                        <th>Montant</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#JK589V80</td>
                                        <td>50.00 € TTC</td>
                                        <td class="complete">En cours</td>
                                        <td><i class="fas fa-eye"></i></td>
                                    </tr>
                                    <tr>
                                        <td>#JK589V80</td>
                                        <td>50.00 € TTC</td>
                                        <td class="complete">Terminée</td>
                                        <td><i class="fas fa-eye"></i></td>
                                    </tr>
                                    <tr>
                                        <td>#JK589V80</td>
                                        <td>50.00 € TTC</td>
                                        <td class="complete">Terminée</td>
                                        <td><i class="fas fa-eye"></i></td>
                                    </tr>
                                    <tr>
                                        <td>#JK589V80</td>
                                        <td>50.00 € TTC</td>
                                        <td class="complete">Terminée</td>
                                        <td><i class="fas fa-eye"></i></td>
                                    </tr>
                                    <tr>
                                        <td>#JK589V80</td>
                                        <td>50.00 € TTC</td>
                                        <td class="cancele">Annulée</td>
                                        <td><i class="fas fa-eye"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include('footer.php'); ?>
<?php include('foot.php'); ?>