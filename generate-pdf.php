<?php

/* *********** ΓΕΝΙΚΗ ΠΕΡΙΓΡΑΦΗ ΛΕΙΤΟΥΡΓΙΑΣ ΑΡΧΕΙΟΥ *********** */
// Το αρχείο αυτό δημιουργεί ένα .pdf με όλα τα οικονομικά στοιχεία του μισθοδοτούμενου
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

include_once('./init.inc.php');

// Require dompdf, libraries, and helper functions
require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();

// reference the Dompdf namespace
use Dompdf\Dompdf;

$afm = fSession::get('afm');
$salt = fSession::get('salt');
$pages = fSession::get('pages');
$name = fSession::get('name');

// instantiate and use the dompdf class
$dompdf = new Dompdf();

$html_header = '
	<!DOCTYPE html>
	<html lang="el">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Document</title>

		<style>
			@page {
				margin: 5px 20px;
			}

			* {
				font-family: DejaVu Sans;
				font-size: 8px;
			}

			.compare {
				border-collapse: collapse;
				width: 100%;
			}

			.compare td {
				border: 1px solid #666;
				text-align: right;
			}

			.totals td {
				border: 1px solid #666;
				text-align: center;
			}

			.special, .special * {
				font-weight: bold;
			}

			.special {
				background: #e5ecf9 !important;
			}

			h3 {
				font-size: 12px;				
			}
			}

			h4 {
				font-size: 10px;				
			}
		</style>
	</head>

	<body>
';

$html_footer = '</body></html>';
$page_break = '<div style="page-break-after: always;"></div>';

$html = $html_header;

// $page_id = 0; // This should be changed to get the value from the ajax request

$page_id = $_GET['pid'];
// dump($pages); die();
if($page_id === 'all'){
	foreach($pages as $page){		
		$html .= $page;	
		$html .= $page_break;	
	}
}else{
	$pid = (int) $page_id;
	$html .= $pages[$pid];
	// $html .= $page_break;	
}

$html .= $html_footer;

$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'); // Αυτό είναι απαραίτητο για να εμφανιστούν τα ελληνικά με το dompdf

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');


if(defined('ORG_URL') && strlen(ORG_URL)>0){
	if(defined('USER_DIR') && is_dir(USER_DIR)){
		// $salt = rand_str(5); 

		// Αν και τα .pdf αρχεία διαγράφονται αυτόματα, 10 λεπτά (CLEAN_UP_AFTER) μετά τη δημιουργία τους, σαν επιπλέον μέτρο ασφαλείας...
		// Δημιουργούμε ένα τυχαίο κείμενο που προστίθεται στο τέλος του ονόματος του pdf αρχείου έτσι ώστε
		// να μην μπορεί κάποιος να κατεβάσει απευθείας το pdf κάποιου άλλου, γνωρίζοντας μόνο το ΑΦΜ του.
		// Το salt και το afm διαβάζονται από το session

		$output_file = trailingslashit(dirname(__FILE__)) . trailingslashit(USER_DIR) . $afm . '_' . $salt . '.pdf';

		// Render the HTML as PDF
		$dompdf->render();

		file_put_contents($output_file, $dompdf->output());
		
		if(!$admin->check_logged_in()){                        
		    $message = date('d/m/Y H:i:s', time()) . ' - Ο χρήστης ' . $name . ' δημιούργησε PDF';
		    savelog($message, 'user_log.txt');
		}else{
		    $message = date('d/m/Y H:i:s', time()) . ' - Ο διαχειριστής είδε δημιούργησε PDF για τον ' . $name;
		    savelog($message);
		}
	}else{
		echo 'Δεν έχει οριστεί φάκελος χρηστών ή αυτός που έχει οριστεί δεν υπάρχει';
	}
}else{
	echo 'Δεν έχει οριστεί η διεύθυνση URL του site';
}