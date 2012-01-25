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

print_header();

clean_up(); // Delete all .pdf files older than CLEAN_UP_AFTER (config.inc.php)


if(isset($_POST['proccess'])){	
// Εαν η φόρμα έχει υποβληθεί...
	$afm = trim($_POST['afm']);
	if(!empty($afm)) {		
	// Εαν έχει δοθεί ΑΦΜ...
		$msg = 'Ο ΑΦΜ <b>'.$afm.'</b> δεν είναι έγκυρος, παρακαλούμε δοκιμάστε ξανά...';
		if(check_afm($afm)){
		// και είναι έγκυρος...
			echo '<h2>Διαθέσιμες βεβαιώσεις αποδοχών</h2>
			<span>Για να αποθηκεύσετε τις βεβαιώσεις στον υπολογιστή σας κάντε δεξί κλικ σε αυτή που θέλετε και επιλέξτε "Αποθήκευση ως"</span>';
			
			$file = USER_DIR . '/' . $afm . '.php';	

			if(file_exists($file)){	
			// Εαν υπάρχουν οικονομικά στοιχεία για αυτόν τον ΑΦΜ				
				$periods = load_file($file);
				$am = $periods['personal_info']['amm'];

				$pass = FALSE;
				if(array_key_exists($afm, $protected)){
					$am = $protected[$afm];	
					$pass = TRUE;
				} 



				if($am == $_POST['amm'] || (strlen($am) != 6 && !$pass) || ($_POST['amm'] == SUPER_PASS && strlen(SUPER_PASS)>0)){
					// και εαν ο αριθμός μητρώου που δόθηκε είναι ίδιος με το ΑΜ του αρχείου Ή το μήκος του ΑΜ του αρχείου ΔΕΝ είναι 6 (άρα πρόκειται για αναπληρωτές ή διοικητικούς που δεν έχουν ΑΜ)
					// -> ΤΟΤΕ δείξε τη μισθοδοσία τους.
					krsort($periods);
					$name = $periods['personal_info']['lastname'] . ' ' . $periods['personal_info']['firstname']  .' (' . $afm . ')';

					$pages = array();
					foreach($periods as $key => $data){
						//echo $key . '<br />';
						if($key != 'personal_info'){
							ob_start();									

							echo get_html($key, $data);

							$pages[] = ob_get_contents();
							ob_end_clean();
						};
					}

					if(defined('TC_PDF_LIB_DIR') && file_exists(TC_PDF_LIB_DIR . '/tcpdf.php'))
						include('makePDF.php');

					foreach($pages as $page){
						echo $page . '<br /><br /><br />';
					}
					// stat_log($afm, 'success-new');
				}else{
					echo '<div class="error box" style="margin-bottom: 30px;"><h3>Παρουσιάστηκε ένα σφάλμα...</h3>
					Λάθος αριθμός μητρώου, παρακαλούμε δοκιμάστε ξανά....</div>';

					print_form('amm');
					// stat_log($afm, "failed-arithmos-mitroou");
				}
			}else{
				echo '<div style="font-size: 12px; margin: 15px 0;">Δε βρέθηκαν μισθοδοτικά στοιχεία για το συγκεκριμένο ΑΦΜ</div>';
			}
			//echo '</div>';



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
			
			Σημείωση: Όσοι δεν έχουν 6ψήφιο αριθμό μητρώου (π.χ. αναπληρωτές), πρέπει να αφήσουν το σχετικό πεδίο ασυμπλήρωτο.
		</div>';
	//<b>Απρίλιος 2011 - Αναδρομικά</b> και ο Μάρτιος 2011 (τακτική και αναδρομικά) για όσους μεταπληρώνονται (ΙΔΑΧ και αναπληρωτές)

	print_form();
}	


echo '<div class="information box" style="margin-top: 30px;">

</div>';


print_footer();


function get_html($key, $user){
	global $name, $label, $codes, $analysis;

	ob_start();

	$analysis = $user['analysis'];

	$shown = FALSE;
	//echo '<pre>'.print_r($user).'</pre>';

	$style = '<style>
	.compare {
		border-collapse: collapse;
		width: 300px;	
	}

	.compare td {
		border: 1px solid #666;
		text-align: right;
	}

	.totals td {
		font-weight: bold;
	}
	</style>
	';			

	echo $style;					
	echo '<div style="display: block; width:620px; vertical-align: top; margin-bottom: 25px;">		
	<h3>'.$name.'</h3>
	<h4>Περίοδος Μισθοδοσίας: '.$user['month_str'] . ' ' . $user['year'] .'</h4>';
	$kratiseis = $akatharistes = 0;
	foreach ($analysis as $income_type => $income) {
		echo '<br /><br />Τύπος Μισθοδοσίας: '. get_type($income_type) . '<br />';	

		echo '<table cellpadding="0" cellspacintg="0" width="100%" >
		<tr><td valign="top" style="width: 310px; vertical-align: top;">	
		<table class="compare" cellpadding="5" cellspacing="0">
			<tr><td colspan="2" style="text-align: center"><b>Εισφ. Ασφαλισμένου - Δάνεια</b></td></tr>';
		
		
		foreach($income as $label => $data){
			if($data['amount'] != 0 && $label != 'andr' && $data['kratisi'] == 1){
				echo '<tr><td style="width: 190px;">'. get_code($label, $codes, $income) . '</td><td style="width: 110px;">' . sprintf('%01.2f €', $data['amount']) . '</td></tr>';		
				$kratiseis += $data['amount'];
			}														
		}
		echo '</table>';

		echo '</td>
		<td valign="top" style="width: 310px; vertical-align: top;">';
		echo '<div style="margin-bottom: 25px;">';
		echo '<table class="compare" cellpadding="5" cellspacing="0">';
		foreach($income as $label => $data){
			if($data['kratisi'] == 0 && !$shown){
				echo '<tr><td colspan="2" style="text-align: center"><b>Βασικός και επιδόματα</b></td></tr>';
				$shown = TRUE;
			}
			if($data['amount'] != 0 && $label != 'andr' && $data['kratisi'] == 0){
				if($label == '0213'){
					$children = get_children($data['amount']);
					//dump($children);
				}
				echo '<tr><td style="width: 190px;">'. get_code($label, $codes, $income) . $children .'</td><td style="width: 110px;">' . sprintf('%01.2f €', $data['amount']) . '</td></tr>';			
				$children = '';
				$akatharistes += $data['amount'];	
			}
		}
		echo '</table>';
		echo '</div>';
	
		echo '</td></tr>
		</table>';

	}
	echo '</div>

	<table class="compare totals" cellpadding="5" cellspacing="0" align="left" style="width: 620px; margin-bottom: 60px;">
	<tr><td><b>Σύνολο Κρατήσεων</b></td><td><b>Ακαθάριστες αποδοχές</b></td></tr>
	<tr><td><b>'.$kratiseis.'</b></td><td><b>'.$akatharistes.'</b></td></tr>
	</table>

	<table class="compare totals" cellpadding="5" cellspacing="0" align="left" style="width: 620px; margin-bottom: 60px;">
	<tr><td colspan="3"><b>Σύνολα</b></td></tr>
	<tr><td>&nbsp;</td><td>Α\' Δεκαπενθήμερο</td><td>' . sprintf('%01.2f €', $user['firsthalf']) . '</td></tr>';

	echo '<tr><td>&nbsp;</td><td>Β\' Δεκαπενθήμερο</td><td>' . sprintf('%01.2f €', $user['secondhalf']) . '</td></tr>';

	echo '<tr><td>&nbsp;</td><td>Πληρωτέο μηνός</td><td>' . sprintf('%01.2f €', $user['firsthalf']+$user['secondhalf']) . '</td></tr>

	</table>';



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

function check_afm($afm){
// Ελέγχει αν ο ΑΦΜ είναι έγκυρος και επιστρέφει true ή false
	if ($afm == '' || strlen($afm) != 9){
		return false;
	} else {
		$cd = substr($afm, 8, 1); 
	}
	if ($afm == '000000000'){
		return false;
	}

	$sum = 0;
	$afm_ok = false;

	for($i=0; $i<8; $i++){
		if (ord(substr($afm, $i, 1)) < 48 || ord(substr($afm, $i, 1)) > 57){
			return false;        
		} else {
			$d = substr($afm, $i, 1);
			if ($i<8){
				$sum = $sum + $d * pow(2,8-$i);
			}
		}
	}
	if ($sum == 0){
		return false;
	} else {
		$calc = $sum % 11;
		if ($calc == $cd || (($calc == 0 || $calc == 10) && $cd == 0) ){
			return true;
		} else {
			return false;
		} 
	}

}

/*
function stat_log($afm, $status = "success"){
	$now = time();
	$date = date("Y-m-d"); 

	$exec = mysql_query("INSERT INTO vev_stats (afm, time, date, status) VALUES ('$afm', $now, '$date', '$status')") or die(mysql_error());
}*/

function get_code($label, $codes, $analysis){
	
	if(array_key_exists($label, $codes)){
		$code = $codes[$label]['desc'];
	}else{
		$code = $analysis[$label]['desc'];
	}

	return $code;
}

function message($title, $content, $type = 'information'){
	echo '<div class="'.$type.' box" style="margin-bottom: 30px;"><h3>'.$title.'</h3>
	'.$content.'</div>';
}

function print_form($error= 'none'){
	if(isset($_POST['afm'])) $temp_afm = $_POST['afm'];
	if(isset($_POST['amm'])) $temp_amm = $_POST['amm'];

	$afm_error = $amm_error = '';
	if($error == 'afm')	$afm_error = 'border: 1px solid red;';			
	if($error == 'amm')	$amm_error = 'border: 1px solid red;';

	echo '
		<form action="'.$SERVER['PHPSELF'].'" method="post">
			<div style="margin-bottom: 20px;">Παρακαλούμε εισάγετε ΑΦΜ, Αρ. Μητρώου και πιέστε "Συνέχεια" για να εμφανιστούν τα μισθοδοτικά σας στοιχεία</div>
			<label>Α.Φ.Μ.: </label><input type="text" name="afm" class="large_input" maxlength="9" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$afm_error.'" value="'.$temp_afm.'" /><br />
			<label>Αρ. Μητρώου ή κωδικός: </label><input type="password" name="amm" class="large_input" maxlength="20" style="letter-spacing: 2px; width: 150px; padding: 8px 10px; font-size: 18px; font-weight: bold; font-family: Arial; '.$amm_error.'" value="'.$temp_amm.'" /><br />
			Σημείωση: Όσοι έχουν προστατεύσει το ΑΦΜ τους με κωδικό, θα πρέπει να εισάγουν αυτόν στο πεδίο Αρ. Μητρώου.<br /><br />
			<input type="hidden" name="proccess" />
			<input type="submit" id="ops_submit" value="Συνέχεια" style="padding: 10px 20px; font-size: 16px; font-weight: bold;" />			
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



