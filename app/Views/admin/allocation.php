<?php include_once ROOTPATH."template/header.php"; ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
          <div class="d-flex">
            <h4><span>Allocation Approved Page</h4>
          </div>

          <div class="d-flex">
            <div class="breadcrumb">
              <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
              <a href="#" class="breadcrumb-item">Allocation Approved</a>
              <span class="breadcrumb-item active">Current</span>
            </div>
          </div>
        <div class="row">
            <?php if($webSessionManager->getFlashMessage('error')): ?>
                <div class="col-6 mx-auto">
                    <div class="alert alert-info text-center">
                        <?= $webSessionManager->getFlashMessage('error'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(!$modelStatus): ?>
                <div class="col-6 mx-auto">
                    <div class="alert alert-info text-center">
                        Oops, no application history available
                    </div>
                </div>
            <?php else: ?>
            <div class="card">
              <h5 class="card-header">Applicant Application</h5>
              <div class="table-responsive text-nowrap">
                <table class="table" id="datatable-buttons">
                  <thead class="table-light">
                    <tr>
                      <th>Application Code</th>
                      <th>Staff Name</th>
                      <th>Category</th>
                      <th>Date of Birth</th>
                      <th>Gender</th>
                      <th>Address</th>
                      <th>Application Status</th>
                      <th>Application Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <?php foreach($modelPayload as $data): ?>
                    <tr>
                      <td><i class="fab fa-angular fa-lg text-info me-3"></i> <strong><?= $data->applicant_allocation->applicant_code; ?></strong></td>
                      <td><?= $data->applicant_allocation->staff->surname.' '.$data->applicant_allocation->staff->firstname.' '.$data->applicant_allocation->staff->othername; ?></td>
                      <td><?= $data->applicant_allocation->category->category_name; ?></td>
                      <td><?= ucfirst($data->applicant_allocation->gender); ?></td>
                      <td><?= $data->applicant_allocation->dob; ?></td>
                      <td><?= $data->applicant_allocation->address; ?></td>
                      <td>
                        <?php
                            $badgeClass = 'warning';
                            if($data->status == 'approved'){
                                $badgeClass = 'success';
                            }
                        ?>
                        <span class="badge bg-label-<?= $badgeClass; ?> me-1"><?= strtoupper($data->status); ?></span>
                      </td>
                      <td><?= dateFormatter($data->applicant_allocation->date_created); ?></td>
                      <td>
                        <?php
                          $statusLink = base_url("changestatus/allocation/off/{$data->ID}");
                        ?>
                        <div class="dropdown">
                          <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle='modal' data-bs-target='#myModal-<?= $data->ID; ?>'
                                ><i class="bx bx-user me-1"></i> View Profile</a
                            >
                            <?php if($data->status == 'approved'): ?>
                            <span data-item-id="<?php echo $data->ID; ?>" data-default='1' data-critical='1'>
                            <a class="dropdown-item" href="<?= $statusLink; ?>"
                              ><i class="bx bx-list-check me-1"></i>Allocation Off</a
                            ></span>
                            <?php endif; ?>
                            <span><a class="dropdown-item" href="<?= base_url("vc/staff/print_application/{$data->applicant_allocation_id}"); ?>"
                              ><i class="bx bx-printer me-1"></i>Print Application</a
                            ></span>
                          </div>
                        </div>
                        <!-- this is the Staff profile modal form -->
                        <div class="modal fade" tabindex="-1" id="myModal-<?= $data->ID; ?>" role="dialog" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-top" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                    <h5 class="modal-title">Staff Profile</h5>
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
                                      $imagePath = $data->applicant_allocation->staff->staff_path ?? base_url('assets/img/avatar2.jpg');
                                    ?>
                                    <img class="d-block rounded mx-auto" src="<?php echo $imagePath; ?>" alt="image" width="100" height="100">
                                  </div>
                                  <h5 class="card-title mb-3 border-bottom py-2">Staff Number: <span class='text-muted'><?php echo $data->applicant_allocation->staff->occupant_num; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Title: <span class='text-muted'><?php echo @$data->applicant_allocation->staff->title->name; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Fullname: <span class='text-muted'><?php echo $data->applicant_allocation->staff->surname.' '.$data->applicant_allocation->staff->firstname.' '.$data->applicant_allocation->staff->othername; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Email: <span class='text-muted'><?php echo $data->applicant_allocation->staff->email; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Phone Number: <span class='text-muted'><?php echo $data->applicant_allocation->staff->phone_number; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Designation: <span class='text-muted'><?php echo @$data->applicant_allocation->staff->designation->designation_name; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Gender: <span class='text-muted'><?php echo ucfirst($data->applicant_allocation->staff->gender); ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Marital Status: <span class='text-muted'><?php echo ucfirst($data->applicant_allocation->staff->marital_status); ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Address: <span class='text-muted'><?php echo $data->applicant_allocation->staff->office_address; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Number of Children: <span class='text-muted'><?php echo $data->applicant_allocation->staff->num_children; ?></span></h5>
                                  <h5 class="card-title mb-3 border-bottom py-2">Staff Status: <span class="badge <?php echo $data->applicant_allocation->staff->status ? 'bg-success' : 'bg-danger'; ?>"><?php echo $data->applicant_allocation->staff->status ? 'Active' : 'Inactive'; ?></span></h5>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- the end modal form -->
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>
<script>
    var inserted = false;

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

      // adding js pagination
      jsDataTablePaginate();
    });

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

