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
// Το αρχείο αυτό εμφανίζει ποίες ενέργειες έκαναν χρήστες ή διαχειριστές και πότε...
// μόνο αν κάποιος είναι συνδεδεμένος ως διαχειριστής.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */


include_once('./init.inc.php');

print_header();

echo '<div id="log-container" class="clearfix">';

if(admin_configured()){

    if($admin->check_logged_in()){
        //echo $admin->message;

        ?>
            <div id="user-log" class="box"><h3>Ενέργειες χρηστών</h3><?php print_log_file('user_log.txt'); ?></div>
            <div id="admin-log" class="box"><h3>Ενέργειες διαχειριστή</h3><?php print_log_file('admin_log.txt'); ?></div>
        <?
    }else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}

echo '</div>';

print_footer();


function print_log_file($file){
    $logfile = trailingslashit(APP_DIR) . $file;

    if(file_exists($logfile)){
        $log = array_reverse(file($logfile));
        
        foreach($log as $line){
            echo $line .'<br />';
        }
    }else{
        echo 'Το αρχείο καταγραφής δε βρέθηκε';
    }
}