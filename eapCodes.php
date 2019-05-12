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
							'4008300' => array('desc' => 'Δάνειο Ταχυδρομικού', 'amount_asf' => 0, 'amount_erg' => 0),
							'4013605' => array('desc' => 'Τ.Π.Δ.Υ. 1%', 'amount_asf' => 0, 'amount_erg' => 0),
							'4051700' => array('desc' => 'ΟΑΕΔ 1%', 'amount_asf' => 0, 'amount_erg' => 0),
							'3082800' => array('desc' => 'Αλληλεγγύης 2%', 'amount_asf' => 0, 'amount_erg' => 0),
							'3082400' => array('desc' => 'Υπερ Σύνταξης', 'amount_asf' => 0, 'amount_erg' => 0),
							'3081100' => array('desc' => 'Υγειον. Περίθαλψη', 'amount_asf' => 0, 'amount_erg' => 0),
							'4052000' => array('desc' => 'Υγειον. Περίθαλψη', 'amount_asf' => 0, 'amount_erg' => 0),
							'4013601' => array('desc' => 'Τ.Π.Δ.Υ.', 'amount_asf' => 0, 'amount_erg' => 0),
							'3011300' => array('desc' => 'Φόρος', 'amount_asf' => 0, 'amount_erg' => 0),
							'3089100' => array('desc' => 'Ειδική εισφορά αλληλεγγυης Ν.3986 Αρ.29 Παρ. 4', 'amount_asf' => 0, 'amount_erg' => 0),
							'4003101' => array('desc' => 'Μ.Τ.Π.Υ.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4003103' => array('desc' => 'Μ.Τ.Π.Υ. Νεοδιόριστου', 'amount_asf' => 0, 'amount_erg' => 0),
							'3324300' => array('desc' => 'Απεργία', 'amount_asf' => 0, 'amount_erg' => 0),
							'3324100' => array('desc' => 'Επιστροφές αποδοχών', 'amount_asf' => 0, 'amount_erg' => 0),
							'3122800' => array('desc' => 'Πρόσθετο ποσοστό 20% στα τέλη χαρτοσήμου', 'amount_asf' => 0, 'amount_erg' => 0),
							'4003108' => array('desc' => 'Μετοχικό Ταμείο Πολιτικών Υπαλλήλων (82985)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4003107' => array('desc' => 'Μ.Τ.Π.Υ. Πρόσθετες Αμοιβές', 'amount_asf' => 0, 'amount_erg' => 0),
							'4012504' => array('desc' => 'Τ.Ε.Α.Δ.Υ. Πρόσθετες Αμοιβές', 'amount_asf' => 0, 'amount_erg' => 0),
							'4009600' => array('desc' => 'ΤΑΠΙΤ (πρώην Τ.Α.Ξ.Υ)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4033500' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. κύρια σύνταξη', 'amount_asf' => 0, 'amount_erg' => 0),
							'4033701' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. ειδική προσαύξηση', 'amount_asf' => 0, 'amount_erg' => 0),
							'4033702' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ ειδικός λογαριασμός', 'amount_asf' => 0, 'amount_erg' => 0),
							'4033800' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. επικουρική ασφάλιση', 'amount_asf' => 0, 'amount_erg' => 0),
							'4033900' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ παροχών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4034000' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. υγεία τεχνικών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4002100' => array('desc' => 'ΙΚΑ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4012501' => array('desc' => 'Τ.Ε.Α.Δ.Υ.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4012502' => array('desc' => 'Τ.Ε.Α.Δ.Υ. Νεοδιόριστου', 'amount_asf' => 0, 'amount_erg' => 0),


							'4012505' => array('desc' => 'Τ.Ε.Α.Δ.Υ - Διαφορά Αποδοχών λόγω Μισθολογικής προαγωγής', 'amount_asf' => 0, 'amount_erg' => 0),
							'4012507' => array('desc' => 'Δάνειο Τ.Ε.Α.Δ.Υ.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4003700' => array('desc' => 'Ο.Δ.Δ.Υ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4059100' => array('desc' => 'Σύλλογος Υπαλλήλων Περιφερειακών Υπηρεσιών Υπουργείου Παιδείας', 'amount_asf' => 0, 'amount_erg' => 0),
							'4000800' => array('desc' => 'Ενωση Ελλήνων Χημικών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014002' => array('desc' => 'Ταμείο Πρόνοιας Προσωπικού ΟΣΕ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4010400' => array('desc' => 'ΤΑΥΤΕΚΩ (πρώην Τ.Α.Π.ΟΤΕ  Ασθένειας)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014001' => array('desc' => 'Ταμείο Πρόνοιας Προσωπικού ΟΣΕ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4054901' => array('desc' => 'ΤΑΥΤΕΚΩ - ΕΙΣΦΟΡΕΣ ΣΕ ΧΡΗΜΑ (ΤΑΠ-ΟΤΕ)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4055001' => array('desc' => 'ΤΑΥΤΕΚΩ - ΕΙΣΦΟΡΕΣ ΕΟΠΥΥ (ΤΑΠ-ΟΤΕ)', 'amount_asf' => 0, 'amount_erg' => 0),



							'4013400' => array('desc' => 'ΤΕΑΧ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4013500' => array('desc' => 'Δάνειο Παρακαταθηκών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4027100' => array('desc' => 'ΑΔΕΔΥ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4044700' => array('desc' => 'Ο.Λ.Μ.Ε.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4999901' => array('desc' => 'Α\' Ε.Λ.Μ.Ε.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4999902' => array('desc' => 'Β\' Ε.Λ.Μ.Ε.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4999903' => array('desc' => 'Γ\' Ε.Λ.Μ.Ε.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014700' => array('desc' => 'Τ.Υ.Δ.Κ.Υ', 'amount_asf' => 0, 'amount_erg' => 0),
							'4038701' => array('desc' => 'Τ.Ε.Α.Δ.Υ. – Τ.Α.Δ.Κ.Υ. Κύρια Σύνταξη', 'amount_asf' => 0, 'amount_erg' => 0),
							'4038702' => array('desc' => 'Τ.Ε.Α.Δ.Υ. – Τ.Α.Δ.Κ.Υ. Επικουρική', 'amount_asf' => 0, 'amount_erg' => 0),
							'4038803' => array('desc' => 'Τ.Π.Δ.Υ. – ΤΠΔΚΥ (Κράτηση πρόνοιας 1% όλων των παλαιών ασφαλισμένων)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4038808' => array('desc' => 'Τ.Π.Δ.Υ. - ΤΠΔΚΥ (Ειδική εισφορά 1% Ν.3986/2011)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014201' => array('desc' => 'Ε.Τ.Α.Α. (Τομέας Σύνταξης Υγεινομικών)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014202' => array('desc' => 'Ε.Τ.Α.Α. Τομέας Υγείας Υγεινομικών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014204' => array('desc' => 'Ε.Τ.Α.Α. Στέγη Υγεινομικών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4014205' => array('desc' => 'Ε.Τ.Α.Α. Τομέας Πρόνοιας Υγ/κών', 'amount_asf' => 0, 'amount_erg' => 0),
							'4023200' => array('desc' => 'Τ.Ε.Α.Ε.Ι.Γ.Ε.', 'amount_asf' => 0, 'amount_erg' => 0),
							'4021200' => array('desc' => 'Ι.Κ.Α Συντ.', 'amount_asf' => 0, 'amount_erg' => 0),

							'4065200' => array('desc' => 'Ι.Κ.Α', 'amount_asf' => 0, 'amount_erg' => 0),
							'4064901' => array('desc' => 'ΕΦΚΑ-Εισφ. Κύριας Ασφάλισης (Κλ. Σύνταξης) Δημοσίου έως 31/12/2010', 'amount_asf' => 0, 'amount_erg' => 0),
							'4064902' => array('desc' => 'ΕΦΚΑ-Εισφ. για Παροχές σε Είδος Υπαλ. Δημ. έως 31/12/2010 (πρώην ΟΠΑΔ)', 'amount_asf' => 0, 'amount_erg' => 0),
							'4064903' => array('desc' => 'ΕΦΚΑ-Εισφ. υπερ Λογ. σε χρήμα Υπαλλ. Δημ. έως 31/12/2010 (πρώην ΟΠΑΔ)', 'amount_asf' => 0, 'amount_erg' => 0),
							'3373900' => array('desc' => 'Τέλη Πειθαρχικής διαδικασίας', 'amount_asf' => 0, 'amount_erg' => 0)
						)

					),

				'epidomata' => array(
						'desc' => 'Ακαθάριστες αποδοχές',
						'data' => array(
							'0217'	  => array('desc' => 'Κίνητρο απόδοσης', 'amount' => 0),
							'0211'	  => array('desc' => 'Βασικός μισθός', 'amount' => 0),
							'0221'	  => array('desc' => 'Υπερβάλλουσα διαφορά', 'amount' => 0),
							'0218'	  => array('desc' => 'Δώρο', 'amount' => 0),
							'0219'	  => array('desc' => 'Μεταπτυχιακό/Διδακτ.', 'amount' => 0),
							'0227'	  => array('desc' => 'Επίδομα θέσης', 'amount' => 0),
							'0213'	  => array('desc' => 'Οικογενειακή παροχή', 'amount' => 0),
							'0232'	  => array('desc' => 'Επίδομα Εξωδιδακτικής', 'amount' => 0),
							'0228'	  => array('desc' => 'Επίδομα Περιοχής', 'amount' => 0),
							'0229'	  => array('desc' => 'Επ. Διδ. Προετοιμασίας', 'amount' => 0),
							'0344'	  => array('desc' => 'Ακαθάριστες αποδοχές', 'amount' => 0),

							'2130104001'	  => array('desc' => 'Ακαθάριστες αποδοχές', 'amount' => 0), // Για αναπληρωτές
							'2120101001'	  => array('desc' => 'Βασικός μισθός', 'amount' => 0),
							'2120103001'	  => array('desc' => 'Υπερβάλλουσα διαφορά', 'amount' => 0),
							'2120104001'	  => array('desc' => 'Επίδομα θέσης', 'amount' => 0),
							'2120102001'	  => array('desc' => 'Οικογενειακή παροχή', 'amount' => 0),
							'2120115001'	  => array('desc' => 'Επίδομα Περιοχής', 'amount' => 0)
						)

				),

				'prostheta' => array ( // Κωδικοί για πρόσθετες αποδοχές - Δεν χρησιμοποιούνται στην παρούσα φάση
					'282' => array('desc' => '<b>ΕΚΠΑΙΔΕΥΤΙΚΗ ΑΔΕΙΑ</b>', 'amount' => 0),
					'512' => array('desc' => '<b>ΕΡΓΑΣΙΑ ΣΕ ΑΡΓΙΕΣ</b>', 'amount' => 0),
					'515' => array('desc' => '<b>ΕΞΕΤΑΣΕΙΣ – ΚΠΓ – ΑΠΟΖΗΜΙΩΣΕΙΣ ΜΕΛΩΝ ΣΥΛΛΟΓΩΝ</b>', 'amount' => 0),
					'516' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΕΚΠΑΙΔΕΥΤΙΚΩΝ</b>', 'amount' => 0),
					'517' => array('desc' => '<b>ΑΜΟΙΒΕΣ ΩΡΩΜΙΣΘΙΩΝ</b>', 'amount' => 0),
					'561' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΣΕ ΓΡΑΦΕΙΑ ΒΟΥΛΕΥΤΩΝ</b>', 'amount' => 0),
					'563' => array('desc' => '<b>ΜΗ ΧΡΗΣΗ ΘΕΡΙΝΗΣ ΑΔΕΙΑΣ</b>', 'amount' => 0),
					'711' => array('desc' => '<s>ΟΔΟΙΠΟΡΙΚΑ</s>', 'amount' => 0)
				)
		);