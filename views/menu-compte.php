<div class="col-lg-4">
	<div class="dashboard_sidebar">
		<div class="dashboard_sidebar_user">
			<img src="assets/img/common/dashboard-user.png" alt="">
			<h3>Yann GOGUET-GALLI</h3>
			<p><a href="tel:+336247171">06 24 99 71 71</a></p>
			<p><a href="mailto:yann@youonline.fr">yann@youonline.fr</a></p>
		</div>
		<?php 
			$page_name = htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8');
		?>
		<div class="dashboard_menu_area">
			<ul>
				<li><a href="mon-compte.php" <?php if($page_name == '/mon-compte.php'){ ?>class="active"<?php } ?>><i class="fas fa-home"></i>Tableau de bord</a></li>
				<li><a href="commandes.php" <?php if($page_name == '/commandes.php'){ ?>class="active"<?php } ?>><i class="fas fa-tachometer-alt"></i>Commandes</a></li>
				<li><a href="profile.php" <?php if($page_name == '/profile.php'){ ?>class="active"<?php } ?>><i class="fas fa-user-circle"></i>Mon profil</a></li>
				<li>
					<a href="#!" data-bs-toggle="modal" data-bs-target="#logoutModal">
						<i class="fas fa-sign-out-alt"></i>Se déconnecter
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body logout_modal_content">
				<div class="btn_modal_closed">
					<button type="button" data-bs-dismiss="modal" aria-label="Close"><i
							class="fas fa-times"></i></button>
				</div>
				<h3>
					Se déconnecter ?
				</h3>
				<div class="logout_approve_button">
					<button data-bs-dismiss="modal" class="btn btn_theme btn_md">Oui confirmer</button>
					<button data-bs-dismiss="modal" class="btn btn_border btn_md">Non annuler</button>
				</div>
			</div>
		</div>
	</div>
</div>