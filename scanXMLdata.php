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

print_header();

$f = 0;
$dataset = array();
foreach(bfglob(XML_DIR, '*.xml', NULL, 10) as $file){
// Διαβάζει ένα-ένα τα αρχεία xml που υπάρχουν στο φάκελο 'XML_DIR'
// και οργανώνει τα οικονομικά στοιχεία ανα ΑΦΜ (μισθοδοτούμενο) στον πίνακα $dataset
	xml_extract($file);	
	echo 'Φορτώθηκε και αναλύθηκε το αρχείο ' . mb_convert_encoding($file, 'UTF-8', 'ISO-8859-7') . '<br />';
	$f++;
}

if($f == 0){
	echo '<h3>Δεν βρέθηκε κανένα έγκυρο αρχείο XML. Παρακαλούμε διαβάστε την τεκμηρίωση: <a href="http://dide.arg.sch.gr/grmixan/misthodosia-online-app/">http://dide.arg.sch.gr/grmixan/misthodosia-online-app/</a></h3>';
}else{
	echo '<br />Αναλύθηκαν <strong>' . $f . '</strong> αρχεία XML. ';
}

$i == 0;
foreach($dataset as $afm => $data){
// Αποθηκεύει ένα αρχείο για κάθε μισθοδοτούμενο με τα οικονομικά του στοιχεία στον φάκελο 'USER_DIR'
	$filename = USER_DIR . '/' . $afm . '.php';
	save_file($filename, $data); // functions.inc.php
	$i++;	
}
if($i > 0) echo 'Αποθηκευτηκαν στοιχεία για <strong>'. $i . '</strong> μισθοδοτούμενους.';

unset($dataset);

print_footer();


function xml_extract($file){
// Διαβάζει το αρχείο $file και τα οργανώνει ανα ΑΦΜ (μισθοδοτούμενο) στον πίνακα $dataset (global)
	global $changed_afm, $codes, $dataset, $months;
	
	$xmlstr = file_get_contents($file);
	$xml = new SimpleXMLElement($xmlstr); // Διαβάζει το xml αρχείο σε μορφή αντικειμένου (συνάρτηση ενσωματωμένη στην PHP).

	$period = $xml->header->transaction->period;
	if($period['month'] < 10) $period['month'] = '0'.$period['month']; // Pad month numbers with zero

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


	foreach($xml->body->organizations->organization->employees->employee as $employee){
	// Για κάθε εργαζόμενο που υπάρχει στο .xml...

		$afm = ''.$employee->identification->tin;
		$amm = ''.$employee->identification->amm;
		$user = $employee->identification;		

		if(array_key_exists($afm, $changed_afm)) {
		// Αν το ΑΦΜ υπάρχει στον πίνακα των ΑΦΜ που έχουν αλλάξει (config.inc.php)...
			$afm = $changed_afm[$afm]; // Τότε ορίζουμε το ΑΦΜ στο πιο πρόσφατο
		// Με αυτό τον τρόπο τα σοιχεία του μισθοδοτούμενου θα ενοποιηθούν κάτω από ΕΝΑ ΑΦΜ (το πιο πρόσφατο) και όχι δύο.
		}	

		$update = FALSE;
		// Αν η τρέχουσα περίοδος είναι πιο πρόσφατη από την καταχωρημένη για αυτό τον μισθοδοτούμενο,
		// τότε και μόνο τότε ενημέρωσε τα προσωπικά του στοιχεία
		if($date_test > $dataset[$afm]['personal_info']['date']){
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
		$temp_codes = $codes;
		
		foreach($employee->payment as $payment){
			// Για κάθε καταχώρηση πληρωμής του εργαζόμενου

			if($payment->income['type'] == '0'){ 
				// Αν πρόκειται για τακτική μισθοδοσία...
				
				$income = $payment->income;
				
				foreach($income->de as $de){
					// Αποθήκευσε αθροιστικά, το ποσό για κάθε κωδικό κράτησης
					$code = '';
					$code = (string) trim($de['code']);				

					if(array_key_exists($code, $temp_codes)){							
						$temp_codes[$code]['amount'] += (float) trim($de['amount']);
					}else {
						$amount = (float) trim($de['amount']);
						$temp_codes[$code] = array('desc' => $code, 'kratisi' => 1, 'amount' => $amount);
					} 
				}				

				foreach($income->gr as $gr){
					// Αποθήκευσε αθροιστικά, το ποσό για κάθε κωδικό επιδόματος
					$code = '';
					$code = (string) trim($gr['kae']);	
					
					if(array_key_exists($code, $temp_codes)){
						$temp_codes[$code]['amount'] += (float) trim($gr['amount']);
					}else {		
						$amount = (float) trim($gr['amount']);
						$temp_codes[$code] = array('desc' => $code, 'kratisi' => 0, 'amount' => $amount);
					} 
					
				}			

				foreach($income->et as $et){
					$code = '';
					$code = (string) trim($et['kae']);	
					
					if(array_key_exists($code, $temp_codes)){
						$temp_codes[$code]['amount'] += (float) trim($et['amount']);						
					}else {							
						//$temp_codes['gen_epid'] += (float) trim($de['amount']);
					} 
					
				}

				// Αποθήκευσε το ποσό που θα πιστωθεί για το πρώτο και δεύτερο δεκαπενθήμερο
				$first += (float) trim($payment->netAmount1['value']);
				$second += (float) trim($payment->netAmount2['value']);
			}else{	
			// Αν πρόκειται για αναδρομική μισθοδοσία...
				$code = '';
				$code = (string) trim($payment->income->gr['kae']);									
				$temp_codes['andr']['total']['amount'] += (float) trim($payment->netAmount1['value']);
				
							
				$temp_codes['andr'][$code]['amount'] += (float) trim($payment->netAmount1['value']);

				$endDate = $payment->income['endDate'];
				$startDate = $payment->income['startDate'];				
				$days = days_diff($endDate, $startDate); // Λεκτική περιγραφή διάρκειας αναδρομικών				

				$temp_codes['andr'][$code]['days'] .= ' και ' . $days;
				
			} // Τέλος "ΑΝ τακτική... αλλιώς..."

			$a++;			
		
		} // ΤΕΛΟΣ "Για κάθε πληρωμή του μισθοδοτούμενου"
		
		// Αν υπάρχουν αναδρομικά, πρόσθεσέ τα στο πρώτο δεκαπενθήμερο
		if($temp_codes['andr']['total']['amount'] > 0) $first += $temp_codes['andr']['total']['amount'];

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
												'month_str' => $month_str, // Λεκτική περιγραφή περιόδου
												'year' => $year, // Έτος μισθοδοτικής περιόδου (σώπα!)
												'firsthalf' => $first, // Α δεκαπενθήμερο
												'secondhalf' => $second, // Β δεκαπενθήμερο
												'days' => $days, // Λεκτική περιγραφή αναδρομικών				
												'analysis' => $temp_codes // Αναλυτικά οι κρατήσεις και τα επιδόματα
											);
		
	 } // Τέλος "Για κάθε εργαζόμενο"

		// Απελευθέρωση μνήμης
		unset($xml); 
		unset($xmlstr);

} // Τέλος συνάρτησης xml_extract


function days_diff($end, $start, $string = TRUE){
	$diff = strtotime($end) - strtotime($start);
	$days = floor($diff / (24 * 60 * 60));

	$gr_start = date('d/m/Y', strtotime($start));
	$gr_end = date('d/m/Y', strtotime($end));
	if($string){ 
		return '<b>' . $days . ' ημέρες</b> (Από ' . $gr_start . ' Εως ' . $gr_end . ')';
	}else{
		return $days;		
	}
}

function bfglob($path, $pattern = '*', $flags = 0, $depth = 0) {
	// Use glob to also scan in subdirectories
    $matches = array();
    $folders = array(rtrim($path, DIRECTORY_SEPARATOR));
    
    while($folder = array_shift($folders)) {
        $matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
        if($depth != 0) {
            $moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
            $depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
            $folders = array_merge($folders, $moreFolders);
        }
    }
    return $matches;
}