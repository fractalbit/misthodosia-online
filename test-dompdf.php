<?php
require_once 'functions.inc.php';

// Require dompdf, libraries, and helper functions
require_once 'dompdf/lib/html5lib/Parser.php';
// require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
// require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
// $dompdf->set_option('defaultFont', 'DejaVu Sans');

$html = file_get_contents('test.html');
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();

// file_put_contents('test.pdf', $dompdf->output());

// echo 'Success';