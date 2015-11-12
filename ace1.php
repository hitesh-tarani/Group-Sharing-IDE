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
"<div><input id='Filename' type='text' placeholder='Enter_File_Name'></div>"+
"<div id='output'></div>");
    </script>
	<!--script src="../build/src-noconflict/ext-language_tools.js" type="text/javascript" charset="utf-8"></script-->
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
	editor.setValue(text["cpp"]);
	editor.gotoLine(1,0,false);
	var lineObj=editor.selection.getCursor();
	$("#test").html(lineObj.row+":"+lineObj.column);
	
	$("#editor002").change(function(){
		var lang=$("#editor002").val();
		var selected_option = $('#editor002 option:selected');
		if(lang=="c_cpp")
		{
			if(selected_option.attr("id")=="editor101")
				editor.setValue(text["cpp"]);
			else
				editor.setValue(text["c"]);
		}
		else if(lang=="python" || lang=="java")
			editor.setValue(text[lang]);/*
		else if(lang=="java")
			editor.setValue(text["java"]);*/
		//console.log(l);
		editor.session.setMode('ace/mode/'+lang);
		editor.gotoLine(1,0,false);
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
		comet.doRequest("E",lang+" "+lineObj.row+" "+line);
		console.log(lineObj.row+" "+line);
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
    var final_code=lang+" "+code;
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
		var login="<?php echo "$loginid"?>"
    	var final_code=lang+" "+login+" "+file+" "+code;
    	console.log(final_code);
		comet.doRequest("Savecode",final_code);
	}


</script>
<script type="text/javascript">
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
      parameters: { 'timestamp' : this.timestamp },
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
    $('#output').html(response['output']);
  },

  doRequest: function(request,content)
  {
    new Ajax.Request(this.url, {
      method: 'get',
      parameters: { 'msg' : request ,'content': content}
    });
  }
}
var comet = new Comet();
comet.connect();
</script>
</body>
</html>
