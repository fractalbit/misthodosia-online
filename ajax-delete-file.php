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

if(admin_configured()){

    if($admin->check_logged_in()){        
        $file = urldecode($_GET['file']);
        //echo $file;
        unlink($file);

        $message = date('d/m/Y H:i:s', time()) . ' - Ο διαχειριστής διέγραψε το αρχείο ' . mb_convert_encoding($file, 'UTF-8', 'ISO-8859-7');
        savelog($message);
    }else{
        echo $admin->message;
    }

}else{
    echo $admin->message;
}
