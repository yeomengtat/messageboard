<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <title>Registration</title>  
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
<link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Montserrat:400,700'>
<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!--webfonts-->
<link href='http://fonts.googleapis.com/css?family=Oxygen:400,300,700' rel='stylesheet' type='text/css'>
 <style type="text/css">
body {    
	background: url('images/header2.jpg') no-repeat center center fixed;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}	
#inner {
    margin: auto;
    width: 20%;

}
</style>	
<!--//webfonts-->
</head>
<body>
<div class="main">
<div class="form">
      <h2>SolvIT</h2>
		<form action="registeration.php" method="post" class="register-form">
		   <div id="inner">
		   <img src="images/download.png"/>
		   </div>
		   <div class="lable-2">
		  
				<input type="text" name="name" class="text" placeholder="Name" id="name" required/>
		        <input name="email" id="email" type="text" class="text" placeholder="your@email.com "  required/>
		        <input type="password" id="password"  name="password" class="text" placeholder="Password " required/>
				<input type="password" id="password2" name="password2" class="text" placeholder="Password "   required/>
				<input type="text" id="chat" name="chat" class="text" placeholder="Chat ID"   />

		   </div>
		   <div class="submit">
			  <input type="submit" name="submit" value="Sign Up" >
			   <h3>Already have an account? <span class="term"><a href="index.php">Login</a></span></h3>
		   </div>
		  
		   <div class="clear"> </div>
		</form>
		<!-----//end-main---->
	
		
			
		</div>
		</div>
		 <!-----start-copyright---->
		
				<!-----//end-copyright---->
</body>
</html>