<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Configuration</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Accueil</a></li>
              <li class="breadcrumb-item active">Configuration</li>
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
					<?php
						$fullconfiguration = getFullConfiguration();
						
						
						foreach($fullconfiguration as $configuration)
						{
					?>
							<div class="form-group">
								<label for="input<?php echo $configuration['name'] ?>"><?php echo $configuration['proper_name'] ?></label>
								<input type="text" id="<?php echo $configuration['name'] ?>" name="<?php echo $configuration['name'] ?>" class="form-control configurationChange" data-id="<?php echo $configuration['id'] ?>" value="<?php echo $configuration['value']; ?>" required>
							</div>
					<?php
						}
					?>

				</div>
				<!-- /.row -->
			</div>
			<!-- /.container-fluid -->
		</section>
		<!-- /.content -->
	</div>