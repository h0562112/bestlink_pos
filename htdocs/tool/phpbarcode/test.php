<?php

include('src/BarcodeGenerator.php');
include('src/BarcodeGeneratorPNG.php');

$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcodeNoText('20191121000026', $generator::TYPE_CODE_39)) . '">';
//file_put_contents('test.png', $generator->getBarcodeNoText('11002BB123456785971', $generator::TYPE_CODE_39));
?>