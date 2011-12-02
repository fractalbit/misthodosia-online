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

$codes = array 	(
					'4008300' => array('desc' => 'Δάνειο Ταχυδρομικού', 'kratisi' => 1, amount => 0),
					'4013605' => array('desc' => 'ΤΠΔΥ 1%', 'kratisi' => 1, amount => 0),
					'4051700' => array('desc' => 'ΟΑΕΔ 1%', 'kratisi' => 1, amount => 0),
					'3082800' => array('desc' => 'Αλληλεγγύης 2%', 'kratisi' => 1, amount => 0),
					'3082400' => array('desc' => 'Υπερ Σύνταξης', 'kratisi' => 1, amount => 0),
					'3081100' => array('desc' => 'Υγειον. Περιθαλψη', 'kratisi' => 1, amount => 0),
					'4013601' => array('desc' => 'ΤΠΔΥ', 'kratisi' => 1, amount => 0),
					//'4012501' => array('desc' => '', 'kratisi' => 1, amount => 0),
					'3011300' => array('desc' => 'Φόρος', 'kratisi' => 1, amount => 0),
					'4003101' => array('desc' => 'ΜΠΤΥ', 'kratisi' => 1, amount => 0),
					'4003103' => array('desc' => 'ΜΠΤΥ Νεοδιόριστου', 'kratisi' => 1, amount => 0),
					'3324300' => array('desc' => 'Απεργία', 'kratisi' => 1, amount => 0),
					'3324100' => array('desc' => 'Στάση', 'kratisi' => 1, amount => 0),
					'3122800' => array('desc' => 'Πρόσθετο ποσοστό 20% στα τέλη χαρτοσήμου', 'kratisi' => 1, amount => 0),
					'4003108' => array('desc' => 'Μετοχικό Ταμείο Πολιτικών Υπαλλήλων (82985)', 'kratisi' => 1, amount => 0),
					'4003107' => array('desc' => 'Μ.Τ.Π.Υ. Πρόσθετες Αμοιβές', 'kratisi' => 1, amount => 0),
					'4012504' => array('desc' => 'ΤΕΑΔΥ Πρόσθετες Αμοιβές', 'kratisi' => 1, amount => 0),
					'4009600' => array('desc' => 'ΤΑΠΙΤ (πρώην Τ.Α.Ξ.Υ)', 'kratisi' => 1, amount => 0),
					'4033500' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. κύρια σύνταξη', 'kratisi' => 1, amount => 0),
					'4033701' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. ειδική προσαύξηση', 'kratisi' => 1, amount => 0),
					'4033702' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ ειδικός λογαριασμός', 'kratisi' => 1, amount => 0),
					'4033800' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. επικουρική ασφάλιση', 'kratisi' => 1, amount => 0),
					'4033900' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. εφάπαξ παροχών', 'kratisi' => 1, amount => 0),
					'4034000' => array('desc' => 'Τ.Σ.Μ.Ε.Δ.Ε. υγεία τεχνικών', 'kratisi' => 1, amount => 0),
					'4002100' => array('desc' => 'ΙΚΑ', 'kratisi' => 1, amount => 0),
					'4012501' => array('desc' => 'ΤΕΑΔΥ Ασφαλισμένου', 'kratisi' => 1, amount => 0),
					'4012502' => array('desc' => 'ΤΕΑΔΥ Νεοδιόριστου', 'kratisi' => 1, amount => 0),
					'4013500' => array('desc' => 'Δάνειο Παρακαταθηκών', 'kratisi' => 1, amount => 0),
					
					'0217'	  => array('desc' => 'Κίνητρο απόδοσης', 'kratisi' => 0, amount => 0),
					'0211'	  => array('desc' => 'Βασικός', 'kratisi' => 0, amount => 0),
					'0218'	  => array('desc' => 'Δώρο', 'kratisi' => 0, amount => 0),
					'0219'	  => array('desc' => 'Μεταπτυχιακό/Διδακτ.', 'kratisi' => 0, amount => 0),
					'0227'	  => array('desc' => 'Επίδομα θέσης', 'kratisi' => 0, amount => 0),
					'0213'	  => array('desc' => 'Οικογενειακή παροχή', 'kratisi' => 0, amount => 0),
					'0232'	  => array('desc' => 'Επίδομα Εξωδιδακτικής', 'kratisi' => 0, amount => 0),
					'0228'	  => array('desc' => 'Επίδομα Περιοχής', 'kratisi' => 0, amount => 0),
					'0344'	  => array('desc' => 'Ακαθάριστες αποδοχές', 'kratisi' => 0, amount => 0),

					// Κωδικοί για πρόσθετες αποδοχές
					'282' => array('desc' => '<b>ΕΚΠΑΙΔΕΥΤΙΚΗ ΑΔΕΙΑ</b>', 'kratisi' => 0, amount => 0),
					'512' => array('desc' => '<b>ΕΡΓΑΣΙΑ ΣΕ ΑΡΓΙΕΣ</b>', 'kratisi' => 0, amount => 0),
					'515' => array('desc' => '<b>ΕΞΕΤΑΣΕΙΣ – ΚΠΓ – ΑΠΟΖΗΜΙΩΣΕΙΣ ΜΕΛΩΝ ΣΥΛΛΟΓΩΝ</b>', 'kratisi' => 0, amount => 0),
					'516' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΕΚΠΑΙΔΕΥΤΙΚΩΝ</b>', 'kratisi' => 0, amount => 0),
					'517' => array('desc' => '<b>ΑΜΟΙΒΕΣ ΩΡΩΜΙΣΘΙΩΝ</b>', 'kratisi' => 0, amount => 0),
					'561' => array('desc' => '<b>ΥΠΕΡΩΡΙΕΣ ΣΕ ΓΡΑΦΕΙΑ ΒΟΥΛΕΥΤΩΝ</b>', 'kratisi' => 0, amount => 0),
					'563' => array('desc' => '<b>ΜΗ ΧΡΗΣΗ ΘΕΡΙΝΗΣ ΑΔΕΙΑΣ</b>', 'kratisi' => 0, amount => 0),
					'711' => array('desc' => '<s>ΟΔΟΙΠΟΡΙΚΑ</s>', 'kratisi' => 0, amount => 0),

					'andr' 	  => array(		
											'total' => array('days' => '', 'amount' => 0)
										)
				);