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

    <title>Verification UIHousing</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/logo/favicon.png"); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/core.css"); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/theme-default.css"); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/demo.css"); ?>" />

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
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center mb-0">
                <a href="<?php echo base_url('/'); ?>" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <a href="<?php echo base_url('/'); ?>" class="logo-link">
                        <img class="logo-img logo-img-lg" src="<?php echo base_url('assets/img/logo/logo.png'); ?>" alt="logo">
                    </a>
                  </span>
                </a>
              </div>
              <!-- /Logo -->

              <!-- this is the notification div for verify_account -->
                <?php  if(isset($type) && $type == 'verify_account'){ ?>
                <h3 class="text-center">Account Verification Page</h3>
                <?php if(isset($success)): ?>
                <div class="alert alert-success">
                  <p class="text-center mt-2" style="font-size:17.5px;"><?php echo $success; ?></p>
                </div>
                <?php endif; } ?>
              <!-- end verify_account -->

              <!-- error notification -->
                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                  <p class="text-center mt-3"><?php echo $error; ?></p>
                </div>
                <?php endif;  ?>
              <!-- end error notification -->

            <!-- this is the reset password form -->
            <?php if(isset($type) && $type == 'forget'){ ?>

              <div class="nk-block-head mb-2">
                  <div class="nk-block-head-content">
                      <h5 class="nk-block-title">Change Password</h5>
                  </div>
              </div><!-- .nk-block-head -->

              <!-- this is the notification section -->
              <div id="notify"></div> 
              <!-- end notification -->

              <form action="<?php echo base_url('auth/forgetPassword'); ?>" method="post" role="form" id="verifyForm">
                  <?php if(isset($email_hash, $email_code)) { ?>
                  <input type="hidden" name="email_hash" id="email_hash" value="<?php echo $email_hash; ?>" />
                  <input type="hidden" name="email_code" id="email_code" value="<?php echo $email_code; ?>" />
                  <?php  } ?>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      name="email"
                      placeholder="Enter your email address"
                      value="<?php echo (isset($email)) ? $email : '';?>"
                      readonly
                    />
                  </div>
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
                  <input type="hidden" name="isajax">
                  <input type="hidden" id='base_path' value="<?php echo base_url(); ?>">
                  <input type="hidden" name="task" value="update">
                  <div class="form-group">
                      <button class="btn btn-primary d-grid w-100" type="submit" id="btnVerify">Submit</button>
                  </div>
              </form><!-- form -->
            
            <?php } ?>
            <!-- end password reset form -->

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
    <script src="<?php echo base_url("assets/js/main.js"); ?> "></script>
    <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>
    <!-- endbuild -->
    <script>
      $(function () {
        var form = $('#verifyForm');
        form.submit(function(event) {
          event.preventDefault();
          var note = $("#notify");
          note.text('');
          note.hide();
          $("#btnVerify").html("processing...").addClass('disabled').prop('disabled', true);
          submitAjaxForm($(this));
        });
      });

      function ajaxFormSuccess(target,data){
        var note = $("#notify");
        if (data.status) {
          note.show();
          note.removeClass('alert alert-warning');
          note.html("<p>"+data.message+"</p>").addClass("alert alert-success alert-dismissible fade show text-center").delay(10000);
          $('#signForm').trigger('reset');
          location.assign("<?php echo base_url('login'); ?>");
        }
        else{
          note.show();
          note.removeClass('alert alert-success');
          note.html("<p>"+data.message+"</p>").addClass("alert alert-danger alert-dismissible fade show text-center");
          $("#btnVerify").html("Submit").removeClass('disabled').prop('disabled', false);
        }
      }
    </script>
  </body>
</html>
