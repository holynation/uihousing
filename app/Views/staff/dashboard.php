<?php include_once ROOTPATH."template/header.php"; ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <?php if(!$staff->staff_path): ?>
      <div class="alert alert-info w-50">
        <span class="text-center">Please update your profile.</span>
        <a href="<?= url_to('staff_profile'); ?>" class="text-danger">Click Here</a>
      </div>
    <?php endif; ?>
  <div class="row">
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Total Children</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['children']); ?></h3>
          <a href="<?php echo base_url("vc/staff/children"); ?>">
            <small class="text-info fw-semibold">View Children
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Tenant</span>
          <h3 class="card-title mb-2"><?php echo @$countData['tenant']; ?></h3>
          <a href="<?php echo base_url("vc/staff/tenant"); ?>">
            <small class="text-success fw-semibold">View Tenant History
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Total Application</span>
          <h3 class="card-title mb-2"><?php echo @$countData['application']; ?></h3>
          <a href="<?php echo base_url("vc/staff/apply"); ?>">
            <small class="text-primary fw-semibold">View Application History
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>

      <!-- Total Revenue -->
      <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="row row-bordered g-0">
            <div class="col-md-12">
              <h5 class="card-header m-0 me-2 pb-3">Application Distribution</h5>
              <div id="totalApplicantChart" class="px-2"></div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<!-- / Content & end for last graph-->

<?php include_once ROOTPATH."template/footer.php"; ?>
<script src="<?php echo base_url('assets/vendor/libs/morris/raphael-min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/libs/morris/morris.min.js') ?>"></script>

<script>
  // addMoreEvent is loaded directly under custom.js
   function addMoreEvent() {
      loadApplicantChart();
   }

   function loadApplicantChart() {
    var val = JSON.parse('<?php echo json_encode(@$applicantDistrix) ?>');
    if (val !== undefined){
      Morris.Bar({
        element: 'totalApplicantChart',
        data:val ,
        xkey: 'application_date',
        ykeys: ['total'],
        barColors: [
          '#5AB1EF',
          '#EB0F82',
          '#2EC7C9',
           '#B6A2DE',
           '#202A5A',
           '#5AB1EF',
           '#ffa9ce',
           '#f4aaa4',
           '#a5eed0',
           '#b695ff',
           '#5ce0aa'
        ],
        labels: ['Total', 'Z', 'A']
      });
    }
   }
</script>
