<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - Equip Images Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Equip Images</a>
      <span class="breadcrumb-item active">Current</span>
    </div>
  </div>
</div>
<!-- /page header -->
<!-- Content -->
<div class="container-xxl flex-grow-1">
    <div class="row">
        <!-- Content area -->
        <div class="content">
          <!-- Basic card -->
          <div class="card">
            <!-- this is the view table for each model -->
            <div class="card-body">
              <div class="card-inner mx-4 my-3">
                <?php if(!$modelStatus){ ?>
                    <div class="alert alert-primary text-center w-50">
                      <span>There seems to be no data available</span>
                    </div>
                <?php }else{ ?>
                    <div class="col-md-12 col-xl-12">
                      <!-- Basic Bootstrap Table -->
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                            <thead>
                              <tr>
                                <th>Equipment Name</th>
                                <th>Equipment Images</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <?php foreach($modelPayload as $image): ?>
                              <tr>
                                <td><?php echo $image['equip_name']; ?></td>
                                <?php
                                  $equipID = $image['equipments_id'];
                                ?>
                                <td>
                                  <span><a href="javascript:void();" data-bs-toggle='modal' data-bs-target='#modal-image-<?php echo $image['ID']; ?>' class="btn btn-primary">View Images</a>
                                  </span>
                                </td>

                                <!-- this is for modal images -->
                                <div id="modal-image-<?php echo $image['ID']; ?>" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-top" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title"></h5>
                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"
                                            ></button>
                                        </div>
                                        <div class="modal-body">
                                          <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                              <?php
                                                $images = getEquipImages($scopeObject,$equipID);
                                              ?>
                                              <?php if(!empty($images)){
                                                foreach($images as $img){
                                              ?>
                                              <div class="carousel-item active">
                                                <img class="d-block w-100" src="<?php echo $img['image_path']; ?>" alt="equip image" />
                                              </div>
                                            <?php } }else{ ?>
                                              <div class="alert alert-primary">
                                                No image(s) available
                                              </div>
                                            <?php } ?>
                                            </div>
                                            <a class="carousel-control-prev" href="#carouselExample" role="button" data-bs-slide="prev">
                                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                              <span class="visually-hidden">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#carouselExample" role="button" data-bs-slide="next">
                                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                              <span class="visually-hidden">Next</span>
                                            </a>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <!-- end modal form -->

                                <script type="text/javascript">
                                </script>
                              </tr>
                            <?php endforeach;  ?>
                            </tbody>
                          </table>
                        </div>
                      <!--/ Basic Bootstrap Table -->
                    </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <!-- /basic card -->
        </div>
        <!-- /content area -->
    </div>
</div>
<!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

