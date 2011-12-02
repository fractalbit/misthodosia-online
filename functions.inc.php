<?php

/*  
	"Μισθοδοσία online" - Εφαρμογή άντλησης και παρουσίασης οικoνομικών στοιχείων από αρχεία XML
    Copyright (C) 2011 Βελέντζας Αλέξανδρος (fractalbit@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/copyleft/gpl.html.
*/

/* *********** ΓΕΝΙΚΗ ΠΕΡΙΓΡΑΦΗ ΛΕΙΤΟΥΡΓΙΑΣ ΑΡΧΕΙΟΥ *********** */
// Συχνά χρησιμοποιούμενες συναρτήσεις (fuf? :P)
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

function save_file($filename, $data){
 	$data = serialize($data); 	
 	$file = fopen($filename, 'w');
 	$data = '<?php $person = \''. $data . '\';';
 	fwrite($file, $data);
 	fclose($file);
 	unset($data);
 }

 function load_file($file){
 	//$contents = file_get_contents($file);
 	include($file);
 	$data = unserialize($person);

 	return $data;
 }

 function rand_str($length = 10) {    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }

    return $string;
}

function print_header(){
	echo '
	<!doctype html>
	<html lang="el">
	<head>
		<meta charset="utf-8">
		<title>Μισθοδοσία online - '.ORG_TITLE.'</title>

		<link rel="stylesheet" href="css/reset.css">
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
	<div class="container">';

	//if(!empty(ORG_TITLE) && !empty(ORG_URL))
		echo '<h2><a href="'.ORG_URL.'" class="button">'.ORG_TITLE.'</a> - <a href="index.php" class="button">Μισθοδοσία online</a></h2><hr /><br />';


}

function print_footer(){
	echo '
	<br />
	<hr />
	<div class="subtle">Λογισμικό ανοικτού κώδικα "<a href="http://dide.arg.sch.gr/grmixan/misthodosia-online-app/" target="_blank">Μισθοδοσία online</a>"</span>
	</div>
	</body>
	</html>';
}

function current_dir(){
	$temp = explode('/', dirname(__FILE__));
	$dir = array_pop($temp);

	return $dir;
}

