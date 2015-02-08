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
// Δημιουργεί τα αρχικά (default) κείμενα που χρησιμοποιούνται στην εφαρμογή.
// Χρήσιμο σε περίπτωση που ο χρήστης θέλει να τα επαναφέρει μετά από λανθασμένη διόρθωση ή διαγραφή.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

include_once('./init.inc.php');

print_header();

if(admin_configured()){

    if($admin->check_logged_in()){

        $txt = array(
                        'header' => '<p>Καλωσήρθατε!</p>
                                     
                                     <p>Παρακαλούμε εισάγετε ΑΦΜ, Αρ. Μητρώου και πιέστε "Συνέχεια" για να εμφανιστούν τα μισθοδοτικά σας στοιχεία</p>',
                        
                        'login_below' => '<p>Σημείωση: Όσοι δεν έχουν 6ψήφιο αριθμό μητρώου (π.χ. αναπληρωτές), πρέπει να αφήσουν το σχετικό πεδίο ασυμπλήρωτο.</p>
                            <p>Σημείωση: Όσοι έχουν προστατεύσει το ΑΦΜ τους με κωδικό, θα πρέπει να εισάγουν αυτόν στο πεδίο Αρ. Μητρώου.</p>',
                        
                        'footer' => '<hr>Προσοχή! Τα μισθοδοτικά στοιχεία παρέχονται για ενημέρωση και μόνο. Αν χρειάζεστε βεβαίωση αποδοχών με υπογραφή εκκαθαριστή και σφραγίδα υπηρεσίας (π.χ. για κατάθεση σε τράπεζα, σε άλλη υπηρεσία κ.τ.λ.), θα πρέπει να τη ζητήσετε από τη Δ/νση.',

                        'afm_label' => 'Α.Φ.Μ.:',
                        'amm_label' => 'Αρ. Μητρώου ή κωδικός:'
                    );

        file_put_contents(APP_DIR . '/cms/lang.json', json_encode($txt)) or die("Αποτυχία αποθήκευσης");
        

        $test = json_decode(file_get_contents(APP_DIR . '/cms/lang.json'), true);
        dump($test);

        echo '<h3>Τα κείμενα της εφαρμογής επανήλθαν</h3>';

    }else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}

print_footer();