<?php 

/*  
	"Μισθοδοσία online" - Εφαρμογή άντλησης και παρουσίασης οικoνομικών στοιχείων από αρχεία XML
    Copyright (C) 2011 Βελέντζας Αλέξανδρος (fractalbit@gmail.com) - http://dide.arg.sch.gr/grmixan/misthodosia-online-app/

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
// Το αρχείο αυτό, διαβάζει τα XML αρχεία που υπάρχουν στο φάκελο 'XML_DIR'
// και αποθηκεύει τα οικονομικά στοιχεία κάθε μισθοδοτούμενου σε αρχείο
// της μορφής ΑΦΜ.php, στο φάκελο 'USER_DIR'. Η δομή του αρχείου είναι απλά ένας seriralized πίνακας.
// Από τη στιγμή που τα αρχεία .php ορίζονται ως εκτελέσιμα (function: save_file, file: functions.inc.php),
// δεν υπάρχει κίνδυνος υποκλοπής των αποθηκευμένων δεδομένων.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

include_once('./init.inc.php');

// $memory_limit = $m = ini_get('memory_limit');
// echo '<h3>' . $m . '</h3>';
ini_set('memory_limit', '516M'); // ευχαριστώ το "ΤΜΗΜΑ ΜΗΧΑΝΟΓΡΑΦΗΣΗΣ ΔΙΠΕ ΚΑΡΔΙΤΣΑΣ" - mixanografisi@dipe.kar.sch.gr
// $memory_limit = $m = ini_get('memory_limit');
// echo '<h3>' . $m . '</h3>';

print_header();

$unknown_codes = array();

if(admin_configured()){

    if($admin->check_logged_in()){

		$f = 0;
		$dataset = array();
		$analyzed = array();
		foreach(bfglob(XML_DIR, '*.xml', NULL, 10) as $file){
		// Διαβάζει ένα-ένα τα αρχεία xml που υπάρχουν στο φάκελο 'XML_DIR'
		// και οργανώνει τα οικονομικά στοιχεία ανα ΑΦΜ (μισθοδοτούμενο) στον πίνακα $dataset
			xml_extract($file);	
			$filepath_array = explode('/', $file);
			$filename = end($filepath_array);
			$filename_with_time = $filename . '_' . filemtime($file);
			$analyzed[$filename_with_time] = time();
			echo 'Φορτώθηκε και αναλύθηκε το αρχείο ' . mb_convert_encoding($file, 'UTF-8', 'ISO-8859-7') . '<br />';
			$f++;
		}

		save_file(APP_DIR . '/scanned_files.php', $analyzed);

		if($f == 0){
			echo '<h3>Δεν βρέθηκε κανένα έγκυρο αρχείο XML. Παρακαλούμε διαβάστε την τεκμηρίωση: <a href="http://dide.arg.sch.gr/grmixan/misthodosia-online-app/">http://dide.arg.sch.gr/grmixan/misthodosia-online-app/</a></h3>';
		}else{
			echo '<br />Αναλύθηκαν <strong>' . $f . '</strong> αρχεία XML. ';
		}


		$i = 0;
		$mithodotoumenoi = array();
		foreach($dataset as $afm => $data){
		// Αποθηκεύει ένα αρχείο για κάθε μισθοδοτούμενο με τα οικονομικά του στοιχεία στον φάκελο 'USER_DIR'
			$filename = USER_DIR . '/' . $afm . '.php';
			save_file($filename, $data); // functions.inc.php
			$i++;	

			$name = $data['personal_info']['lastname'] . ' ' . $data['personal_info']['firstname'];
			$misthodotoumenoi[] = array('name' => $name, 'afm' => $afm);
		}

		$txt = '';
		if($i > 0) $txt = 'Αποθηκευτηκαν στοιχεία για <strong>'. $i . '</strong> μισθοδοτούμενους.';
		echo $txt;

		sort($misthodotoumenoi);
		save_file(USER_DIR . '/all.php', $misthodotoumenoi);

		savelog(time(), 'scan_times.txt');
		$message = date('d/m/Y H:i:s', time()) . ' - Αναλύθηκαν ' . $f . ' XML αρχεία. ' . $txt;
		savelog($message);

		echo '<br/><br/><a href="manage_xml.php" class="button">Επιστροφή στη Διαχείριση Αρχείων</a>';

		unset($dataset);

}else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}


print_footer();


function xml_extract($file){
// Διαβάζει το αρχείο $file και τα οργανώνει ανα ΑΦΜ (μισθοδοτούμενο) στον πίνακα $dataset (global)
	global $changed_afm, $pliromes, $dataset, $months, $first, $second, $codes, $unknown_codes;
	
	$xmlstr = file_get_contents($file);
	$xml = new SimpleXMLElement($xmlstr); // Διαβάζει το xml αρχείο σε μορφή αντικειμένου (συνάρτηση ενσωματωμένη στην PHP).

	$period = $xml->header->transaction->period;
	//$period['month'] = str_replace('0', '', $period['month']); // Remove leading zero from months - This was bugged, it would also remove zero from October (10)
	$period['month'] = ltrim($period['month'], '0'); // Remove leading zero from months
	if($period['month'] < 10) $period['month'] = '0'.$period['month']; // Pad month numbers with zero
	// The 2 lines above ensure compatibility with both ex. '04' and '4' as month in the xml

	// Απαραίτητο για να μπορούμε να ταξινομήσουμε σωστά το επίδομα άδειας σε σχέση με τις άλλες μισθοδοτικές περιόδους
	// ********* Ίσως θα πρέπει να προστεθεί κάτι παρόμοιο για δώρο Πάσχα/Χριστουγέννων ********* 
	if($period['month'] == 14){
		$period['month'] = '065';
		$m = '07';
	}elseif($period['month'] == 13){
		$period['month'] = '045';
		$m = '05';
	}else{
		$m = $period['month'];
	}	
	
	$month = (string) $period['month'];
	$year = (string) $period['year'];	
	$month_str = $months[$month];
	
	$date_test = strtotime('01-'.$m.'-'.$year); // Δημιουργία timestamp για τη μισθοδοτική περίοδο

	// Δημιουργία μοναδικού αλφαρηθμιτικού που χρησιμεύει ως αναγνωριστικό περιόδου μισθοδοσίας και ταξινομείται σωστά χρονολογικά
	$period_str = $period['year'] . '_' . $period['month'] . '_' . $xml->header->transaction->periodType['value'];


	foreach($xml->body->organizations->organization as $org){
		// Για κάθε οργανισμό...
		
		foreach($org->employees->employee as $employee){
		// Για κάθε εργαζόμενο που υπάρχει στον οργανισμό...
			
			$pliromes = array();

			$afm = ''.$employee->identification->tin;
			$amm = ''.$employee->identification->amm;
			$rank = ''.$employee->identification->scale->rank;
			$mk = ''.$employee->identification->scale->mk; //dump($mk);
			$category = ''.$employee->identification->category['value'];
			$user = $employee->identification;		

			if(array_key_exists($afm, $changed_afm)) {
			// Αν το ΑΦΜ υπάρχει στον πίνακα των ΑΦΜ που έχουν αλλάξει (config.inc.php)...
				$afm = $changed_afm[$afm]; // Τότε ορίζουμε το ΑΦΜ στο πιο πρόσφατο
			// Με αυτό τον τρόπο τα σοιχεία του μισθοδοτούμενου θα ενοποιηθούν κάτω από ΕΝΑ ΑΦΜ (το πιο πρόσφατο) και όχι δύο.
			}	

			$update = FALSE;
			// Αν η τρέχουσα περίοδος είναι πιο πρόσφατη από την καταχωρημένη για αυτό τον μισθοδοτούμενο,
			// τότε και μόνο τότε ενημέρωσε τα προσωπικά του στοιχεία

			if(!isset($dataset[$afm]) ||  $date_test > $dataset[$afm]['personal_info']['date']){
				$update = TRUE;	
			}else{
				$update = FALSE;
			}
			
			// Με αυτό τον τρόπο αποθηκεύονται μόνο τα προσωπικά στοιχεία της πιο πρόσφατης περιόδου.
			// Σημαντικό αν π.χ. έχει διορθωθεί ο ΑΜ του μισθοδοτούμενου.

			$a = 0;
			$anadromika = 0;	
			$days = '';
			$first = $second = 0;
		
			foreach($employee->payment as $payment){
				// Για κάθε καταχώρηση πληρωμής του εργαζόμενου

				foreach ($payment->income as $income) {
					$income_type = (string) $income['type'];	
					
					if(!isset($pliromes[$income_type])){
						$pliromes[$income_type] = array(
														'kratiseis' => array('desc' => '', 'data'=> array()),
														'epidomata' => array('desc' => '', 'data' => array()),
														'prostheta' => array()
													);				
					}	
					
					analyze_data($income, $income_type);
				}

				$first += (float) trim($payment->netAmount1['value']);
				$second += (float) trim($payment->netAmount2['value']);	
							
				$a++;			
			
			} // ΤΕΛΟΣ "Για κάθε πληρωμή του μισθοδοτούμενου"
			
			if($update){
				// Ανανέωσε τα προσωπικά δεδομένα μόνο αν είναι νεότερα (βλέπε τον ορισμό της $update)
				$dataset[$afm]['personal_info'] = array(
															'firstname' => ''.$user->firstName,
															'lastname' => ''.$user->lastName, 
															'amm' => ''.$amm,
															'afm' => ''.$afm,
															'iban' => ''.$user->bankAccount['iban'],
															'mk' => $mk,
															'date' => $date_test
														);
			}

			// Πρόσθεσε τα οικονομικά δεδομένα της περιόδου
			$dataset[$afm][$period_str] = array(
													'month' => $month,
													'month_str' => $month_str, // Λεκτική περιγραφή περιόδου
													'year' => $year, // Έτος μισθοδοτικής περιόδου (σώπα!)
													'firsthalf' => $first, // Α δεκαπενθήμερο
													'secondhalf' => $second, // Β δεκαπενθήμερο
													'days' => $days, // Λεκτική περιγραφή αναδρομικών				
													'analysis' => $pliromes, // Αναλυτικά οι κρατήσεις και τα επιδόματα
													'rank' => $rank, // Βαθμός και κατηγορία εκπαίδευσης - ν. 4024/2011
													'mk' => $mk, // ΜΚ ν. 4354/2015
													'category' => $category
												);

			// dump($dataset[$afm][$period_str]); die();
			
		 } // Τέλος "Για κάθε εργαζόμενο"

	}// Τέλος "Για κάθε οργανισμό"

		// Απελευθέρωση μνήμης
		unset($xml); 
		unset($xmlstr);

} // Τέλος συνάρτησης xml_extract



function analyze_data($income, $income_type){
	global $pliromes, $payment, $first, $second, $unknown_codes, $codes;

	// Εισφορές ασφαλισμένου
	foreach($income->de as $de){
		$code = '';
		$code = (string) trim($de['code']);				
		
		if(array_key_exists($code, $pliromes[$income_type]['kratiseis']['data'])){							
			$pliromes[$income_type]['kratiseis']['data'][$code]['amount_asf'] += (float) trim($de['amount']);
		}else {
			$amount = (float) trim($de['amount']);
			$pliromes[$income_type]['kratiseis']['data'][$code] = array('desc' => $code, 'amount_asf' => $amount, 'amount_erg' => 0);
			//die($code);
		} 

		// Check unknown codes. Disable before pushing to github
		/*if(!array_key_exists($code, $codes['kratiseis']['data'])){
			if(!in_array($code, $unknown_codes)) {
				$unknown_codes[$code] = $code;
				echo '<hr>' . $code . '<hr>';
			}
		}*/
	}				
			
	// Εργοδοτικές εισφορές			
	foreach($income->et as $et){
		$code = '';
		$code = (string) trim($et['code']);	
			
		if(array_key_exists($code, $pliromes[$income_type]['kratiseis']['data'])){
			$pliromes[$income_type]['kratiseis']['data'][$code]['amount_erg'] += (float) trim($et['amount']);						
		}else {							
			$amount = (float) trim($et['amount']);
			$pliromes[$income_type]['kratiseis']['data'][$code] = array('desc' => $code, 'amount_asf' => 0, 'amount_erg' => $amount);
			//die('erg-'.$code);
		}
	}


	// Βασικός και επιδόματα		
	foreach($income->gr as $gr){
		$code = '';
		$code = (string) trim($gr['kae']);	
		
		if(array_key_exists($code, $pliromes[$income_type]['epidomata']['data'])){
			$pliromes[$income_type]['epidomata']['data'][$code]['amount'] += (float) trim($gr['amount']);
		}else {		
			$amount = (float) trim($gr['amount']);
			$pliromes[$income_type]['epidomata']['data'][$code] = array('desc' => $code, 'amount' => $amount);				
		}		
	}			
		


}




function days_diff($end, $start, $string = TRUE){
	return '';

/*
	$diff = strtotime($end) - strtotime($start);
	$days = floor($diff / (24 * 60 * 60));

	if($days > 0){
		$gr_start = date('d/m/Y', strtotime($start));
		$gr_end = date('d/m/Y', strtotime($end));
		if($string){ 
			return '<b>' . $days . ' ημέρες</b> (Από ' . $gr_start . ' Εως ' . $gr_end . ')';
		}else{
			return $days;		
		}
	}else{
		return '';
	}*/
}

function bfglob($path, $pattern = '*', $flags = 0, $depth = 0) {
	// Use glob to also scan in subdirectories
	define('DIRECTORY_SEP', '/');
    $matches = array();
    $folders = array(rtrim($path, DIRECTORY_SEP));
    
    while($folder = array_shift($folders)) {
        $matches = array_merge($matches, glob($folder.DIRECTORY_SEP.$pattern, $flags));
        if($depth != 0) {
            $moreFolders = glob($folder.DIRECTORY_SEP.'*', GLOB_ONLYDIR);
            $depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
            $folders = array_merge($folders, $moreFolders);
        }
    }
    return $matches;
}