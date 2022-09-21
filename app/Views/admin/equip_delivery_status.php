
<?php include_once ROOTPATH."template/header.php"; ?>
<style type="text/css">
  .timeline { color: #8094ae; line-height: 1.3; }
.timeline + .timeline, .timeline-list + .timeline-head { margin-top: 1.75rem; }

.timeline-head { font-size: 14px; color: #8094ae; margin-bottom: 1rem; }

.timeline-item { position: relative; display: flex; align-items: flex-start; }

.timeline-item:not(:last-child) { padding-bottom: 1.5rem; }

.timeline-item:not(:last-child):before { position: absolute; height: calc(100% - 11px); width: 1px; background: #dbdfea; content: ''; top: 13px; left: 5px; }

.timeline-status { position: relative; height: 11px; width: 11px; border-radius: 50%; flex-shrink: 0; margin-top: 2px; }

.timeline-status.is-outline:after { position: absolute; height: 7px; width: 7px; border-radius: 50%; background: #fff; content: ''; top: 2px; left: 2px; }

.timeline-date { position: relative; color: #8094ae; width: 90px; margin-left: .75rem; flex-shrink: 0; line-height: 1rem; }

.timeline-date .icon { right: 0; margin-right: 0.25rem; vertical-align: middle; color: #8094ae; display: inline-block; position: absolute; top: 2px; }

.timeline-data { padding-left: 8px; }

.timeline-title { font-size: 15px; color: #364a63; margin-bottom: .75rem; }

.timeline-des { color: #8094ae; }

.timeline-des p { margin-bottom: .25rem; }

.timeline .time { display: block; font-size: 12px; color: #8094ae; }

@media (min-width: 576px) { .timeline + .timeline, .timeline-list + .timeline-head { margin-top: 2.5rem; } }

@media (max-width: 413px) { .timeline-item { flex-wrap: wrap; }
  .timeline-date { width: 80px; }
  .timeline-data { padding: .75rem 0 0 24px; } }
</style>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - Equip Delivery Status Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Equip Delivery Status</a>
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
            <!-- this is the view table for each model -->
            <div class="card-body">
              <div class="card-inner mx-4 my-3">
                <?php if(!$modelStatus){ ?>
                    <div class="alert alert-primary text-center w-50">
                      <span>There are no delivery status available at the moment</span>
                    </div>
                <?php }
                else{
                  $payload = $modelPayload['deliveryInfo'];
                  $equipName = $modelPayload['equipName'];
                  $ownerName = $modelPayload['ownerName'];
                  $equipOrder = $modelPayload['equipOrder'];
                ?>
                  <div class="row">
                    <div class="col-md-12 col-xl-12">
                      <div class="timeline">
                        <h6 class="timeline-head"><strong>Equipment Name:</strong> <?php echo strtoupper($equipName); ?>&nbsp;&nbsp;
                        | &nbsp;&nbsp;<strong>Quantity:</strong> <?php echo $equipOrder->quantity; ?> &nbsp;&nbsp;
                         | &nbsp;&nbsp;<strong>Hirer Name:</strong> <?php echo ucfirst($equipOrder->hirers->fullname); ?>
                        </h6>
                        <ul class="timeline-list">
                          <?php foreach($payload as $val): ?>
                            <li class="timeline-item">
                                <div class="timeline-status bg-primary is-outline"></div>
                                <div class="timeline-date">
                                  <?php
                                  $time = $time::parse($val['date_created']);
                                  echo $time->toLocalizedString('MMM d, yy');
                                ?> <em class="icon ni ni-alarm-alt"></em></div>
                                <div class="timeline-data">
                                    <h6 class="timeline-title"><?php echo getDeliveryStatus($val['delivery_status']); ?></h6>
                                    <div class="timeline-des">
                                        <p>Date Modified:<?php echo $time->toLocalizedString('MMM d, yyyy'); ?></p>
                                        <span class="time"><?php echo $time->humanize(); ?></span>
                                    </div>
                                </div>
                            </li>
                          <?php endforeach; ?>
                        </ul>
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

