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

    <title>Reset UIHousing</title>

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
              <h4 class="mb-2">Forgot Password? 🔒</h4>
              <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>

                <?php echo form_open("auth/forgetPassword", array('class' => 'mb-3', 'id' => 'resetPass')); ?>
                <?= csrf_field(); ?>
                <!-- this is the notification section -->
                <div id="notify"></div>
                <!-- end notification -->

                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
                    autofocus
                  />
                </div>
                <div class="mb-3">
                </div>
                <input type="hidden" name="isajax" value="true">
                <input type="hidden" name="task" value="reset" />
                <input type="hidden" id='base_path' value="<?php echo base_url(); ?>">
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit" id="btnReset">Send Reset Link</button>
                </div>
              </form>
              <div class="text-center">
                <a href="<?php echo base_url('login'); ?>" class="d-flex align-items-center justify-content-center">
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                  Back to login
                </a>
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
    <script type="text/javascript">
        resetFunc();
        function resetFunc(){
            var form = $('#resetPass');
            form.submit(function(event) {
                event.preventDefault();
                var note = $("#notify");
                note.text('').hide();
                $("#btnReset").html("processing...").addClass('disabled').prop('disabled', true);
                submitAjaxForm($(this));
            });
        }

        function ajaxFormSuccess(target,data){
            var note = $("#notify");
            if (data.status) {
                note.show();
                note.removeClass('alert alert-warning');
                note.html("<p>"+data.message+"</p>").addClass("alert alert-success alert-dismissible fade show text-center");
                $('#resetPass').trigger('reset');
                $("#btnReset").html("Send Reset Link").removeClass('disabled').prop('disabled', false);
            }
            else{
                note.show();
                note.removeClass('alert alert-success');
                note.html("<p>"+data.message+"</p>").addClass("alert alert-danger alert-dismissible fade show text-center");
                $("#btnReset").html("Send Reset Link").removeClass('disabled').prop('disabled', false);
            }
        }
    </script>
  </body>
</html>
