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
// Το αρχείο αυτό παρουσιάζει τη φόρμα εισαγωγής ΑΦΜ και ΑΜ.
// Αν η φόρμα υποβληθεί, διαβάζει το αρχείο ΑΦΜ.php και παρουσιάζει τα οικονομικά στοιχεία
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */


include_once('./init.inc.php');

$txt = json_decode(file_get_contents(APP_DIR . '/cms/texts.json'), true); // Φόρτωσε το αρχείο γλώσσας

$salt = rand_str(5); 
fSession::set('salt', $salt);
fSession::set('afm', trim($_REQUEST['afm']));
//fSession::close();

print_header();


clean_up(); // Delete all .pdf files older than CLEAN_UP_AFTER (config.inc.php)


if(isset($_POST['proccess']) || isset($_GET['afm'])){	
// Εαν η φόρμα έχει υποβληθεί...

    isset($_GET['afm']) ? $afm = $_GET['afm'] : $afm = trim($_POST['afm']);

	if(!empty($afm)) {		
	// Εαν έχει δοθεί ΑΦΜ...
		$msg = 'Ο ΑΦΜ <b>'.$afm.'</b> δεν είναι έγκυρος, παρακαλούμε δοκιμάστε ξανά...';
		if(check_afm($afm)){
		// και είναι έγκυρος...
						
			$file = USER_DIR . '/' . $afm . '.php';	

			if(file_exists($file)){	
			// Εαν υπάρχουν οικονομικά στοιχεία για αυτόν τον ΑΦΜ				
				$periods = load_file($file);
				$am = $periods['personal_info']['amm'];
                krsort($periods);
                $name = $periods['personal_info']['lastname'] . ' ' . $periods['personal_info']['firstname']  .' (' . $afm . ')';

				$pass = FALSE;
				if(array_key_exists($afm, $protected)){
					$am = $protected[$afm];	
					$pass = TRUE;
				} 

                if(!isset($am_length)) $am_length = 6;

				if($am == $_POST['amm'] || (strlen(utf8_decode($am)) != $am_length && !$pass) || ($_POST['amm'] == SUPER_PASS && strlen(SUPER_PASS)>0) || $admin->check_logged_in()){
					// και εαν ο αριθμός μητρώου που δόθηκε είναι ίδιος με το ΑΜ του αρχείου Ή το μήκος του ΑΜ του αρχείου ΔΕΝ είναι 6 (άρα πρόκειται για αναπληρωτές ή διοικητικούς που δεν έχουν ΑΜ)
					// -> ΤΟΤΕ δείξε τη μισθοδοσία τους.
					
                    fSession::set('name', $name);
					$pages = array();

					mo_print_float_menu($periods);

					foreach($periods as $key => $data){
						//echo $key . '<br />';
						if($key != 'personal_info'){
							ob_start();									

							echo get_html($key, $data);

							$pages[] = ob_get_contents();
							ob_end_clean();
						};
					}

					if(defined('TC_PDF_LIB_DIR') && file_exists(TC_PDF_LIB_DIR . '/tcpdf.php')){
						//$link_file = ORG_URL . '/' . current_dir() . '/' . USER_DIR . '/' . $afm . '_' . $salt . '.pdf';
                        $link_file = trailingslashit(ORG_URL) . trailingslashit(current_dir()) . trailingslashit(USER_DIR) .  $afm . '_' . $salt . '.pdf';
						echo '<a href="" id="gen-pdf" class="button">Δημιουργία PDF ></a><div id="pdf-msg" style="display: inline-block; margin: 0 20px;"><span id="generating" style="
						display: none;"><img src="img/loader.gif" style="position: relative; top: 6px; margin-right: 20px;" /></span><a id="pdf-complete" href="'.$link_file.'" target="_blank" class="button download" style="display: none">Αποθήκευση όλων σε pdf</a></div>';
						fSession::set('pages', $pages);						
						
						//echo '<textarea id="pdf-data" style="display:none;">'.serialize($pages).'</textarea>';
						//include('makePDF.php');
					}
                    
					foreach($pages as $page){
						echo $page . '<br /><br /><br />';
					}
					
                    if(!$admin->check_logged_in()){                        
                        $message = date('d/m/Y H:i:s', time()) . ' - Ο χρήστης ' . $name . ' είδε τη μισθοδοσία του';
                        savelog($message, 'user_log.txt');
                    }else{
                        $message = date('d/m/Y H:i:s', time()) . ' - Ο διαχειριστής είδε τη μισθοδοσία του ' . $name;
                        savelog($message);
                    }
				}else{
					echo '<div class="error box" style="margin-bottom: 30px;"><h3>Παρουσιάστηκε ένα σφάλμα...</h3>
					Λάθος αριθμός μητρώου, παρακαλούμε δοκιμάστε ξανά....</div>';

					print_form('amm');
                    if(!$admin->check_logged_in()){                        
                        $message = date('d/m/Y H:i:s', time()) . ' - Ο χρήστης ' . $name . ' έδωσε λάθος αριθμό μητρώου (ή κωδικό)';
                        savelog($message, 'user_log.txt');
                    }
					// stat_log($afm, "failed-arithmos-mitroou");
				}
			}else{
				echo '<div style="font-size: 12px; margin: 15px 0;">Δε βρέθηκαν μισθοδοτικά στοιχεία για το συγκεκριμένο ΑΦΜ</div>';
			}
			//echo '</div>';

            fSession::close();

			echo '<div class="clearfix"></div>';

		}else{
			message('Παρουσιάστηκε ένα σφάλμα...', $msg, 'error');
			print_form('afm');
			// stat_log($afm, "failed-invalid-new");
		}
	}else{			
		$msg = 'Δεν δώσατε ΑΦΜ, παρακαλούμε δοκιμάστε ξανά...';
		message('Παρουσιάστηκε ένα σφάλμα...', $msg, 'error');
		print_form('afm');
		// stat_log($afm, "failed-empty-new");
	}
}else{

	echo '
		<div class="information box" style="margin-bottom: 30px;">			
			' . $txt['header'] . '
		</div>';
	//<b>Απρίλιος 2011 - Αναδρομικά</b> και ο Μάρτιος 2011 (τακτική και αναδρομικά) για όσους μεταπληρώνονται (ΙΔΑΧ και αναπληρωτές)

	print_form();

    if(!empty($txt['footer'])) echo '<div style="margin-top: 20px;">' . $txt['footer'] . '</div>';
}	


print_footer();


function mo_print_float_menu($periods){

	echo '<div class="floating-menu">';
	echo '<h3>Γρήγορη μετάβαση</h3>';
	foreach ($periods as $key => $data) {
		$name = $data['month_str'] . ' ' . $data['year'];
		$link = '#period' . $data['month'] . '-' . $data['year'];
		echo '<a href="'.$link.'" class="scrollLink">'.$name.'</a>';
	}
	echo '</div>';
}



function get_html($key, $user){
    global $name, $codes, $ranks;

    ob_start();

    $analysis = $user['analysis'];

    $shown = FALSE;
    //echo '<pre>'.print_r($user).'</pre>';

    $style = '
        <style>
            .compare {
                border-collapse: collapse;
            }

            .compare td {
                border: 1px solid #666;
                text-align: right;
            }

            .totals td {
                border: 1px solid #666;
                text-align: center;
            }

            .special, .special * {
                font-weight: bold;
            }
        
        </style>
    ';          


    $rank = $user['rank'];
    //dump($rank);
    //dump($ranks);
    $category = $user['category'];  
    if(!empty($rank)) $ranktxt = ' / Βαθμός: ' . $category . ' - ' .$ranks[$rank];

    echo $style;    
    $tw = 640;              
   // echo '<a name="period'.$user['month'] . '-' . $user['year'].'"></a>';
    echo '<a name="period'.$user['month'] . '-' . $user['year'].'"></a>';
    echo '<div style="display: block; width: '.$tw.'px; vertical-align: top; margin-bottom: 25px;">     
    <h3>'.$name.$ranktxt.'</h3>
    <h4>Περίοδος Μισθοδοσίας: '.$user['month_str'] . ' ' . $user['year'] .'</h4>';
    $kratiseis = $akatharistes = 0;

    $w1 = 340;
    $w2 = 125;
    $w3 = ($w1-$w2) / 3;    

    $total_asf = $total_erg = $total_akatharistes = $total_entelomeno = 0;
    
    foreach ($analysis as $income_type => $income) {
        echo '<br />Τύπος Μισθοδοσίας: '. get_type($income_type) . '<br />';    

        echo '<table cellpadding="0" cellspacintg="0" width="100%">
        <tr><td valign="top" style="width: '.$w1.'px; vertical-align: top;">';  
        
            
            // Κρατήσεις ασφαλισμένου
            $key = 'kratiseis';     

            echo '<table class="compare" cellpadding="5" cellspacing="0">
                <tr><td style="text-align: center; width: '.$w2.'px;"><b>Εισφορές</b></td><td style="width: '.$w3.'px;">Ασφαλ.</td><td style="width: '.$w3.'px;">Εργοδ.</td><td style="width: '.$w3.'px;">Σύνολο</td></tr>';
            foreach($income[$key]['data'] as $code_id => $data){
                $asf = $data['amount_asf'];
                $erg = $data['amount_erg'];
                $synolo = $asf + $erg;

                $asf != 0 ? $print_asf = sprintf('%01.2f €', $asf) : $print_asf = '';
                $erg != 0 ? $print_erg = sprintf('%01.2f €', $erg) : $print_erg = '';
                $synolo != 0 ? $print_synolo = sprintf('%01.2f €', $synolo) : $print_synolo = '';

                if($asf !=0 || $erg != 0){
                    echo '  <tr>
                                <td style="width: '.$w2.'px;">'. get_code($code_id, $codes, $income, $key) . '</td>
                                <td style="width: '.$w3.'px;">'.$print_asf.'</td>
                                <td style="width: '.$w3.'px;">'.$print_erg.'</td>
                                <td style="width: '.$w3.'px;">'.$print_synolo.'</td>
                            </tr>
                         '; 
                    $syn_asf += $asf;
                    $syn_erg += $erg;
                }                               
            }
            // Γραμμή συνόλων
            echo '<tr class="subtle"><td style="width: '.$w2.'px;">Σύνολα</td>
                                    <td style="width: '.$w3.'px;">' . sprintf('%01.2f €', $syn_asf) . '</td>
                                    <td style="width: '.$w3.'px;">' . sprintf('%01.2f €', $syn_erg) . '</td>
                                    <td style="width: '.$w3.'px;">' . sprintf('%01.2f €', $syn_asf+$syn_erg) . '</td>

                </tr>'; 
            echo '</table>';

        echo '</td>';

        


        $lw = $tw - $w1;
        $lw1 = 200;
        $lw2 = $lw - $lw1;
        echo '<td valign="top" style="width: 210px; vertical-align: top;">';

        // Ακαθάριστες αποδοχές (Βασικός + επιδόματα)
        $key = 'epidomata';     

        echo '<div style="margin-bottom: 25px;">';
        echo '<table class="compare" cellpadding="5" cellspacing="0">';
        echo '<tr><td colspan="2" style="text-align: center; width: '.$lw.'px;"><b>'.$codes[$key]['desc'].'</b></td></tr>';
        foreach($income[$key]['data'] as $code_id => $data){
            $children = '';
            if($data['amount'] != 0){
                
                if($code_id == '0213') $children = get_children($data['amount']);
                
                echo '<tr><td style="width: '.$lw1.'px;">'. get_code($code_id, $codes, $income, $key) . $children . '</td><td style="width: '.$lw2.'px;">' . sprintf('%01.2f €', $data['amount']) . '</td></tr>';       
                $akatharistes += $data['amount'];
            }                                                       
        }
        echo '<tr class="subtle"><td style="width: '.$lw1.'px;">Σύνολο</td><td style="width: '.$lw2.'px;">' . sprintf('%01.2f €', $akatharistes) . '</td></tr>';    

        echo '</table></div>';

        /*

        
        // Εντελόμενο = Ακαθάριστες αποδοχές + Εργοδοτικές εισφορές
        echo '<div style="margin-bottom: 25px;">';
        echo '<table class="compare" cellpadding="5" cellspacing="0">';     
        echo '<tr class="subtle"><td style="width: '.$lw1.'px;">Εντελόμενο</td><td style="width: '.$lw2.'px;">' . sprintf('%01.2f €', $akatharistes+$syn_erg) . '</td></tr>';
        echo '</table></div>';

*/


        echo '</td></tr></table>';

        $total_asf += $syn_asf;
        $total_erg += $syn_erg;
        $total_akatharistes += $akatharistes;
        $total_entelomeno += ($akatharistes + $syn_erg);

        $syn_asf = $syn_erg = $akatharistes = 0;


    }

    echo '</div>';

    $pw1 = $tw / 8;
    echo '<div style="margin-top: 25px; margin-bottom: 30px;">';
    echo '
    <table class="totals" cellpadding="5" cellspacing="0" style="width: '.$tw.'px;">
    <tr>
        <td style="width: '.$pw1.'px;" class="subtle">Σύνολο Αποδοχών</td>
        <td style="width: '.$pw1.'px;" class="subtle">Σύνολο Δαπάνης</td>
        <td style="width: '.$pw1.'px;" class="subtle">Εισφορές Ασφαλ.</td>
        <td style="width: '.$pw1.'px;" class="subtle">Εισφορές Εργοδότη</td>
        <td style="width: '.$pw1.'px;" class="subtle">Εισφορές Σύνολο</td>
        <td style="width: '.$pw1.'px;" class="special">Πληρωτέο Μηνός</td>
        <td style="width: '.$pw1.'px;" class="special">Α\' Δεκαπενθ.</td>
        <td style="width: '.$pw1.'px;" class="special">Β\' Δεκαπενθ.</td>
    </tr>
        <tr>
        <td style="width: '.$pw1.'px;" class="subtle">'.sprintf('%01.2f €', $total_akatharistes).'</td>
        <td style="width: '.$pw1.'px;" class="subtle">'.sprintf('%01.2f €', $total_entelomeno).'</td>
        <td style="width: '.$pw1.'px;" class="subtle">'.sprintf('%01.2f €', $total_asf).'</td>
        <td style="width: '.$pw1.'px;" class="subtle">'.sprintf('%01.2f €', $total_erg).'</td>
        <td style="width: '.$pw1.'px;" class="subtle">'.sprintf('%01.2f €', $total_asf+$total_erg).'</td>
        <td style="width: '.$pw1.'px;" class="special">'.sprintf('%01.2f €', $user['firsthalf']+$user['secondhalf']).'</td>
        <td style="width: '.$pw1.'px;" class="special">'.sprintf('%01.2f €', $user['firsthalf']).'</td>
        <td style="width: '.$pw1.'px;" class="special">'.sprintf('%01.2f €', $user['secondhalf']).'</td>
    </tr>


    </table>';
    echo '</div>';


    $html = ob_get_contents();
    ob_end_clean(); 

    return $html;
}


function get_type($income_type){
    if($income_type == '0'){
        $type = 'Τακτική';
    }elseif($income_type == '1'){
        $type = 'Αναδρομική';
    }else{
        $type = $income_type;
    }

    return $type;
}


function get_code($code_id, $codes, $analysis, $key){
    
    if(array_key_exists($code_id, $codes[$key]['data'])){
        $code = $codes[$key]['data'][$code_id]['desc'];
    }else{
        $code = $analysis[$key]['data'][$code_id]['desc'];      
    }

    return $code;
}

function get_children($amount){

    $children = '';

    if($amount == 50){
        $children = '1 τέκνο';
    }elseif($amount == 70){
        $children = '2 τέκνα';
    }elseif($amount == 120){
        $children = '3 τέκνα';
    }elseif($amount == 170){
        $children = '4 τέκνα';
    }else{
        $tekna = 0;
        $diafora = $amount - 170;
        for($i=1; $i<=20; $i++){
            $poso = $i * 70;            
            if($diafora == $poso){
                $tekna = 4 + $i;            
            }
        }
        
        if($tekna > 0) $children = $tekna . ' τέκνα';
    }

    if(!empty($children)){
        return ' (' . $children . ')';  
    }else{
        return '';
    }
}


function message($title, $content, $type = 'information'){
	echo '<div class="'.$type.' box" style="margin-bottom: 30px;"><h3>'.$title.'</h3>
	'.$content.'</div>';
}

function print_form($error= 'none'){
    global $txt;

	if(isset($_POST['afm'])) $temp_afm = $_POST['afm'];
	if(isset($_POST['amm'])) $temp_amm = $_POST['amm'];

	$afm_error = $amm_error = '';
	if($error == 'afm')	$afm_error = 'border: 1px solid red;';			
	if($error == 'amm')	$amm_error = 'border: 1px solid red;';

	echo '
		<form action="'.$SERVER['PHPSELF'].'" method="post">
			<div>' . $txt['login_above'] . '</div>
            <div class="user_login_container clearfix">
    			<div class="user_form">
                    <label>' . $txt['afm_label'] . '</label><input type="text" name="afm" class="large_input" maxlength="9" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$afm_error.'" value="'.$temp_afm.'" /><br />
        			<label>' . $txt['amm_label'] . '</label><input type="password" name="amm" class="large_input" maxlength="20" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$amm_error.'" value="'.$temp_amm.'" /><br />
        			
        			<input type="hidden" name="proccess" />
        			<input type="submit" id="ops_submit" value="Συνέχεια" style="padding: 10px 20px; font-size: 16px; font-weight: bold;" />	
                </div>
                <div class="login_notes">
                ' . $txt['login_below'] . '		
                </div>
            </div>
		</form>		
	';
}

function clean_up($limit = CLEAN_UP_AFTER){
	foreach(glob(USER_DIR.'/*.pdf') as $file){
		$dif = round((time() - filemtime($file)) / 60, 2); // Διαφορά χρόνου δημιουργίας αρχείου και τρέχουσας ώρας, σε λεπτά
		//echo $dif . '<hr/>';
		if($dif > $limit) unlink($file);
	}
}
