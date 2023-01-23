<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
     <!-- favicon -->
     <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/ui-logo.png'); ?>" type="image/x-icon">
     <!-- fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <!-- bootstrap -->
     <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
     <link href="<?= base_url('assets/css/aos.min.css'); ?>" rel="stylesheet">
     <!-- icons -->
     <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap-icon.css') ?>">
     <!-- variable css -->
     <link rel="stylesheet" href="<?= base_url('assets/css/variable.css'); ?>">
     <!--main css -->
     <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">

    <title>University of Ibadan Housing</title>
</head>
<body class="bg-light">
  <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img class="navbar-brand-img img-fluid d-inline-block align-item-top" src="<?= base_url('assets/img/favicon/ui-logo.png'); ?>" alt="UI Logo" style="width: 40px; height: 40px;">
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarToggler" 
            aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarToggler">

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                </ul>
                <a class="btn btn-primary ms-auto" href="<?= base_url('login'); ?>">
                Login
                </a>
                
            </div>
        </div>
    </nav>
    <!-- slide/side info -->
    <section class="header-section pt-4 pt-md-11 mt-5">
        <div class="header container">
            <div class="row align-items-center">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 col-lg-6 text-center">
                      <div id="headercarousel" class="carousel slide me-1" data-bs-ride="carousel">
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <img src="<?= base_url('assets/img/carousel_img/Prof. Adebowale.jpeg') ?>" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-none d-md-block">
                              <h5>Professor K.O. Adebowale, <small>FAS, mini</small></h5>
                              <p class="text-white">Vice Chancellor, University of Ibadan</p>
                            </div>
                          </div>
                          <div class="carousel-item">
                            <img src="<?= base_url('assets/img/gallery/image8.jpg'); ?>" class="d-block w-100" alt="...">
                          </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#headercarousel" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#headercarousel" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Next</span>
                        </button>
                      </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-6">
                      <!-- Heading -->
                      <h3 class="display-4 text-center text-md-start">
                        Welcome to <span class="text-primary">UI Senior Staff Housing Management</span>.
                      </h3>
          
                      <!-- Text -->
                      <p class="text-center text-md-start text-muted mb-6 mb-lg-8">
                        University of Ibadan, Nigeria
                      </p>
          
                      <!-- Buttons -->
                      <div class="text-center text-md-start">
                        <a href="<?= base_url('register'); ?>" class="btn btn-primary me-1">
                         Get Started <i class="bi bi-arrow-right-short d-none d-md-inline ms-0"></i>
                        </a>
                      </div>
                      
                    </div>
                  </div>
            </div>
        </div>

    </section>
      <section id="about" class="about position-relative">
        <div class="container" data-aos="fade-up">
          <div class="section-header text-center pb-4">
            <h2 class="fs-1 fw-3 mb-3">About Us</h2>
            <p class="mx-auto text-secondary">
              This is a platform where you can apply for an 
          accomodation and monitor the progress and update your record.
          We are here to make housing application easier and better. This platform will also
          act as a housing records for every University of Ibadan Senior Staff occupant.
            </p>
          </div>
          <div class="row g-4 g-lg-5 mt-3" data-aos="fade-up" data-aos-delay="200">
            <div class="col-lg-5">
              <div class="about-img position-relative">
                <img src="<?= base_url('assets/img/site-images/house.jpg'); ?>" class="img-fluid rounded" alt="a house">
              </div>
            </div>
            <div class="col-lg-7">
              <h4 class="fw-3 fs-2 mb-2">We are here to make housing allocation better.</h4>
              <!-- Tabs -->
              <ul class="nav nav-pills mb-3">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab1">UI Vision</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab2">UI Mission</a></li>
              </ul>
              <!-- Tab Content -->
              <div class="tab-content">
                <div class="tab-pane fade show active" id="tab1">
                    <div class="d-flex align-items-center mt-4">
                      <i class="bi bi-check2"></i>
                       <h5>To be a world-class institution for academic excellence geared towards meeting societal needs.</h5>
                    </div>
                </div>

                <div class="tab-pane fade show" id="tab2">
                  <div class="d-flex align-items-center mt-4">
                    <i class="bi bi-check2"></i>
                     <h5>To expand the frontiers of knowledge through provision of excellent conditions for learning and research.</h5>
                  </div>
                  <div class="d-flex align-items-center mt-4">
                    <i class="bi bi-check2"></i>
                     <h5>To produce graduates who are worthy in character and sound judgement.</h5>
                  </div>
                  <div class="d-flex align-items-center mt-4">
                    <i class="bi bi-check2"></i>
                     <h5>To contribute to the transformation of society through creativity and innovation.</h5>
                  </div>
                  <div class="d-flex align-items-center mt-4">
                    <i class="bi bi-check2"></i>
                     <h5>To serve as a dynamic custodian of society's salutary values and thus sustain its integrity.</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Get started card -->
      <section id="gsc" class="gsc">
        <div class="container" data-aos="zoom-out">
          <div class="row g-5">
            <div class="col-lg-8 col-md-6 content d-flex flex-column justify-content-center order-last order-md-first">
              <h3>Ready to get <em>started?</em></h3>
              <a class="gsc-btn align-self-start" href="<?= base_url('register'); ?>">Click Here</a>
            </div>
  
            <div class="col-lg-4 col-md-6 order-first order-md-last d-flex align-items-center">
              <div class="img">
                <img src="<?= base_url('assets/img/site-images/house1.jpg'); ?>" alt="A house...at least a hand" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Our services card -->
      <section id="services" class="services">
        <div class="container" data-aos="fade-up">
  
          <div class="section-header">
            <h2>Our Services</h2>
            <p>Ea vitae aspernatur deserunt voluptatem impedit deserunt magnam occaecati dssumenda quas ut ad dolores adipisci aliquam.</p>
          </div>
  
          <div class="row gy-5">
  
            <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
              <div class="service-item">
                <div class="img">
                  <img src="<?= base_url('assets/img/site-images/uibello.jpg'); ?>" class="img-fluid" alt="">
                </div>
                <div class="details position-relative">
                  <div class="icon">
                    <i class="bi bi-house"></i>
                  </div>
                  <a href="<?= base_url('login'); ?>" class="stretched-link">
                    <h3>Book a House</h3>
                  </a>
                  <p>Provident nihil minus qui consequatur non omnis maiores. Eos accusantium minus dolores iure perferendis.</p>
                </div>
              </div>
            </div><!-- End Service Item -->
  
            <div class="col-xl-4 col-md-6" data-aos="zoom-in" data-aos-delay="300">
              <div class="service-item">
                <div class="img">
                  <img src="<?= base_url('assets/img/site-images/uiservice2.jpg'); ?>" class="img-fluid" style="width:500px; height:200px;" alt="">
                </div>
                <div class="details position-relative">
                  <div class="icon">
                    <i class="bi bi-file-plus"></i>
                  </div>
                  <a href="<?= base_url('login'); ?>" class="stretched-link">
                    <h3>Update Records</h3>
                  </a>
                  <p>Ut autem aut autem non a. Sint sint sit facilis nam iusto sint. Libero corrupti neque eum hic non ut nesciunt dolorem.</p>
                </div>
              </div>
            </div><!-- End Service Item -->
          </div>
        </div>
      </section>
      <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery section-bg">
      <div class="container">

        <div class="section-header">
          <h2>Gallery</h2>
          <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
        </div>

        <div class="row">
        <div class="row gallery-container">

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="200" >
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image11.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div>
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="300">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image10.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="400">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image3.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="500">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image5.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div>
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="600">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image9.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="600">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image6.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="700">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image12.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="700">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image7.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div> 
          </div>

          <div class="col-lg-4 col-md-6 gallery-item" data-aos="zoom-in" data-aos-delay="700">
            <div class="gallery-wrap">
              <img src="<?= base_url('assets/img/gallery/image8.jpg'); ?>" class="img-fluid" alt="image containing a school environment">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Staff Section -->
    <section id="team" class="team">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Our Staffs</h2>
          <p>These are the staffs incharge</p>
        </div>

        <div class="row gy-5">

          <div class="col-xl-4 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="200">
            <div class="team-member">
              <div class="member-img">
                <img src="<?= base_url('assets/img/ui-staff/uistaff2.jpg'); ?>" class="img-fluid" alt="image containing a person">
              </div>
              <div class="member-info">
                <div class="social">
                  <a href="#"><i class="bi bi-twitter"></i></a>
                  <a href="#"><i class="bi bi-facebook"></i></a>
                  <a href="#"><i class="bi bi-instagram"></i></a>
                  <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
                <h4>Dr. Ayinla</h4>
                <span>Chief Executive Officer</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-xl-4 col-md-6 d-flex" data-aos="zoom-in" data-aos-delay="400">
            <div class="team-member">
              <div class="member-img">
                <img src="<?= base_url('assets/img/ui-staff/uistaff1.jpg'); ?>" class="img-fluid" alt="image containing a person">
              </div>
              <div class="member-info">
                <div class="social">
                  <a href="#"><i class="bi bi-twitter"></i></a>
                  <a href="#"><i class="bi bi-facebook"></i></a>
                  <a href="#"><i class="bi bi-instagram"></i></a>
                  <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
                <h4>Dummy Text</h4>
                <span>Product Manager</span>
              </div>
            </div>
          </div><!-- End Team Member -->
        </div>
      </div>
    </section>
    <!-- Contact Us -->
    <section id="contact" class="contact section-bg">

      <div class="container">
        <div class="section-header">
          <h2>Contact Us</h2>
          <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
        </div>

        <div class="row">

          <div class="col-lg-6 d-flex align-items-stretch infos">

            <div class="row">

              <div class="col-lg-6 info d-flex flex-column align-items-stretch">
                <i class="bi bi-geo-alt-fill"></i>
                <h4>Address</h4>
                <p>University of Ibadan,<br>Ibadan, Oyo State. Nigeria</p>
              </div>
              <div class="col-lg-6 info info-bg d-flex flex-column align-items-stretch">
                <i class="bi bi-phone"></i>
                <h4>Call Us</h4>
                <p>+234 589 55488 55<br>+234 589 22548 64</p>
              </div>
              <div class="col-lg-6 info info-bg d-flex flex-column align-items-stretch">
                <i class="bi bi-envelope"></i>
                <h4>Email Us</h4>
                <p>contact@example.com<br>info@example.com</p>
              </div>
              <div class="col-lg-6 info d-flex flex-column align-items-stretch">
                <i class="bi bi-clock"></i>
                <h4>Working Hours</h4>
                <p>Mon - Fri: 8AM to 4PM<br>Sunday: 9AM to 1PM</p>
              </div>
            </div>

          </div>

          <div class="col-lg-6 d-flex align-items-stretch contact-form-wrap">
            <form action="#" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <label for="name">Your Name</label>
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <label for="email">Your Email</label>
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <label for="subject">Subject</label>
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mt-3">
                <label for="message">Message</label>
                <textarea class="form-control" name="message" rows="8" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div>

        </div>
    </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="footer">
      <div class="footer-content">
        <div class="container">
          <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="footer-info">
                <h3>University <br> of <br> Ibadan</h3>
                <p>
                  University of Ibadan, <br>
                  Ibadan, Oyo State, Nigeria<br><br>
                  <strong>Phone:</strong> +234 589 55488 55<br>
                  <strong>Email:</strong> info@example.com<br>
                </p>
              </div>
            </div>
  
            <div class="col-lg-4 col-md-6 footer-links">
              <h4>Useful Links</h4>
              <ul>
                <li><i class="bi bi-chevron-right"></i> <a href="<?= base_url(); ?>">Home</a></li>
                <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
                <li><i class="bi bi-chevron-right"></i> <a href="#services">Services</a></li>
              </ul>
            </div>
  
            <div class="col-lg-4 col-md-6 footer-links">
              <h4>Our Services</h4>
              <ul>
                <li><i class="bi bi-chevron-right"></i> <a href="<?= base_url('login'); ?>">Book a House</a></li>
                <li><i class="bi bi-chevron-right"></i> <a href="<?= base_url('login'); ?>">Update Records</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
  
      <div class="footer-legal text-center">
        <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
  
          <div class="d-flex flex-column align-items-center align-items-lg-start">
            <div class="copyright">
              &copy; Copyright <strong><span>Computer Science Students supervised by Drs. Ayinla and Onasanya</span></strong>
            </div>
          </div>
  
          <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </div>
    </footer>
    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    
    <script src="<?= base_url('assets/js/aos.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/script.js'); ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js'); ?>"></script>
</body>
</html>