<?php include_once ROOTPATH."template/header.php"; ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
          <div class="d-flex">
            <h4><span>Staff Apply Page</h4>
          </div>

          <div class="d-flex">
            <div class="breadcrumb">
              <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
              <a href="#" class="breadcrumb-item">Apply</a>
              <span class="breadcrumb-item active">Current</span>
            </div>
          </div>
        <div class="row">
            <div class="card d-flex">
                <div class="my-2 d-grid justify-content-end">
                    <a href="#" class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#myModal'><em class="menu-icon tf-icons bx bx-plus-medical"></em><span>Apply Form</span>
                    </a>
                </div>
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
              <h5 class="card-header">Allocation Application History</h5>
              <div class="table-responsive text-nowrap">
                <table class="table">
                  <thead class="table-light">
                    <tr>
                      <th>Application Code</th>
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
                      <td><i class="fab fa-angular fa-lg text-info me-3"></i> <strong><?= $data['applicant_code']; ?></strong></td>
                      <td><?= $data['category_id']; ?></td>
                      <td><?= ucfirst($data['gender']); ?></td>
                      <td><?= $data['dob']; ?></td>
                      <td><?= $data['address']; ?></td>
                      <td>
                        <?php
                            $badgeClass = 'warning';
                            if($data['applicant_status'] == 'rejected'){
                                $badgeClass = 'danger';
                            }else if($data['applicant_status'] == 'approved'){
                                $badgeClass = 'primary';
                            }
                        ?>
                        <span class="badge bg-label-<?= $badgeClass; ?> me-1"><?= strtoupper($data['applicant_status']); ?></span>
                      </td>
                      <td><?= dateFormatter($data['date_created']); ?></td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= base_url("vc/staff/print_application/{$data['ID']}"); ?>"
                              ><i class="bx bx-printer me-1"></i>Print Application</a
                            >
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <?php endif; ?>

            <!-- this is the modal form -->
            <div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-dialog-top" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                            <h5 class="modal-title">Allocation Application Form</h5>
                            <button
                              type="button"
                              class="btn-close"
                              data-bs-dismiss="modal"
                              aria-label="Close"
                            ></button>
                      </div>
                      <div class="modal-body">
                        <?php
                            $hidden = ['staff_id'=>$staff->ID];
                            $showStatus = false;
                            $exclude = [];
                            $submitLabel = 'Submit Application';

                            $formContent = $modelFormBuilder->start('apply_table')
                            ->appendInsertForm('applicant_allocation',true,$hidden,'',$showStatus,$exclude)
                            ->addSubmitLink(null)
                            ->appendResetButton('Reset','btn btn-md btn-danger mt-3')
                            ->appendSubmitButton($submitLabel,'btn btn-md btn-primary mt-3')
                            ->build();

                            echo $formContent;
                        ?>
                      </div>
                  </div>
              </div>
            </div>
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

