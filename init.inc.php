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
// Το αρχείο αυτό γίνεται include στα αρχεία της εφαρμογής και φορτώνει όλα τα απαραίτητα αρχεία για τη λειτουργία της
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

error_reporting(E_ALL); ini_set('display_errors', '1');

if(!file_exists('config.inc.php')) die('To arxeio config.inc.php de vrethike. Parakaloume diavaste <a href="https://github.com/fractalbit/misthodosia-online/blob/master/readme.md">tin tekmiriosi</a>');

include('./functions.inc.php');
include('./config.inc.php');
include('./efpp.class.php');


include('./ranks.php');
include('./eapCodes.php');


if(file_exists('passwords.php') && !file_exists(APP_DIR . '/passwords.php')){
    // Αυτό θα (πρεπει) να τρέξει μόνο μία φορά για να υπάρξει ομαλή μετάβαση στο νέο σύστημα διαχείρισης κωδικών!
    
    include('./passwords.php'); // Φόρτωσε τους υπάρχοντες κωδικούς
    if(!empty($protected)) save_file(APP_DIR . '/passwords.php', $protected); // Αν υπάρχουν, αποθήκευσέ τους στο νέο αρχείο

    // Φτιάξε ένα νέο passwords.php με επεξηγηματικό κείμενο για το νέο σύστημα
    $explain = '<?php // Το αρχείο αυτό δεν χρησιμοποείται πλέον. Η διαχείριση των κωδικών γίνεται από τη σελίδα "Ρυθμίσεις". Κάντε είσοδο ως διαχειριστής για να διαχειριστείτε τους κωδικούς.';
    file_put_contents('passwords.php', $explain);
}

if(file_exists('passwords.sample.php')) unlink('passwords.sample.php');

// Load the protected array (afm->password combinations)
if(file_exists(APP_DIR . '/passwords.php')){
    $protected = load_file(APP_DIR . '/passwords.php'); // Φόρτωσε το αρχείο με τους κωδικούς
}else{
    $protected = array();
}

// Φόρτωσε τα κείμενα της αρχικής σελίδας
if(file_exists(APP_DIR . '/texts.json')) {
    $txt = json_decode(file_get_contents(APP_DIR . '/texts.json'), true); // Φόρτωσε το αρχείο γλώσσας
}else{
    $txt = array(
                        'header' => '<p>Παρακαλούμε εισάγετε ΑΦΜ, Αρ. Μητρώου και πιέστε "Συνέχεια" για να εμφανιστούν τα μισθοδοτικά σας στοιχεία</p>',
                        
                        'login_below' => '<p>Σημείωση: Όσοι δεν έχουν 6ψήφιο αριθμό μητρώου (π.χ. αναπληρωτές), πρέπει να αφήσουν το σχετικό πεδίο ασυμπλήρωτο.</p>
                            <p>Σημείωση: Όσοι έχουν προστατεύσει το ΑΦΜ τους με κωδικό, θα πρέπει να εισάγουν αυτόν στο πεδίο Αρ. Μητρώου.</p>',
                        
                        'footer' => 'Προσοχή! Τα μισθοδοτικά στοιχεία παρέχονται για ενημέρωση και μόνο. Αν χρειάζεστε βεβαίωση αποδοχών με υπογραφή εκκαθαριστή και σφραγίδα υπηρεσίας (π.χ. για κατάθεση σε τράπεζα, σε άλλη υπηρεσία κ.τ.λ.), θα πρέπει να τη ζητήσετε από τη Δ/νση.',

                        'afm_label' => 'Α.Φ.Μ.:',
                        'amm_label' => 'Αρ. Μητρώου ή κωδικός:'
                    );
}

// $session_path = trailingslashit(APP_DIR) . 'session_data';
// if(!empty($session_path)) fSession::setPath($session_path);
fSession::setLength('1 hour');
fSession::open();

fAuthorization::setAuthLevels(
    array(
        'admin' => 100,
        // 'user'  => 50,
        // 'guest' => 25
    )
);


$admin = new efpp_user(SUPER_USER, SUPER_PASS, false);
