<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <?php

	$pagetoedits = getpages();
	$profils = getProfils();
	
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<table style="width:100%" class="table table-hover table-striped display">
				<thead>
					<tr>
						<th></th>
						<?php foreach($profils as $profil) { ?>
						<th><?php echo $profil['libelle'] ?></th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($pagetoedits as $pagetoedit) { ?>
					<tr>
						<th><?php echo $pagetoedit['mod'] ?></th>
						<?php foreach($profils as $profil) { ?>
						<td><input type="checkbox" id="<?php echo $pagetoedit['id'].'_'.$profil['id']; ?>" name="<?php echo $pagetoedit['id'].'_'.$profil['id']; ?>" <?php if(getAutorisation($pagetoedit['id'],$profil['id'])){ echo 'checked'; } ?> onclick="changePermission(<?php echo $pagetoedit['id'].','.$profil['id']; ?>);"></td>
						<?php } ?>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
        <!-- /. box -->
    </div>
</section>

</div>

<?php if($page == 'profils'){?>
  <script type="text/javascript">
	function changePermission(page,profil){
		 
		$.ajax({
		   url : 'actions/changepermission.php', // La ressource ciblée
		   type : 'POST', // Le type de la requête HTTP.
		   data : 'page=' + page + '&profil='+profil
		});
	   
	}
	</script>

<?php }  ?>