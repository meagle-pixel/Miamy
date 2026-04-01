<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <?php

$admins = getAllClients();

?>
	<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Clients</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
              <li class="breadcrumb-item active">Clients</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
	
	<section class="content-header">
      <div class="row">
        <div class="col-12">
          <a href="index.php?mod=add-client" class="btn btn-success float-right">Ajouter un client</a>
        </div>
      </div>
    </section>
	
	<section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Clients</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped projects">
              <thead>
                  <tr>
                      <th style="width: 50%">
                          Nom / Prénom
                      </th>
                      <th style="width: 20%">
                          Numéro Portable
                      </th>
                      <th style="width: 29%" class="text-center">
                          Actions
                      </th>
                  </tr>
              </thead>
              <tbody>
				<?php
					foreach($admins as $admin)
					{
						$user = getUserID($admin['id'],1);
						
					?>
						 <tr>
						  <td>
							<a href="index.php?mod=security-users&id=<?php echo $admin['id']; ?>"><?php echo $admin['prenom'].' '.$admin['nom']; ?></a>
						  </td>
						  <td class="project_progress">
							  <?php echo $admin['telephone']; ?>
						  </td>
						  <td class="project-actions text-center">
							  <a class="btn btn-info btn-sm" href="index.php?mod=edit-client&id=<?php echo $admin['id']; ?>">
								  <i class="fas fa-pencil-alt"></i>
							  </a>
							  <a class="btn btn-primary btn-sm" href="index.php?mod=security-users&id=<?php echo $admin['id']; ?>">
								  <i class="fa-solid fa-eye"></i>
							  </a>
							  
							 <?php /* <a class="btn btn-danger btn-sm" href="index.php?mod=delete-client&id=<?php echo $admin['id']; ?>">
								  <i class="fas fa-trash">
								  </i>
								  Supprimer
							  </a> */ ?>
						  </td>
						</tr>
					<?php
					}
					?>       
			  
                 
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </section>

</div>