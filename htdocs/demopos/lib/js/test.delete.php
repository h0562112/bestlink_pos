<?php
include '../../../demoinv/cancelinv.php';
$filename='C0501_CE39575748_20180413093521.xml';
$xml=new SimpleXMLElement($xmlstr);
$xml->addChild('CancelInvoiceNumber','CE39575748');
$xml->addChild('InvoiceDate','20180412');
$xml->addChild('BuyerId','0000000000');
$xml->addChild('SellerId','68338064');
$xml->addChild('CancelDate','20180413');
$xml->addChild('CancelTime','09:35:21');
$xml->addChild('CancelReason','作廢發票');
$xml->saveXML('../../../print/noread/'.$filename);
?>