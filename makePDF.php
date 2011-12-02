<?php

/* *********** ΓΕΝΙΚΗ ΠΕΡΙΓΡΑΦΗ ΛΕΙΤΟΥΡΓΙΑΣ ΑΡΧΕΙΟΥ *********** */
// Το αρχείο αυτό δημιουργεί ένα .pdf με όλα τα οικονομικά στοιχεία του μισθοδοτούμενου
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

require_once(TC_PDF_LIB_DIR . '/config/lang/eng.php');
require_once(TC_PDF_LIB_DIR . '/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
$pdf->SetHeaderData('', '', ORG_URL, '');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);


foreach($pages as $page){
		
	// add a page
	$pdf->AddPage();

	// output the HTML content
	$pdf->writeHTML($page, true, false, true, false, '');

	// reset pointer to the last page
	$pdf->lastPage();
}

if(defined('ORG_URL') && strlen(ORG_URL)>0){
	if(defined('USER_DIR') && is_dir(USER_DIR)){
		$salt = rand_str(5); 
		// Αν και τα .pdf αρχεία διαγράφονται αυτόματα, 10 λεπτά (CLEAN_UP_AFTER) μετά τη δημιουργία τους, σαν επιπλέον μέτρο ασφαλείας...
		// Δημιουργούμε ένα τυχαίο κείμενο που προστίθεται στο τέλος του ονόματος του pdf αρχείου έτσι ώστε
		// να μην μπορεί κάποιος να κατεβάσει απευθείας το pdf κάποιου άλλου, γνωρίζοντας μόνο το ΑΦΜ του.

		$output_file = USER_DIR . '/' . $afm . '_' . $salt . '.pdf';
		$link_file = ORG_URL . '/' . current_dir() . '/' . USER_DIR . '/' . $afm . '_' . $salt . '.pdf';
		//Close and output PDF document
		$pdf->Output($output_file, 'F');
		echo '<br /><br /><a href="'.$link_file.'" target="_blank" class="button download">Αποθήκευση όλων σε pdf</a><br /><br />';
	}else{
		echo 'Δεν έχει οριστεί φάκελος χρηστών ή αυτός που έχει οριστεί δεν υπάρχει';
	}
}else{
	echo 'Δεν έχει οριστεί η διεύθυνση URL του site';
}