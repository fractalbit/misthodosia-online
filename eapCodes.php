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
// Εδώ μπορούν να προστεθούν κωδικοί που δεν υπάρχουν ήδη.
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */

$codes = array(	'kratiseis' => array(
						'desc' => 'Εισφ. Ασφαλισμένου - Δάνεια',
						'data' => array(
							'4008300' => array('desc' => 'Δάνειο Ταχυδρομικού', amount_asf => 0, amount_erg => 0),
							'4013605' => array('desc' => 'ΤΠΔΥ 1%', amount_asf => 0, amount_erg => 0),
							'4051700' => array('desc' => 'ΟΑΕΔ 1%', amount_asf => 0, amount_erg => 0),
							'3082800' => array('desc' => 'Αλληλεγγύης 2%', amount_asf => 0, amount_erg => 0),
							'3082400' => array('desc' => 'Υπερ Σύνταξης', amount_asf => 0, amount_erg => 0),
							'3081100' => array('desc' => 'Υγειον. Περιθαλψη', amount_asf => 0, amount_erg => 0),
							'4052000' => array('desc' => 'Υγειον. Περιθαλψη', amount_asf => 0, amount_erg => 0),
							'4013601' => array('desc' => 'ΤΠΔΥ', amount_asf => 0, amount_erg => 0),
							'3011300' => array('desc' => 'Φόρος', amount_asf => 0, amount_erg => 0),
							'3089100' => array('desc' => 'Ειδική εισφορά αλληλεγγύης 2012', amount_asf => 0, amount_erg => 0),
							'4003101' => array('desc' => 'ΜΠΤΥ', amount_asf => 0, amount_erg => 0),
							'4003103' => array('desc' => 'ΜΠΤΥ Νεοδιόριστου', amount_asf => 0, amount_erg => 0),
							'3324300' => array('desc' => 'Απεργία', amount_asf => 0, amount_erg => 0),
							'3324100' => array('desc' => 'Επιστροφές αποδοχών', amount_asf => 0, amount_erg => 0),
							'3122800' => array('desc' => 'Πρόσθετο ποσοστό 20% στα τέλη χαρτοσήμου', amount_asf => 0, amount_erg => 0),
							'4003108' => array('desc' => 'Μετοχικό Ταμείο Πολιτικών Υπαλλήλων (82985)', amount_asf => 0, amount_erg => 0),
							'4003107' => array('desc' => 'Μ.Τ.Π.Υ. Πρόσθετες Αμοιβές', amount_asf => 0, amount_erg => 0),
							'4012504' => array('desc' => 'ΤΕΑΔΥ Πρόσθετες Αμοιβές', amount_asf => 0, amount_erg => 0),
							'4009600' => array('desc' => 'ΤΑΠΙΤ (πρώην Τ.Α.Ξ.Υ)', amount_asf => 0, amount_erg => 0),
							'4033500' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. κύρια σύνταξη', amount_asf => 0, amount_erg => 0),
							'4033701' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. ειδική προσαύξηση', amount_asf => 0, amount_erg => 0),
							'4033702' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ ειδικός λογαριασμός', amount_asf => 0, amount_erg => 0),
							'4033800' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. επικουρική ασφάλιση', amount_asf => 0, amount_erg => 0),
							'4033900' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ παροχών', amount_asf => 0, amount_erg => 0),
							'4034000' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. υγεία τεχνικών', amount_asf => 0, amount_erg => 0),
							'4002100' => array('desc' => 'ΙΚΑ', amount_asf => 0, amount_erg => 0),
							'4012501' => array('desc' => 'ΤΕΑΔΥ', amount_asf => 0, amount_erg => 0),
							'4012502' => array('desc' => 'ΤΕΑΔΥ Νεοδιόριστου', amount_asf => 0, amount_erg => 0),
							'4013400' => array('desc' => 'ΤΕΑΧ', amount_asf => 0, amount_erg => 0),
							'4013500' => array('desc' => 'Δάνειο Παρακαταθηκών', amount_asf => 0, amount_erg => 0)
						)

					),

				'epidomata' => array(
						'desc' => 'Ακαθάριστες αποδοχές',
						'data' => array(
							'0217'	  => array('desc' => 'Κίνητρο απόδοσης', amount => 0),
							'0211'	  => array('desc' => 'Βασικός', amount => 0),
							'0218'	  => array('desc' => 'Δώρο', amount => 0),
							'0219'	  => array('desc' => 'Μεταπτυχιακό/Διδακτ.', amount => 0),
							'0227'	  => array('desc' => 'Επίδομα θέσης', amount => 0),
							'0213'	  => array('desc' => 'Οικογενειακή παροχή', amount => 0),
							'0232'	  => array('desc' => 'Επίδομα Εξωδιδακτικής', amount => 0),
							'0228'	  => array('desc' => 'Επίδομα Περιοχής', amount => 0),
							'0229'	  => array('desc' => 'Επ. Διδ. Προετοιμασίας', amount => 0),
							'0344'	  => array('desc' => 'Ακαθάριστες αποδοχές', amount => 0)
						)

				),

				// Κωδικοί για πρόσθετες αποδοχές - Δεν χρησιμοποιούνται στην παρούσα φάση
				'282' => array('desc' => '<b>ΕΚΠΑΙΔΕΥΤΙΚΗ ΑΔΕΙΑ</b>', amount => 0),
				'512' => array('desc' => '<b>ΕΡΓΑΣΙΑ ΣΕ ΑΡΓΙΕΣ</b>', amount => 0),
				'515' => array('desc' => '<b>ΕΞΕΤΑΣΕΙΣ – ΚΠΓ – ΑΠΟΖΗΜΙΩΣΕΙΣ ΜΕΛΩΝ ΣΥΛΛΟΓΩΝ</b>', amount => 0),
				'516' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΕΚΠΑΙΔΕΥΤΙΚΩΝ</b>', amount => 0),
				'517' => array('desc' => '<b>ΑΜΟΙΒΕΣ ΩΡΩΜΙΣΘΙΩΝ</b>', amount => 0),
				'561' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΣΕ ΓΡΑΦΕΙΑ ΒΟΥΛΕΥΤΩΝ</b>', amount => 0),
				'563' => array('desc' => '<b>ΜΗ ΧΡΗΣΗ ΘΕΡΙΝΗΣ ΑΔΕΙΑΣ</b>', amount => 0),
				'711' => array('desc' => '<s>ΟΔΟΙΠΟΡΙΚΑ</s>', amount => 0),

				);


	/*$codes = array 	(
					'4008300' => array('desc' => 'Δάνειο Ταχυδρομικού', amount => 0),
					'4013605' => array('desc' => 'ΤΠΔΥ 1%', amount => 0),
					'4051700' => array('desc' => 'ΟΑΕΔ 1%', amount => 0),
					'3082800' => array('desc' => 'Αλληλεγγύης 2%', amount => 0),
					'3082400' => array('desc' => 'Υπερ Σύνταξης', amount => 0),
					'3081100' => array('desc' => 'Υγειον. Περιθαλψη', amount => 0),
					'4052000' => array('desc' => 'Υγειον. Περιθαλψη', amount => 0),
					'4013601' => array('desc' => 'ΤΠΔΥ', amount => 0),
					//'4012501' => array('desc' => '', amount => 0),
					'3011300' => array('desc' => 'Φόρος', amount => 0),
					'3089100' => array('desc' => 'Ειδική εισφορά αλληλεγγύης 2012', amount => 0),
					'4003101' => array('desc' => 'ΜΠΤΥ', amount => 0),
					'4003103' => array('desc' => 'ΜΠΤΥ Νεοδιόριστου', amount => 0),
					'3324300' => array('desc' => 'Απεργία', amount => 0),
					'3324100' => array('desc' => 'Στάση', amount => 0),
					'3122800' => array('desc' => 'Πρόσθετο ποσοστό 20% στα τέλη χαρτοσήμου', amount => 0),
					'4003108' => array('desc' => 'Μετοχικό Ταμείο Πολιτικών Υπαλλήλων (82985)', amount => 0),
					'4003107' => array('desc' => 'Μ.Τ.Π.Υ. Πρόσθετες Αμοιβές', amount => 0),
					'4012504' => array('desc' => 'ΤΕΑΔΥ Πρόσθετες Αμοιβές', amount => 0),
					'4009600' => array('desc' => 'ΤΑΠΙΤ (πρώην Τ.Α.Ξ.Υ)', amount => 0),
					'4033500' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. κύρια σύνταξη', amount => 0),
					'4033701' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. ειδική προσαύξηση', amount => 0),
					'4033702' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ ειδικός λογαριασμός', amount => 0),
					'4033800' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. επικουρική ασφάλιση', amount => 0),
					'4033900' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ παροχών', amount => 0),
					'4034000' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. υγεία τεχνικών', amount => 0),
					'4002100' => array('desc' => 'ΙΚΑ', amount => 0),
					'4012501' => array('desc' => 'ΤΕΑΔΥ Ασφαλισμένου', amount => 0),
					'4012502' => array('desc' => 'ΤΕΑΔΥ Νεοδιόριστου', amount => 0),
					'4013500' => array('desc' => 'Δάνειο Παρακαταθηκών', amount => 0),
					
					'0217'	  => array('desc' => 'Κίνητρο απόδοσης', amount => 0),
					'0211'	  => array('desc' => 'Βασικός', amount => 0),
					'0218'	  => array('desc' => 'Δώρο', amount => 0),
					'0219'	  => array('desc' => 'Μεταπτυχιακό/Διδακτ.', amount => 0),
					'0227'	  => array('desc' => 'Επίδομα θέσης', amount => 0),
					'0213'	  => array('desc' => 'Οικογενειακή παροχή', amount => 0),
					'0232'	  => array('desc' => 'Επίδομα Εξωδιδακτικής', amount => 0),
					'0228'	  => array('desc' => 'Επίδομα Περιοχής', amount => 0),
					'0229'	  => array('desc' => 'Επ. Διδ. Προετοιμασίας', amount => 0),
					'0344'	  => array('desc' => 'Ακαθάριστες αποδοχές', amount => 0),

					// Κωδικοί για πρόσθετες αποδοχές
					'282' => array('desc' => '<b>ΕΚΠΑΙΔΕΥΤΙΚΗ ΑΔΕΙΑ</b>', amount => 0),
					'512' => array('desc' => '<b>ΕΡΓΑΣΙΑ ΣΕ ΑΡΓΙΕΣ</b>', amount => 0),
					'515' => array('desc' => '<b>ΕΞΕΤΑΣΕΙΣ – ΚΠΓ – ΑΠΟΖΗΜΙΩΣΕΙΣ ΜΕΛΩΝ ΣΥΛΛΟΓΩΝ</b>', amount => 0),
					'516' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΕΚΠΑΙΔΕΥΤΙΚΩΝ</b>', amount => 0),
					'517' => array('desc' => '<b>ΑΜΟΙΒΕΣ ΩΡΩΜΙΣΘΙΩΝ</b>', amount => 0),
					'561' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΣΕ ΓΡΑΦΕΙΑ ΒΟΥΛΕΥΤΩΝ</b>', amount => 0),
					'563' => array('desc' => '<b>ΜΗ ΧΡΗΣΗ ΘΕΡΙΝΗΣ ΑΔΕΙΑΣ</b>', amount => 0),
					'711' => array('desc' => '<s>ΟΔΟΙΠΟΡΙΚΑ</s>', amount => 0),

					'andr' 	  => array(		
											'total' => array('days' => '', 'amount' => 0)
										)
				);*/