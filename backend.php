
<?php
// session_start();
// $loginid = $_SESSION["loginid"];
$conn_error ='could not connect.';

$mysql_host ='localhost';
$mysql_user ='root';
$mysql_pass ='hitesh_1995';

$mysql_db ='ide';


if(!($con=mysqli_connect ($mysql_host, $mysql_user , $mysql_pass, $mysql_db)) || !mysqli_select_db($con, $mysql_db)){

    die($conn_error);

}

$loginid=$_GET['login'];
chdir('./bin/user_files/'.$loginid);
$currentdirectory=getcwd();
//shell_exec('sudo chmod -R 0777 '.$currentdirectory);
$filename  = 'temp.cpp';
$filename1  = 'data.txt';
$outfile  = 'output.txt';
//$timestamp = dirname(__FILE__).'/timestamp.txt';
$timestamp='timestamp.txt';
//file_put_contents($filename,1);
$back_dir=dirname(__FILE__);
// store new message in the file
$response = array();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$content = isset($_GET['content']) ? $_GET['content'] : '';

if ($msg != '')
{
    $a=$msg." ".$content;
    file_put_contents($filename1,$a);
    switch ($msg) {
        case 'E': //edit line or whole
            $line=split(" ", $content,3);
            $lang=$line[0];
            $fname=$line[1];
            //sleep(1);
            /*if($lang!="python")
              $lang_file=$fname.".".$lang;
              else
              $lang_file=$fname.".py";*/
            $query="SELECT File_id FROM Files natural join Sharing WHERE Files.File_name='$fname' and Sharing.Login_id='$loginid'";
            $query_run = mysqli_query($con,$query) ;
            $query_num_rows= mysqli_num_rows($query_run);
            file_put_contents("data.txt","Edit ".$content);
            file_put_contents("timestamp.txt",microtime(true));
            if($query_num_rows>0)
            {
                $prevdir=getcwd();
                chdir('./../');
                $tempdir=getcwd();
                $temptime=microtime(true);
                while($row=mysqli_fetch_assoc($query_run))
                {
                    $tempfileid=$row["File_id"];
                }
                $query4="select Login_id from Sharing where File_id='$tempfileid' and Login_id!='$loginid'"; 
                $query_run4 = mysqli_query($con,$query4) ;
                $query_num_rows= mysqli_num_rows($query_run);

                while($row=mysqli_fetch_assoc($query_run4))
                {
                    $templogin=$row["Login_id"];
                    echo $templogin;
                    chdir($tempdir.'/'.$templogin);
                    $logintime=file_get_contents("timestamp.txt");
                    if($temptime-$logintime<600)
                    {
                        file_put_contents("data.txt","E ".$content);
                        file_put_contents("timestamp.txt",microtime(true));
                    }
                    else
                    {
                        $fileload=file_get_contents($fname);
                        //echo $templogin;
                    }
                    /*echo "
                        <!DOCTYPE html>
<meta http-equiv='Content-Type' content='text/html'; charset='utf-8'>

<meta content='utf-8' http-equiv='encoding'>
                    <script src='ot.js'></script>
                    <script>var oper=".$line[2].";console.log(oper);JSON.parse(JSON.stringify('1'));
                    var op=new ot.TextOperation();
                    var operation = ot.TextOperation.fromJSON(JSON.parse('".$line[2]."'));console.log(operation);
                    //console.log(op.apply(".$templogin."));
                    console.log(op.apply('Hello'));
                    </script>";*/
                }
            }
            /*if($line[2]=="whole")
                file_put_contents($fname,$line[3]);
            else
                shell_exec("sed -i '".$line[2]."s`.*`".$line[3]."`' ".$fname);
            file_put_contents($timestamp,microtime(true));*/
            //$x="sed -i '".$line[0]."s/.*/".$line[1]."/' ".$filename;

            break;

        case 'S': //start
            $line=split(" ", $content,2);
            $lang=$line[0];
            $fname=$line[1];
            //$code=file_get_contents($back_dir."/".$lang."_default");
            shell_exec("cp ".$back_dir."/".$lang."_default ".$fname);
            /*if($lang!="python")
                file_put_contents("temp.".$lang,$code);
            else
                file_put_contents("temp.py",$code);*/
            file_put_contents($outfile,"");
            break;

        case 'L': //load file
            file_put_contents($timestamp,microtime(true));
            file_put_contents($outfile,"");
            break;

        case 'Run': //Compile and run code
            $text=split("`",$content,4);
            $lang=$text[0];
            $fname=$text[1];
            /*if($lang!="python")
              $lang_file=$fname.".".$lang;
              else
              $lang_file=$fname.".py";*/
            $code=$text[2];
            $input=$text[3];
            //echo $text." Hello ".$content;
            file_put_contents("input.dat", $input);
            file_put_contents($fname,$code);
            shell_exec("bash ".$back_dir."/".$lang."_run ".$fname);
            //echo "this is ".$x." code";
            file_put_contents($timestamp,microtime(true));
            //file_put_contents('output.txt',$x);
            break;

        case 'Savecode': //save code in file
            $text=split(" ",$content,3);
            $lang=$text[0];
            /*if($lang!="python")
              $filenamefinal=$text[1].".".$lang;
              else
              $filenamefinal=$text[1].".py";*/
            $filenamefinal=$text[1];
            $code=$text[2];
            $query="SELECT File_id FROM Files natural join Sharing WHERE Files.File_name='$filenamefinal' and Sharing.Login_id='$loginid'";
            $query_run = mysqli_query($con,$query) ;
            $query_num_rows= mysqli_num_rows($query_run);

            if($query_num_rows==0){
                //insert into query
                $query2="insert into Files (File_name) values ('$filenamefinal')"; 
                $query_run2 = mysqli_query($con,$query2) ;

                $query3="insert into Sharing values ((select MAX(File_id) from Files),'$loginid')"; 
                $query_run3 = mysqli_query($con,$query3) ;

                file_put_contents($filenamefinal,$code);
                file_put_contents($timestamp,microtime(true));
            }
            else
            {
                $prevdir=getcwd();
                chdir('./../');
                $tempdir=getcwd();
                $temptime=microtime(true);
                while($row=mysqli_fetch_assoc($query_run))
                {
                    $tempfileid=$row["File_id"];
                }
                $query4="select Login_id from Sharing where File_id='$tempfileid'"; 
                $query_run4 = mysqli_query($con,$query4) ;

                while($row=mysqli_fetch_assoc($query_run4))
                {
                    $templogin=$row["Login_id"];
                    chdir($tempdir.'/'.$templogin);
                    file_put_contents($filenamefinal,$code);

                    file_put_contents($timestamp,$temptime);
                }
                chdir($prevdir);
                //file_put_contents($outfile,$x);
            }
            break;

        case 'Share':
            $text=split(" ",$content,2);
            $templogin=$text[0];
            $tempfile=$text[1];
            $query="SELECT File_id FROM Files natural join Sharing WHERE Files.File_name='$tempfile' and Sharing.Login_id='$loginid'";
            $query_run = mysqli_query($con,$query) ;
            $query_num_rows= mysqli_num_rows($query_run);

            if($query_num_rows>0)
            {
                $row=mysqli_fetch_assoc($query_run);
                $tempfileid=$row["File_id"];
                $query4="insert into Sharing values ('$tempfileid','$templogin')";
                $query_run4 = mysqli_query($con,$query4);

                $code=file_get_contents($tempfile);
                $prevdir=getcwd();
                chdir('../');
                $tempdir=getcwd();
                chdir($templogin);
                file_put_contents($tempfile,$code);
                chdir($prevdir);
            }
            break;
        default:break;
    }

    die();
}

// infinite loop until the output file is not modified
$lastmodif    = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;
$currentmodif = file_get_contents($timestamp);
while ($currentmodif <= $lastmodif) // check if the data file has been modified
{
    usleep(1000); // sleep 1ms to unload the CPU
    clearstatcache();
    $currentmodif = file_get_contents($timestamp);
}

$last = file_get_contents($filename1);
$last=split(" ",$last,2);
$last_msg=$last[0];
$last_content=$last[1];
if($last_msg=='L')
{
    $load=pathinfo($last[1]);
    $response['Load'] = $load['extension']." ".$load['filename']." ".file_get_contents($last[1]);
}
else if($last_msg=='E')
{
    $edit=split(" ",$last[1],3);
    $fname=pathinfo($edit[1],PATHINFO_FILENAME);
    $response['Edit'] = array($edit[0],$fname,$edit[2]);
}
$response['output'] = file_get_contents($outfile);
$response['timestamp'] = $currentmodif;

// return a json array
echo json_encode($response);
flush();

?>
