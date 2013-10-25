<?php
	echo substr(file_get_contents('../.git/refs/heads/master'), 0, 8);
?>