<?php
//2022/5/30 原先有寫在main.js中，但是被註解，忘記當初用途，先保留(最早有開一版面只為了處理nidin訂單，沒有跟POS串接，可能用在這邊)
require_once("../../tool/TCPDF/examples/tcpdf_include.php");

$paymentmethod=parse_ini_file("../data/paymentmethod.ini",true);

$list=$_POST['getlistarray'];
if(isset($list)&&sizeof($list['list'])>0){
	//產生PDF明細單
	// create new PDF document
	$pdf = new TCPDF("P", "mm", array(72,297), true, "UTF-8", false);
	//$pdf = new TCPDF(P:直式、L:橫式, 單位(mm), 紙張大小(長短邊；不分長寬：array(,) ), true, "UTF-8", false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor("Nicola Asuni");
	$pdf->SetTitle("clientlist");
	$pdf->SetSubject("");
	$pdf->SetKeywords("TCPDF, PDF, example, clientlist");

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

	if (@file_exists(dirname(__FILE__)."/../tool/TCPDF/examples/lang/eng.php")) {
		require_once(dirname(__FILE__)."/../tool/TCPDF/examples/lang/eng.php");
		$pdf->setLanguageArray($l);
	}

	// set font
	$pdf->SetFont("msungstdlight", "", 10);

	// add a page
	$pdf->AddPage();

	$html = '
	<style>
		div {
			width:100%;
			float:left;
			margin:0;
			padding:0;
			height:max-content;
		}
		table {
			width:100%;
			margin:0;
			padding:0;
			float:left;
		}
		.th {
			text-align:center;
		}
		.number {
			text-align:right;
		}
		.bottomline {
			border-top:1px solid #000000;
		}
		.topline {
			border-bottom:1px solid #000000;
		}
	</style>
	<div>
		<table>
			<tr>
				<td colspan="12"><span style="font-size:20px;">';
			if($list["list"][0]["order_info"]["order_method"]==2){
				$html .= "預定";
			}
			else{
			}
			switch($list["list"][0]["order_info"]["delivery_type"]){
				case 1:
					$html .= "自取";
					break;
				case 2:
					$html .= "內用";
					break;
				case 3:
					$html .= "外送";
					break;
				case 4:
					$html .= "外帶";
					break;
				case 5:
					$html .= "吧檯";
					break;
				default:
					$html .= "未知";
					break;
			}
			$html .= '</span><br>'.$list['list'][0]['order_info']['order_sn'].'</td>
			</tr>
			<tr>
				<td colspan="6">訂購人</td>
				<td colspan="6">'.$list["list"][0]["order_info"]["order_name"].'</td>
			</tr>
			<tr>
				<td colspan="6">訂購電話</td>
				<td colspan="6">'.$list["list"][0]["order_info"]["order_phone"].'</td>
			</tr>
			<tr>
				<td colspan="6">預定時間</td>
				<td colspan="6">'.$list["list"][0]["order_info"]["delivery_reserv_date"]."(";
			switch(date("N",$list["list"][0]["order_info"]["delivery_reserv_date"])){
				case 1:
					$html .= "一";
					break;
				case 2:
					$html .= "二";
					break;
				case 3:
					$html .= "三";
					break;
				case 4:
					$html .= "四";
					break;
				case 5:
					$html .= "五";
					break;
				case 6:
					$html .= "六";
					break;
				case 7:
					$html .= "日";
					break;
				default:
					break;
			}
			$html .= ")<br>".substr($list["list"][0]["order_info"]["order_time"],0,5)."~".substr($list["list"][0]["order_info"]["delivery_reserv_time"],0,5).'</td>
			</tr>';
		if($list["list"][0]["order_info"]["delivery_type"]!=3){
		}
		else{
			$html .= '<tr>
					<td colspan="6">外送地址</td>
					<td colspan="6">'.$list["list"][0]["order_info"]["address"].'</td>
				</tr>';
		}
		$html .= '<tr>
				<td colspan="6">付款狀態</td>
				<td colspan="6">';
			switch($list["list"][0]["order_info"]["shopper_payment_status"]){
				case 11:
					$html .= '尚未付款';
					break;
				case 12:
					$html .= '付款處理中';
					break;
				case 13:
					$html .= '已付款';
					break;
				case 21:
					$html .= '退款處理中';
					break;
				case 22:
					$html .= '已退款';
					break;
				default:
					break;
			}
			if($list["list"][0]["order_info"]["shopper_payment_status"]!=11){
				if(isset($list["list"][0]["order_payments"])&&sizeof($list["list"][0]["order_payments"])>0){
					$payment=array();
					for($p=0;$p<sizeof($list["list"][0]["order_payments"]);$p++){
						if(isset($payment[$list["list"][0]["order_payments"][$p]['method']])){
							$payment[$list["list"][0]["order_payments"][$p]['method']]=floatval($payment[$list["list"][0]["order_payments"][$p]['method']])+floatval($list["list"][0]["order_payments"][$p]['money']);
						}
						else{
							$payment[$list["list"][0]["order_payments"][$p]['method']]=$list["list"][0]["order_payments"][$p]['money'];
						}
					}
					if(sizeof($payment)>0){
						$html .= '<br>(';
						$payindex=1;
						foreach($payment as $paymethod=>$paymoney){
							if($payindex!=1){
								$html .= ',';
							}
							else{
							}
							$html .= $paymentmethod['method'][$paymethod];
							$payindex++;
						}
						$html .= ')';
					}
					else{
					}
				}
				else{
				}
			}
			else{
			}
			$html .= '</td>
			</tr>';
		if($list["list"][0]["order_info"]["ein"]==""&&$list["list"][0]["invoice_usage"]["donate_code"]==10){
		}
		else{
			$html .= '<tr>
					<td colspan="6">發票類型</td>
					<td colspan="6">';
				if($list["list"][0]["order_info"]["ein"]!=""){
					$html .= "統一編號";
				}
				else if($list["list"][0]["invoice_usage"]["carrier_type"]!=""){
					if($list["list"][0]["invoice_usage"]["carrier_type"]=="3J0002"){
						$html .= "手機載具";
					}
					else{
						$html .= "其餘載具";
					}
				}
				else if($list["list"][0]["invoice_usage"]["donate_code"]==30){
					$html .= "愛心碼";
				}
				else{
				}
				$html .= '</td>
				</tr>
				<tr>
					<td colspan="6">載具</td>
					<td colspan="6">';
				if($list["list"][0]["order_info"]["ein"]!=""){
					$html .= $list["list"][0]["order_info"]["ein"];
				}
				else if($list["list"][0]["invoice_usage"]["carrier_type"]!=""){
					$html .= $list["list"][0]["invoice_usage"]["carrier_id1"];
				}
				else if($list["list"][0]["invoice_usage"]["donate_code"]==30){
					$html .= $list["list"][0]["invoice_usage"]["npoban"];
				}
				else{
				}
				$html .= '</td>
				</tr>';
		}
	$html .= '
			<tr>
				<td class="topline" colspan="12"></td>
			</tr>
			<tr>
				<td colspan="6" class="th topline">Items</td>
				<td colspan="3" class="topline number">U/P</td>
				<td colspan="3" class="topline number">Sub</td>
			</tr>';
		$sizearraycode=array(10,12,14,16,18);
		for($t=0;$t<sizeof($list["list"][0]["items"]);$t++){
			$itemdata['name']='';
			$itemdata['size']='';
			$itemdata['number']='';
			$itemdata['price']='';
			$html .= '<tr>';
			$html .= '<td colspan="6">';

			$itemdata['name']=$list["list"][0]["items"][$t]["name"];
			if($list["list"][0]["items"][$t]["amount"]>1){
				$itemdata['number']='*'.$list["list"][0]["items"][$t]["amount"];
			}
			else{
			}
			//$html .= '</td>';
			//$html .= '<td colspan="3" class="number">';
			$itemdata['price']=$list["list"][0]["items"][$t]["price"];
			//$html .= '</td>';
			if($list["list"][0]["items"][$t]["type"]=="1"){//產品名稱
				$itemmoney=$list["list"][0]["items"][$t]["money"];
				$taste='';
				if(isset($list["list"][0]["items"][$t]["options"])&&sizeof($list["list"][0]["items"][$t]["options"])>0){
					$taste .= '<tr><td colspan="12">';
					for($o=0;$o<sizeof($list["list"][0]["items"][$t]["options"]);$o++){
						if($list["list"][0]["items"][$t]["options"][$o]['type']=='2'&&!in_array($list["list"][0]["items"][$t]["options"][$o]['sub_type'],$sizearraycode)){
							$itemmoney=floatval($itemmoney)+floatval($list["list"][0]["items"][$t]["options"][$o]["money"]);
							if($taste!='<tr><td colspan="12">'){
								$taste .= ',';
							}
							else{
								$taste .= '&nbsp;&nbsp;＋';
							}
							$taste .= $list["list"][0]["items"][$t]["options"][$o]["name"];
							if($list["list"][0]["items"][$t]["options"][$o]["amount"]>$list["list"][0]["items"][$t]["amount"]){
								$taste .= '*'.intval($list["list"][0]["items"][$t]["options"][$o]["amount"])/intval($list["list"][0]["items"][$t]["amount"]);
							}
							else{
							}
						}
						else{
							$itemmoney=floatval($itemmoney)+floatval($list["list"][0]["items"][$t]["options"][$o]["money"]);
							$itemdata['size']='('.$list["list"][0]["items"][$t]["options"][$o]["name"].')';
						}
					}
					$taste .= '</td>';
				}
				else{
				}
				$html .= $itemdata['name'].$itemdata['size'].$itemdata['number'].'</td>';
				$html .= '<td colspan="3" class="number">'.$itemdata['price']=$list["list"][0]["items"][$t]["price"].'</td>';
				$html .= '<td colspan="3" class="number">'.$itemmoney.'</td>';
				if($taste==''){
				}
				else{
					$html .= '</tr>'.$taste;
				}
			}
			else{//$list["list"][0]["items"][$t]["type"]=="3"//套餐名稱
				$html .= $itemdata['name'].$itemdata['size'].$itemdata['number'].'</td>';
				$html .= '<td colspan="3" class="number">'.$itemdata["price"].'</td>';

				$itemmoney=$list["list"][0]["items"][$t]["money"];
				$subitem='';
				if(isset($list["list"][0]["items"][$t]["items"])&&sizeof($list["list"][0]["items"][$t]["items"])>0){
					for($sub=0;$sub<sizeof($list["list"][0]["items"][$t]["items"]);$sub++){
						$itemdata['name']='';
						$itemdata['size']='';
						$itemdata['number']='';
						$itemdata['price']='';

						if($sub!=0){
							$subitem .= '</tr>';
						}
						else{
						}
						$itemmoney=floatval($itemmoney)+floatval($list["list"][0]["items"][$t]["items"][$sub]["money"]);
						$subitemmoney=$list["list"][0]["items"][$t]["items"][$sub]["money"];
						$subitem .= '<tr>';
						$subitem .= '<td colspan="6">';

						$itemdata['name']='－'.$list["list"][0]["items"][$t]["items"][$sub]["name"];

						if($list["list"][0]["items"][$t]["items"][$sub]["amount"]>1){
							$itemdata['number']='*'.$list["list"][0]["items"][$t]["items"][$sub]["amount"];
						}
						else{
						}
						//$subitem .= '</td>';
						//$subitem .= '<td colspan="3" class="number">';
						$itemdata['price']=$list["list"][0]["items"][$t]["items"][$sub]["price"];
						//$subitem .= '</td>';
						$subtaste='';
						if(isset($list["list"][0]["items"][$t]["items"][$sub]["options"])&&sizeof($list["list"][0]["items"][$t]["items"][$sub]["options"])>0){
							$subtaste .= '<tr><td colspan="12">';
							for($o=0;$o<sizeof($list["list"][0]["items"][$t]["items"][$sub]["options"]);$o++){
								if($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]['type']=='2'&&!in_array($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]['sub_type'],$sizearraycode)){
									$itemmoney=floatval($itemmoney)+floatval($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$subitemmoney=floatval($subitemmoney)+floatval($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									if($subtaste!='<tr><td colspan="12">'){
										$subtaste .= ',';
									}
									else{
										$subtaste .= '&nbsp;&nbsp;&nbsp;&nbsp;＋';
									}
									$subtaste .= $list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["name"];
									if($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["amount"]>$list["list"][0]["items"][$t]["items"][$sub]["amount"]){
										$subtaste .= '*'.intval($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["amount"])/intval($list["list"][0]["items"][$t]["items"][$sub]["amount"]);
									}
									else{
									}
								}
								else{
									$itemmoney=floatval($itemmoney)+floatval($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$subitemmoney=floatval($subitemmoney)+floatval($list["list"][0]["items"][$t]["items"][$sub]["options"][$o]["money"]);
									$itemdata['size']='('.$list["list"][0]["items"][$t]["items"][$sub]["options"][$o]['name'].')';
								}
							}
							$subtaste .= '</td>';
						}
						else{
						}
						$subitem .= $itemdata['name'].$itemdata['size'].$itemdata['number'].'</td>';
						$subitem .= '<td colspan="3" class="number">'.$itemdata['price'].'</td>';
						$subitem .= '<td colspan="3" class="number">'.$subitemmoney.'</td>';
						if($subtaste==''){
						}
						else{
							$subitem .= '</tr>'.$subtaste;
						}
					}
				}
				else{
				}
				$html .= '<td colspan="3" class="number">'.$itemmoney.'</td>';
				$html .= '</tr>'.$subitem;
			}
			$html .= '</tr>';
		}
	$html .= '<tr>
				<td class="bottomline" colspan="12"></td>
			</tr>
			<tr>
				<td colspan="6">商品數量</td>
				<td colspan="6" class="number">'.$list["list"][0]["order_info"]["amount"].'</td>
			</tr>
			<tr>
				<td colspan="6">總計</td>
				<td colspan="6" class="number">'.$list["list"][0]["order_info"]["money"].'</td>
			</tr>
			<tr>
				<td colspan="6" class="topline">折扣</td>
				<td colspan="6" class="number topline">'.($list["list"][0]["order_info"]["money"]-$list["list"][0]["order_info"]["paid_money"]).'</td>
			</tr>
			<tr>
				<td colspan="6">實收金額</td>
				<td colspan="6" class="number">'.$list["list"][0]["order_info"]["paid_money"].'</td>
			</tr>
			<tr>
				<td colspan="12">備註</td>
			</tr>
			<tr>
				<td colspan="12">'.$list["list"][0]["order_info"]["shopper_remark"].'</td>
			</tr>
		</table>
	</div>
	';

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, "");

	// reset pointer to the last page
	$pdf->lastPage();

	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

	$filename=$list["list"][0]["order_info"]["order_sn"].'_client';
	switch($list["list"][0]["order_info"]["delivery_type"]){
		case 1:
			$filename .= "4";
			break;
		case 2:
			$filename .= "1";
			break;
		case 3:
			$filename .= "3";
			break;
		case 4:
			$filename .= "2";
			break;
		case 5:
			$filename .= "N";
			break;
		default:
			$filename .= "N";
			break;
	}
	$filename .= 'm1_'.date('YmdHis');

	//Close and output PDF document
	$pdf->Output(dirname(__FILE__)."/../../print/read/".$filename.".pdf", "F");

	if($_POST['printtype']==='1'){
		$f=fopen("../../print/noread/".$filename.".prt",'w');
		fclose($f);
	}
	else{
	}

	//============================================================+
	// END OF FILE
	//============================================================+

}
else{
}
?>