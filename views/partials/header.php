	<!-- preloader Area -->
	<div class="preloader">
		<div class="d-table">
			<div class="d-table-cell">
				<div class="lds-spinner">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Header Area -->
	<header class="main_header_arae">
		<!-- Top Bar -->
		<div class="topbar-area">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-6 col-md-6">
						<ul class="topbar-list">
							<li>
								<a href="#!"><i class="fab fa-facebook"></i></a>
								<a href="#!"><i class="fab fa-twitter-square"></i></a>
								<a href="#!"><i class="fab fa-instagram"></i></a>
								<a href="#!"><i class="fab fa-linkedin"></i></a>
							</li>
							<li><a href="#!"><span>contact@miamy.fr</span></a></li>
						</ul>
					</div>
					<div class="col-lg-6 col-md-6">
						<ul class="topbar-others-options">
							<?php if (isset($_SESSION['connected']) && $_SESSION['connected'] == true): ?>
								<?php $profil = $_SESSION['user']['profil'] ?? 3; ?>
								<li><span><?= htmlspecialchars($_SESSION['user-info']['prenom'] ?? 'Mon compte') ?></span></li>
								<?php if ($profil === 1): ?>
									<li><a href="dashboard">Dashboard Admin</a></li>
								<?php endif; ?>
								<li><a href="<?= $profil <= 2 ? 'mon-compte-restaurateur' : 'mon-compte' ?>">Mon compte</a></li>
								<li><a href="deconnexion">Déconnexion</a></li>
							<?php else: ?>
								<li><a href="connexion">Connexion</a></li>
								<li><a href="inscription-client">Inscription</a></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Navbar Bar -->
		<div class="navbar-area">
			<div class="main-responsive-nav">
				<div class="container">
					<div class="main-responsive-menu">
						<div class="logo">
							<a href="accueil">
								<img src="<?php echo APP_URL; ?>/assets/img/miamy-logo-mini.jpg" alt="Logo miamy menu de restaurant interactif">
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="main-navbar">
				<div class="container">
					<nav class="navbar navbar-expand-md navbar-light">
						<a class="navbar-brand" href="accueil">
							<img src="<?php echo APP_URL; ?>/assets/img/miamy-logo-mini.jpg" alt="logo miamy menu de restaurant interactif">
						</a>
						<div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
							<ul class="navbar-nav">
								<li class="nav-item">
									<a href="accueil" class="nav-link active">Accueil</a>
								</li>

								<li class="nav-item">
									<a href="liste-restaurants" class="nav-link active">Restaurants</a>
								</li>

								<li class="nav-item">
									<a href="a-propos" class="nav-link active">A propos</a>
								</li>

								<li class="nav-item">
									<a href="faq" class="nav-link active">FAQ</a>
								</li>

								<li class="nav-item">
									<a href="contact" class="nav-link">Contact</a>
								</li>


							</ul>
							<div class="others-options d-flex align-items-center">

								<div class="option-item">
									<div class="cart-btn">
										<a href="#" data-bs-toggle="modal" data-bs-target="#CartModal"><i
												class='fas fa-shopping-bag'></i><span>3</span></a>
									</div>
								</div>

								<div class="option-item">
									<a href="#" class="search-box">
										<i class="bi bi-search"></i></a>
								</div>

								<div class="option-item">
									<?php $profil_nav = $_SESSION['user']['profil'] ?? 3; ?>
									<a href="<?= (isset($_SESSION['connected']) && $_SESSION['connected'] && $profil_nav <= 2) ? 'mon-compte-restaurateur' : 'mon-compte' ?>" class="btn btn_navber">Mon compte</a>
								</div>

							</div>
						</div>
					</nav>
				</div>
			</div>
			<div class="others-option-for-responsive">
				<div class="container">

					<div class="dot-menu">
						<div class="inner">
							<div class="circle circle-one"></div>
							<div class="circle circle-two"></div>
							<div class="circle circle-three"></div>
						</div>
					</div>
					<div class="container">
						<div class="option-inner">
							<div class="others-options d-flex align-items-center">
								<div class="option-item">
									<a href="#" class="search-box"><i class="fas fa-search"></i></a>
								</div>

								<div class="option-item">
									<div class="cart-btn">
										<a href="#" data-bs-toggle="modal" data-bs-target="#CartModal"><i
												class='fas fa-shopping-bag'></i><span>3</span></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<!-- search -->
	<div class="search-overlay">
		<div class="d-table">
			<div class="d-table-cell">
				<div class="search-overlay-layer"></div>
				<div class="search-overlay-layer"></div>
				<div class="search-overlay-layer"></div>
				<div class="search-overlay-close">
					<span class="search-overlay-close-line"></span>
					<span class="search-overlay-close-line"></span>
				</div>
				<div class="search-overlay-form">
					<form>
						<input type="text" class="input-search" placeholder="Rechercher...">
						<button type="button"><i class="fas fa-search"></i></button>
					</form>
				</div>
			</div>
		</div>
	</div>