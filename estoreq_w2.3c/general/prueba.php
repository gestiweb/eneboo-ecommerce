<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>3-col layout via CSS</title>
	
<style type="text/css">
/* <![CDATA[ */

	body {
		margin:0; padding:0;
		font:11pt/1.5 sans-serif;
		}

	#left {
		float:left;
		width:150px;
		margin:0; padding:0;
		background:url("corner.gif") top right no-repeat;
		font-size:80%;
		}
	#right {
		float:right;
		width:150px;
		margin:0; padding:0;
		background:url("corner.gif") top right no-repeat;
		font-size:80%;
		}
	#middle {
		margin:0 150px;
		background:yellow;
		font-size:80%;
		}
	.column-in {
		margin:0; padding:0.5em 1em;
		}
	.cleaner {
		clear:both;
		height:1px;
		font-size:1px;
		border:none;
		margin:0; padding:0;
		background:transparent;
		}
		
	h1,h2,h3,h4 { margin: 0.2em 0 }
	p { margin: 0.5em 0 }
	a { color:black }
	
	.copy { text-align:center; font-size:80% }

/* ]]> */
</style>

</head>
<body>


	<div id="left">
	This is content of the LEFT column. It can be <a href="javascript:toggleContent('lccont',1)">short</a>, <a href="javascript:toggleContent('lccont',3)">longer</a> or <a href="javascript:toggleContent('lccont',10)">very long</a>.
	</div>

	<div id="right">
	This is content of the RIGHT column. It can be <a href="javascript:toggleContent('rccont',1)">short</a>, <a href="javascript:toggleContent('rccont',3)">longer</a> or <a href="javascript:toggleContent('rccont',10)">very long</a>.
	</div>

	<div id="middle">
	This is content of the MIDDLE column. It can be <a href="javascript:toggleContent('mccont',1)">short</a>, <a href="javascript:toggleContent('mccont',10)">longer</a> or <a href="javascript:toggleContent('mccont',40)">very long</a>.
	</div>

	<div class="cleaner">&nbsp;</div>


</body>
</html>
