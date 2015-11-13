<?php
session_start();
$loginid = $_SESSION["loginid"];
if($loginid=='')
{
  	header("Location:mbox/sessionexpired.php");
}
chdir('./bin/user_files/'.$loginid);
$time=time();

//$timestamp=dirname(__FILE__).'/timestamp.txt';
//echo $timestamp;

file_put_contents('timestamp.txt',$time);

$conn_error ='could not connect.';

$mysql_host ='localhost';
$mysql_user ='root';
$mysql_pass ='hitesh_1995';

$mysql_db ='ide';


if(!($con=mysqli_connect ($mysql_host, $mysql_user , $mysql_pass, $mysql_db)) || !mysqli_select_db($con, $mysql_db)){

	die($conn_error);

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ace autocompletion test</title>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 50px;
            right: 0;
						bottom:0;
						left:40px;
						width:1200px;
						height:400px;
		 }
		.editor_lang{
			display:block;
			position:relative;	
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
        #Load{
            position:relative;
            top:420px;
        }
		#Compile_Run{
			position: relative;
			top: 420px;
		}
		#Save{
			position: relative;
			top: 420px;
		}
		#Filename{
			position: relative;
			top: 420px;
		}
        #output{
            position:relative;
            top: 440px;
            left: 100px;
        }
    </style>
</head>
<body>
<script type="text/javascript" src="prototype.js"></script>
<script src="ace.js"></script>
<script src="jquery.min.js"></script>
<script src="ext-language_tools.js"></script>

<script>
		var text = {cpp:"#include <iostream>\nusing namespace std;\n\nint main(){\n    //Write your code here\n    \n    return 0;\n}",c:"#include <stdio.h>\n\nint main(void) {\n    //Write your code here\n    \n    return 0;\n}",python:"#Write your Code Here\n",java:"import java.util.*;\nimport java.lang.*;\nimport java.io.*;\n\n/* Name of the class has to be \"Main\" only if the class is public. */\nclass Test\n{\n	public static void main (String[] args) throws java.lang.Exception\n	{\n		// your code goes here\n	}\n}"};
</script>
		
<!--script>
		var c_plus_plus_text="#include <iostream>\nusing namespace std;\n\nint main(){\n		//Write your code here\n        return 0;\n}";
		var c_text="#include <stdio.h>\n\nint main(void) {\n	// your code goes here\n	return 0;\n}";
		var python_text="Write your Code Here"
		var java_text="import java.util.*;\nimport java.lang.*;\nimport java.io.*;\n\n/* Name of the class has to be \"Main\" only if the class is public. */\nclass Test\n{\n	public static void main (String[] args) throws java.lang.Exception\n	{\n		// your code goes here\n	}\n}";
</script-->

<div style="width:500px;height:500px">
	<div id="editor001" class="editor_lang">
	<script>
	$("#editor001").html("<select id='editor002' class='editor_lang'>"+
			"<option id='editor101' class='lang-select' value='c_cpp' selected>C++ 4.9.2</option>"+
			"<option id='editor102' class='lang-select' value='c_cpp'>C</option>"+
			"<option id='editor103' class='lang-select' value='python'>Python</option>"+
			"<option id='editor104' class='lang-select' value='java'>Java</option>"+
		"</select>"+
	"</div>"+
	"<div id='test'>0:0</div>"+
    "<div id='editor'></div>"+
"</div>"+
"<div id='Compile_Run'><button type='button'>Compile,Run</button></div>"+
"<div id='Save'><button type='button'>Saveas</button></div>"+
"<div id='Load'><button type='button'>Load File</button></div>"+
"<div><input id='Filename' type='text' placeholder='Enter_File_Name'></div>"+
"<div id='output'></div>");
var login = "<?php echo $loginid ?>";
console.log("Login: "+login);
    </script>
	<script>
	
	var Comet = Class.create();
Comet.prototype = {
  timestamp: 0,
  url: './backend.php',
  noerror: true,

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
      onComplete: function(transport) {
        // send a new ajax request when this request is finished
        if (!this.comet.noerror)
          // if a connection problem occurs, try to reconnect each 5 seconds
          setTimeout(function(){ comet.connect() }, 5000); 
        else
          this.comet.connect();
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
        temp_lang_ext=lang; 
        if(lang=="c"||lang=="cpp")
            lang="c_cpp";
        else if(lang=="py")
            lang="python";
        $("div.editor001 select").val(lang);
        var rem=data.substr(ind+1);
        var ind=rem.indexOf(' ');
        fname=rem.substr(0,ind);
        var code=rem.substr(ind+1);
        editor.setValue(code);
        console.log(code);
        editor.gotoLine(1,0,false);
    }
    else if(response['Edit'])
    {
        /*var data=reponse['Edit'];
        var arr=data.split(' ');
        var Range = require("ace/range").Range;
        var row = arr[2];
        var newText = arr[3];
        var range=new Range(row, 0, row, Number.MAX_VALUE);
        editor.session.replace(range, newText);
        console.log(range);*/
        console.log(response['Edit']);
    }    
    $('#output').html(response['output']);
  },

  doRequest: function(request,content)
  {
    new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'login' : login ,'msg' : request ,'content': content}
    });
  }
}
var comet = new Comet();
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
    var text_lang_ext="cpp";
    editor.setValue(text["cpp"]);
    //comet.doRequest("E",lang+" "+lineObj.row+" "+line);
    /*comet.doRequest("L","ace.cpp");
    fname="ace";*/
	editor.gotoLine(1,0,false);
	var lineObj=editor.selection.getCursor();
	$("#test").html(lineObj.row+":"+lineObj.column);
	
	$("#editor002").change(function(){
		var lang=$("#editor002").val();
		var selected_option = $('#editor002 option:selected');
		if(lang=="c_cpp")
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
		else if(lang=="python" || lang=="java")
			editor.setValue(text[lang]);
		//console.log(l);
		editor.session.setMode('ace/mode/'+lang);
		editor.gotoLine(1,0,false);
        if(lang=="c_cpp")
		{
			if(selected_option.attr("id")=="editor101")
				text_lang_ext="cpp";
			else
				text_lang_ext="c";
		}
        else if(lang=="python")
            text_lang_ext="py";
	});
	
	editor.session.selection.on('changeCursor',function(){
		var lineObj=editor.selection.getCursor();
		$("#test").html(lineObj.row+":"+lineObj.column);
		//console.log(line.length);
	});
	
	editor.session.on('change',function(){
		var lineObj=editor.selection.getCursor();
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
		comet.doRequest("E",lang+" "+fname+"."+text_lang_ext+" "+lineObj.row+" "+line);
		console.log(lang+" "+fname+"."+text_lang_ext+" "+lineObj.row+" "+line);
	});

    $("#Compile_Run").click(function(){
        compile();
    });

	function compile(){
    var lang=$("#editor002").val();
		var selected_option = $('#editor002 option:selected');
		if(lang=="c_cpp")
		{
			if(selected_option.attr("id")=="editor101")
				lang="cpp";
			else
				lang="c";
		}
	var code=editor.getValue();
    var final_code=lang+" "+fname+" "+code;
    console.log(final_code);
	comet.doRequest("Run",final_code);
}

$("#Save").click(function(){
        Save();
    });

	function Save(){
	    var lang=$("#editor002").val();
		var selected_option = $('#editor002 option:selected');
		if(lang=="c_cpp")
		{
			if(selected_option.attr("id")=="editor101")
				lang="cpp";
			else
				lang="c";
		}
		var file=$("#Filename").val();
		var code=editor.getValue();

		var final_code=lang+" "+file+" "+code;
    	console.log(final_code);
		comet.doRequest("Savecode",final_code);
	}
$("#Load").click(function(){
        var file=$("#Filename").val();
        comet.doRequest("L",file);
    });

</script>

</body>
</html>
