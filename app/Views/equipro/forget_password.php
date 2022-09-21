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
    <title>Reset Password | Daabo Device Protection</title>
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
                                        <h5 class="nk-block-title">Reset password</h5>
                                        <div class="nk-block-des">
                                            <p>If you forgot your password, well, then we’ll email you instructions to reset your password.</p>
                                        </div>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <form action="<?php echo base_url('auth/forgetPassword'); ?>" method="post" role="form" id="resetPass" name="resetPass">
                                    <!-- this is the notification section -->
                                    <div id="notify"></div>
                                    <!-- end notification -->
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email</label>
                                        </div>
                                        <input type="email" class="form-control form-control-lg" name="email" id="default-01" placeholder="Enter your email address" required>
                                    </div>
                                    <input type="hidden" name="task" value="reset" />
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block" id="btnReset">Send Reset Link</button>
                                    </div>
                                </form><!-- form -->
                                <div class="form-note-s2 pt-5">
                                    <a href="<?php echo base_url('login'); ?>"><strong>Return to login</strong></a>
                                </div>
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
                                       
                                    </ul><!-- .nav -->
                                </div>
                                <div class="mt-3">
                                    <p>&copy; 2021 Daabo. All Rights Reserved.</p>
                                </div>
                            </div><!-- .nk-block -->
                        </div><!-- .nk-split-content -->
                        <div class="nk-split-content nk-split-stretch bg-abstract"></div><!-- .nk-split-content -->
                    </div><!-- .nk-split -->
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
        resetFunc();
        function resetFunc(){
            var form =$('#resetPass');
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
                note.html("<p>"+data.message+"</p>").addClass("alert alert-success alert-dismissible fade show").append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
                $('#resetPass').trigger('reset');
                $("#btnReset").html("Send Reset Link").removeClass('disabled').prop('disabled', false);
            }
            else{
                note.show();
                note.removeClass('alert alert-success');
                note.html("<p>"+data.message+"</p>").addClass("alert alert-danger alert-dismissible fade show").append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"></span></button>');
                $("#btnReset").html("Send Reset Link").removeClass('disabled').prop('disabled', false);
            }
        }
    </script>
</html>