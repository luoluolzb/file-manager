<?php
	include_once('config.php');
	if(! NEED_PASS){
		setcookie('password', md5(PASSWORD));
		header("Location: index.php");
	}
	$errmsg = '';
	$password = '';
	if(isset($_REQUEST['password'])){
		$password = $_REQUEST['password'];
		if($password == PASSWORD){
			setcookie('password', md5(PASSWORD));
			header("Location: index.php");
		}else{
			$errmsg = '密码错误';
		}
	}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>登录页面</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!--link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet"-->
	<!--[if lt IE 9]>
	<script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
body{
	background: #eee;
	margin-top: 50px;
}

.form-control{
	font-size: 16px;
	padding: 20px 10px;
}

input[name="password"]{
	margin-bottom: 12px;
}

input[type="submit"]{
	font-size: 16px;
	font-weight: bold;
	margin-top: 15px;
}

.error{
	color: red;
	font-size: 16px;
}

	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1">
				<h2>登录密码</h2>
				<form action="login.php">
					<input type="password" class="form-control" placeholder="请输入密码" name="password" autofocus="autofocus" value="<?php echo $password; ?>" />
					<input type="submit" class="btn btn-primary btn-block" value="登录">
					<p class="error"><?php echo $errmsg; ?></p>
				</form>
			</div>
		</div>
	</div>
</body>
</html>