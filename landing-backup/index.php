
<!DOCTYPE html>
<html lang="en-US" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title>Y.SG</title>
		
		<!-- Seo Tags -->
		<meta name="description" content="Website Y.SG" />
		<meta name="keywords" content="YSG"/>
		<meta name="robots" content="index, follow"> 
		
		<!-- Favicon -->
		<!-- Included CSS Files -->
		<link href="https://fonts.googleapis.com/css?family=Sansita:400,400i,700,700i,800,800i,900,900i" rel="stylesheet">
		<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
		<script type="text/javascript" src="js/modernizr.js"></script>
		
	</head>
	
	<body style="
    background-size: cover;
	">
		
		
		<div class="wrap flex">
			
			<!-- Main -->
			<div id="main">
				<div class="inner fade-in">
					
					<!-- Header -->
					<header class="site-header">
						<h1 class="site-title"><img src="img/logo.png" width="200" height="160" alt="InTime" /></h1>
					</header>
					
					<!-- Content -->
					<section class="content" style="text-align:center;">
						
						<div><h1>ACT On Your Primal Instincts</h1> </div>
						<div class="flipper" data-reverse="true" data-datetime="2020-10-28 00:00:00" data-template="dd|HH|ii|ss" data-labels="Days|Hours|Minutes|Seconds" id="myFlipper"></div>
<div>Sign up now to enjoy irresistible and unbeatable bargains at YSG! </div>
						<!-- Modal page toggle -->
						<div class="modal-toggle" >
							<?php 
								
								if(isset($_POST['send'])){
					            	$koneksi=mysqli_connect("localhost","root","contigo2234!","ysg_production");
									
									$name=$_POST['nama'];
									$email=$_POST['email'];
									$insert=mysqli_query($koneksi,"INSERT INTO `user` (`id`, `name`, `email`) VALUES (NULL, '$name', '$email')");
									if($insert){
										
										echo "<div style='font-family: sans-serif;
										color: #fff;
										font-style: initial;
										font-style: italic;' >Thank you for your submission</span>";
										}else{
										echo "Error.";
									}	
								}
							?>
							<form action="" method="POST">
								<input type="text" name="nama" value="" placeholder="Name" required>
								<input type="email" name="email" value="" placeholder="Enter your email" required>
								<div>
									<input style="
									background: #ffaa34 !important;
									border-radius: 20px;
									height: 36px;
									width: 140px;
									color: #44007a;
									font-size: 11px;
									font-weight: bold;
									" type="submit" name="send" value="Count Me In!">
								</div>
							</form>
						</div>
						
						<!-- Social links -->
						<!--<div class="social">
							<p style="color: #fff;font-family: sans-serif;margin-bottom: -9px;">Follow Us</p>
							<a href="#"><img src="img/ig.png" ></a>
							<a href="#"><img src="img/twitter.png" ></a>
							<a href="#"><img src="img/fb.png" ></a>
						</div>-->
					</section>
				</div>
			</div>
			
			
			
		</div>
		
		
		
		<!-- Included JS Files -->
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script src="js/jquery.flipper-responsive.js"></script>
		<script>
			jQuery(function ($) {
				$('#myFlipper').flipper('init');
				$('#modalFlipper').flipper('init');
			});
		</script>
		<style>
      body {

  font-family: sans-serif;
}

.flipper {
  color: #333;
  display: block;
  font-size: 50px;
  line-height: 100%;
  padding: 0;
  margin: 0;
  height: 1.7em;
}
.flipper.flipper-invisible {
  font-size: 0px !important;
}

.flipper-group {
  position: relative;
  white-space: nowrap;
  display: block;
  float: left;
  padding: 0;
  margin: 0;
}
.flipper-group label {
  position: absolute;
  color: #fff;
  font-size: 20%;
  top: 100%;
  line-height: 1em;
  left: 50%;
  -webkit-transform: translate(-50%, 0);
          transform: translate(-50%, 0);
  text-align: center;
  padding-top: .5em;
}

.flipper-digit {
  white-space: nowrap;
  position: relative;
  padding: 0;
  margin: 0;
  display: inline-block;
  float: left;
  height: 1.2em;
  overflow-y: hidden;
}
.flipper-digit span {
  font-size: 25%;
}

.flipper-delimiter {
  white-space: nowrap;
  display: block;
  float: left;
  padding: 0;
  margin: 0;
  color: #fff;
  min-width: .1em;
  white-space: nowrap;
  display: block;
  padding-top: 0.1em;
  padding-bottom: 0.1em;
  line-height: 1em;
}

.digit-face {
  display: block;
  visibility: hidden;
  position: relative;
  border-radius: 0.1em;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 8;
  padding-top: 0.1em;
  padding-bottom: 0.1em;
  padding-left: 0.1em;
  padding-right: 0.1em;
  box-sizing: border-box;
  text-align: center;
}

.digit-next {
  display: block;
  position: relative;
  border-radius: 0.1em;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 8;
  height: 1.2em;
  background: #fff;
  padding-top: 0.1em;
  padding-bottom: 0.1em;
  padding-left: 0.1em;
  padding-right: 0.1em;
  box-sizing: border-box;
  text-align: center;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.digit-top {
  z-index: 10;
  top: 0;
  left: 0;
  right: 0;
  height: 50%;
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  pointer-events: none;
  overflow: hidden;
  position: absolute;
  background: #fff;
  padding-top: 0.1em;
  padding-bottom: 0;
  padding-left: 0.1em;
  padding-right: 0.1em;
  border-top-left-radius: 0.1em;
  border-top-right-radius: 0.1em;
  box-sizing: border-box;
  text-align: center;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  transition: background 0s linear, -webkit-transform 0s linear;
  transition: transform 0s linear, background 0s linear;
  transition: transform 0s linear, background 0s linear, -webkit-transform 0s linear;
  -webkit-transform-origin: 0 0.6em 0 !important;
          transform-origin: 0 0.6em 0 !important;
  -webkit-transform-style: preserve-3d !important;
          transform-style: preserve-3d !important;
  z-index: 20;
}
.digit-top.r {
  transition: background 0.2s linear, -webkit-transform 0.2s linear;
  transition: transform 0.2s linear, background 0.2s linear;
  transition: transform 0.2s linear, background 0.2s linear, -webkit-transform 0.2s linear;
  -webkit-transform: rotateX(90deg);
          transform: rotateX(90deg);
  background: #cccccc;
}

.digit-top2 {
  visibility: hidden;
  position: absolute;
  height: 50%;
  left: 0;
  right: 0;
  background: #cccccc;
  transition: -webkit-transform 0.2s linear;
  transition: transform 0.2s linear;
  transition: transform 0.2s linear, -webkit-transform 0.2s linear;
  line-height: 0em !important;
  top: 50% !important;
  bottom: auto !important;
  padding-top: 0;
  padding-bottom: 0.1em;
  padding-left: 0.1em;
  padding-right: 0.1em;
  border-bottom-left-radius: 0.1em;
  border-bottom-right-radius: 0.1em;
  overflow: hidden;
  text-align: center;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  transition: background 0s linear, -webkit-transform 0s linear;
  transition: transform 0s linear, background 0s linear;
  transition: transform 0s linear, background 0s linear, -webkit-transform 0s linear;
  -webkit-transform: rotateX(-90deg);
          transform: rotateX(-90deg);
  -webkit-transform-style: preserve-3d !important;
          transform-style: preserve-3d !important;
  -webkit-transform-origin: 0 0 0 !important;
          transform-origin: 0 0 0 !important;
  z-index: 20;
}
.digit-top2.r {
  visibility: visible;
  transition: background 0.2s linear 0.2s, -webkit-transform 0.2s linear 0.2s;
  transition: transform 0.2s linear 0.2s, background 0.2s linear 0.2s;
  transition: transform 0.2s linear 0.2s, background 0.2s linear 0.2s, -webkit-transform 0.2s linear 0.2s;
  -webkit-transform: rotateX(0deg);
          transform: rotateX(0deg);
  background: #fff;
}

.digit-bottom {
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  pointer-events: none;
  position: absolute;
  overflow: hidden;
  background: #fff;
  height: 50%;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9;
  line-height: 0em;
  padding-top: 0;
  padding-bottom: 0.1em;
  padding-left: 0.1em;
  padding-right: 0.1em;
  border-bottom-left-radius: 0.1em;
  border-bottom-right-radius: 0.1em;
  box-sizing: border-box;
  text-align: center;
  transition: none;
}
.digit-bottom.r {
  transition: background 0.2s linear;
  background: #cccccc;
}

.flipper-digit:after {
  content: "";
  position: absolute;
  height: 2px;
  background: rgba(0, 0, 0, 0.5);
  top: 50%;
  display: block;
  z-index: 30;
  left: 0;
  right: 0;
}

.flipper-dark {
  color: #fff;
}
.flipper-dark .flipper-delimiter {
  color: #333;
}
.flipper-dark .digit-next {
  background: #333;
}
.flipper-dark .digit-top {
  background: #333;
}
.flipper-dark .digit-top.r {
  background: black;
}
.flipper-dark .digit-top2 {
  background: black;
}
.flipper-dark .digit-top2.r {
  background: #333;
}
.flipper-dark .digit-bottom {
  background: #333;
}

.flipper-dark-labels .flipper-group label {
  color: #333;
}

    </style>
	</body>
</html>