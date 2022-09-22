             
                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                  <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                    <div class="mb-2 mb-md-0">
                      ©
                      <script>
                        document.write(new Date().getFullYear());
                      </script>
                      UIHousing. All Rights Reserved
                    </div>
                  </div>
                </footer>
                <!-- / Footer -->
                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?php echo base_url("assets/vendor/libs/popper/popper.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/js/bootstrap.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"); ?> "></script>
    <script src="<?php echo base_url('assets/vendor/libs/toastr/toastr.min.js'); ?>"></script>
    <script src="<?php echo base_url("assets/vendor/js/menu.js"); ?> "></script>
    <!-- endbuild -->

    <?php 
    if($userType == 'admin'): ?>
    <script src="<?php echo base_url('assets/vendor/libs/datatables/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/libs/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
    <script type="text/javascript">
        // Setting datatable defaults
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [{ 
                orderable: false,
                width: 100,
                targets: [ 5 ]
            }],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Search...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            }
        });
        var table = $('#datatable-buttons').DataTable({
            dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            buttons: {   
                dom: {
                    button: {
                        className: 'btn btn-primary'
                    }
                },
                buttons: [
                    'csvHtml5',
                    'excelHtml5',
                    'print'
                ]
            }
        });

        /* Code for changing active link on clicking */
        const menuItems = $(".bg-menu-theme .menu-inner > .menu-item");
        const navItems = $(".bg-menu-theme .menu-inner > .menu-item");
        for (let i = 1; i < menuItems.length; i++) {
            let currentChild = menuItems[i].children[1].children;
                for(let j = 0; j < currentChild.length; j++){
                    if (currentChild[j].firstElementChild.href == location.href) {
                        menuItems[0].className = menuItems[0].className.replace(" active", "");
                        menuItems[i].className += " active open";
                        currentChild[j].className += " active";
                    }  
                }
        }

    </script>
    <?php endif; ?>

    <!-- Vendors JS -->
    <!-- Main JS -->
    <script src="<?php echo base_url("assets/js/main.js"); ?> "></script>
    <script src="<?php echo base_url("assets/js/custom.js"); ?> "></script>
    <!-- Page JS -->
    <!-- Place this tag in your head or just before your close body tag. -->
  </body>
</html>