<?php include_once ROOTPATH."template/header.php"; ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                  <h5 class="card-header">Profile Details</h5>
                  <!-- Account -->
                  <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                      <?php if (@$staff->staff_path): ?>
                        <img class="d-block rounded" src="<?php echo base_url($staff->img_path) ?>" alt="staff profile picture" width="100" height="100">
                        <?php else: ?>
                          <img class="d-block rounded" src="<?php echo base_url('assets/img/avatar2.jpg'); ?>" alt="staff profile picture" width="100" height="100">
                        <br /> 
                      <?php endif ?>
                      <div class="showupload btn btn-primary btn-block">Change Photo</div>
                      <div class="button-wrapper upload-control" style="display: none;">
                        <form id="data_profile_change" method="post" enctype="multipart/form-data" action="<?php echo base_url('mc/update/staff/'.$staff->ID.'/1') ?> ">
                        <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                          <span class="d-none d-sm-block">Choose new photo</span>
                          <i class="bx bx-upload d-block d-sm-none"></i>
                          <input
                            type="file"
                            id="upload"
                            name="staff_path"
                            class="account-file-input"
                            hidden
                            accept="image/png, image/jpeg, image/jpg"
                          />
                        </label>
                        <button type="submit" class="btn btn-outline-primary account-image-reset mb-4" name="submit-btn">
                          <i class="bx bx-reset d-block d-sm-none"></i>
                          <span class="d-none d-sm-block">Upload Photo</span>
                        </button>

                        <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </form>
                      </div>
                    </div>
                  </div>
                  <hr class="my-0" />
                  <div class="card-body">
                    <form id="formAccountSettings" method="POST" onsubmit="return false">
                      <div class="row">
                        <div class="mb-3 col-md-6">
                          <label class="form-label" for="country">Title</label>
                          <select id="title" name="title" class="select2 form-select">
                            <option value="">Select Title</option>
                            <?php
                              $query = "select id,name as value from title";
                              $option = buildOptionFromQuery($db,$query,[],$staff->title_id);
                              echo $option
                            ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="firstName" class="form-label">First Name</label>
                          <input
                            class="form-control"
                            type="text"
                            id="firstname"
                            name="firstname"
                            value="<?= $staff->firstname; ?>"
                            autofocus
                          />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="lastName" class="form-label">Last Name</label>
                          <input class="form-control" type="text" name="surname" id="surname" value="<?= $staff->surname; ?>" />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="lastName" class="form-label">Other Name</label>
                          <input class="form-control" type="text" name="othername" id="othername" value="<?= $staff->othername; ?>" />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="email" class="form-label">E-mail</label>
                          <input
                            class="form-control"
                            type="text"
                            id="email"
                            name="email"
                            value="<?= $staff->email; ?>"
                            placeholder="john.doe@example.com"
                          />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label class="form-label" for="phone_number">Phone Number</label>
                          <div class="input-group input-group-merge">
                            <input
                              type="text"
                              id="phone_number"
                              name="phone_number"
                              class="form-control"
                              placeholder="202 555 0111"
                              value="<?= $staff->phone_number; ?>"
                            />
                          </div>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="gender" class="form-label">Gender</label>
                          <select name='gender' id='gender' class='form-control' required>
                          <?php
                            $arr =array('male'=>'Male','female'=>'Female');
                            $option = buildOptionUnassoc2($arr,$staff->gender);
                            echo $option;
                          ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="marital_status" class="form-label">Marital Status</label>
                          <select name='marital_status' id='marital_status' class='form-control' required>
                          <?php
                            $arr =array('married'=>'Married','single'=>'Single','others'=>'Others');
                            $option = buildOptionUnassoc2($arr,$staff->marital_status);
                            echo $option;
                          ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="office_address" class="form-label">Office Address</label>
                          <input type="text" class="form-control" id="office_address" name="office_address" placeholder="Office Address" value="<?= $staff->office_address; ?>" />
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="academic_status" class="form-label">Academic Status</label>
                          <select id="academic_status" name="academic_status" class="select2 form-select">
                            <option value="">Select Academic Status</option>
                            <?php
                              $arr =array('student'=>'Student','academic'=>'Academic','non_teaching'=>'Non Teaching','others'=>'Others');
                              $option = buildOptionUnassoc2($arr,$staff->academic_status);
                              echo $option;
                            ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="state_of_origin" class="form-label">State Of Origin</label>
                          <select id="state_of_origin" name="state_of_origin" class="select2 form-select autoload" data-child='lga_of_origin' data-load='lga'>
                            <option value="">Select State</option>
                            <?php
                              $states = loadStates();
                              $option = buildOptionUnassoc($states,$staff->state_of_origin);
                              echo $option;
                            ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="lga_of_origin" class="form-label">LGA</label>
                          <select id="lga_of_origin" name="lga_of_origin" class="select2 form-select">
                            <option value="">Select LGA</option>
                            <?php
                              $option='';
                              if ($staff->lga_of_origin) {
                                $arr=array($lga_of_origin);
                                $option = buildOptionUnassoc($arr,$lga_of_origin);
                                echo $option;
                              }
                            ?>
                          </select>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label class="form-label" for="country">Designation</label>
                          <select id="designation_id" name="designation_id" class="select2 form-select">
                            <option value="">Select Designation</option>
                            <?php
                              $query = "select id,designation_name as value from designation";
                              $option = buildOptionFromQuery($db,$query,[],$staff->designation_id);
                              echo $option
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                      </div>
                    </form>
                  </div>
                  <!-- /Account -->
                </div>
            </div>
        </div>
    </div>
    <!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

 <script>
  function addMoreEvent() {
    $('.showupload').click(function(event) {
      $(this).hide();
      $('.upload-control').show();
    });

     $("#data_profile_change").submit(function(e){
      e.preventDefault();
      submitAjaxForm($(this));
     });

     $("#form_change_password").submit(function(event) {
       event.preventDefault();
       if ($('#')) {}
     });
  }
  function ajaxFormSuccess(target,data) {
    reportAndRefresh(target,data);
  }
 </script>

