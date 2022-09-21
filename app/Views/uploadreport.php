 <?php include_once ROOTPATH.'template/header.php'; ?>

        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Page header -->
            <div class="page-header page-header-light">
                <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
                    <div class="d-flex">
                        <div class="breadcrumb">
                            <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                            <a href="#" class="breadcrumb-item">Upload Report</a>
                            <span class="breadcrumb-item active">Current</span>
                        </div>
                        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                    </div>
                </div>
            </div>
            <!-- /page header -->

            <!-- Content area -->
            <div class="content">
                <!-- Basic card -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title"><?php echo removeUnderscore(ucfirst($model));  ?> Upload Report</h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                                <a class="list-icons-item" data-action="remove"></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-sm-12">
                          <?php if ($status): ?>
                            <div class="alert alert-success"><?php echo  wordwrap(ucfirst($message), 100 , "\n", true);  ?></div>
                            <?php else: ?>
                              <div class="alert alert-danger"><?php echo ucfirst($message); ?></div>
                          <?php endif; ?>
                        </div>
                    </div>
                    <br>
                    <a href="<?php echo @$backLink?$backLink:''; ?>" class="btn btn-primary col-sm-4 mx-4 mb-3 px-4">
                      <i class="fa fa-arrow-back"></i> Back
                    </a>
                </div>
                <!-- /basic card -->
            </div>
            <!-- /content area -->

 <?php include_once ROOTPATH.'template/footer.php'; ?>
 