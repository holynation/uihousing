<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - Equipments Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Equipments</a>
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
                        <table class="table" id="datatable-buttons">
                          <thead>
                            <tr>
                              <th>Owners Name</th>
                              <th>Equipment Name</th>
                              <th>Cost Of Hire</th>
                              <th>Quantity</th>
                              <th>Equip Images</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody class="table-border-bottom-0">
                            <?php foreach($modelPayload as $equip): ?>
                            <tr>
                              <?php
                                $equipID = $equip['ID'];
                              ?>
                              <td><?php echo $equip['owners_name']; ?></td>
                              <td><?php echo $equip['equip_name']; ?></td>
                              <td><?php echo $equip['cost_of_hire']; ?></td>
                              <td><?php echo $equip['quantity']; ?></td>
                              <td>
                                <span><a href="javascript:void();" data-bs-toggle='modal' data-bs-target='#modal-image-<?php echo $equip['ID']; ?>' class="btn btn-primary">View Images</a>
                                </span>
                              </td>
                              <td><?php echo $equip['status'] ? 'Active' : 'Inactive'; ?></td>
                              <td>
                                <?php
                                  $editLink = base_url("edit/equipments/$equipID");
                                  $statusLink = $equip['status'] ? base_url("ac/disable/equipments/$equipID"): base_url("ac/enable/equipments/$equipID");
                                  $viewMoreLink = base_url("vc/admin/view_more/equipments/all/{$equipID}");
                                ?>
                                <div class="btn-group">
                                  <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-end text-center" style="">
                                    <span data-item-id="<?php echo $equipID; ?>" data-default='1' data-critical='1'><a href="<?php echo $statusLink; ?>" class="dropdown-item"><?php echo $equip['status'] ? 'Disapprove' : 'Approve';  ?></a></span>
                                    <span data-item-id="<?php echo $equipID; ?>" data-default='1' data-critical='0' data-ajax-edit='1'><a href="<?php echo $editLink; ?>" class="dropdown-item">Edit</a></span>
                                    <span data-item-id="<?php echo $equipID; ?>" data-default='1' data-critical='0'><a href="<?php echo $viewMoreLink; ?>" class="dropdown-item">View More</a></span>
                                </div>
                              </td>
                              
                              <!-- this is for modal images -->
                              <div id="modal-image-<?php echo $equip['ID']; ?>" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
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
                                              $images = getEquipImages($scopeObject,$equipID);
                                              // print_r($images);exit;
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
                                            <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?> ">
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

<!-- this is for edit modal form -->
<div id="modal-edit" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
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
            <p id="edit-container"> </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<!-- end edit modal form -->

<?php include_once ROOTPATH."template/footer.php"; ?>

<script>
    var inserted=false;

    let formGrp = $('div[class=form-group]');
    let formLabel = $('label[for]');
    formGrp.addClass('mb-3');
    formLabel.addClass('form-label');

    $(document).ready(function() {
      $('.modal').on('hidden.bs.modal', function (e) {
        if (inserted) {
          inserted = false;
          location.reload();
        }
      });
      $('.close').click(function(event) {
        if (inserted) {
          inserted = false;
          location.reload();
        }
      });
      $('span[data-ajax-edit=1] a').click(function(event){
        event.preventDefault();
        var link = $(this).attr('href');
        var action = $(this).text();
        sendAjax(null,link,'','get',showUpdateForm);
      });
    });

    function showUpdateForm(target,data) {
      var data = JSON.parse(data);
      if (data.status==false) {
        showNotification(false,data.message);
        return;
      }

       var container = $('#edit-container');
       container.html(data.message);
       //rebind the autoload functions inside
       
      let formGrp = $('div[class=form-group]');
      let formLabel = $('label[for]');
      formGrp.addClass('mb-3');
      formLabel.addClass('form-label');

      $('#modal-edit').modal('show');
    }

    function ajaxFormSuccess(target,data) {
      if (data.status) {
        inserted=true;
        $('form').trigger('reset');
      }
      showNotification(data.status,data.message);
      var btnSubmit = $('input[type=submit]');
      btnSubmit.removeClass('disabled');
      btnSubmit.prop('disabled', false);
      btnSubmit.html('Submit');
      if (typeof target ==='undefined') {
        location.reload();
      }
    }
</script>

