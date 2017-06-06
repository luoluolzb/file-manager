<?php

#write
	$handle = fopen('temp.txt', 'w');
	for($i = 0; $i < 102400; ++ $i){
		fwrite($handle, '0123456789');
	}
	fclose($handle);

#send
	$handle = fopen('log.txt', 'w');

	$data = file_get_contents('temp.txt');
	$size = filesize('temp.txt') / 1024;
	$start = time();
	fputs($handle, $start);
	echo "<!-- ".$data." -->";
	$stop = time();
	fputs($handle, $stop);

	fputs($handle, $size / ($stop - $start));
	fputs($handle, 'kb/s');
	fclose($handle);
?>