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


        echo '<div id="xmlfiles-container">';


        if(isset($_POST['process'])){
           // die('ok!!!!!');
            $uploader = new fUpload();
            $uploader->setMIMETypes(
                array(
                    'application/xml',
                    'text/xml'
                ),
                'Το αρχείο που ανεβάσατε δεν είναι XML'
            );
            $uploader->enableOverwrite();

            $dir = full_dir() . DIRECTORY_SEPARATOR . XML_DIR;

            $uploader->move($dir, 'file');

            //dump($uploader);
            $message = date('d/m/Y H:i:s', time()) . ' - Ο διαχειριστής ανέβασε το αρχείο ' . $_FILES['file']['name'];
            savelog($message);
        }

       ?>

            <form id="upload-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <label for="file">Νέο αρχείο: </label>
                <input id="file" type="file" name="file" />
                <input type="submit" value="Ανέβασμα" />
                <input type="hidden" name="process" value="yes">
                <div>Σημείωση: Οι ελληνικοί χαρακτήρες σβήνονται από το όνομα του αρχείου κατά το ανέβασμα. Χρησιμοποιείστε greeklish (ή ανεβάστε τα με FTP) αν θέλετε να εμφανίζονται στην παρακάτω λίστα</div>
            </form>

       <?php

        $xml_files = array();
        foreach(glob(XML_DIR . '/*.xml') as $file){
            // $test = new fFile($file);
            // $test2 = $test->getMimeType();
            // dump($test2);
            
            $time = filemtime($file);
            $period = get_period_from_xml($file);
            $period_str = $period['period_str'];
            $xml_files[$period_str] = array('filename' => $file, 'period' => $period, 'time' => $time);
            //echo $name . ' <span>Ανέβηκε στις '.date('d/m/Y H:i', filemtime($file)).'</span><br />';
        }

        $scanned_files = trailingslashit(APP_DIR) . 'scanned_files.php';
        if(file_exists($scanned_files)){
            $analyzed = load_file($scanned_files);
        }else{
            $analyzed = array();
        }
        //dump($analyzed);
        krsort($xml_files);

/*        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $upload_mb = min($max_upload, $max_post, $memory_limit);
        dump($upload_mb);*/

        //$test = full_dir(); dump($test);

        echo '<h2>Λίστα XML αρχείων</h2><ul>';
        $red_flag = false;
        $i = 1;
        foreach($xml_files as $p_str => $data){
            $time = $data['time'];
            $temp_name = explode('/', $data['filename']);
            $name = end($temp_name);
            $period_id = $data['period']['month'] . ' ' . $data['period']['year'];
            $file_and_time = $name .'_'. $time;
            if(isset($analyzed[$file_and_time])){
                $class='scanned';
            }else{
                $class='notscanned';
                $red_flag = true;
            }
            echo '<li class="'.$class.'">' . $i . '. ' . $period_id . ' <span>(' .mb_convert_encoding($name, 'UTF-8', 'ISO-8859-7') . ') - Ανέβηκε στις '.date('d/m/Y H:i', $time).'</span> - <a href="file='.urlencode($data['filename']).'" class="delete confirm" rel="Είστε σίγουρος ότι θέλετε να διαγράψετε αυτό το αρχείο;">Διαγραφή</a></li>';
            $i++;
        }        
        echo '</ul>';

        echo '</div>';

        // dump(count($xml_files));
        // dump(count($analyzed));

        if($red_flag || count($xml_files) != count($analyzed)){
            echo '<div class="info_box red">Ο πίνακας αναλυθέντων αρχείων δε συμφωνεί με τα αρχεία που υπάρχουν στο φάκελο XMLDATA. Θα πρέπει να τρέξετε την... <br /><br /><a class="button" href="scanXMLdata.php" target="_blank">Ανάλυση αρχείων</a></div>';
        }else{
            echo '<div class="info_box green">Όλα τα αρχεία έχουν αναλυθεί</div>';
        }

        echo '<div class="info_box">Στη σελίδα αυτή εμφανίζονται όλα τα αρχεία XML που έχουν ανέβει. Αν ένα αρχείο έχει αναλυθεί εμφανίζεται με πράσινο χρώμα, αν όχι με κόκκινο. Εδώ μπορείτε επίσης να ανεβάσετε ένα νέο αρχείο, να διαγράψετε κάποιο από τα υπάρχοντα ή να τρέξετε εκ νέου την ανάλυση των αρχείων (απαραίτητο αν ανεβάσετε νέο ή διαγράψετε κάποιο για να ενημρωθούν τα στοιχεία των μισθοδοτούμενων)</div>';
        
        $scanlog = trailingslashit(APP_DIR) . 'scan_times.txt';
        if(file_exists($scanlog)) {
            $scanlog_loaded = file($scanlog);
            $last_scan_time = (int) end($scanlog_loaded);
            $last_scan_info = '<br /><br />Η ανάλυση αρχείων έτρεξε για τελευταία φορά στις ' . date('d/m/Y H:i:s', $last_scan_time);
        }else{
            $last_scan_info = '';
        }
        
        echo '<div class="info_box"><a class="button" href="scanXMLdata.php" target="_blank">Ανάλυση αρχείων</a>'.$last_scan_info.'</div>';




    }else{
        echo '<div class="error">'.$admin->message.'</div>';
        echo $admin->show_login_form();
    }

}else{
    echo $admin->message;
}

echo '</div>';

print_footer();


function get_period_from_xml($file){
// Διαβάζει ένα αρχείο XML και επιστρέφει πληροφορίες για τη μισθοδοτική περίοδο που αυτό αφορά
    global $changed_afm, $pliromes, $codes, $dataset, $months, $first, $second;
    
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

    // Δημιουργία μοναδικού* αλφαρηθμιτικού που χρησιμεύει ως αναγνωριστικό περιόδου μισθοδοσίας και ταξινομείται σωστά χρονολογικά
    $period_str = $period['year'] . '_' . $period['month'] . '_' . $xml->header->transaction->periodType['value'] . '_' . rand_str(8);
    // Προστέθηκε ένα τυχαίο string στο τέλος για να ξεχωρίζουν αρχεία που αφορούν στην ίδια περίοδο

    return array('period_str' => $period_str, 'month' => $month_str, 'year' => $year);
}