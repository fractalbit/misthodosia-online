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

$salt = rand_str(5); 
fSession::set('salt', $salt);
if(isset($_REQUEST['afm'])) fSession::set('afm', trim($_REQUEST['afm']));
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

                if(isset($_POST['amm'])) $given_amm = $_POST['amm']; else $given_amm = '';

                // dump($given_amm);
                // dump($am);
				if($am == $given_amm || (strlen(utf8_decode($am)) != $am_length && !$pass) || ($given_amm == SUPER_PASS && strlen(SUPER_PASS)>0) || $admin->check_logged_in()){
					// και εαν ο αριθμός μητρώου που δόθηκε είναι ίδιος με το ΑΜ του αρχείου Ή το μήκος του ΑΜ του αρχείου ΔΕΝ είναι 6 (άρα πρόκειται για αναπληρωτές ή διοικητικούς που δεν έχουν ΑΜ)
					// -> ΤΟΤΕ δείξε τη μισθοδοσία τους.
					
                    fSession::set('name', $name);
                    $pages = array();
                    $select_values = array();
                    $pdf_years = array();

					mo_print_float_menu($periods);
                    $pid = 0;
					foreach($periods as $key => $data){
						//echo $key . '<br />';
						if($key != 'personal_info'){
							ob_start();									

                            // echo get_html($data);
                            // dump($data);
                            $current = new misthodosia($data);
                            $current->ektiposi();

							$pages[] = ob_get_contents();
                            ob_end_clean();
                            
                            $pid++;
						};
                    }

                    $allow_pdf = TRUE; // Maybe turn this into a setting?
					if($allow_pdf){
						//$link_file = ORG_URL . '/' . current_dir() . '/' . USER_DIR . '/' . $afm . '_' . $salt . '.pdf';
                        $link_file = trailingslashit(ORG_URL) . trailingslashit(current_dir()) . trailingslashit(USER_DIR) .  $afm . '_' . $salt . '.pdf';
                        print_pdf_select_menu($select_values, $pdf_years);
						echo '<a href="" id="gen-pdf" class="button">Δημιουργία PDF ></a><div id="pdf-msg" style="display: inline-block; margin: 0 20px;"><span id="generating" style="
						display: none;"><img src="img/loader.svg" height="20" style="position: relative; top: 6px; margin-right: 20px;" /></span><a id="pdf-complete" href="'.$link_file.'" target="_blank" class="button download" style="display: none">Εμφάνιση/λήψη του pdf</a></div>';
						fSession::set('pages', $pages);						
						fSession::set('pdf_years', $pdf_years);	
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
        if($key != 'personal_info'){
            $name = $data['month_str'] . ' ' . $data['year'];
            $link = '#period' . $data['month'] . '-' . $data['year'];
            echo '<a href="'.$link.'" class="scrollLink">'.$name.'</a>';
        }
	}
	echo '</div>';
}

Class misthodosia {
    private $raw_data;
    private $user = array('name' => '', 'ranktxt' => '');
    
    // private $total_asf = 0, $total_erg = 0, $total_akatharistes = 0, $total_entelomeno = 0, $syn_asf = 0, $syn_erg = 0, $akatharistes = 0, $syn_erg_only = 0;
    private $sinolo = array('asf' => 0, 'erg' => 0, 'apodoxon' => 0, 'dapanis' => 0); // Για όλο το μήνα αθροιστικά
    private $meriko_sinolo = array('asf' => 0, 'erg' => 0, 'apodoxon' => 0, 'dapanis' => 0); // Για κάθε τύπο μισθοδοσίας μέσα στο μήνα (π.χ. Τακτική ή αναδρομική)
    
    // private $income, $income_type;

    public function __construct($raw){
        $this->raw_data = $raw; 
        $this->user['name'] = $GLOBALS['name'];        
    }

    private function print_header(){
        global $select_values, $pdf_years, $pid;

        $data = $this->raw_data;

        $rank = isset($data['rank']) ? $data['rank'] : '';
        $mk = isset($data['mk']) ? $data['mk'] : '';
        $category = isset($data['category']) ? $data['category'] : '';         
        if(!empty($rank)) $this->user['ranktxt'] = ' / Βαθμός: ' . $category . ' - ' .$GLOBALS['ranks'][$rank]; else $this->user['ranktxt'] = '';
        if(!empty($mk)) $this->user['ranktxt'] = ' / Κατηγορία: ' . $category . ' / ΜΚ ν. 4354/2015: ' . $mk;

        echo '<a name="period'.$data['month'] . '-' . $data['year'].'"></a>';
        echo '
            <h3>'.$this->user['name'].$this->user['ranktxt'].'</h3>
            <h4>Περίοδος Μισθοδοσίας: '.$data['month_str'] . ' ' . $data['year'] .'</h4> 
        ';
        
        $select_values[] = $data['month_str'] . ' ' . $data['year']; // This is needed for the print_pdf_select_menu function
        $pdf_years[$data['year']][] = $pid;  
    }    

    public function ektiposi(){

        $this->print_header();

        foreach ($this->raw_data['analysis'] as $income_type => $income) {
            echo '<div style="margin-bottom: 5px; border: 1px solid #ccc; padding: 5px; text-align: left; color: #666; font-style: italic">Τύπος Μισθοδοσίας: '. $this->get_type($income_type) . '</div>';          

            $this->meriki_ektiposi($income, $income_type);
        }

        $this->print_totals();
        
    }    

    private function meriki_ektiposi($income, $income_type){
        echo '
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="vertical-align: top; width: 45%; padding-right: 10px;">', $this->ektiposi_apodoxon($income, $income_type) ,'</td>
                    <td style="vertical-align: top; width: 55%;">', $this->ektiposi_kratiseon($income, $income_type) ,'</td>
                </tr>
            </table>
        ';
    }

    private function ektiposi_apodoxon($income, $income_type){
        
        $this->meriko_sinolo['apodoxon'] = $this->meriko_sinolo['erg'] = 0;

        $percents = array(22, 58, 20);

        echo '<table class="compare" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; border: 1px solid #666">';
        echo '<tr><td colspan="3" class="special" style="text-align: center; font-weight: bold">Αποδοχές & Εργοδ. Εισφορές</td></tr>';
        echo '<tr class="special"><td style="text-align: center; width: '.$percents[0].'%">ΑΛΕ</td><td style="width: '.$percents[1].'%">Περιγραφή</td><td style="width: '.$percents[2].'%">Ποσό</td></tr>';
        
        
        $key = 'epidomata';
        foreach($income[$key]['data'] as $code_id => $epidomata){
            $children = '';
            if($epidomata['amount'] != 0){
                
                if($code_id == '0213' || $code_id == '2120102001') $children = $this->guess_children($epidomata['amount']);
                
                echo '<tr><td style="white-space: nowrap">'.$code_id.'</td><td>'. $this->get_description($code_id, $GLOBALS['codes'], $key) . $children . '</td><td style="white-space: nowrap">' . sprintf('%01.2f €', $epidomata['amount']) . '</td></tr>';       
                $this->meriko_sinolo['apodoxon'] += $epidomata['amount'];
            }                                                       
        }
        echo '<tr><td colspan="3" class="special">Σύνολο αποδοχών: ' . sprintf('%01.2f €', $this->meriko_sinolo['apodoxon']) . '</td></tr>';    
       
       
        $key = 'kratiseis'; 
        foreach($income[$key]['data'] as $code_id => $kratiseis){
            $erg = $kratiseis['amount_erg'];
            // dump($kratiseis);
            $code = array_key_exists('ale_kae', $kratiseis) ? $kratiseis['ale_kae'] : $code_id;
            
            $erg != 0 ? $print_erg = sprintf('%01.2f €', $erg) : $print_erg = '';

            if($erg != 0){
                echo '  <tr>
                            <td>'.$code.'</td>
                            <td>'. $this->get_description($code_id, $GLOBALS['codes'], $key) . ' Εργοδ.</td>
                            <td>'.$print_erg.'</td>
                        </tr>
                        '; 
                 $this->meriko_sinolo['erg'] += $erg;
            }                               
        }
        $this->meriko_sinolo['dapanis'] = $this->meriko_sinolo['apodoxon'] + $this->meriko_sinolo['erg'];
        echo '<tr><td colspan="3" class="special">Σύνολο αποδοχών & Εργοδ. Εισφορών: ' . sprintf('%01.2f €', $this->meriko_sinolo['dapanis']) . '</td></tr>'; 
        echo '</table>';

        $this->sinolo['apodoxon'] += $this->meriko_sinolo['apodoxon'];
        $this->sinolo['erg'] += $this->meriko_sinolo['erg'];
        $this->sinolo['dapanis'] += $this->meriko_sinolo['dapanis'];
    }

    private function ektiposi_kratiseon($income, $income_type){
        $this->meriko_sinolo['asf'] = $this->meriko_sinolo['erg'] = 0;

        $key = 'kratiseis';    
        $percents = array(15, 55, 15, 15);
        
           
        echo '<table class="compare" cellpadding="5" cellspacing="0" style="width: 100%">
            <tr><td colspan="4"  class="special" style="text-align: center;">Κρατήσεις & φόροι</td></tr>
            <tr class="special"><td style="text-align: center; width: '.$percents[0].'%">Κωδικός</td><td style="width: '.$percents[1].'%">Περιγραφή</td>
            <td style="width: '.$percents[2].'%">Ποσό Ασφαλ.</td><td style=" width:'.$percents[3].'%">Ποσό Εργοδ.</td></tr>';
        foreach($income[$key]['data'] as $code_id => $kratiseis){
            $asf = $kratiseis['amount_asf'];
            $erg = $kratiseis['amount_erg'];
            $synolo = $asf + $erg;

            $asf != 0 ? $print_asf = sprintf('%01.2f €', $asf) : $print_asf = '-';
            $erg != 0 ? $print_erg = sprintf('%01.2f €', $erg) : $print_erg = '-';
            $synolo != 0 ? $print_synolo = sprintf('%01.2f €', $synolo) : $print_synolo = '';

            if($asf !=0 || $erg != 0){
                echo '  <tr>
                            <td>'.$code_id.'</td>
                            <td>'. $this->get_description($code_id, $GLOBALS['codes'], $key) . '</td>
                            <td style="white-space: nowrap">'.$print_asf.'</td>
                            <td style="white-space: nowrap">'.$print_erg.'</td>
                        </tr>
                    '; 
                $this->meriko_sinolo['asf'] += $asf;
                $this->meriko_sinolo['erg'] += $erg;
            }                               
        }
        // Γραμμή συνόλων
        echo '
            <tr class="special"><td colspan="2" style="text-align: right">Σύνολα Κρατήσεων: </td>
                <td>' . sprintf('%01.2f €', $this->meriko_sinolo['asf']) . '</td>
                <td>' . sprintf('%01.2f €', $this->meriko_sinolo['erg']) . '</td>
            </tr>
        '; 
            
        $pliroteo = $this->meriko_sinolo['apodoxon'] - $this->meriko_sinolo['asf'];
            
        echo '
            <tr class="special"><td colspan="2" style="text-align: right">Πληρωτέο: </td>
            <td colspan="2" style="text-align: center">' . sprintf('%01.2f €', $pliroteo) . '</td>
            </tr>
        '; 
        echo '</table>';
            
        $this->sinolo['asf'] += $this->meriko_sinolo['asf'];
        // $this->sinolo['erg'] += $this->meriko_sinolo['erg']; // this has already been calculated from ektiposi_apodoxon()
    }

    private function print_totals(){               
        echo '
            <div style="margin-top: 15px; margin-bottom: 20px;">      
                <table style="width: 100%">
                    <tr><td colspan="2" cellpadding="5" style="text-align: center; font-size: 14px; padding: 5px; font-weight: bold">'.$this->raw_data['month_str'] . ' ' . $this->raw_data['year'] .' - Σύνολα περιόδου μισθοδοσίας</td></tr>
                    <tr style="background: none;">
                        <td style="vertical-align: top; padding-right: 10px; width: 45%">', $this->sinolo_dapanis() , '</td>
                        <td style="vertical-align: top; width: 55%;">', $this->pliroteo_mina() , '</td>
                    </tr>
                </table>         
                
            </div>
        ';
    }

    private function sinolo_dapanis(){
        echo '
        <table class="compare" cellpadding="5" cellspacing="0">
            <tr>                    
                <td class="special" style="width: 75%">Σύνολο Ακαθάριστων αποδοχών</td>
                <td class="special" style="width: 25%">'.sprintf('%01.2f €', $this->sinolo['apodoxon']).'</td>
            </tr>
            <tr>
                <td>Εργοδοτικές εισφορές</td>
                <td>'.sprintf('%01.2f €', $this->sinolo['erg']).'</td>                    
            </tr>
            <tr>
                <td>Σύνολο δαπάνης</td>
                <td>'.sprintf('%01.2f €', $this->sinolo['dapanis']).'</td>
            </tr>  
            <tr>
                <td>Εισφορές ασφαλισμένου</td>
                <td>'.sprintf('%01.2f €', $this->sinolo['asf']).'</td>
            </tr>      
        </table>
        ';
    }

    private function pliroteo_mina(){
        echo '
        <table class="compare" cellpadding="5" cellspacing="0">
            <tr>                    
                <td class="special">Πληρωτέο Μήνα</td>
                <td class="special">'.sprintf('%01.2f €', $this->raw_data['firsthalf']+$this->raw_data['secondhalf']).'</td>
            </tr>
            <tr>
                <td>Α\' Δεκαπενθ.</td>
                <td>'.sprintf('%01.2f €', $this->raw_data['firsthalf']).'</td>                    
            </tr>
            <tr>
                <td>Β\' Δεκαπενθ.</td>
                <td>'.sprintf('%01.2f €', $this->raw_data['secondhalf']).'</td>
            </tr>    
        </table>
        ';
    }    

    private function get_type($income_type){
        if($income_type == '0'){
            $type = 'Τακτική';
        }elseif($income_type == '1'){
            $type = 'Αναδρομική';
        }else{
            $type = $income_type;
        }
    
        return $type;
    }
    
    
    private function get_description($code_id, $codes, $key){        
        if(array_key_exists($code_id, $codes[$key]['data'])){
            return $codes[$key]['data'][$code_id]['desc'];
        }else{
            return $code_id;
        }
    }
    
    private function guess_children($amount){
    
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
}




function message($title, $content, $type = 'information'){
	echo '<div class="'.$type.' box" style="margin-bottom: 30px;"><h3>'.$title.'</h3>
	'.$content.'</div>';
}

function print_form($error= 'none'){
    global $txt;

	if(isset($_POST['afm'])) $temp_afm = $_POST['afm']; else $temp_afm = '';
	if(isset($_POST['amm'])) $temp_amm = $_POST['amm']; else $temp_amm = '';

	$afm_error = $amm_error = '';
	if($error == 'afm')	$afm_error = 'border: 1px solid red;';			
	if($error == 'amm')	$amm_error = 'border: 1px solid red;';

	echo '
		<form action="'.$_SERVER['PHP_SELF'].'" method="post">			
            <div class="user_login_container clearfix">
    			<div class="user_form">
                    <label>' . $txt['afm_label'] . '</label><input type="text" name="afm" class="large_input" maxlength="9" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$afm_error.'" value="'.$temp_afm.'" /><br />
        			<label>' . $txt['amm_label'] . '</label><input type="password" name="amm" class="large_input" maxlength="20" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$amm_error.'" value="'.$temp_amm.'" /><br />
        			
        			<input type="hidden" name="proccess" />
        			<input type="submit" id="ops_submit" value="Συνέχεια" style="padding: 10px 20px; font-size: 16px; font-weight: bold;" />	
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

function print_pdf_select_menu($select_values, $pdf_years){
    echo '<select id="pdf-period" style="margin-right: 10px;">';
    // echo '<option value="all">Όλες οι μισθοδοσίες</option>';
    foreach($pdf_years as $year => $arr){
        echo '<option value="'.$year.'">Έτος '.$year.'</option>';
    }

    echo ' <option disabled>------------------------</option>';

    foreach($select_values as $i => $period){
        ($i == 0) ? $selected = ' selected="selected"' : $selected = '';
        echo '<option value="'.$i.'"'.$selected.'>'.$period.'</option>';
    }
    echo '</select>';
}