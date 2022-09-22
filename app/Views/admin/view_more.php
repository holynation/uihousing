<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - <?php echo ucwords($pageTitle); ?> Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item"><?php echo $pageTitle; ?></a>
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
                  <div class="row">
                    <div class="col-md-12 col-xl-12 col-12">
                      <!-- Basic Bootstrap Table -->
                      <div class="card">
                        <h5 class="card-header">Detail Info on <?php echo ucwords($pageTitle); ?></h5>
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                            <tbody class="table-border-bottom-0">
                              <?php foreach($modelPayload as $key => $val): ?>
                              <tr>
                                <?php if(startsWith($val, base_url()) || endsWith($key, 'path') !== false): ?>
                                  <td><strong>View Image:</strong></td>
                                  <td>
                                    <span><a href="javascript:void();" data-bs-toggle='modal' data-bs-target='#modal-image' class="btn btn-primary">Click here</a>
                                    </span>
                                  </td>
                                  <!-- this is for modal images -->
                                  <div id="modal-image" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
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
                                            <div class="card h-50 w-75 mx-auto">
                                              <?php 
                                                $imagePath = ($val) ? $val : base_url('assets/img/avatar1.jpg');
                                              ?>
                                              <img class="card-img-top" src="<?php echo $imagePath; ?>" alt="image" style="height:23rem;">
                                            </div>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                          </div>
                                      </div>
                                    </div>
                                  </div>
                                  <!-- end modal form -->
                                <?php elseif(strpos(strtolower($key), 'status') !== false && $modelName == 'equip_request'): ?>
                                  <td><strong><?php echo removeUnderscore($key); ?>:</strong></td>
                                  <td class="text-primary"><?php echo $val; ?></td>

                                  <?php else: ?>

                                  <td><strong><?php echo removeUnderscore($key); ?>:</strong></td>
                                  <td><?php echo $val; ?></td>
                                <?php endif; ?>
                              </tr>
                            <?php endforeach; ?>

                            <?php if(($modelName == 'equip_request' || $modelName == 'equipments') && $modelInfo): ?>
                            <tr>
                              <td><strong>Action:</strong></td>
                              <td>
                                <button class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#modal-image'>View Equipment Images</button>

                                <!-- this is for modal images -->
                                <div id="modal-image" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
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
                                            <?php
                                                $images = $equipImages;
                                            ?>
                                            <ol class="carousel-indicators">
                                              <?php if(!empty($images) && $images){
                                                foreach($images as $idx => $val){
                                              ?>
                                              <li data-bs-target="#carouselExample" data-bs-slide-to="<?php echo $idx; ?>" class="<?php echo ($idx == 0) ? 'active' : '' ?>"></li>
                                              <?php } } ?>
                                            </ol>

                                            <div class="carousel-inner">
                                              <?php if(!empty($images)){
                                                foreach($images as $key => $img){
                                              ?>
                                              <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?>">
                                                <img class="d-block w-100" src="<?php echo $img['equip_images_path']; ?>" alt="equip image" />
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
                                <!-- end  images modal form -->
                                <?php endif; ?>

                            <?php if($modelName == 'equip_request' && $modelInfo): ?>
                                <button class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#modal-hirer'>View Hirer Profile</button>
                                <button class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#modal-owner'>View Owner Profile</button>
                              </td>

                              <!-- this is for modal hirers -->
                              <div id="modal-hirer" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-top" role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Hirers Profile</h5>
                                          <button
                                              type="button"
                                              class="btn-close"
                                              data-bs-dismiss="modal"
                                              aria-label="Close"
                                          ></button>
                                      </div>
                                      <div class="modal-body">
                                        <div class="card h-50 w-75 mx-auto">
                                          <div class="mb-4">
                                            <?php 
                                              $imagePath = $equipHirers->hirers_path ?? base_url('assets/img/avatar1.jpg');
                                            ?>
                                            <img class="card-img-top w-100 rounded-circle" src="<?php echo $imagePath; ?>" alt="image" style="height:19rem;">
                                          </div>
                                          <h5 class="card-title mb-3">Fullname: <span class='text-muted'><?php echo $equipHirers->fullname; ?></span></h5>
                                          <h5 class="card-title mb-3">Email: <span class='text-muted'><?php echo $equipHirers->email; ?></span></h5>
                                          <h5 class="card-title mb-3">Phone Number: <span class='text-muted'><?php echo $equipHirers->phone_number; ?></span></h5>
                                          <h5 class="card-title mb-3">Address: <span class='text-muted'><?php echo $equipHirers->address; ?></span></h5>
                                          <h5 class="card-title mb-3">Hirer Status: <span class="badge <?php echo $equipHirers->status ? 'bg-success' : 'bg-danger'; ?>"><?php echo $equipHirers->status ? 'Verified' : 'Not Verified'; ?></span></h5>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                      </div>
                                  </div>
                                </div>
                              </div>
                              <!-- end hirer modal form -->

                              <!-- this is for modal owners -->
                              <div id="modal-owner" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-top" role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Owners Profile</h5>
                                          <button
                                              type="button"
                                              class="btn-close"
                                              data-bs-dismiss="modal"
                                              aria-label="Close"
                                          ></button>
                                      </div>
                                      <div class="modal-body">
                                        <div class="card h-50 w-75 mx-auto">
                                          <div class="mb-4">
                                            <?php 
                                              $imagePath = $equipOwners->hirers_path ?? base_url('assets/img/avatar1.jpg');
                                            ?>
                                            <img class="card-img-top w-100 rounded-circle" src="<?php echo $imagePath; ?>" alt="image" style="height:19rem;">
                                          </div>
                                          <h5 class="card-title">Fullname: <span class='text-muted'><?php echo $equipOwners->fullname; ?></span></h5>
                                          <h5 class="card-title">Email: <span class='text-muted'><?php echo $equipOwners->email; ?></span></h5>
                                          <h5 class="card-title">Phone Number: <span class='text-muted'><?php echo $equipOwners->phone_number; ?></span></h5>
                                          <h5 class="card-title">Address: <span class='text-muted'><?php echo $equipOwners->address; ?></span></h5>
                                          <h5 class="card-title">Owner Status: <span class="badge <?php echo $equipHirers->status ? 'bg-success' : 'bg-danger'; ?>"><?php echo $equipOwners->status ? 'Verified' : 'Not Verified'; ?></span></h5>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                      </div>
                                  </div>
                                </div>
                              </div>
                              <!-- end owner modal form -->
                            </tr>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="float-right">
                        <a href="javascript:history.back();" class="btn btn-primary">Go Back</a>
                      </div>
                      <!--/ Basic Bootstrap Table -->
                    </div>
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

