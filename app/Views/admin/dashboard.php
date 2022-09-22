<?php include_once ROOTPATH."template/header.php"; ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Approved</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['approvedOrder']); ?></h3>
          <a href="<?php echo base_url("vc/admin/view_model/equip_request?type=approved"); ?>">
            <small class="text-info fw-semibold">Total Approved Order
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Hirers</span>
          <h3 class="card-title mb-2"><?php echo @$countData['hirers']; ?></h3>
          <a href="<?php echo base_url("vc/admin/view_model/hirers"); ?>">
            <small class="text-success fw-semibold">Total Registered Hirers
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
              <h5 class="card-header m-0 me-2 pb-3">Total Revenue Distribution</h5>
              <div id="totalRevenueChart" class="px-2"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- here is donut graph -->
      <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="row row-bordered g-0">
            <div class="col-md-6">
              <h5 class="card-header m-0 me-2 pb-3">Total Booking Status</h5>
              <div class="chart has-fixed-height" id="orders-pie"></div>
            </div>
            <div class="col-md-6">
              <h5 class="card-header m-0 me-2 pb-3">Total Withdrawal Status</h5>
              <div class="chart has-fixed-height" id="withdrawal-pie"></div>
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
      loadRevenueChart();
      loadOrderChart();
      loadWithdrawalChart();
   }

   function loadRevenueChart() {
    var val = JSON.parse('<?php echo json_encode($revenueDistrix) ?>');
    if (val !== undefined){
      Morris.Bar({
        element: 'totalRevenueChart',
        data:val ,
        xkey: 'date_paid',
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
        labels: ['Amount', 'Z', 'A']
      });
    }
   }

   function loadOrderChart() {
    var val = JSON.parse('<?php echo json_encode($orderStatusDistrix) ?>');
    if (val !== undefined){
      Morris.Donut({
        element: 'orders-pie',
        data:val,
        xkey: 'order_status',
        ykeys: ['total'],
        labels: ['Y', 'Z', 'Rejected'],
        colors: [
          '#00BCD4',
          '#B2EBF2',
          '#ffa9ce',
           '#B6A2DE',
           '#EB0F82',
           '#202A5A'
        ]
      });
    }
   }

   function loadWithdrawalChart() {
    var val = JSON.parse('<?php echo json_encode($withdrawalStatusDistrix) ?>');
    if (val !== undefined){
      Morris.Donut({
        element: 'withdrawal-pie',
        data:val,
        xkey: 'request_status',
        ykeys: ['total'],
        labels: ['Y', 'Z', 'A'],
        colors: [
          '#2EC7C9',
           '#B6A2DE',
           '#ffa9ce'
        ]
      });
    }
   }
</script>
