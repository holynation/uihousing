<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo base_url("assets/") ?>"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Register UIHousing</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/logo/favicon.ico"); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/fonts/boxicons.css"); ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/core.css"); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/theme-default.css"); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/demo.css"); ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"); ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/pages/page-auth.css"); ?>" />
    <!-- Helpers -->
    <script src="<?php echo base_url("assets/vendor/js/helpers.js"); ?> "></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo base_url("assets/js/config.js"); ?> "></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrappers w-75 mx-auto authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="<?php echo base_url('/'); ?>" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <a href="<?php echo base_url('/'); ?>" class="logo-link">
                        <img class="logo-img logo-img-lg" src="<?php echo base_url('assets/img/logo/logo.png'); ?>" alt="logo">
                    </a>
                  </span>
                </a>
              </div>
              <!-- /Logo -->
              <div class="mx-auto text-center">
                  <h4 class="mb-2">Welcome to UIHOUSING App! 👋</h4>
                  <p class="mb-4">Make your housing management easy and fun!</p>
              </div>

                <?php echo form_open("auth/register", array('class' => 'mb-3', 'id' => 'signForm')); ?>
                <?= csrf_field(); ?>

                <!-- this is the notification section -->
                <div id="notify"></div>
                <!-- end notification -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="staff_number" class="form-label">Staff Number</label>
                          <input
                            type="text"
                            class="form-control"
                            id="staff_number"
                            name="staff_number"
                            placeholder="Enter your staff number"
                            autofocus
                            required
                          />
                        </div>
                    </div>
                    <div class="col-lg-6"></div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="firstname" class="form-label">First Name</label>
                          <input
                            type="text"
                            class="form-control"
                            id="firstname"
                            name="firstname"
                            placeholder="Enter your firstname"
                            required
                          />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="lastname" class="form-label">Last Name</label>
                          <input
                            type="text"
                            class="form-control"
                            id="lastname"
                            name="lastname"
                            placeholder="Enter your lastname"
                            required
                          />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="othername" class="form-label">Other Name</label>
                          <input
                            type="text"
                            class="form-control"
                            id="othername"
                            name="othername"
                            placeholder="Enter your othername"
                            
                          />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="email" class="form-label">Email</label>
                          <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                          />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="designation" class="form-label">Designation</label>
                          <select name="designation" id="designation" class="form-control">
                              <?php $option = buildOptionFromQuery($db,"SELECT id,designation_name as value from    designation order by value asc",null,'');
                                echo $option;
                              ?>
                          </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                          <label for="appointment_status" class="form-label">Appointment Status</label>
                          <select name="appointment_status" id="appointment_status" class="form-control">
                              <?php 
                                $arr = [
                                    'student' => 'Student',
                                    'academic' => 'Academic',
                                    'non_teaching' => 'Non Teaching',
                                    'others' => 'Others'
                                ];
                                $option = buildOptionUnassoc2($arr,'');
                                echo $option;
                              ?>
                          </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3 form-password-toggle">
                          <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">Password</label>
                          </div>
                          <div class="input-group input-group-merge">
                            <input
                              type="password"
                              id="password"
                              class="form-control"
                              name="password"
                              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                              aria-describedby="password"
                            />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                          </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3 form-password-toggle">
                          <div class="d-flex justify-content-between">
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                          </div>
                          <div class="input-group input-group-merge">
                            <input
                              type="password"
                              id="confirm_password"
                              class="form-control"
                              name="confirm_password"
                              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                              aria-describedby="confirm_password"
                            />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                          </div>
                        </div>
                    </div>
                    <div class="mb-3">
                    </div>
                    <input type="hidden" name="isajax" value="true">
                    <input type="hidden" id='base_path' value="<?php echo base_url(); ?>">
                    <div class="mb-3 col-lg-6 mx-auto">
                      <button class="btn btn-primary d-grid w-100" type="submit" id="btnReg">Register</button>
                    </div>
                  </form>
                  <p class="text-center">
                    <span>Already have an account?</span>
                    <a href="<?php echo base_url('login'); ?>">
                      <span>Sign in instead</span>
                    </a>
                  </p>
                </div>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <script src="<?php echo base_url("assets/vendor/libs/jquery/jquery.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/libs/popper/popper.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/js/bootstrap.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"); ?> "></script>

    <script src="<?php echo base_url("assets/vendor/js/menu.js"); ?> "></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?php echo base_url("assets/js/main.js"); ?> "></script>
    <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>
    <script>
        $(function(){
            // here is the registration
            var form = $('#signForm');
            var note = $("#notify");
            note.text('').hide();

            form.submit(function(event) {
              event.preventDefault();
              $("#btnReg").html("Processing...").addClass('disabled').prop('disabled', true);;

              var password = $('#password').val(),
              confirm_password = $('#confirm_password').val();

              if(password == '' || confirm_password == ''){
                note.show();
                note.html('Password can not be empty...').addClass("alert alert-danger alert-dismissible show text-center");
                  $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
                  return false;
              }
              else if(password != confirm_password ){
                note.show();
                note.html('New password must match Confirm password...').addClass("alert alert-danger alert-dismissible show text-center");
                $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
                return false;
              }else{
                submitAjaxForm($(this));
                // $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
              }
            });
        })

        function ajaxFormSuccess(target,data){
            var notify = $('#notify');
            notify.text('').show();
            var elem = document.getElementById("notify");
                elem.scrollIntoView();
            if (data.status) {
              notify.html(data.message).removeClass('alert alert-danger').addClass("alert alert-success alert-dismissible show text-center").css({"font-size":"12.368px"});
              $('#signForm').trigger('reset');
            }
            else{
              notify.html(data.message).addClass("alert alert-danger alert-dismissible show text-center").css({"font-size":"12.368px"});
              var elem = document.getElementById("notify");
                elem.scrollIntoView();
              $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
            }
            showNotification(data.status,data.message);
            $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
        }
    </script>
  </body>
</html>
