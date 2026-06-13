<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true); 
$options->set('isRemoteEnabled', true);       

$dompdf = new Dompdf($options);

ob_start();

include __DIR__ . '/report.php'; 

$html = ob_get_clean();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

if (ob_get_length()) {
    ob_end_clean();
}

$dompdf->stream("report.pdf", array("Attachment" => true));
exit;