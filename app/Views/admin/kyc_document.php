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
                        <table class="table" id="datatable-buttons">
                          <thead>
                            <tr>
                              <th>Hirers Name</th>
                              <th>Document Name</th>
                              <th>Document Image</th>
                              <th>KYC Status</th>
                              <th>Date Modified</th>
                              <th>Date Created</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody class="table-border-bottom-0">
                            <?php foreach($modelPayload as $kyc): ?>
                            <tr>
                              <?php
                                $kycID = $kyc['ID'];
                              ?>
                              <td><?php echo $kyc['hirers_name']; ?></td>
                              <td><?php echo $kyc['document_name']; ?></td>
                              <td>
                                <span><a href="javascript:void();" data-bs-toggle='modal' data-bs-target='#modal-image-<?php echo $kycID; ?>' class="btn btn-primary">View Document</a>
                                </span>
                              </td>
                              <td><?php echo $kyc['status'] ? 'Approved' : 'Rejected'; ?></td>
                              <td><?php echo $kyc['date_modified']; ?></td>
                              <td><?php echo $kyc['date_created']; ?></td>
                              <td>
                                <?php
                                  $editLink = base_url("edit/kyc_document/$kycID");
                                  $statusLink = $kyc['status'] ? base_url("ac/disable/kyc_document/$kycID"): base_url("ac/enable/kyc_document/$kycID");
                                ?>
                                <div class="btn-group dropup">
                                  <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                  </button>
                                  <div class="dropdown-menu dropdown-menu-end text-center">
                                    <span data-item-id="<?php echo $kycID; ?>" data-default='1' data-critical='0' data-ajax-edit='1'><a href="<?php echo $editLink; ?>" class="dropdown-item">Edit</a></span>
                                    <span data-item-id="<?php echo $kycID; ?>" data-default='1' data-critical='1'><a href="<?php echo $statusLink; ?>" class="dropdown-item"><?php echo $kyc['status'] ? 'Disapprove' : 'Approve';  ?></a></span>
                                  </div>
                                </div>
                              </td>
                              <!-- this is for modal images -->
                              <div id="modal-image-<?php echo $kycID; ?>" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
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
                                        <div class="card h-50">
                                          <?php if($kyc['image_path']){ ?>
                                          <img class="card-img-top" src="<?php echo $kyc['image_path']; ?>" alt="<?php echo $kyc['document_name']; ?> image">
                                          <div class="card-body">
                                            <h5 class="card-title">Document Name: <span class='text-muted'><?php echo $kyc['document_name']; ?></span></h5>
                                            <p class="card-text">
                                              <small class="text-muted">Last Updated <?php echo $timeObject->humanize($kyc['date_modified']); ?></small>
                                            </p>
                                          </div>
                                          <?php } else{ ?>
                                            <div class="alert alert-primary">
                                              No image(s) available
                                            </div>
                                          <?php } ?>
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

