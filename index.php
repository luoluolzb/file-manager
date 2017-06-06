<?php
	include_once('config.php');
	if(!isset($_COOKIE['password']) || $_COOKIE['password'] != md5(PASSWORD)){
		header("Location: login.php");
	}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>简单文件管理器</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<script src="jquery/jquery-1.12.4.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>

	<link href="css/index.css" rel="stylesheet">
	<script src="js/index.js"></script>
	<script src="js/clipboard.min.js"></script>
</head>
<body>
	<div class="container">
		<!-- Button trigger modal -->
		<button type="button" data-toggle="modal" data-target="#alertModal" class="hidden" id="alertToggle"></button>

		<!-- msg Modal -->
		<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">提示消息</h4>
		      </div>
		      <div class="modal-body" id="alertText"></div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- Button trigger modal -->
		<button type="button" data-toggle="modal" data-target="#dialogModal" class="hidden" id="dialogToggle"></button>

		<!-- msg Modal -->
		<div class="modal fade" id="dialogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">操作提示</h4>
		      </div>
		      <div class="modal-body" id="dialogText"></div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal" id="dialogYBtn">确定</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal" id="dialogNBtn">取消</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- Button trigger modal -->
		<button type="button" data-toggle="modal" data-target="#inputModal" class="hidden" id="inputToggle"></button>

		<!-- msg Modal -->
		<div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">操作提示</h4>
		      </div>
		      <div class="modal-body">
				<label for="message-text" id="inputText"></label>
				<input type="text" class="form-control" id="message-text" />
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal" id="inputYBtn">确定</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal" id="inputNBtn">取消</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div id="dirShowBar">
			<div class="input-group input-group-sm">
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-folder-open"></span>
				</span>
				<input type="text" class="form-control" id="showDir"/>
				<span class="input-group-btn">
					<button class="btn btn-default" id="changeDir"> > </button>
				</span>
			</div>
		</div>
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>名称</th>
					<th>大小</th>
					<th>属性</th>
					<th>操作</th>
					<th><input class="checkbox checkbox-toggle" type="checkbox"/></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div class="btn-toolbar">
			<div class="btn-group btn-group-justified">
				<div class="btn-group">
					<button class="btn btn-primary" onclick="MenuOption('backdir');">返回</button>
				</div>
				<div class="btn-group">
					<button class="btn btn-primary" onclick="MenuOption('backroot');">根目录</button>
				</div>
				<div class="btn-group">
					<button class="btn btn-primary" onclick="MenuOption('refresh');">刷新</button>
				</div>
				<div class="btn-group dropup">
				  <button class="btn btn-primary dropdown-toggle" id="MenuToggle" type="button" data-toggle="dropdown">
					菜单<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu dropdown-menu-right">
				    <li class="selected"><a onclick="MenuOption('rename');">重命名</a></li>
				    <li class="selected"><a onclick="MenuOption('deleteselected');">删除选中</a></li>
				    <li class="selected"><a onclick="MenuOption('copyurl');">复制文件链接</a></li>
				    <li class="selected"><a onclick="MenuOption('unzipfile');">解压文件</a></li>
				    <li class="selected"><a onclick="MenuOption('zipfile');">压缩文件</a></li>
				    <li><a onclick="MenuOption('newdir');">新建文件夹</a></li>
				  </ul>
				</div>
			</div>
		</div>
		<br />
		<div class="btn-toolbar">
			<div class="btn-group btn-group-justified">
				<div class="btn-group">
					<form id="uploadForm" method="post" enctype="multipart/form-data">
						<input type="text" name="fileFiled" value="file" class="hidden" />
						<input type="text" name="saveDir" value="" class="hidden" />
						<label for="inputFile">上传文件：</label>
						<input name="file" type="file" id="inputFile"/>
						<input type="submit" value="上传"/>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="clipboard" class="hidden"></div>
	<script>dir = rootDir = '<?php echo $_SERVER['DOCUMENT_ROOT']; ?>';</script>
</body>
</html>