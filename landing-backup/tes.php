
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
	<link href='http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" media="all" href="css/style.css" />
	<script type="text/javascript" src="js/modernizr.js"></script>

</head>

<body>


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

					<div><img src="img/content.png" ></div>
					
					<!-- Modal page toggle -->
					<div class="modal-toggle" >
					    <?php 
					           
					          if(isset($_POST['send'])){
					            	$koneksi=mysqli_connect("localhost","ysgcom_user","vickyrolanda","ysgcom_db");

                        		$name=$_POST['nama'];
                        		$email=$_POST['email'];
                        		$insert=mysqli_query($koneksi,"INSERT INTO `user` (`id`, `name`, `email`) VALUES (NULL, '$name', '$email')");
                        		if($insert){
                        			echo "<div >thank you for your submission</span>";
                        		}else{
                        			echo "Error.";
                        		}	
	}
					    ?>
						<form action="" method="POST">
							<input type="text" name="nama" value="" placeholder="Name" required>
							<input type="email" name="email" value="" placeholder="Enter your email" required>
							<div>
							<input type="submit" name="send" value="SUBMIT NOW">
							</div>
						</form>
					</div>

					<!-- Social links -->
					<div class="social">
						<p style="color: #fff;font-family: sans-serif;margin-bottom: -9px;">Follow Us</p>
						 <a href="#"><img src="img/ig.png" ></a>
						 <a href="#"><img src="img/twitter.png" ></a>
						<a href="#"><img src="img/fb.png" ></a>
					</div>
				</section>
			</div>
		</div>

		

	</div>



	<!-- Included JS Files -->
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>

</body>
</html>