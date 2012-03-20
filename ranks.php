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
// Το αρχείο αυτό περιέχει την κωδικοποίηση για τους βαθμούς του ενιαίου μισθολογίου
/* *********** ΤΕΛΟΣ ΓΕΝΙΚΗΣ ΠΕΡΙΓΡΑΦΗΣ *********** */


$ranks = array(	'548' => 'ΣΤ0',
				'549' => 'Ε0',
				'550' => 'Δ0',
				'551' => 'Γ0',
				'552' => 'Β0',
				'553' => 'Α0',
				'554' => 'Ε1',
				'555' => 'Δ1',
				'556' => 'Γ1',
				'557' => 'Β1',
				'558' => 'Α1',
				'559' => 'Ε2',
				'560' => 'Δ2',
				'561' => 'Γ2',
				'562' => 'Β2',
				'563' => 'Α2',
				'564' => 'Ε3',
				'565' => 'Δ3',
				'566' => 'Γ3',
				'567' => 'Β3',
				'568' => 'Α3',
				'569' => 'Ε4',
				'570' => 'Δ4',
				'571' => 'Γ4',
				'572' => 'Β4',
				'573' => 'Α4',
				'574' => 'Ε5',
				'575' => 'Δ5',
				'576' => 'Γ5',
				'577' => 'Β5',
				'578' => 'Α5',
				'579' => 'Δ6',
				'580' => 'Γ6',
				'581' => 'Β6',
				'582' => 'Α6',
				'583' => 'Γ7',
				'584' => 'Β7',
				'585' => 'Α7',
				'586' => 'Γ8',
				'587' => 'Β8'
			);