<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <title>Login</title> 
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
      <h2>Message Board</h2>
	
	  
		<form action="login.php" method="post" class="login-form">
		<div id="inner">
		 
		   </div>
		   <div class="lable-2">
		   
		        <input type="text" name="name" class="text" placeholder="Name "    required/>
		        <input type="password" name="password" class="text" placeholder="Password " required/>
		   </div>
		   <div class="submit">
			  <input name="submit" type="submit" value="Login" >
			 <h3>Not registered? <span class="term"><a href="register.php">Create an account</a></span></h3>
		   </div>
		  
		   <div class="clear"> </div>
		</form>		
		</div>
		</div>
</body>
</html>