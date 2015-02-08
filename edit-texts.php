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
// Το αρχείο αυτό επιτρέπει τη διαχείριση των αρχείων XML...
// μόνο αν κάποιος είναι συνδεδεμένος ως διαχειριστής.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */


include_once('./init.inc.php');


print_header();


echo '<div class="clearfix">';
if(admin_configured()){

    if($admin->check_logged_in()){

        if(isset($_POST['header'])){
            // Αποθήκευση των κειμένων
            $txt_new = array('header' => $_POST['header'], 'login_below' => $_POST['login_below'], 'footer' => $_POST['footer'], 'afm_label' => $_POST['afm_label'], 'amm_label' => $_POST['amm_label']);
            file_put_contents(APP_DIR . '/cms/lang.json', json_encode($txt_new)) or die("Αποτυχία αποθήκευσης");
            echo '<h2 style="color: green">Τα δεδομένα αποθηκεύτηκαν με επιτυχία</h2>';
        }

        $txt = json_decode(file_get_contents(APP_DIR . '/cms/lang.json'), true); // Φόρτωσε το αρχείο γλώσσας
        
        ?>
            <!-- Include FROALA JS files. -->
            <script src="js/froala/js/froala_editor.min.js"></script>
            <script src="js/froala/js/plugins/colors.min.js"></script>
            <script src="js/froala/js/plugins/font_size.min.js"></script>
            <script src="js/froala/js/plugins/lists.min.js"></script>

            <!-- Include IE8 JS. -->
            <!--[if lt IE 9]>
                <script src="../js/froala_editor_ie8.min.js"></script>
            <![endif]-->

            <!-- Initialize the FROALA editor. -->
            <script>
                $(function() {
                    $('.text').editable({
                        inlineMode: false,
                        buttons: ["undo", "redo", "sep", "bold", "italic", "underline", "strikeThrough", "sep", "fontFamily", "fontSize", "color", "formatBlock",  "align", "sep", "insertOrderedList", "insertUnorderedList", "sep", "createLink", "insertHorizontalRule", "sep", "html", "removeFormat"]
                    })
                });
            </script>

            <form id="texts" action="" method="POST">
                <h2>Κείμενα αρχικής σελίδας</h2>
                <textarea class="text" name="header" id="header"><?php echo $txt['header'] ?></textarea><br /><br />
                <textarea class="text" name="login_below" id="login_below"><?php echo $txt['login_below'] ?></textarea><br /><br />
                <textarea class="text" name="footer" id="footer"><?php echo $txt['footer'] ?></textarea><br /><br />
                <h2>Ετικέτες για τη φόρμα σύνδεσης</h2>
                <input style="padding: 8px" type="text" name="afm_label" value="<?php echo $txt['afm_label'] ?>" /><br /><br />
                <input style="padding: 8px" type="text" name="amm_label" value="<?php echo $txt['amm_label'] ?>" /><br /><br />

                <br />

                <input type="submit" class="button" value="Αποθήκευση" style="padding: 10px 10px; font-size: 16px; font-weight: bold;" />
            </form>
        <?php
       



    }else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}

echo '</div>';

print_footer();

