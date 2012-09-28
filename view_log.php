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
        
        if(isset($_POST['user_lines'])){
            $user_lines = (int) $_POST['user_lines'];
            if($user_lines < 0) $user_lines = 0;
            cleanup_log_file('user_log.txt', $user_lines);
            //dump($user_lines);
        }  

        if(isset($_POST['admin_lines'])){
            $admin_lines = (int) $_POST['admin_lines'];
            if($admin_lines < 0) $admin_lines = 0;
            cleanup_log_file('admin_log.txt', $admin_lines);
            //dump($admin_lines);
        }
       
        ?>
            <div id="user-log" class="box">
                <h3>Ενέργειες χρηστών</h3>
                <div class="cleanup">
                    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                        Κράτα μόνο τις 
                        <select name="user_lines" id="">
                            <option value="0">0</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100" selected="selected">100</option>
                            <option value="200">200</option>
                            <option value="300">300</option>
                            <option value="500">500</option>
                        </select> 
                        πιο πρόσφατες ενέργειες <input type="submit" value="Εκκαθάριση" class="confirm_cleanup"  rel="Η εκκαθάριση του αρχείου καταγραφής είναι ΜΗ αναστρέψιμη ενέργεια. Είστε σίγουρος ότι θέλετε να συνεχίσετε;" />
                    </form>                    
                </div>
                <?php print_log_file('user_log.txt'); ?>
            </div>
            <div id="admin-log" class="box">
                <h3>Ενέργειες διαχειριστή</h3>
                <div class="cleanup">
                    <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                        Κράτα μόνο τις 
                        <select name="admin_lines" id="">
                            <option value="0">0</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50" selected="selected">50</option>
                            <option value="100">100</option>
                        </select> 
                        πιο πρόσφατες ενέργειες <input type="submit" value="Εκκαθάριση" class="confirm_cleanup" rel="Η εκκαθάριση του αρχείου καταγραφής είναι ΜΗ αναστρέψιμη ενέργεια. Είστε σίγουρος ότι θέλετε να συνεχίσετε;" />
                    </form>                                     
                </div>
                <?php print_log_file('admin_log.txt'); ?>
            </div>
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
    // Διαβάζει ένα αρχείο καταγραφής και το εμφανίζει με ανάποδη σειρά
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

function cleanup_log_file($file, $lines = 20){
    // Κρατά μόνο τις τελευταίες $lines σειρές από ένα αρχείο καταγραφής
    $logfile = trailingslashit(APP_DIR) . $file;

    if(file_exists($logfile)){
        $full = file($logfile);
        $slice = array_slice($full, -$lines, $lines);

        $cleaned_up = implode("", $slice);

        $fp = fopen($logfile, 'w');
        fwrite($fp, $cleaned_up);
        fclose($fp);
    }
}