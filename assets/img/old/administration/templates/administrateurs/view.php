<?php

$id = $_GET['id'];

$admin = getAdmin($id);

$user = getUserID($admin['id'],1);

$ips = userIPS($id);

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Aperçu administrateur</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
              <li class="breadcrumb-item active">Aperçu administrateur</li>
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
              <h3 class="card-title">Aperçu</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="inputNom">Nom</label>
                <input type="text" id="inputNom" class="form-control" value="<?php echo $admin['nom']; ?>" disabled="disabled">
              </div>
              <div class="form-group">
                <label for="inputPrenom">Prénom</label>
                <input type="text" id="inputPrenom" class="form-control" value="<?php echo $admin['prenom']; ?>" disabled="disabled">
              </div>
              <div class="form-group">
                <label for="inputTelephone">Téléphone</label>
                <input type="text" id="inputTelephone" class="form-control" value="<?php echo $admin['telephone']; ?>" disabled="disabled">
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
		<div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Informations de sécurité</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive mailbox-messages">
                <table id="dataTable" class="table table-hover table-striped display" style="width:100%">
                  <thead>
				  <tr>
					<td>IP</td>
					<td>Infos</td>
					<td>Date</td>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach($ips as $ip)
				{
					$date = DateTime::createFromFormat('!Y-m-d H:i:s',$ip['date'] )->getTimestamp();
					$date = date ('d/m/Y H:i',$date);
				?>
					<tr>
						<td><a href="https://whatismyipaddress.com/ip/<?php echo $ip['ip']; ?>" target="_blank"><?php echo $ip['ip']; ?></a></td>
						<td><?php echo $ip['infos']; ?></td>
						<td><?php echo $date; ?></td>
					</tr>
				<?php
				}
				?>       
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
			  <br/>
              <!-- /.mail-box-messages -->
			  <div class="table-responsive mailbox-messages">
                <table id="dataTable" class="table table-hover table-striped display" style="width:100%">
					<tbody>
					<tr>
						<td>Date inscription</td>
						<td><?php 
						$dt = strtotime($user['dateinscription']); //make timestamp with datetime string
						setlocale(LC_TIME, "fr_FR");
						echo utf8_encode(strftime("%d %B %Y &agrave; %H:%M",$dt)); 
						?></td>
					</tr>  
					<tr>
						<td>Date connection</td>
						<td><?php 
						$dt = strtotime($user['dateconnect']); //make timestamp with datetime string
						setlocale(LC_TIME, "fr_FR");
						echo utf8_encode(strftime("%d %B %Y &agrave; %H:%M",$dt)); 
						?></td>
					</tr>   
					<tr>
						<td>Date dernière action</td>
						<td><?php 
						$dt = strtotime($user['dateaction']); //make timestamp with datetime string
						setlocale(LC_TIME, "fr_FR");
						echo utf8_encode(strftime("%d %B %Y &agrave; %H:%M",$dt)); 
						?></td>
					</tr>   					
					</tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <a href="index.php?mod=admins" class="btn btn-secondary">Annuler</a>
        </div>
      </div>
	  </form>
    </section>
    <!-- /.content -->
  </div>