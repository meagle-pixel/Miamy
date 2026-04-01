<?php

if(isset($_REQUEST['id']))
	$commande = getCommande($_REQUEST['id']);
else
	exit();

$client = getClient($commande['id_client']);

$gradations = getGradationsByIdCommande($commande['id']);

?>
<!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Commande <?php echo $commande['ref_commande']; ?></h1>
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
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="callout callout-info no-print">
						<h5><i class="fas fa-info"></i> Note:</h5>
						Cette page est conçu pour être imprimée
					</div>

					<div class="tracking p-3 mb-3 no-print">
						<div class="row">
							<div class="col-12">
								<?php
								
								$status = getStatus($commande['status']);
								$trackingClient = getTrackingClient($commande['id']); 
								$trackingEGA = getTracking($commande['id']);
								
								?>
								<h2>Suivi de la commande :</h2>
								Etat de la commande : <?php echo $status['libelle']; ?><br/>
								Numéro de suivi client : <?php if(isset($trackingClient)){ ?><a href="https://m.17track.net/fr/track-details?nums=<?php echo $trackingClient; ?>" target="_blank"><?php echo $trackingClient; ?></a><?php } ?><br/>
								Numéri de suivi EGA : <?php if(isset($trackingEGA)){ ?><a href="https://m.17track.net/fr/track-details?nums=<?php echo $trackingEGA; ?>" target="_blank"><?php echo $trackingEGA; ?></a><?php } ?><br/>
							</div>
						</div>
					</div>

					<div class="tracking p-3 mb-3 no-print">
						<div class="row">
							<div class="col-12">
								<?php
								$etiquettesok = true;
								
								$gradations = getGradationsByIdCommande($commande['id']);
								
								$ids = '';
								
								foreach($gradations as $gradation)
								{
									$ids .= $gradation['id'].',';
									
									if($gradation['actif'] == 0)
										$etiquettesok = false;
								}
								
								if($etiquettesok && $ids != ''){
								?>
								<a href="actions/etiquette.php?ids=<?php echo $ids; ?>" target="_blank" class="btn btn-default">Imprimer étiquettes</a>
								<?php
								}
								else
								{
								?>
									<div class="alert alert-danger">Terminez vos gradations afin d'imprimer toutes les étiquettes</div>
								<?php
								}
								?>
							</div>
						</div>
					</div>

					<div class="invoice p-3 mb-3">
						<div class="row">
							<div class="col-12">
								<h4><img src="https://egagrade.com/administration/dist/img/EGALogoalt.png"><small class="float-right">Date: <?php echo date("d-m-Y", strtotime($commande['date_commande'])); ?></small></h4>
							</div>
						</div>

						<div class="row invoice-info">
							<div class="col-sm-4 invoice-col">
								De
								<address>
									<strong>EGAGrade - Joseph TRUONG</strong><br>
									<?php echo getConfigurationByName('ega_adresse'); ?><br>
									<?php echo getConfigurationByName('ega_cp_ville'); ?><br>
									<br>
									Email: <?php echo getConfigurationByName('ega_mail'); ?>
								</address>
							</div>

							<div class="col-sm-4 invoice-col">
								A
								<address>
									<strong><?php echo $client['nom']; ?> <?php echo $client['prenom']; ?></strong><br>
									<?php echo $client['adresse']; ?><br>
									<?php if($client['adresse_comp'] != ""){ ?><?php echo $client['adresse_comp']; ?><br><?php } ?>
									<?php echo $client['codepostal']; ?> <?php echo $client['ville']; ?><br>
									<br>
									Téléphone: <?php echo $client['telephone']; ?><br>
									Email: <?php echo $client['email']; ?>
								</address>
							</div>

							<div class="col-sm-4 invoice-col">
								<b>Facture <?php echo $commande['ref_commande']; ?></b><br>
								<br>
								<b>Commande : </b> <?php echo $commande['ref_commande']; ?><br>
								<b>Payé le : </b> <?php echo date("d-m-Y H:i:s", strtotime($commande['date_commande'])); ?>
							</div>
						</div>

						<div class="row">
							<div class="col-12 table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Carte</th>
											<th>Valeur déclarée</th>
											<th>Sous-total</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										
										$panier = json_decode($commande['json_panier']);
		
										$totalttc = 0;
										$percent = 0;
										$totaldeclare = 0;
										
										if(isset($commande['id_promo']) && $commande['id_promo'] != 0)
										{
											$promo = getPromoById($commande['id_promo']);
											$percent = $promo['percent'];
										}
										
										foreach($gradations as $gradation) 
										{ 
											$pu = getConfigurationByPrice($gradation['valeurdeclaree']);
											
											if($pu['price'] == '')
													$pu['price'] = 0;
											
											$totaldeclare = $totaldeclare + $gradation['valeurdeclaree'];
											$totalttc = $totalttc + (int)$pu['price'];
										?>
											<tr>
												<td><?php echo $gradation['nom_carte']; ?> - <?php echo $gradation['num_carte']; ?> - <?php echo $gradation['nom_set']; ?> - CODE : <?php echo $gradation['ref_gradation']; ?></td>
												<td><?php echo $gradation['valeurdeclaree']; ?>€</td>
												<td>
													<?php echo $pu['price']; ?>€ TTC
												</td>
											</tr>
										<?php  
										} 
										
										$totalttcar = ($totalttc * (100 - $percent))/100;
										
										$remise = $totalttcar - $totalttc;
										
										$fdp = getTarifTransport(1,$totaldeclare);
										
										if($fdp == '')
											$fdp = 0;
										
										?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-6">
								<p class="lead">Payée par : Carte bancaire</p>
								<p class="lead">Transporteur : Colissimo</p>
								<p class="text-muted well well-sm shadow-none" style="margin-top: 10px;"></p>
							</div>

							<div class="col-6">
								<p class="lead">Payée le :</p>
							<div class="table-responsive">
								<table class="table">
									<tbody>
										<tr>
											<th style="width:50%">Sous-total:</th>
											<td><?php echo $totalttc; ?>€ TTC</td>
										</tr>
										<tr>
											<th>Remise (<?php echo $percent; ?>%)</th>
											<td><?php echo $remise; ?>€ TTC</td>
										</tr>
										<tr>
											<th>Frais de port:</th>
											<td><?php echo $fdp; ?>€ TTC</td>
										</tr>
										<tr>
											<th>Total:</th>
											<td><?php echo $totalttcar + $fdp; ?>€ TTC</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
							<div class="col-12">
								<div class="text-center"><?php echo getConfigurationByName('facture_baseline'); ?></div>
							</div>
						</div>
				</div>

				<div class="row no-print">
					<div class="col-12">
						<a href="#" onclick="document.title = '<?php echo $commande['ref_commande']; ?>';window.print();" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Imprimer</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

</div>
<!-- /.wrapper -->