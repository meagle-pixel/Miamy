
    <!-- Common Banner Area -->
    <section id="common_banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="common_bannner_text">
                        <h2>Mon profil</h2>
                        <ul>
                            <li><a href="index.php">Accueil</a></li>
                            <li><span><i class="fas fa-circle"></i></span>Mon profil</li>
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
                        <h3>Mon profil</h3>
                        <div class="profile_update_form">
                            <form action="!#" id="profile_form_area">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="f-name">Prénom</label>
                                            <input type="text" class="form-control" id="f-name" placeholder="Prénom"
                                                value="Yann">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="l-name">Nom</label>
                                            <input type="text" class="form-control" id="l-name" placeholder="Nom" value="GOGUET-GALLI">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="mail-address">Email</label>
                                            <input type="text" class="form-control" id="mail-address" placeholder="nom@domaine.fr"
                                                value="yann@youonline.fr">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="mobil-number">Téléphone</label>
                                            <input type="text" class="form-control" id="mobil-number" placeholder="06 88 88 88 88"
                                                value="06 24 99 71 71">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group change_password_field">
                                            <label for="password">Mot de passe</label>
                                            <input type="password" class="form-control" id="password" value="cdkdkdd">
                                            <p>Changer mot de passe</p>
                                        </div>
                                    </div>
                                    <div class="change_password_input_boxed">
                                        <h3>Changer mot de passe</h3>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="password" class="form-control"
                                                        placeholder="Ancien mot de passe">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="password" class="form-control"
                                                        placeholder="Nouveau mot de passe">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Logout Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body logout_modal_content">
                    <div class="btn_modal_closed">
                        <button type="button" data-bs-dismiss="modal" aria-label="Close"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <h3>
                        Are you sure? <br>
                        you want to log out.
                    </h3>
                    <div class="logout_approve_button">
                        <button data-bs-dismiss="modal" class="btn btn_theme btn_md">Yes Confirm</button>
                        <button data-bs-dismiss="modal" class="btn btn_border btn_md">No Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

