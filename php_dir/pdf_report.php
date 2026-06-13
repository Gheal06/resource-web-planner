<?php
// 1. Suppress PHP warnings about legacy syntax so they don't corrupt the PDF
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);

// 2. Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// 3. Define the missing legacy constants that Dompdf 0.6.2 expects
if (!defined('DOMPDF_TEMP_DIR')) {
    define('DOMPDF_TEMP_DIR', sys_get_temp_dir());
}
if (!defined('DOMPDF_CHROOT')) {
    define('DOMPDF_CHROOT', __DIR__);
}
if (!defined('DOMPDF_UNICODE')) {
    define('DOMPDF_UNICODE', true);
}
if (!defined('DOMPDF_FONT_DIR')) {
    define('DOMPDF_FONT_DIR', sys_get_temp_dir());
}
if (!defined('DOMPDF_FONT_CACHE')) {
    define('DOMPDF_FONT_CACHE', sys_get_temp_dir());
}

// 4. Instantiate the legacy DOMPDF class
$dompdf = new DOMPDF();

// 5. Configure settings
$dompdf->set_option("enable_html5_parser", true);
$dompdf->set_option("enable_remote", true); 

// 6. Start Output Buffering
ob_start();

// 7. Include your normal report layout
include __DIR__ . '/report.php'; 

// 8. Capture the HTML output string
$html = ob_get_clean();

// 9. Load, render, and stream
$dompdf->load_html($html);
$dompdf->set_paper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("report.pdf", array("Attachment" => 1));
exit;