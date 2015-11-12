<?php
session_start();
$loginid = $_SESSION["loginid"];
if($loginid=='')
{
	session_destroy();
	header("Location:mbox/sessionexpired.php");
}
$conn_error ='could not connect.';

$mysql_host ='localhost';
$mysql_user ='root';
$mysql_pass ='';

$mysql_db ='Online_Exam';


if(!mysql_connect ($mysql_host, $mysql_user , $mysql_pass) || !mysql_select_db($mysql_db)){ 

	die($conn_error);

}

?>

<html>
<head>
    <!-- Custom CSS -->
<link href="css/exam.css" rel="stylesheet">

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>
MCQ Exam
</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script src="js/jquery.min.js"></script>
</head>
<h1>MCQ Exam , Welcome <?php echo"$loginid"; ?></h1>
<body>

<?php

if($_SESSION['check']==2){
	$correct=0;
	$i=1;
	while($i<5){      

		$var11=$_SESSION["Option_A.$i"];
		$var22=$_SESSION["Option_B.$i"];
		$var33=$_SESSION["Option_C.$i"];
		$var44=$_SESSION["Option_D.$i"];

		

		print '<form action="mcqexam.php" method="post">';
		//BEGINNING OF QUESTION ONE
		echo "<div class='x'>"; 
		echo "Q"; echo $i; echo"."; echo "&nbsp;&nbsp;";
		echo $_SESSION["Question.$i"];
		echo "</div>";
		if ($_POST["answer$i"]=="A")
		{
			echo "<div class='squaredTwo'>";
			echo "<input class='x' type='radio' checked disabled id='answer1$i' name='answer$i' value='A'/>";
			echo "<label for='answer1$i'><div class='y'>$var11</div></label>";
			echo "</div>";
		}
		else
		{
			echo "<div class='squaredTwo'>";
			echo "<input class='x' type='radio' disabled name='answer$i' id='answer1$i' value='A'/>";
			echo "<label for='answer1$i'><div class='y'>$var11</div></label>";
			echo "</div>";
		}
		if ($_POST["answer$i"]=="B")
		{
			echo "<div class='squaredTwo'>";
			echo "<input type='radio' checked disabled name='answer$i' id='answer2$i' value='B'/>";
			echo "<label for='answer2$i'><div class='y'>$var22</div></label>";
			echo "</div>";
		}
		else
		{
			echo "<div class='squaredTwo'>";
			echo "<input type='radio' disabled name='answer$i' id='answer2$i' value='B'/>";
			echo "<label for='answer2$i'><div class='y'>$var22</div></label>";
			echo "</div>";
		}
		if ($_POST["answer$i"]=="C")
		{
			echo "<div class='squaredTwo'>";
			echo "<input type='radio' checked disabled name='answer$i' id='answer3$i' value='C'/>";
			echo "<label for='answer3$i'><div class='y'>$var33</div></label>";
			echo "</div>";
		}
		else
		{
			echo "<div class='squaredTwo'>";	
			echo "<input type='radio' disabled name='answer$i' id='answer3$i' value='C'/>";
			echo "<label for='answer3$i'><div class='y'>$var33</div></label>";
			echo "</div>";
		}
		if ($_POST["answer$i"]=="D")
		{
			echo "<div class='squaredTwo'>";
			echo "<input type='radio' checked disabled name='answer$i' id='answer4$i' value='D'/>";
			echo "<label for='answer4$i'><div class='y'>$var44</div></label>";
			echo "</div>";
		}
		else
		{
			echo "<div class='squaredTwo'>";
			echo "<input type='radio' disabled name='answer$i' id='answer4$i' value='D'/>";
			echo "<label for='answer4$i'><div class='y'>$var44</div></label>";
			echo "</div>";
		}
		if($_POST["answer$i"]==$_SESSION["Answer.$i"])
		$correct++;
		echo '<div class="x">';
		echo 'Correct answer='.$_SESSION["Answer.$i"].'<br>'.'<br>';
		echo'</div>';
		$i++;

	}
	echo '<div class="x"><center>';
	print "Your score is $correct/4.<br/><br/>";
	echo'</center></div>';
	print '</form>';

	
	$query5="insert into `marks`(`User_id`,`Marks`) values('$loginid',$correct)";
	if($query_run5 = mysql_query($query5)){
	echo 'successfully added';
	
	}else{echo 'failed';}	
	
	
	session_destroy();
	    echo "<a href='home.php' class='btn btn-xl'>Home</a>";
	
	
}

else{
	
	      echo "<div>
                <div class='special'>
                    <div id='counter'>
                        
                    </div>

                    <script type='text/javascript' src='js/C3counter.js'></script>
                    <script type='text/javascript'>
                        // Default options
                        C3Counter('counter', { startTime: 60 });
                    </script>
                </div>
        </div>";

	$query="CREATE TEMPORARY TABLE IF NOT EXISTS temp AS (SELECT * FROM Question_bank ORDER BY RAND() limit 4)";

	$query_run = mysql_query($query) ;
	if($query_run){

		$query2="SELECT * FROM temp ";

		$query_run2 = mysql_query($query2) ;

		$query_num_rows2= mysql_num_rows($query_run2);

		if($query_num_rows2==4){

		}else{ echo 'value not selected from temp';}


	}else { echo 'table not created';}




	$i=1;
	while($query_row2= mysql_fetch_assoc($query_run2)){      

		$_SESSION["Answer.$i"]=$query_row2['Answer'];
		$_SESSION["Question.$i"]=$query_row2['Question'];
		$var=$query_row2['Sr_no'];
		$query3= " SELECT * FROM `Option` WHERE  `Sr_no` = '$var' "; 
		$query_run3 = mysql_query($query3);
		echo mysql_error();
		if($query_run3)
		$query_num_rows3= mysql_num_rows($query_run3);
		
		if($query_num_rows3==1){
			
			$query_row3= mysql_fetch_assoc($query_run3);

			$_SESSION["Option_A.$i"]=$query_row3['Option_A'];
			$_SESSION["Option_B.$i"]=$query_row3['Option_B'];
			$_SESSION["Option_C.$i"]=$query_row3['Option_C'];
			$_SESSION["Option_D.$i"]=$query_row3['Option_D'];

			$var1=$_SESSION["Option_A.$i"];
			$var2=$_SESSION["Option_B.$i"];
			$var3=$_SESSION["Option_C.$i"];
			$var4=$_SESSION["Option_D.$i"];
		}
		else {  echo 'query not run';}
		print '<form action="mcqexam.php" method="post">';
		//BEGINNING OF QUESTION ONE
		echo "<div class='x'>";
		echo "Q"; echo $i; echo"."; echo "&nbsp;&nbsp;"; 
		echo $query_row2['Question'].'</br>';
		echo "</div>";
		echo "<div class='squaredTwo'>"; 
		echo "<input type='radio' id='answer1$i' name='answer$i' value='A'/>";
		echo "<label for='answer1$i'><div class='y'>$var1</div></label>";
		echo "</div>";
		echo "<div class='squaredTwo'>";
		echo "<input type='radio' id='answer2$i' name='answer$i' value='B'/>";
		echo "<label for='answer2$i'><div class='y'>$var2</div></label>";
		echo "</div>";
		echo "<div class='squaredTwo'>";
		echo "<input type='radio' id='answer3$i' name='answer$i' value='C'/>";
		echo "<label for='answer3$i'><div class='y'>$var3</div></label>";
		echo "</div>";
		echo "<div class='squaredTwo'>";
		echo "<input type='radio' id='answer4$i' name='answer$i' value='D'/>";
		echo "<label for='answer4$i'><div class='y'>$var4</div></label>";
		echo "</div>";
		$i++;
		
	}

	$_SESSION['check']=2;
	echo "<div class='col-lg-12 text-center'>";
	echo "<button name='submitexam' id='submitexam' type='submit' class='btn btn-xl'>Submit Exam</button>";
    echo "</div>";
	print "</form>";
}

?>
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