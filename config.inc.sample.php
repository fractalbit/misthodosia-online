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
// Αρχείο γενικών ρυθμίσεων της εφαρμογής.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */



/*** ΠΡΕΠΕΙ ΝΑ ΡΥΜΘΙΣΤΟΥΝ ΠΡΙΝ ΤΗ ΧΡΗΣΗ ***/

define('ORG_TITLE', 'Όνομα υπηρεσίας'); // Εδώ ορίζουμε τον τίτλο του οργανισμού. Π.χ. ΔΔΕ Αργολίδας
define('ORG_URL', 'http://url-tou-site-sas'); // Εδώ ορίζουμε τη διεύθυνση της ιστοσελίδας μας. Π.χ. http://dide.arg.sch.gr/grmixan (ΔΕΝ συμπεριλαμβάνουμε το φάκεελο της εφαρμογής εδώ)

// Ορισμός λογαριασμού διαχειριστή - ΑΠΑΡΑΙΤΗΤΟ για να λειτουργήσει το περιβάλλον διαχείρισης
define('SUPER_USER', ''); // Ορίστε όνομα διαχειριστή. Π.χ. define('SUPER_USER', 'argolida_admin');
define('SUPER_PASS', ''); // Ορίστε κωδικό διαχειριστή. Τουλάχιστον 6 χαρακτήρες

/*** ΤΕΛΟΣ ΥΠΟΧΡΕΩΤΙΚΩΝ ΡΥΘΜΙΣΕΩΝ ***/



define('USER_DIR', 'userData'); // Ο φάκελος στον οποίο αποθηκεύονται τα δεδομένα των μισθοδοτούμενων μετά την ανάλυση των XML
define('XML_DIR', 'XMLData'); // Ο φάκελος στον οποίο πρέπει να τοποθετούνται τα .xml αρχεία που στέλνονται στην ΕΑΠ
define('APP_DIR', 'appData'); // Ο φάκελος στον οποίο αποθηκεύονται βοηθητικά αρχεία για την εφαρμογή
define('TC_PDF_LIB_DIR', 'tcpdf'); // O φάκελος της βιβλιοθήκης TCPDF που παράγει τα pdf αρχεία (προεραιτικό).




define('CLEAN_UP_AFTER', 10); // Καθορίζει μετά από πόσα λεπτά θα πρέπει να σβήνονται τα προσωρινά .pdf αρχεία που δημιουργούνται


// Σε αυτό τον πίνακα, θα πρέπει να καταχωρηθούν όσοι ΑΦΜ έχουν αλλάξει από την περίοδο του πιο παλιού αρχείου που θέλετε να ανεβάσετε.
// Αν ας πούμε το ΑΦΜ 04992804 στο XML που στάλθηκε τον Ιούνιο του 2011 έχει αλλάξει στα επόμενα σε 105343387 θα πρέπει να το εισάγετε
// στον πίνακα ως '049922804' => '105343387'. Δείτε παρακάτω και ένα παράδειγμα με πολλές αλλαγές...
$changed_afm = array();
/*Για παράδειγμα...
$changed_afm = array(//Παλιά		// Νέα
					'015826574' => '114930402',
					'033174053'	=> '036133899',
					'037107622' => '135564994',
					'037145189'	=> '031328978',
					'037687372'	=> '031590317'					
				);*/

$months = array( 					
 					'01' => 'Ιανουάριος',
 					'02' => 'Φεβρουάριος',
 					'03' => 'Μάρτιος',
                    '04' => 'Απρίλιος',
 					'045' => 'Δώρο Πάσχα',
 					'05' => 'Μάιος',
 					'06' => 'Ιούνιος',
 					'065' => 'Επίδομα Άδειας',
 					'07' => 'Ιούλιος',
 					'08' => 'Αύγουστος',
 					'09' => 'Σεπτέμβριος',
 					'10' => 'Οκτώβριος',
 					'11' => 'Νοέμβριος',
                    '12' => 'Δεκέμβριος',
 					'15' => 'Δώρο Χριστουγέννων',
	 			);

/**
 * Automatically includes classes
 * 
 * @throws Exception
 * 
 * @param  string $class_name  Name of the class to load
 * @return void
 */

//$site_root = $_SERVER['DOCUMENT_ROOT'] . 'misthodosia';
$site_root = full_dir();

function __autoload($class_name)
{
    global $site_root;
    // Customize this to your root Flourish directory
    $flourish_root = $site_root . DIRECTORY_SEPARATOR . 'flourish';
    $file = $flourish_root . DIRECTORY_SEPARATOR . $class_name . '.php';
    //$file = str_replace('\\', '/', $file);
    //dump($file);
 
    if (file_exists($file)) {
        include $file;
        return;
    }
    
    throw new Exception('The class ' . $class_name . ' could not be loaded');

}
