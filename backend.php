<?php
// session_start();
// $loginid = $_SESSION["loginid"];
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

// store new message in the file
$response = array();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$content = isset($_GET['content']) ? $_GET['content'] : '';

if ($msg != '')
{
        $a=$msg." ".$content;
        file_put_contents($filename1,$a);
	switch ($msg) {
		case 'E': //edit line
			$line=split(" ", $content,4);
			$lang=$line[0];
            $fname=$line[1];
			/*if($lang!="python")
	            $lang_file=$fname.".".$lang;
	         else
	         	$lang_file=$fname.".py";*/
			shell_exec("sed -i '".$line[2]."s/.*/".$line[3]."/' ".$fname);
            file_put_contents($timestamp,time());
            //$response['timestamp'] = time();
			//$x="sed -i '".$line[0]."s/.*/".$line[1]."/' ".$filename;
			
			break;
		case 'S': //start
            $lang=$content;
            $code=file_get_contents($lang."_default");
            if($lang!="python")
	            file_put_contents("temp.".$lang,$code);
	         else
	         	file_put_contents("temp.py",$code);
            file_put_contents($outfile,"");
            break;
      case 'L': //load file
            file_put_contents($timestamp,time());
            file_put_contents($outfile,"");
            break;
      case 'Run':
            $text=split(" ",$content,3);
            $lang=$text[0];
            $fname=$text[1];
            /*if($lang!="python")
	            $lang_file=$fname.".".$lang;
	        else
	         	$lang_file=$fname.".py";*/
            $code=$text[2];
            file_put_contents($fname,$code);
			$x=exec('compile.bat');
            
            file_put_contents($timestamp,time());
            file_put_contents('output.txt',$x);
            break;
		case 'Savecode':
            $text=split(" ",$content,3);
            $lang=$text[0];
            if($lang!="python")
	            $filenamefinal=$text[1].".".$lang;
         else
	            $filenamefinal=$text[1].".py";
            
            $code=$text[2];
            
            file_put_contents($filenamefinal,$code);
            
            file_put_contents($timestamp,time());
            //file_put_contents($outfile,$x);
            break;
		default:
			break;
	}
  	
  	die();
}

// infinite loop until the output file is not modified
$lastmodif    = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;
$currentmodif = filemtime($timestamp);
while ($currentmodif <= $lastmodif) // check if the data file has been modified
{
  usleep(10000); // sleep 10ms to unload the CPU
  clearstatcache();
  $currentmodif = filemtime($timestamp);
}

$last = file_get_contents($filename1);
$last=split(" ",$last,2);
$last_msg=$last[0];
$last_content=$last[1];
if($last_msg=='L')
{
    $lang=pathinfo($last[1],PATHINFO_EXTENSION);
    $response['Load'] = $lang." ".$last[1]." ".file_get_contents($last[1]);
}
else if($last_msg=='E')
{
    //$edit=split(" ",$last[1],4);
    $response['Edit'] = $last[1];
}
$response['output'] = file_get_contents($outfile);
$response['timestamp'] = $currentmodif;

// return a json array
echo json_encode($response);
flush();

?>
