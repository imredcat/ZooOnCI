<?php

define("DOC_ROOT", $_SERVER['DOCUMENT_ROOT']);
define("DIR_CACHE_JSCSS", ROOTPATH."writable/cache/cssjs/");
define("DIR_ZOO", ROOTPATH."zoo/");


define("TIME_UNIX", time()) ;
define("TIME_UNIX_MICRO", microtime()) ;
define("TIME_DATETIME", date('Y-m-d H:i:s', TIME_UNIX)) ;
define("TIME_DATETIME_PLAIN", date('YmdHis', TIME_UNIX)) ;