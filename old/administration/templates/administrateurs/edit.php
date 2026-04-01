<?php

$id = $_GET['id'];

$admin = getAdmin($id);

$user = getUserID($admin['id'],1);

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Editer administrateur</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
              <li class="breadcrumb-item active">Editer administrateur</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
	  <form action="index.php?mod=edit-admin&id=<?php echo $id; ?>" method="post">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edition</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="inputNom">Nom</label>
                <input type="text" id="inputNom" class="form-control" value="<?php echo $admin['nom']; ?>" required="required">
              </div>
              <div class="form-group">
                <label for="inputPrenom">Prénom</label>
                <input type="text" id="inputPrenom" class="form-control" value="<?php echo $admin['prenom']; ?>" required="required">
              </div>
              <div class="form-group">
                <label for="inputTelephone">Téléphone</label>
                <input type="text" id="inputTelephone" class="form-control" value="<?php echo $admin['telephone']; ?>" required="required">
              </div>
			  <div class="form-group">
                <label for="inputEmail">Email</label>
                <input type="email" id="inputEmail" class="form-control" value="<?php echo $user['email']; ?>" disabled="disabled">
              </div>
			  <div class="form-group">
                <label for="inputPassword">Mot de passe</label>
                <input type="text " id="inputPassword" class="form-control" value="<?php echo $user['motdepasse']; ?>" disabled="disabled">
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <a href="index.php?mod=admins" class="btn btn-secondary">Annuler</a>
          <input type="submit" value="Sauvegarder" class="btn btn-success float-right">
        </div>
      </div>
	  </form>
    </section>
    <!-- /.content -->
  </div>