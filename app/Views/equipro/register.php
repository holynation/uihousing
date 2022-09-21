<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Daabo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard for Daabo Device Protection">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/public/daabo_icon.png">
    <!-- Page Title  -->
    <title>Register | Daabo Device Protection</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/public/css/dashlite.css">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white w-lg-45">
                            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                                <a href="#" class="toggle btn btn-white btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
                            </div>
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="<?php echo base_url('/'); ?>" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg" src="<?php echo base_url(); ?>assets/public/daabo.png" alt="logo">
                                        <img class="logo-dark logo-img logo-img-lg" src="<?php echo base_url(); ?>assets/public/daabo.png"  alt="logo-dark">
                                    </a>
                                </div>
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title">Register</h5>
                                        <div class="nk-block-des">
                                            <p>Create New Daabo Account</p>
                                        </div>
                                        <div class="text-center pt-4 pb-3">
                                            <h6 class="overline-title overline-title-sap"><span>OR</span></h6>
                                        </div>
                                        <div class="nk-block-des text-center">
                                            <a href="<?php echo base_url('register?type=company'); ?>">Click to create a business account</a>
                                        </div>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <!-- <form action="html/pages/auths/auth-success-v3.html"> -->
                                <?php echo form_open("auth/register?_=".time(), array('class'=> 'form','id'=>'signForm')); ?>
                                        <!-- this is the notification section -->
                                        <div id="notify"></div>
                                        <!-- end notification -->
                                    <div class="form-group">
                                        <label class="form-label" for="full-name">Full Name</label>
                                        <input type="text" class="form-control form-control-lg" name="fullname" id="name" placeholder="Enter your name">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" id="email" placeholder="Enter your email address">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control form-control-lg" name="phone_number" id="phone_number" placeholder="Enter your phone number">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="form-control-wrap">
                                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter your password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">Confirm Password</label>
                                        <div class="form-control-wrap">
                                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="confirm_password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="confirm_password" id="confirm_password" placeholder="Confirm password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-control-xs custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="agree" id="checkbox">
                                            <label class="custom-control-label" for="checkbox">I agree to Daabo <a tabindex="-1" href="#">Privacy Policy</a> &amp; <a tabindex="-1" href="#"> Terms.</a></label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="register_type" value="user" />
                                    <input type="hidden" name="ref" id="ref" value="<?php echo isset($_GET['_p']) ? $_GET['_p'] : ""; ?>" />
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary btn-block" name="btnReg" id="btnReg">Register</button>
                                    </div>
                                </form><!-- form -->
                                <div class="form-note-s2 pt-4"> Already have an account ? <a href="<?php echo base_url('login'); ?>"><strong>Sign in instead</strong></a>
                                </div>
                                <div class="text-center pt-4 pb-3">
                                    <h6 class="overline-title overline-title-sap"><span>OR</span></h6>
                                </div>
                                <ul class="nav justify-center gx-8">
                                    <li class="nav-item"><a class="nav-link" href="#">Facebook</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#">Google</a></li>
                                </ul>
                            </div><!-- .nk-block -->
                            <div class="nk-block nk-auth-footer">
                                <div class="nk-block-between">
                                    <ul class="nav nav-sm">
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('terms'); ?>">Terms & Condition</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('privacy_policy'); ?>">Privacy Policy</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('faq'); ?>">FAQs</a>
                                        </li>
                                        
                                    </ul><!-- nav -->
                                </div>
                                <div class="mt-3">
                                    <p>&copy; 2021 Daabo. All Rights Reserved.</p>
                                </div>
                            </div><!-- nk-block -->
                        </div><!-- nk-split-content -->
                        <div class="nk-split-content nk-split-stretch bg-abstract">
                        </div><!-- nk-split-content -->
                    </div><!-- nk-split -->
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="<?php echo base_url(); ?>assets/public/js/bundle.js"></script>
    <script src="<?php echo base_url(); ?>assets/public/js/scripts.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/custom.js?_=<?php echo time(); ?>"></script>
    <script>
        $(function(){
            // here is the registration
            var form =$('#signForm');
            var note = $("#notify");
            note.text('').hide();

            form.submit(function(event) {
              event.preventDefault();
              $("#btnReg").html("Processing...").addClass('disabled').prop('disabled', true);;

              var password = $('#password').val(),
              confirm_password = $('#confirm_password').val();

              if(password == '' || confirm_password == ''){
                note.show();
                note.html('Password can not be empty...').addClass("alert alert-danger alert-dismissible show text-center").append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
                  $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
                  return false;
              }
              else if(password != confirm_password ){
                note.show();
                note.html('New password must match Confirm password...').addClass("alert alert-danger alert-dismissible show text-center").append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
                $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
                return false;
              }else{
                submitAjaxForm($(this));
                $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
              }
            });
        })
        function ajaxFormSuccess(target,data){
            var notify = $('#notify');
            notify.text('').show();
            var elem = document.getElementById("notify");
                elem.scrollIntoView();
            if (data.status) {
              notify.html(data.message).removeClass('alert alert-danger').addClass("alert alert-success alert-dismissible show text-center").css({"font-size":"12.368px"}).append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
              $('#signForm').trigger('reset');
            }
            else{
              notify.html(data.message).addClass("alert alert-danger alert-dismissible show text-center").css({"font-size":"12.368px"}).append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
              var elem = document.getElementById("notify");
                elem.scrollIntoView();
              $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
            }
            showNotification(data.status,data.message);
            $("#btnReg").removeClass("disabled").removeAttr('disabled').html("Register");
        }
    </script>
</html>