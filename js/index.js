
var rootDir, dir, baseDir = '';
var $tbody, $showDir;

function getFullName(fileName){
	return dir + '/' + fileName;
}

function getUrl(fullName){
	return baseDir + fullName.substr(rootDir.length);
}

function CopyToClipboard(text)
{
	$('#clipboard').attr('data-clipboard-text', text).trigger('click');
}

function getSelected()
{
	var $nodes = $('table>tbody>tr:has(:checkbox:checked)>td>.filename');
	var list = [];
	$nodes.each(function(){
		list.push($(this).text());
	});
	return list;
}

function MenuOption(option, data)
{
	switch(option){
		case 'changedir':
			dir = getFullName(data);
			MenuOption('refresh');
		break;

		case 'backdir':
			if(dir != rootDir){
				pos = dir.lastIndexOf('/');
				dir = dir.substr(0, pos);
				if(dir.length < rootDir.length){
					dir = rootDir;
				}
				MenuOption('refresh');
			}
		break;

		case 'backroot':
			if(dir != rootDir){
				dir = rootDir;
				MenuOption('refresh');
			}
		break;

		case 'refresh':
			show();
		break;

		case 'showmsg':
			$('#alertText').text(data);
			$('#alertToggle').trigger('click');
		break;

		case 'dialogmsg':
			$('#dialogText').text(data.text);
			if(data.yesFun){
				$('#dialogYBtn').one('click', data.yesFun);
			}
			if(data.noFun){
				$('#dialogNBtn').one('click', data.noFun);
			}
			$('#dialogToggle').trigger('click');
		break;

		case 'deleteone':
			deleteFile(data);
		break;

		case 'deleteselected':
			var list = getSelected();
			if(list.length){
				deleteFile(list);
			}
		break;

		case 'zipfile':
			var list = getSelected();
			if(list.length){
				$('#inputText').text('输入压缩文件名：');
				$('#message-text').val(list.length > 1 ? 'newfile.zip' : list[0] + '.zip');
				$('#inputYBtn').one('click', function(){
					$.ajax({
						'typr': 'POST',
						'url': 'api/zipfile.php',
						'dataType': 'json',
						'data': {
							'dir': dir,
							'fileList': list,
							'toName': $('#message-text').val()
						},
						'success': function(ret){
							if(ret.errcode != 0){
								MenuOption('showmsg', '压缩文件失败！');
							}
							else{
								//MenuOption('showmsg', '压缩文件成功！');
								MenuOption('refresh');
							}
						},
						'error': function(xmlHttpRequest, textStarus, errorThrown){
							/*console.log(textStarus);
							console.log(errorThrown);*/
							MenuOption('showmsg', '压缩文件失败！');
						}
					});
				});
				$('#inputToggle').trigger('click');
			}
		break;

		case 'unzipfile':
			var list = getSelected();
			if(list.length){
				$.ajax({
					'typr': 'POST',
					'url': 'api/unzipfile.php',
					'dataType': 'json',
					'data': {
						'dir': dir,
						'fileName': list[0]
					},
					'success': function(ret){
						if(ret.errcode != 0){
							MenuOption('showmsg', '解压失败');
						}
						else{
							//MenuOption('showmsg', '解压成功！');
							MenuOption('refresh');
						}
					},
					'error': function(xmlHttpRequest, textStarus, errorThrown){
						/*console.log(textStarus);
						console.log(errorThrown);*/
						MenuOption('showmsg', '解压失败');
					}
				});
			}
		break;

		case 'copyurl':
			var list = getSelected();
			if(list.length){
				var text = '';
				for(var i = 0, len = list.length; i < len; ++ i){
					var fullName = getFullName(list[i]);
					var fileUrl =  window.location.protocol + '//' + document.domain + '/' + getUrl(fullName);
					text += fileUrl;
					if(len > 1){
						text += '\r\n';
					}
				}
				CopyToClipboard(text);
			}
		break;

		case 'rename':
			var list = getSelected();
			if(list.length){
				var fileName = list[0];
				$('#inputText').text('输入新名称：');
				$('#message-text').val('');
				$('#inputYBtn').one('click', function(){
					$.ajax({
						'typr': 'POST',
						'url': 'api/rename.php',
						'dataType': 'json',
						'data': {
							'dir': dir,
							'fileName': fileName,
							'newName': $('#message-text').val()
						},
						'success': function(ret){
							if(ret.errcode != 0){
								MenuOption('showmsg', '重命名失败！');
							}
							else{
								//MenuOption('showmsg', '新建文件夹成功！');
								MenuOption('refresh');
							}
						},
						'error': function(xmlHttpRequest, textStarus, errorThrown){
							/*console.log(textStarus);
							console.log(errorThrown);*/
							MenuOption('showmsg', '重命名失败！');
						}
					});
				});
				$('#inputToggle').trigger('click');
			}
		break;

		case 'newdir':
			$('#inputText').text('输入新建文件夹名：');
			$('#message-text').val('');
			$('#inputYBtn').one('click', function(){
				$.ajax({
					'typr': 'POST',
					'url': 'api/create-dir.php',
					'dataType': 'json',
					'data': {
						'dir': dir,
						'newdir': $('#message-text').val()
					},
					'success': function(ret){
						if(ret.errcode != 0){
							MenuOption('showmsg', '新建文件夹失败！');
						}
						else{
							//MenuOption('showmsg', '新建文件夹成功！');
							MenuOption('refresh');
						}
					},
					'error': function(xmlHttpRequest, textStarus, errorThrown){
						/*console.log(textStarus);
						console.log(errorThrown);*/
						MenuOption('showmsg', '新建文件夹失败！');
					}
				});
			});
			$('#inputToggle').trigger('click');
		break;
	}
	$('.checkbox-toggle').trigger('cancel');
}

function show(){
	$.ajax({
		'typr': 'POST',
		'url': 'api/get-filelist.php',
		'dataType': 'json',
		'data': {
			'dir': dir,
			'rootDir': rootDir
		},
		'success': function(ret){
			if(ret.errcode != 0){
				MenuOption('showmsg', '列表获取失败！');
			}
			else{
				$tbody.empty();
				for(var i = 0, len = ret.data.length; i < len; ++ i){
					var fileName = ret.data[i].name;
					var fullName = getFullName(fileName);
					var fileUrl = getUrl(fullName);

					if(ret.data[i].type == 'dir'){
						$tr = $('<tr>\
							<td>\
								<span class="glyphicon glyphicon-folder-close"></span> \
								<a class="filename" onclick="javascript:MenuOption(\'changedir\', \'' + fileName + '\');">' + fileName + '</a>\
							</td>\
							<td>----</td>\
							<td>' + ret.data[i].perms_string + '</td>\
							<td>\
								<a onclick="javascript:MenuOption(\'deleteone\', \'' + fileName + '\');">删除</a>\
							</td>\
							<td>\
								<input class="checkbox" type="checkbox" />\
							</td>\
						<tr>');
					}
					else{
						$tr = $('<tr>\
							<td>\
								<span class="glyphicon glyphicon-file"></span> \
								<a class="filename" target="_blank" href="' + fileUrl + '">' + fileName + '</a>\
							</td>\
							<td>' + ret.data[i].size_string + '</td>\
							<td>' + ret.data[i].perms_string + '</td>\
							<td>\
								<a href="' + fileUrl + '" download>下载</a>\
								<a onclick="javascript:MenuOption(\'deleteone\', \'' + fileName + '\');">删除</a>\
							</td>\
							<td>\
								<input class="checkbox" type="checkbox" />\
							</td>\
						<tr>');
					}
					$tbody.append($tr);
				}
			}
		},
		'error': function(xmlHttpRequest, textStarus, errorThrown){
			/*console.log(textStarus);
			console.log(errorThrown);*/
			MenuOption('showmsg', '列表获取失败！');
		}
	});

	if(dir == rootDir){
		$showDir.val('/');
	}else{
		$showDir.val(getUrl(dir));
	}
	$('.checkbox-toggle').trigger('cancel');
}

function deleteFile(fileList){
	MenuOption('dialogmsg', {
		'text': '确定删除吗？',
		'yesFun': function(){
			$.ajax({
				'typr': 'POST',
				'url': 'api/delete-file.php',
				'dataType': 'json',
				'data': {
					'dir': dir,
					'fileList': fileList
				},
				'success': function(ret){
					if(ret.errcode != 0){
							console.log(ret);
						MenuOption('showmsg', '删除失败！');
					}
					else{
						if(typeof fileList != 'string'){
							MenuOption('showmsg', '删除成功');
						}
						MenuOption('refresh');
					}
				},
				'error': function(xmlHttpRequest, textStarus, errorThrown){
					/*console.log(textStarus);
					console.log(errorThrown);*/
					MenuOption('showmsg', '删除失败！');
				}  
			});
		}
	});
}

$(function(){
	$tbody = $('table>tbody');
	$showDir = $('#showDir');

	$('#changeDir').click(function(){
		dir = rootDir + $showDir.val();
		MenuOption('refresh');
	});

	$('#uploadForm :submit').click(function(){
		if($('input[type="file"]').val().length <= 0){
			return false;
		}
		$('input[name="saveDir"]').val(dir);
		var formData = new FormData($( "#uploadForm" )[0]);
		$.ajax({
			'type': 'POST',
			'url': 'api/upload-file.php',
			'data': formData,
			'cache': false,
			'contentType': false,
			'processData': false,

			success: function (ret) {
				//console.log(ret);
				if(ret.errcode != 0){
					MenuOption('showmsg', '上传失败！');
				}else{
					MenuOption('refresh');
				}
			},

			error: function (xmlHttpRequest, textStarus, errorThrown) {
				/*console.log(textStarus);
				console.log(errorThrown);*/
				MenuOption('showmsg', '上传失败！');
			}
		});
		return false;
	});

	$(document).on('keydown', '#dirShowBar input', function(){
		if (event.keyCode == 13){
			$('#changeDir').trigger('click');
		}
	});

	$(document).on('keydown', '#message-text', function(){
		if (event.keyCode == 13){
			$('#inputYBtn').trigger('click');
		}
	});

	$('.checkbox-toggle').click(function(){
		var checked = $(this).prop('checked');
		if(checked){
			$('.checkbox').prop('checked', true);
		}else{
			$('.checkbox').prop('checked', false);
		}
	})
	.on('cancel', function(){
		$('.checkbox').prop('checked', false);
	});

	$(document).on('click', '.checkbox:not(.checkbox-toggle)', function(){
		var all_checked = true;
		$('.checkbox:not(.checkbox-toggle)').each(function(){
			if(!$(this).prop('checked')){
				all_checked = false;
			}
		});
		if($('.checkbox-toggle').prop('checked') != all_checked){
			$('.checkbox-toggle').prop('checked', all_checked);
		}
	});

	$('#MenuToggle').click(function(){
		var count = getSelected().length;
		if(count == 0){
			$(this).parent().find('.selected').addClass('disabled');
		}else{
			$(this).parent().find('.selected').removeClass('disabled');
		}
	});

	/* 剪贴板中介元素 */
    var clipboard = document.getElementById('clipboard');
    var clipboard = new Clipboard(clipboard);

    //复制成功执行的回调，可选
    clipboard.on('success', function(e) {
        //console.log(e.text);
    });

    //复制失败执行的回调，可选
    clipboard.on('error', function(e) {
        //console.log(e);
    });
    /*******************/

	MenuOption('refresh');
});
