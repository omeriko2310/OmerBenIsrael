<?php
include "config.php";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

//testing connection success
if (mysqli_connect_errno()) {
    die("DB connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
}

//get data from DB
$query = "SELECT * FROM portfolio_OmerBenIsrael";
$result = mysqli_query($connection, $query);
if (!$result) {
    die("DB query failed.");
}
?>


<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8"/>
        <title>Omer Ben Israel Portfolio</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>

        <!-- GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet" type="text/css">
        <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css"/>
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

        <!-- PAGE LEVEL PLUGIN STYLES -->
        <link href="css/animate.css" rel="stylesheet">

        <!-- THEME STYLES -->
        <link href="css/layout.css" rel="stylesheet" type="text/css"/>

        <!-- Favicon -->
        <link rel="shortcut icon" href="favicon.ico"/>
    </head>
    <!-- END HEAD -->

    <!-- BODY -->
    <body id="body" data-spy="scroll" data-target=".header">

        <!--========== HEADER ==========-->
        <header class="header navbar-fixed-top">
            <!-- Navbar -->
            <nav class="navbar" role="navigation">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="menu-container js_nav-item">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="toggle-icon"></span>
                        </button>

                        <!-- Logo -->
                        <div class="logo">
                            <a class="logo-wrap" href="#body">
                                <img class="logo-img logo-img-main" src="img/logo.png" alt="Asentus Logo">
                                <!-- <img class="logo-img logo-img-active" src="img/logo.jpg" alt="Asentus Logo"> -->
                            </a>
                        </div>
                        <!-- End Logo -->
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse nav-collapse">
                        <div class="menu-container">
                            <ul class="nav navbar-nav navbar-nav-right">
                                <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#body">Home</a></li>
                                <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#about">About</a></li>
                                <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#service">Experience</a></li>
                                <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#portfolio">Work</a></li>
                                <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#contact">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- End Navbar Collapse -->
                </div>
            </nav>
            <!-- Navbar -->
        </header>
        <!--========== END HEADER ==========-->

        <!--========== SLIDER ==========-->
        <div class="promo-block parallax-window" data-parallax="scroll" data-image-src="img/1920x1080/01.png">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="promo-block-divider">
                            <h1 class="promo-block-title">Omer<br/>Ben Israel</h1>
                            <p class="promo-block-text">Software Engineer &amp; Saxophone Player</p>
                        </div>
                        <ul class="list-inline">
                            <li><a class="social-icons" href="https://github.com/omeriko2310" target="_blank"><i class="icon-social-github"></i></a></li>
                            <li><a href="https://www.facebook.com/omer.benisrael/" target="_blank" class="social-icons"><i class="icon-social-facebook"></i></a></li>
                            <li><a href="https://www.instagram.com/omerbenisrael/" target="_blank" class="social-icons"><i class="icon-social-instagram"></i></a></li>
                            <li><a href="https://www.linkedin.com/in/omer-ben-israel-3890a0234/" target="_blank" class="social-icons"><i class="icon-social-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
                <!--// end row -->
            </div>
        </div>
        <!--========== SLIDER ==========-->

        <!--========== PAGE LAYOUT ==========-->
        <!-- About -->
        <div id="about">
            <div class="container content-lg">
                <div class="row">
                    <div class="col-sm-3 sm-margin-b-30">
                        <div class="text-right sm-text-left">
                            <h2 class="margin-b-0">Intro</h2>
                            <h3>What I am all about.</h3>
                            <h3 class ="CV"><a href="docs/CV - Omer-Ben-Israel.pdf" target="_blank" download><span>Download My CV</span></a></h3>
                        </div>
                    </div>
                    <div class="col-sm-8 col-sm-offset-1">
                        <div class="margin-b-60">
                            <h3>A highly motivated B.Sc. Software Engineering student with a broad entrepreneurial
                            perspective, business acumen, and a creative mindset. I am a team player with a "can-do"
                            attitude and possess significant autodidactic capabilities. I am seeking a position that
                            allows me to both leverage my existing skills and acquire new knowledge, providing
                            opportunities for personal and professional growth within the company.
                            </h3>
                        </div>
                        <!-- Progress Box -->
                        <div class="progress-box">
                            <h5>PHP <span class="color-heading pull-right">85%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="85"></div>
                            </div>
                        </div>
                        <div class="progress-box">
                            <h5>HTML5/CSS <span class="color-heading pull-right">95%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="95"></div>
                            </div>
                        </div>
                        <div class="progress-box">
                            <h5>JavaSript <span class="color-heading pull-right">80%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="80"></div>
                            </div>
                        </div>
                        <div class="progress-box">
                            <h5>C <span class="color-heading pull-right">80%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="80"></div>
                            </div>
                        </div>
                        <div class="progress-box">
                            <h5>C++ <span class="color-heading pull-right">85%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="85"></div>
                            </div>
                        </div>
                        <div class="progress-box">
                            <h5>SQL/JSON <span class="color-heading pull-right">85%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-color-base" role="progressbar" data-width="85"></div>
                            </div>
                        </div>
                        <!-- End Progress Box -->
                    </div>
                </div>
                <!--// end row -->
            </div>
        </div>
    
        
        <section id="service" class="site-section section-services overlay text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h3>What i do</h3>
                        <img src="img/lines.svg" class="img-lines" alt="lines">
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/front-development.png" class="img-lines" alt="Front End Developer">
                            <h4>Front-end</h4>
                            <p>As a javascript developer, I have experience in HTML5 and CSS3 techniques working with
                                jQuery or more advanced javascript MVC frameworks such as angular</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/server.png" alt="Back End Developer">
                            <h4>Back-end</h4>
                            <p>With expertise in back-end development, I design and implement robust server-side
                                solutions using technologies like PHP, Python, and SQL, ensuring efficient data
                                processing and server functionality.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/c-.png" alt="C/C++ Developer">
                            <h4>C/C++ Developer</h4>
                            <p>As a skilled C/C++ developer, I excel at building high-performance applications and
                                optimizing code for efficiency, leveraging my deep understanding of procedural and
                                object-oriented programming principles.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/project-management.png" alt="Project Manager">
                            <h4>Project Manager</h4>
                            <p>With a strong background in operational management and business understanding, I am
                                capable of overseeing projects, coordinating teams, and ensuring timely delivery of
                                results, all while maintaining a keen focus on client satisfaction.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/social-media.png" alt="Marketing">
                            <h4>Marketing</h4>
                            <p>Combining my programming knowledge with my experience in digital marketing, I possess the
                                ability to devise effective marketing strategies, utilize data analytics, and optimize
                                online campaigns to drive business growth and maximize ROI.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="service">
                            <img src="img/product-development.png" alt="Product">
                            <h4>Product</h4>
                            <p>As a bridge between programmers and clients, I leverage my comprehensive understanding of
                                both technical and business aspects to translate client requirements into actionable
                                plans, ensuring the successful development and delivery of products that meet and exceed
                                expectations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Experience -->

        <!-- Work -->
        <section id="portfolio" class="site-section section-portfolio">
            <div class="container">
                <div class="text-center">
                    <h3>My Recent Works</h3>
                    <img src="img/lines.svg" class="img-lines" alt="lines">
                </div>
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="col-md-4 col-xs-6">
                            <div class="portfolio-item">
                                <img src="<?php echo htmlspecialchars($row['imgPath']); ?>" class="img-res" alt="">
                                <div class="portfolio-item-info">
                                    <h4>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </h4>
                                    <a href="#" data-toggle="modal"
                                        data-target="#portfolioItem<?php echo htmlspecialchars($row['id']); ?>"><span
                                            class="glyphicon glyphicon-eye-open"></span></a>
                                    <a href="<?php echo htmlspecialchars($row['url']); ?>"><span
                                            class="glyphicon glyphicon-link"></span></a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </section>
        <!-- End Work -->
            
        <!-- Contact -->
        <section id="contact" class="site-section section-form text-center">
            <div class="container">

                <h3>Contact</h3>
                <img src="img/lines.svg" class="img-lines" alt="lines">
                <form action="php/jobAlert.php" method="post">
                    <div class="row">
                        <div class="col-sm-6 m-3">
                            <input type="text" name="name" class="form-control mt-x-0" placeholder="Name" required>
                        </div>
                        <div class="col-sm-6 m-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-sm-12 m-3">
                            <textarea name="message" id="message" class="form-control" placeholder="Message"
                                required></textarea>
                        </div>
                    </div>
                    <button class="btn btn-border" type="submit">Hire Me <span
                            class="glyphicon glyphicon-send"></span></button>
                </form>
            </div>
        </section>
        <!-- End Contact -->
        <!--========== END PAGE LAYOUT ==========-->

        <!--========== FOOTER ==========-->
        <footer class="footer">
            <div class="content container">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <a class="social-icons" href="https://github.com/omeriko2310" target="_blank"><i class="icon-social-github"></i></a>
                        <a href="https://www.facebook.com/omer.benisrael/" class="social-icons"><i class="icon-social-facebook"></i></a>
                        <a href="https://www.linkedin.com/in/omer-ben-israel-3890a0234/" class="social-icons"><i class="icon-social-linkedin"></i></a>
                        <a href="https://www.instagram.com/omerbenisrael/" class="social-icons"><i class="icon-social-instagram"></i></a>
                        <div class="emailAndPhone">
                            <p><i class="icon-envelope"></i><a href="mailto:blabla@gmail.com"> obenisrael2@gmail.com</a></p>
                            <p><i class="icon-phone"></i> +972523858785</p>
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-offset-0 col-xs-6 col-xs-offset-3"><a class="copyright"
                        href="https://www.shenkar.ac.il/he/departments/engineering-software-department">© Omer Ben Israel |
                        תואר ראשון בהנדסת תוכנה בשנקר</a>
                    </div>
                </div>
                <!--// end row -->
            </div>
        </footer>
        <!--========== END FOOTER ==========-->
        <ul class="list-inline">
            <li><a href="https://www.facebook.com/omer.benisrael/" class="social-icons"><i class="icon-social-facebook"></i></a></li>
            <li><a href="https://www.instagram.com/omerbenisrael/" class="social-icons"><i class="icon-social-instagram"></i></a></li>
            <li><a href="https://www.linkedin.com/in/omer-ben-israel-3890a0234/" class="social-icons"><i class="icon-social-linkedin"></i></a></li>
            <li><a class="social-icons" href="https://github.com/omeriko2310" target="_blank"><i class="icon-social-github"></i></a></li>
        </ul>

        <?php

    //get data from DB
    $query = "SELECT * FROM portfolio_OmerBenIsrael";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("DB query failed.");
    }

    //Iterate over the results
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $name = $row['name'];
        $url = $row['url'];
        $description = $row['description'];
        $imgPath = $row['imgPath'];


        // Echo out the HTML code for each portfolio item.
        echo "<div id='portfolioItem$id' class='modal fade' role='dialog'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <a class='close' data-dismiss='modal'><span class='glyphicon glyphicon-remove'></span></a>
                        <img class='img-res' src='$imgPath' alt=''>
                    </div>
                    <div class='modal-body'>
                        <h4 class='modal-title'>$name</h4>
                        <p>$description</p>
                    </div>
                    <div class='modal-footer'>
                        <a href='$url' class='btn btn-fill'>Visit Page</a>
                    </div>
                </div>
            </div>
        </div>";
    }
    ?>

        <!-- Back To Top -->
        <a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>

        <!-- JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- CORE PLUGINS -->
        <script src="vendor/jquery.min.js" type="text/javascript"></script>
        <script src="vendor/jquery-migrate.min.js" type="text/javascript"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

        <!-- PAGE LEVEL PLUGINS -->
        <script src="vendor/jquery.easing.js" type="text/javascript"></script>
        <script src="vendor/jquery.back-to-top.js" type="text/javascript"></script>
        <script src="vendor/jquery.smooth-scroll.js" type="text/javascript"></script>
        <script src="vendor/jquery.wow.min.js" type="text/javascript"></script>
        <script src="vendor/jquery.parallax.min.js" type="text/javascript"></script>
        <script src="vendor/jquery.appear.js" type="text/javascript"></script>
        <script src="vendor/masonry/jquery.masonry.pkgd.min.js" type="text/javascript"></script>
        <script src="vendor/masonry/imagesloaded.pkgd.min.js" type="text/javascript"></script>

        <!-- PAGE LEVEL SCRIPTS -->
        <script src="js/layout.min.js" type="text/javascript"></script>
        <script src="js/components/progress-bar.min.js" type="text/javascript"></script>
        <script src="js/components/masonry.min.js" type="text/javascript"></script>
        <script src="js/components/wow.min.js" type="text/javascript"></script>
        <?php
    //release returned data
            mysqli_free_result($result);
        ?>
    </body>
    <!-- END BODY -->
</html>

<?php
//close DB connection
mysqli_close($connection);
?>