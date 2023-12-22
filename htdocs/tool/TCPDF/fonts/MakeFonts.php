<?php
require_once('../tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$font = TCPDF_FONTS::addTTFfont('msjh.ttf');
?>