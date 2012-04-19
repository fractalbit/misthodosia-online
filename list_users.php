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
// Το αρχείο αυτό εμφανίζει λίστα με όλους τους μισθοδοτούμενους και επιτρέπει αναζήτηση με autocomplete...
// μόνο αν κάποιος είναι συνδεδεμένος ως διαχειριστής.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */


include_once('./init.inc.php');


print_header();

echo '<div id="userlist-container">';
if(admin_configured()){

    if($admin->check_logged_in()){
        //echo $admin->message;

        $file = USER_DIR . '/all.php';
        if(file_exists($file)){  

            echo '<label for="search_filter">Γρήγορη αναζήτηση</label><br  /><input type="text" id="search_filter" />';
            echo '<ul id="user-list">';             
            $all = load_file($file);
            foreach ($all as $user) {
                $test = trim($user['name']);
                if(!empty($test)) {
                    $link = '<li><a href="index.php?afm='.$user['afm'].'" target="_blank">'.$user['name']. '</a> (' . $user['afm'] . ')</li>';
                    echo $link;
                }
            }
            echo '</ul>';
        }else{
            echo 'Δε βρέθηκαν μισθοδοτούμενοι!';
        }

    }else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}

echo '</div>';

print_footer();