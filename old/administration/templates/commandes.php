<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Commandes</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accueil</a></li>
              <li class="breadcrumb-item active">Commandes</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
	<section class="content">
      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Commandes</h3>
        </div>
        <div class="card-body">
          <table id="table1" class="table table-bordered table-striped">
              <thead>
                  <tr>
                      <th class="text-center" style="display:none;">
                          ID
                      </th>
					  <th class="text-center">
                          Référence
                      </th>
                      <th class="text-center">
                          Client
                      </th>
                      <th class="text-center">
                          Status
                      </th>
					  <th class="text-center">
                          Montant
                      </th>
					  <th class="text-center">
                          Date
                      </th>
                  </tr>
              </thead>
              <tbody>
				<?php
					$commandes = getCommandes();
				
					foreach($commandes as $commande)
					{
						$client = getClient($commande['id_client']);
						
						$statusTab = getAllStatus();
						
						$colorstatus = "bg-danger ";
						
						if($commande['status'] == 2)
							$colorstatus = "bg-warning";
						if($commande['status'] == 3)
							$colorstatus = "bg-warning";
						if($commande['status'] == 4)
							$colorstatus = "bg-primary";
						if($commande['status'] == 5)
							$colorstatus = "bg-primary";
						if($commande['status'] == 6)
							$colorstatus = "bg-success";
					?>
					<tr class="<?php echo $colorstatus ?>">
                      <td class="text-center" style="display:none;">
                          <?php echo $commande['id']; ?>
                      </td>
					  <td class="text-center">
                          <a href="index.php?mod=voir-commande&id=<?php echo $commande['id']; ?>" title="Voir Commande" class="text-white"><?php echo $commande['ref_commande']; ?></a>
                      </td>
                      <td class="text-center">
                          <?php echo $client['nom']; ?> <?php echo $client['prenom']; ?>
                      </td>
                      <td class="text-center">
                          <select class="changeStatus" data-order-id="<?php echo $commande['id']; ?>">
							<?php 
							foreach($statusTab as $status) {
								$selected = '';
								
								if($status['id'] == $commande['status'])
									$selected = 'selected';
							?>
							<option value="<?php echo $status['id']; ?>" <?php echo $selected; ?>><?php echo $status['libelle']; ?></option>
							<?php 
							}
							?>
						  </select>
                      </td>
					  <td class="text-center">
                          <?php echo $commande['totalttc']; ?>€ TTC
                      </td>
					  <td class="text-center">
                          <?php echo date("d-m-Y H:i:s", strtotime($commande['date_commande'])); ?>
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
<!-- /.wrapper -->