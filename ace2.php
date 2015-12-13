<?php
session_start();
$loginid = $_SESSION["loginid"];
if($loginid=='')
{
	header("Location:bin/mbox/sessionexpired.php");
}
if($loginid=='anonymous')
{
	chdir('./bin/user_files/'.$loginid);
	$time=microtime(true);

//$timestamp=dirname(__FILE__).'/timestamp.txt';
//echo $timestamp;

	file_put_contents('timestamp.txt',$time);
	file_put_contents('output.txt',"");
}
else
{
	chdir('./bin/user_files/'.$loginid);
	$time=microtime(true);

//$timestamp=dirname(__FILE__).'/timestamp.txt';
//echo $timestamp;

	file_put_contents('timestamp.txt',$time);
	file_put_contents('output.txt',"");
	$conn_error ='could not connect.';

	$mysql_host ='localhost';
	$mysql_user ='root';
	$mysql_pass ='hitesh_1995';

	$mysql_db ='ide';


	if(!($con=mysqli_connect ($mysql_host, $mysql_user , $mysql_pass, $mysql_db)) || !mysqli_select_db($con, $mysql_db)){

		die($conn_error);

	}
}

?>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html"; charset="utf-8">
	<meta content="utf-8" http-equiv="encoding">
	<title>Ace autocompletion test</title>
	<!-- Bootstrap Core CSS -->
	<link href="bin/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="bin/css/agency.css" rel="stylesheet">

	<style type="text/css" media="screen">
		#editor {
			position: relative;
			top: 10px;
			right: 0;
			bottom:0;
			left:0px;
			width:1200px;
			height:400px;
		}
		.editor_lang{
			display:block;
			position:absolute;
			top:20px;	
			width:200px;
			height:30px;
			font-family:Arial,Helvetica,sans-serif;
			color:#635348;
			background-color:#fff;
		}
		select{
			box-sizing: border-box;
			cursor: pointer;
		}
		li{
			cursor: pointer;
		}
		#Filename{
			position:relative;
			top:-25px;
			left:72px;
		}
		#Compile_Run{
			position: relative;
			top: 20px;
		}
		#Load{
			position:relative;
			top: 50px;
		}
		#user_files_select{
			position: relative;
			top: -166px;
			left: 102px;
		}
		#Save{
			position: relative;
			top: 32px;
		}
		#output{
			position:relative;
			top: -80px;
			left: -80px;
		}
		#Share{
			position:relative;
			top:67px;
			left:-285px;
		}
		#Share_user{
			position:relative;
			top:38px;
			left:-212px;
		}
		#custom_input{
			position:relative;
			top:-80px;
			left:-220px;
		}
	</style>
</head>
<body>
	<script type="text/javascript" src="prototype.js"></script>
	<script src="ace/ace.js"></script>
	<script src="jquery.min.js"></script>
	<script src="ace/ext-language_tools.js"></script>
	<script src="ot.js"></script>
	<script src="aceadapter.js"></script>

	<script>
		var text = {cpp:"#include <iostream>\nusing namespace std;\n\nint main(){\n    //Write your code here\n    \n    return 0;\n}",c:"#include <stdio.h>\n\nint main(void) {\n    //Write your code here\n    \n    return 0;\n}",python:"#Write your Code Here\n",java:"import java.util.*;\nimport java.lang.*;\nimport java.io.*;\n\n/* Name of the class has to be \"Main\" only if the class is public. */\nclass temp\n{\n	public static void main (String[] args) throws java.lang.Exception\n	{\n		// your code goes here\n	}\n}"};
	</script>

	<section id="login">
		<div>
			<div id="editor001" class="container" style="margin-top: -130px;">
				<script>
					$("#editor001").html("<div class='row'><div class='col-md-6'><div class='form-group'><select style='width: 200px; margin-bottom:-20px' id='editor002' class='form-control'>"+
						"<option id='editor101' class='lang-select' value='c_cpp' selected>C++ 4.9.2</option>"+
						"<option id='editor102' class='lang-select' value='c_cpp'>C</option>"+
						"<option id='editor103' class='lang-select' value='python'>Python</option>"+
						"<option id='editor104' class='lang-select' value='java'>Java</option>"+
						"</select>"+"</div></div></div>"+
						"</div>"+
						"<div id='test'>1:0</div>"+
						"<div id='editor'></div>"+
						"</div>"+
						"<div id='Compile_Run'><button type='button'>Compile,Run</button></div>"+
						"<div id='Save'><button <?php if($loginid=='anonymous') echo 'disabled'; ?> type='button'>Save File</button></div>"+
						"<div id='Load'><button <?php if($loginid=='anonymous') echo 'disabled'; ?> type='button'>Load File</button></div>"+
						"<div class='col-md-3'><input <?php if($loginid=='anonymous') echo 'disabled'; ?> id='Filename' type='text' class='form-control' placeholder='Enter_File_Name'></div>"+
						"<div id='Share'><button <?php if($loginid=='anonymous') echo 'disabled'; ?> type='button'>Share File</button></div>"+
						"<div class='col-md-3'><input <?php if($loginid=='anonymous') echo 'disabled'; ?> id='Share_user' type='text' class='form-control' placeholder='Enter username to share'></div>"+
						"<div class='col-md-3'><textarea id='custom_input' type='text' style='height:160px; width:350px; resize:none;' class='form-control' placeholder='Enter custom input'></textarea></div>"+
						"<div class='col-md-3'><textarea disabled id='output' type='text' style='height:160px; width:350px; resize:none; cursor:default;' class='form-control' placeholder='The output for the program will be presented here'></textarea></div></section>");
var login = "<?php echo $loginid ?>";
</script>
<div class="row">
	<select id='user_files_select' style='width:255px;' class='form-control' <?php if($loginid=="anonymous") echo "disabled"; ?>>

		<?php
		$query="SELECT File_name FROM Files natural join Sharing WHERE Sharing.Login_id = '$loginid'"; 
		$query_run = mysqli_query($con,$query);
		while($query_row= mysqli_fetch_assoc($query_run))  
		{
			echo"<option>".$query_row['File_name']."</option>";
		}

		?>
	</select>
</div>
<script>

	var Comet = Class.create();
	Comet.prototype = {
		timestamp: 0,
		url: './backend.php',
		noerror: true,
		buffers: [],
		ack: true,
		funcrun: false,
		initialize: function() { },

		connect: function()
		{
			this.ajax = new Ajax.Request(this.url, {
				method: 'get',
				parameters: { 'login' : login ,'timestamp' : this.timestamp },
				onSuccess: function(transport) {
// handle the server response
var response = transport.responseText.evalJSON();
console.log(response);
this.comet.timestamp = response['timestamp'];
this.comet.handleResponse(response);
this.comet.noerror = true;
},
onComplete: function(transport) 
{
// send a new ajax request when this request is finished
if (!this.comet.noerror)
// if a connection problem occurs, try to reconnect each 5 seconds
setTimeout(function(){ comet.connect() }, 5000); 
else
{
	this.comet.connect();
	this.comet.ack=true;
}
this.comet.noerror = false;
}
});
			this.ajax.comet = this;
		},

		disconnect: function()
		{
		},

		handleResponse: function(response)
		{
			if(response['Load'])
			{
				var data=response['Load'];
				console.log(data);
				var ind=data.indexOf(' ');
				var lang=data.substr(0,ind);
				text_lang_ext=lang; 
				if(lang=="c"||lang=="cpp")
					lang="c_cpp";
				else if(lang=="py")
					lang="python";
				$("div.editor001 select").val(lang);
				var rem=data.substr(ind+1);
				var ind=rem.indexOf(' ');
				fname=rem.substr(0,ind);
				var code=rem.substr(ind+1);
				editor2.onload=true;        
				editor.setValue(code);
        //console.log(code);
        editor.gotoLine(1,0,false);
        editor2.onload=false;
    }
    else if(response['Edit'])
    {
    	var data=response['Edit'];
        //console.log(data);
        var currLine=editor.selection.getCursor();  
        var lang1=data[0];
        var tempfile=data[1];
        var operation=data[2];
        //console.log(fname+","+tempfile);
        //console.log(text_lang_ext+","+lang);
        if(tempfile==fname && text_lang_ext==lang1)
        {
        	var code=editor.getValue();
        	var newop = ot.TextOperation.fromJSON(JSON.parse(operation));
            //console.log(newop);
            editor2.applyOperationToACE(newop);
            /*var line=data[2];
            var code=data[3];
            if(line=="whole")
            {
                editor.setValue(code);
                editor.gotoLine(currLine.row+1,currLine.column,true);
            }
            else
            {
                var Range = ace.require("ace/range").Range;
                var range=new Range(line-1, 0, line-1, Number.MAX_VALUE);
                editor.session.replace(range, code);
                console.log(range);
            }*/
        }
        //console.log(response['Edit']);*/
    }    
    $('#output').html(response['output']);
},

processBuffer: function()
{
	this.funcrun=true;
	while(this.buffers.length!=0)
	{
		if(this.ack==true)
		{
			this.ack=false;
			var that=this;
			console.log(this.buffers);
            //setTimeout(function(){ new Ajax.Request(that.url,that.buffers.shift());    }, 1000);
            new Ajax.Request(that.url,that.buffers.shift());
        }
        else
        {
        	var that=this;
        	setTimeout(function(){ that.processBuffer();    }, 100);
        	break;
        }
    }
    this.funcrun=false;
},

doRequest: function(request,content)
{
	console.log("1 "+this.ack);
    /*this.buffers.push({method: 'get',
    parameters: { 'login' : login ,'msg' : request ,'content': content}
    });
    console.log(this.buffers);
    new Ajax.Request(this.url,this.buffers.pop());*/
    this.buffers.push({method: 'get',
    	parameters: { 'login' : login ,'msg' : request ,'content': content}
    });
    if(!this.funcrun)
    {
    	this.processBuffer();            
    }
    /*if(!this.ack)
    {
        //wait(1000);
        //setTimeout(function(){ comet.doRequest(request,content) }, 2000);
        this.buffers.push({method: 'get',
            parameters: { 'login' : login ,'msg' : request ,'content': content}
            });
            console.log(this.buffers);
    }
    else
    {
        
    }*/
}
}
var comet = new Comet();
console.log(this.comet.buffers);
comet.connect();
</script><!--script src="../build/src-noconflict/ext-language_tools.js" type="text/javascript" charset="utf-8"></script-->
<script>    	
	ace.require("ace/ext/language_tools");
	var editor = ace.edit("editor");

	editor.session.setMode("ace/mode/c_cpp");
	editor.setTheme("ace/theme/monokai");
	editor.setOptions({
		enableBasicAutocompletion: true,
		enableSnippets: true,
		enableLiveAutocompletion: true
	});
	var fname="temp";
	var lang="cpp";
	var text_lang_ext="cpp";
	editor.setValue(text["cpp"]);
//comet.doRequest("E",lang+" "+lineObj.row+" "+line);
/*comet.doRequest("L","ace.cpp");
fname="ace";*/
editor.gotoLine(1,0,false);
var editor2=new ACEAdapter(editor);
comet.doRequest("S",text_lang_ext+" "+fname+"."+text_lang_ext);
var lineObj=editor.selection.getCursor();
$("#test").html(lineObj.row+":"+lineObj.column);

$("#editor002").change(function(){
	lang=$("#editor002").val();
	var selected_option = $('#editor002 option:selected');
	if(lang=="c_cpp")
	{
		if(selected_option.attr("id")=="editor101")
		{
			text_lang_ext="cpp";
			lang="cpp";
		}
		else
		{
			text_lang_ext="c";
			lang="c";
		}
	}
	else if(lang=="python")
		text_lang_ext="py";
	else if(lang=="java")
		text_lang_ext="java";

        /*if(lang=="c_cpp")
        {
        if(selected_option.attr("id")=="editor101")
        {
        editor.setValue(text["cpp"]);
        //lang="cpp";
        }
        else
        {
        editor.setValue(text["c"]);
        //lang="c";
        }
        }
        else if(lang=="python" || lang=="java")*/
        	editor.setValue(text[lang]);
        //console.log(l);
        editor.session.setMode('ace/mode/'+lang);
        editor.gotoLine(1,0,false);
        comet.doRequest("S",lang+" "+fname+"."+text_lang_ext);
    });

editor.session.selection.on('changeCursor',function(){
	var lineObj=editor.selection.getCursor();
	lineObj.row++;
	$("#test").html(lineObj.row+":"+lineObj.column);
        //console.log(line.length);
    });

var total_lines=editor.session.getLength();

/*editor.session.on('change',function(e){
        var edit=e.lines;
        console.log(e);
        var lineObj=editor.selection.getCursor();
        console.log(editor.session.doc.positionToIndex(lineObj));
        var line=editor.session.getLine(lineObj.row);
        lineObj.row++;
        var lang=$("#editor002").val();
        var selected_option = $('#editor002 option:selected');
        if(lang=="c_cpp")
        {
        if(selected_option.attr("id")=="editor101")
        lang="cpp";
        else
        lang="c";
        }
        //comet.doRequest("L","ace.cpp");
        if(editor.session.getLength()!=total_lines)
        {
        comet.doRequest("E",lang+" "+fname+"."+text_lang_ext+" whole "+editor.getValue());
        total_lines=editor.session.getLength();
        }
        else
        {
            comet.doRequest("E",lang+" "+fname+"."+text_lang_ext+" "+lineObj.row+" "+line);
            console.log(lang+" "+fname+"."+text_lang_ext+" "+lineObj.row+" "+line);
        }
        console.log(editor.session.getLength());
    });*/

$("#Compile_Run").click(function(){
	compile();
});

function compile(){
	var input=$('textarea#custom_input').val();
    /*var lang=$("#editor002").val();
    var selected_option = $('#editor002 option:selected');
    if(lang=="c_cpp")
    {
        if(selected_option.attr("id")=="editor101")
            lang="cpp";
        else
            lang="c";
    }*/
    var code=editor.getValue();
    var final_code=lang+"`"+fname+"."+text_lang_ext+"`"+code+"`"+input;
    console.log(final_code);
    comet.doRequest("Run",final_code);
}

$("#Save").click(function(){
	Save();
});

function Save(){
    /*var lang=$("#editor002").val();
    var selected_option = $('#editor002 option:selected');
    if(lang=="c_cpp")
    {
        if(selected_option.attr("id")=="editor101")
            lang="cpp";
        else
            lang="c";
    }*/
    var file=$("#Filename").val();
    var code=editor.getValue();

    var final_code=lang+" "+file+"."+text_lang_ext+" "+code;
    fname=file;
    console.log(final_code);
    comet.doRequest("Savecode",final_code);
}
$("#Load").click(function(){
	var file=$("#user_files_select").val();
	comet.doRequest("L",file);
});
$("#Share").click(function(){
        //var file=$("#Filename").val();
        var user_login=$("#Share_user").val();
        comet.doRequest("Share",user_login+" "+fname+"."+text_lang_ext);
    });


</script>

</body>
</html>
