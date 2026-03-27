<?php

	$id = $_GET['id'];

	$ips = userIPS($id,2);
	$user = getClient($id);
	
?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Info de sécurité</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accueil</a></li>
              <li class="breadcrumb-item active">Info de sécurité</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

<section class="content">
		<div class="col-md-12 mb-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Commandes</h3>

              <div class="box-tools pull-right">
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive mailbox-messages">
                <table id="dataTable" class="table table-hover table-striped display" style="width:100%">
				<tbody>
				<?php
				$commandes = getCommandesClient($_GET['id']);
				
				foreach($commandes as $commande)
				{
					$status = getStatus($commande['status']);

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
						<td><a href="index.php?mod=voir-commande&id=<?php echo $commande['id']; ?>" target="_blank" class="text-white"><?php echo $commande['ref_commande']; ?></a></td>
						<td><?php echo $status['libelle']; ?></td>
						<td><?php echo date("d-m-Y H:i:s", strtotime($commande['date_commande'])); ?></td>
					</tr>
				<?php
				}
				?>       
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
		<div class="col-md-12 mb-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Gradations</h3>

              <div class="box-tools pull-right">
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive mailbox-messages">
                <table id="dataTable" class="table table-hover table-striped display" style="width:100%">
				<tbody>
				<?php
				$gradations = getGradationsClient($_GET['id']);
				
				foreach($gradations as $gradation)
				{
					$colorstatus = "bg-danger ";
						
					if($gradation['actif'] == 1)
						$colorstatus = "bg-success";
				?>
					<tr class="<?php echo $colorstatus ?>">
                      <td class="text-left">
                          <a href="index.php?mod=voir-gradation&id=<?php echo $gradation['id']; ?>" title="Voir Gradation" class="text-white"><?php echo $gradation['nom_carte']; ?> - <?php echo $gradation['num_carte']; ?> - <?php echo $gradation['nom_set']; ?></a>
                      </td>
					  <td class="text-center">
                          <a href="index.php?mod=voir-gradation&id=<?php echo $gradation['id']; ?>" title="Voir Gradation" class="text-white"><?php echo $gradation['ref_gradation']; ?></a>
                      </td>
                      <td class="text-center">
                          <?php echo $gradation['valeurdeclaree']; ?>€
                      </td>
					  <td class="text-center">
                          <?php if($gradation['actif'] == 1){ ?><a href="actions/etiquette.php?id=<?php echo $gradation['id']; ?>" target="_blank" class="text-white"><i class="fa-solid fa-print"></i></a><?php } ?> 
                      </td>
					</tr>
				<?php
				}
				?>       
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
		<div class="col-md-12 mb-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">IP et infos de l'utilisateur</h3>

              <div class="box-tools pull-right">
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
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
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
		<div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations</h3>

              <div class="box-tools pull-right">
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
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
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
</section>
</div>