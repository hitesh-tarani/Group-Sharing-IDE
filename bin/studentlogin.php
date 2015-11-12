<?php
session_start();
$loginid = $_SESSION["loginid"];
if($loginid=='')
{
	header("Location:mbox/sessionexpired.php");
}
$conn_error ='could not connect.';

$mysql_host ='localhost';
$mysql_user ='root';
$mysql_pass ='';

$mysql_db ='IDE';


if(!($con=mysqli_connect ($mysql_host, $mysql_user , $mysql_pass, $mysql_db)) || !mysqli_select_db($con, $mysql_db)){

	die($conn_error);

}

?>



<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Online MCQ Exam</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/agency.css" rel="stylesheet">
	<link href="css/table.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body id="page-top" class="index">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">Online MCQ Exam</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#marks">View marks</a>
                    </li>
                    <li>
                        <a href="mcqexam.php">Give Exam</a>
                    </li>
                    <li>
                        <a href="home.php">Logout</a>
                    </li>

                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="intro-text">
                <div style="color: #185875" class="intro-lead-in">Online MCQ Exam!</div>
				<style>
				h6{
				color: #185875;
				font-size:50px;
				}
				</style>
                <h6>Welcome, <?php echo"$loginid";?>(Student)</h6>
                <a href="#marks" class="page-scroll btn btn-xl">View Marks</a>
            </div>
        </div>
    </header>


    <!-- Marks -->
	
<section id="marks">


        
        
    <center><h1><span class="blue">Marksheet</span></h1></center>


    <table class="container">
        <thead>
            <tr>
                <th><h1>Exam no.</h1></th>
                <th><h1>Marks</h1></th>
            </tr>
        </thead>
        <tbody>
		<?php

		$query="SELECT `Marks` from `marks` where `User_id`='$loginid'"; 
		$query_run = mysql_query($query) ;
		$i=1;
		while($query_row= mysql_fetch_array($query_run))  
		{
			echo"<tr>";
            echo"<td>$i</td>";
            echo"<td>".$query_row['Marks']."</td>";
            echo"</tr>";
			
			$i++;
			}
	?>
        </tbody>
    </table>        
           
				
        
	

    </section>

        
 <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Plugin JavaScript -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="js/classie.js"></script>
    <script src="js/cbpAnimatedHeader.js"></script>
    <!-- Contact Form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <!--<script src="js/contact_me.js"></script>-->
    <!-- Custom Theme JavaScript -->
    <script src="js/agency.js"></script>
</body>

</html>
