<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - Equip Extended Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Extend Equip Request</a>
      <span class="breadcrumb-item active">Current</span>
    </div>
  </div>
</div>
<!-- /page header -->
<!-- Content -->
<div class="container-xxl flex-grow-1">
    <div class="row">
        <!-- Content area -->
        <div class="content">
          <!-- Basic card -->
          <div class="card">
            <div class="card-header header-elements-inline">
              <h5 class="card-title"></h5>
              <div class="header-elements">
                <div class="list-icons">
                  <a class="list-icons-item" data-action="collapse"></a>
                </div>
              </div>
            </div>
            <!-- this is the view table for each model -->
            <div class="card-body">
              <div class="card-inner mx-4 my-3">
                <?php if(!$modelStatus){ ?>
                    <div class="alert alert-primary text-center w-50">
                      <span>There seems to be no data available</span>
                    </div>
                <?php }else{ ?>
                  <div class="row">
                    <div class="col-md-8 col-xl-6">
                      <!-- Basic Bootstrap Table -->
                      <div class="card">
                        <h5 class="card-header">Extended Request Details</h5>
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Order Number:</strong></td>
                                <td>#<?php echo $modelPayload['order_number']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Equip Name:</strong></td>
                                <td><?php echo $modelPayload['equip_name']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Order Status:</strong></td>
                                <td><?php echo strtoupper($modelPayload['order_status']); ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>
                                Amount:</strong></td>
                                <td> <?php echo number_format($modelPayload['total_amount']);?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class=" <?php echo $modelPayload['payment_status'] ? 'badge bg-label-success me-1' : 'badge bg-label-warning me-1' ; ?>"> <?php echo $modelPayload['payment_status'] ? 'PAID' : 'NOT PAID'; ?></span></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Quantiy:</strong></td>
                                <td><?php echo $modelPayload['quantity']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Rental Period:</strong></td>
                                <td><?php echo $modelPayload['rental_from'] ." --- ". $modelPayload['rental_to']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Description:</strong></td>
                                <td><?php echo $modelPayload['description']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Delivery Location:</strong></td>
                                <td><?php echo $modelPayload['delivery_location']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Order Date:</strong></td>
                                <td><?php echo $modelPayload['date_created']; ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!--/ Basic Bootstrap Table -->
                    </div>
                    <div class="col-md-4 col-xl-6">
                      <div class="card">
                        <h5 class="card-header">Hirers Details</h5>
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Fullname:</strong></td>
                                <td><?php echo $hirers['fullname']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Email:</strong></td>
                                <td><?php echo $hirers['email']; ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Phone Number:</strong></td>
                                <td><?php echo strtoupper($hirers['phone_number']); ?></td>
                              </tr>
                              <tr>
                                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Address:</strong></td>
                                <td><?php echo strtoupper($hirers['address']); ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <!-- /basic card -->
        </div>
        <!-- /content area -->
    </div>
</div>
<!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

