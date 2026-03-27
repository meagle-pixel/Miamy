<?php

if(isset($_POST['envoi']))
{
	$client['email'] = $_POST['email'];
	if(isset($_POST['motdepasse']))
		$client['motdepasse'] = $_POST['motdepasse'];
	if(isset($_POST['civilite']) && $_POST['civilite'] == 1)
		$client['civilite'] = 1;
	if(isset($_POST['civilite']) && $_POST['civilite'] == 2)
		$client['civilite'] = 2;
	if(isset($_POST['civilite']) && $_POST['civilite'] == 3)
		$client['civilite'] = 3;
	$client['nom'] = $_POST['nom'];
	$client['prenom'] = $_POST['prenom'];
	$client['telephone'] = $_POST['telephone'];
	$client['adresse'] = $_POST['adresse'];
	$client['adresse_comp'] = $_POST['adresse_comp'];
	$client['codepostal'] = $_POST['codepostal'];
	$client['ville'] = $_POST['ville'];
	$client['id'] = $_POST['id'];
	
	updateClient($client);
	// Enregistrer client
	
	$message['type'] = 'success';
	$message['titre'] = 'Enregistrement réussi';
	$message['message'] = 'Les modifications ont été enregistrés correctement';
}

if(!isset($_REQUEST['id']))
{
	$page == 'add-client';
}

$client = getClient($_REQUEST['id']);

?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Editer un client</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accueil</a></li>
              <li class="breadcrumb-item active">Edition de client</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-primary">
					<div class="card-header">
						<h3 class="card-title">Modifier les informations sur le client :</h3>
						<div class="card-tools">
						</div>
					</div>
					<div class="card-body">
					
						<?php
						if(isset($message))
						{
						?>
						
						<div class="alert alert-<?php echo $message['type']; ?> alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h5><i class="icon fas fa-check"></i> <?php echo $message['titre']; ?></h5>
							<?php echo $message['message']; ?>
						</div>
						
						<?php
						}
						?>
					
						<form action="index.php" method="post">
							<div class="form-group">
								<label for="inputEmail">Email</label>
								<input type="email" id="email" name="email" class="form-control" data-table="clients" data-col="email" value="<?php echo $client['email']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputMotdepasse">Mot de passe (laisser vide pour ne pas changer)</label>
								<input type="text" id="motdepasse" name="motdepasse" class="form-control" data-table="clients" data-col="motdepasse" value="">
							</div>
							<div class="form-group">
								<label for="inputCivlite">Civilité</label>
								<div class="form-check">
									<input class="form-check-input" type="radio" id="civilite_1" name="civilite" data-table="clients" data-col="civilite" <?php if($client['civilite'] == "1"){ echo 'checked'; }?> value="1">
									<label class="form-check-label">M.</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" id="civilite_2" name="civilite" data-table="clients" data-col="civilite" <?php if($client['civilite'] == "2"){ echo 'checked'; }?> value="2">
									<label class="form-check-label">Mme</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" id="civilite_3" name="civilite" data-table="clients" data-col="civilite" <?php if($client['civilite'] == "3"){ echo 'checked'; }?> value="3">
									<label class="form-check-label">Mlle</label>
								</div>
							</div>
							<div class="form-group">
								<label for="inputName">Nom</label>
								<input type="text" id="nom" name="nom" class="form-control" data-table="clients" data-col="nom" value="<?php echo $client['nom']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputSurname">Prénom</label>
								<input type="text" id="prenom" name="prenom" class="form-control" data-table="clients" data-col="prenom" value="<?php echo $client['prenom']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputTel">Téléphone</label>
								<input type="text" id="telephone" name="telephone" class="form-control" data-table="clients" data-col="telephone" value="<?php echo $client['telephone']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputAddress">Adresse</label>
								<input type="text" id="adresse" name="adresse" class="form-control" data-table="clients" data-col="adresse" value="<?php echo $client['adresse']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputCompAddress">Complément d'adresse</label>
								<input type="text" id="adresse_comp" name="adresse_comp" class="form-control" data-table="clients" data-col="adresse_comp" value="<?php echo $client['adresse_comp']; ?>">
							</div>
							<div class="form-group">
								<label for="inputCP">Code postal</label>
								<input type="text" id="codepostal" name="codepostal" class="form-control" data-table="clients" data-col="codepostal" value="<?php echo $client['codepostal']; ?>" required>
							</div>
							<div class="form-group">
								<label for="inputVille">Ville</label>
								<input type="text" id="ville" name="ville" class="form-control" data-table="clients" data-col="ville" value="<?php echo $client['ville']; ?>" required>
							</div>
							<div class="form-group">
								<input type="hidden" id="mod" name="mod" value="<?php echo $page; ?>">
								<input type="hidden" id="id" name="id" value="<?php echo $_REQUEST['id']; ?>">
								<input type="submit" id="envoi" name="envoi" class="form-control btn btn-primary" value="Mettre à jour">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
			</div>
		</div>
	</section>
</div>
<!-- /.wrapper -->