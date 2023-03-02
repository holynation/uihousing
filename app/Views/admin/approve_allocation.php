<?php include_once ROOTPATH."template/header.php"; ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex">
          <h4><span>Admin Allocation Page</h4>
        </div>

        <div class="d-flex">
          <div class="breadcrumb">
            <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
            <a href="#" class="breadcrumb-item">Allocation</a>
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

            <div class="card">
                <h5 class="card-header">Allocate Applicant</h5>
                <div class="row m-4">
                  <div class="col-6">
                    <?php 
                    $model = "allocation";
                    $hidden = ['applicant_allocation_id' => $id];
                    $showStatus = true;
                    $exclude = [];
                    $submitLabel = 'Submit Approval';

                    if($applicantAllocation->allocation){
                        $id = $applicantAllocation->allocation->ID;
                        $formContent = $modelFormBuilder->start($model.'_edit')
                            ->appendUpdateForm($model,true,$id,$exclude,'')
                            ->addSubmitLink(null,false)
                            ->appendSubmitButton('Update Approval','btn btn-success mt-3')
                            ->build();
                    }
                    else{
                      $formContent = $modelFormBuilder->start($model.'_table')
                          ->appendInsertForm($model,true,$hidden,'',$showStatus,$exclude)
                          ->addSubmitLink()
                          ->appendResetButton('Reset','btn btn-md btn-danger mt-3')
                          ->appendSubmitButton($submitLabel,'btn btn-md btn-primary mt-3')
                          ->build();
                    }
                    echo $formContent;
                    ?>
                  </div>
                  <div class="col-6">
                    <div class="card h-50 w-75 mx-auto">
                      <div class="mb-4">
                        <?php 
                          $imagePath = $applicantAllocation->staff->staff_path ?? base_url('assets/img/avatar2.jpg');
                        ?>
                        <img class="card-img-top rounded-circle" src="<?php echo $imagePath; ?>" alt="image" width='100' height='140'>
                      </div>
                      <h5 class="card-title mb-3 border-bottom py-2">Occupant Num: <span class='text-muted'><?php echo $applicantAllocation->staff->occupant_num; ?></span></h5>
                      <h5 class="card-title mb-3 border-bottom py-2">Fullname: <span class='text-muted'><?php echo $applicantAllocation->staff->surname .' '.$applicantAllocation->staff->firstname; ?></span></h5>
                      <h5 class="card-title mb-3 border-bottom py-2">Email: <span class='text-muted'><?php echo $applicantAllocation->staff->email; ?></span></h5>
                      <h5 class="card-title mb-3 border-bottom py-2">Phone Number: <span class='text-muted'><?php echo $applicantAllocation->staff->phone_number; ?></span></h5>
                      <h5 class="card-title mb-3 border-bottom py-2">Designation: <span class='text-muted'><?php echo $applicantAllocation->staff->designation->designation_name; ?></span></h5>
                      <h5 class="card-title mb-3">Number of Children: <span class='text-muted'><?php echo $applicantAllocation->staff->num_children; ?></span></h5>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

<script>

    let formGrp = $('div[class=form-group]');
    let formLabel = $('label[for]');
    formGrp.addClass('mb-3');
    formLabel.addClass('form-label');

    function ajaxFormSuccess(target,data) {
      showNotification(data.status,data.message);
      if (data.status) {
        $('form').trigger('reset');
        location.reload();
      }
      var btnSubmit = $('input[type=submit]');
      btnSubmit.removeClass('disabled');
      btnSubmit.prop('disabled', false);
      btnSubmit.html('Submit');
    }
</script>