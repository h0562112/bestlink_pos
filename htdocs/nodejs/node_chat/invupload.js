var fs=require('fs');
var request=require('request');
var loadIniFile=require('read-ini-file');
var looptime=30;//循環時間(分)
var timeout=5;//逾時時間(秒)
var mainlogdir='../../print/invuploadlog';//上傳發票紀錄檔路徑
var logdir=mainlogdir+'/log';//log存放路徑
var invuploaddir=mainlogdir+'/waitupload';//檢查待上傳檔案路徑
var completedir=mainlogdir+'/uploaded';//上傳後存放檔案路徑

const zdninvsetup=loadIniFile.sync('../../database/setup.ini');
//console.log(zdninvsetup);
const zdnid=zdninvsetup.zdninv.id;
const zdnpw=zdninvsetup.zdninv.psw;
const zdnurl=zdninvsetup.zdninv.url;

function getNowData(){
	var date=new Date();
	return date.getFullYear()+("0" + (date.getMonth() + 1)).slice(-2)+("0" + date.getDate()).slice(-2);
};
function getNowTime(){
	var date=new Date();
	return date.getFullYear()+'/'+("0" + (date.getMonth() + 1)).slice(-2)+'/'+("0" + date.getDate()).slice(-2)+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+' ';
};
function writelog(text,invname=''){
	if(invname!=''){
		fs.open(logdir+'/'+invname.substr(15,10)+'.txt','a',666,function(e,id){
			fs.write(id,text+'\r\n',null,'utf8',function(){
				fs.close(id,function(){
					console.log(text);
					return true;
				});
			});
		});
	}
	else{
		fs.open(logdir+'/'+getNowData()+'log.txt','a',666,function(e,id){
			fs.write(id,text+'\r\n',null,'utf8',function(){
				fs.close(id,function(){
					console.log(text);
					return true;
				});
			});
		});
	}
};
function get401invdetail(invfilename){
	var invdata=fs.readFileSync(invfilename);
	var tinvdata=JSON.parse(invdata);
	var main=tinvdata['Main'];
	var buyer=tinvdata['Main']['Buyer'];
	var seller=tinvdata['Main']['Seller'];
	var amount=tinvdata['Amount'];
	var sendC0401=new Array();
	var salelist=new Array();
	var productitem=tinvdata['Details']['ProductItem'];
	for(var i=0;i<productitem.length;i++){
		var detail={};
		detail['seq']=productitem[i]['SequenceNumber'];
		detail['name']=productitem[i]['Description'];
		detail['saleprice']=productitem[i]['UnitPrice'];
		detail['amount']=productitem[i]['Quantity'];
		detail['total']=productitem[i]['Amount'];
		salelist.push(detail);
	}
	//console.log(salelist);
	//console.log(JSON.stringify(salelist));
	if(typeof main['CarrierType']==='undefined'&&typeof main['NPOBAN']==='undefined'){//沒有使用載具或愛心碼
		sendC0401["taxid"]=seller['Identifier'];
		sendC0401["customer_taxid"]=buyer['Identifier'];
		sendC0401["sales_sum"]=amount['TotalAmount'];
		sendC0401["cdate"]=main['InvoiceDate'].substr(0,4)+'-'+main['InvoiceDate'].substr(4,2)+'-'+main['InvoiceDate'].substr(6,2)+' '+main['InvoiceTime'];
		sendC0401["invoice_number"]=main['InvoiceNumber'];
		sendC0401["address"]=seller['Address'];
		sendC0401["tel"]="";//2020/8/7 因為中鼎產生的XML中沒有該欄位且API規定一定要有這個欄位，因此填入空值//2020/8/5 先不填測試是否能更成功，因為中鼎產生的XML中並沒有該欄位
		sendC0401["seller_name"]=seller['Name'];
		sendC0401["random"]=main['RandomNumber'];
		sendC0401["tax_sale_amount"]=amount['SalesAmount'];
		sendC0401["tax_free_sale_amount"]=amount['FreeTaxSalesAmount'];
		sendC0401["tax_zero_sale_amount"]=amount['ZeroTaxSalesAmount'];
		sendC0401["tax_amount"]=amount['TaxAmount'];
		sendC0401["tax_type"]=amount['TaxType'];
		sendC0401["carrierType"]="";
		sendC0401["carrierID"]="";
		sendC0401["pcode"]="";
		sendC0401["salelist"]=JSON.stringify(salelist);
		sendC0401["db_check"]=1;
	}
	else if(typeof main.CarrierType!=='undefined'){//使用載具
		sendC0401["taxid"]=seller['Identifier'];
		sendC0401["customer_taxid"]=buyer['Identifier'];
		sendC0401["sales_sum"]=amount['TotalAmount'];
		sendC0401["cdate"]=main['InvoiceDate'].substr(0,4)+'-'+main['InvoiceDate'].substr(4,2)+'-'+main['InvoiceDate'].substr(6,2)+' '+main['InvoiceTime'];
		sendC0401["invoice_number"]=main['InvoiceNumber'];
		sendC0401["address"]=seller['Address'];
		sendC0401["tel"]="";//2020/8/7 因為中鼎產生的XML中沒有該欄位且API規定一定要有這個欄位，因此填入空值//2020/8/5 先不填測試是否能更成功，因為中鼎產生的XML中並沒有該欄位
		sendC0401["seller_name"]=seller['Name'];
		sendC0401["random"]=main['RandomNumber'];
		sendC0401["tax_sale_amount"]=amount['SalesAmount'];
		sendC0401["tax_free_sale_amount"]=amount['FreeTaxSalesAmount'];
		sendC0401["tax_zero_sale_amount"]=amount['ZeroTaxSalesAmount'];
		sendC0401["tax_amount"]=amount['TaxAmount'];
		sendC0401["tax_type"]=amount['TaxType'];
		sendC0401["carrierType"]=main['CarrierType'];
		sendC0401["carrierID"]=main['CarrierId1'];
		sendC0401["pcode"]="";
		sendC0401["salelist"]=JSON.stringify(salelist);
		sendC0401["db_check"]=1;
	}
	else{//使用愛心碼
		sendC0401["taxid"]=seller['Identifier'];
		sendC0401["customer_taxid"]=buyer['Identifier'];
		sendC0401["sales_sum"]=amount['TotalAmount'];
		sendC0401["cdate"]=main['InvoiceDate'].substr(0,4)+'-'+main['InvoiceDate'].substr(4,2)+'-'+main['InvoiceDate'].substr(6,2)+' '+main['InvoiceTime'];
		sendC0401["invoice_number"]=main['InvoiceNumber'];
		sendC0401["address"]=seller['Address'];
		sendC0401["tel"]="";//2020/8/7 因為中鼎產生的XML中沒有該欄位且API規定一定要有這個欄位，因此填入空值//2020/8/5 先不填測試是否能更成功，因為中鼎產生的XML中並沒有該欄位
		sendC0401["seller_name"]=seller['Name'];
		sendC0401["random"]=main['RandomNumber'];
		sendC0401["tax_sale_amount"]=amount['SalesAmount'];
		sendC0401["tax_free_sale_amount"]=amount['FreeTaxSalesAmount'];
		sendC0401["tax_zero_sale_amount"]=amount['ZeroTaxSalesAmount'];
		sendC0401["tax_amount"]=amount['TaxAmount'];
		sendC0401["tax_type"]=amount['TaxType'];
		sendC0401["carrierType"]="";
		sendC0401["carrierID"]="";
		sendC0401["pcode"]=main['NPOBAN'];
		sendC0401["salelist"]=JSON.stringify(salelist);
		sendC0401["db_check"]=1;
	}

	return sendC0401;
};
function get501invdetail(invfilename){
	var invdata=fs.readFileSync(invfilename);
	var tinvdata=JSON.parse(invdata);
	var amount=tinvdata['Amount'];
	var sendC0501=new Array();

	sendC0501["taxid"]=zdnid;
	sendC0501["CancelInvoiceNumber"]=tinvdata['CancelInvoiceNumber'];//註銷發票號碼
	sendC0501["InvoiceDate"]=tinvdata['InvoiceDate'];//發票開立日期
	sendC0501["BuyerId"]=tinvdata['BuyerId'];//買方統編
	sendC0501["SellerId"]=tinvdata['SellerId'];//賣方統編
	sendC0501["CancelDate"]=tinvdata['CancelDate'];//作廢日期
	sendC0501["CancelTime"]=tinvdata['CancelTime'];//作廢時間
	sendC0501["CancelReason"]=tinvdata["CancelReason"];

	return sendC0501;
};
async function loop(i,maxfiles,files){
	if(i>=maxfiles){
		setTimeout(function(){
			fs.readdir(invuploaddir,async function(err,files){
				await writelog(getNowTime()+'new check loop.');
				loop(0,files.length,files);
			});
		},looptime*1000);
	}
	else{

		//read inv data file(.ini)
		var upfile=files[i];
		if(upfile.substr(0,5)=='C0401'){
			var invarray=get401invdetail(invuploaddir+'/'+upfile);
			//console.log(invarray);
			var options={
				uri:zdnurl+'getCurToken',
				method:'POST',
				form:{
					"taxid":zdnid,
					"password":zdnpw
				},
				timeout:5000
			};
			request(options,async function (error,response,body) {
				if(!error&&response.statusCode==200) {
					var getdata=JSON.parse(body);
					if(typeof getdata['token']!=='undefined'){
						options={
							uri:zdnurl+'fGenC0401',
							method:'POST',
							form:invarray,
							headers:{
								"Accept":"application/json",
								"Authorization":"Bearer "+getdata['token']
							},
							timeout:5000
						};
						
						request(options,async function (error,response,body) {
							if(!error&&response.statusCode==200) {
								var getresponse=JSON.parse(body);
								//console.log(getresponse);
								if(typeof getresponse['success']!=='undefined'&&getresponse['success']){
									await writelog(getNowTime()+'upload seccess. : '+body,upfile);
									await writelog(getNowTime()+upfile+' upload done.(seccess)');
								}
								else if(typeof getresponse['success']!=='undefined'&&!getresponse['success']){
									await writelog(getNowTime()+'upload fail. : message => '+getresponse['message'],upfile);
									await writelog(getNowTime()+upfile+' upload done.(fail)');
								}
								else{
									await writelog(getNowTime()+'upload error. : '+body,upfile);
									await writelog(getNowTime()+upfile+' upload done.(ERROR)');
								}
								fs.rename(invuploaddir+'/'+upfile,completedir+'/'+upfile,async function(err){
									if(err){
										await writelog(getNowTime()+err);
									}
									else{
										await writelog(getNowTime()+upfile+' move success.');
									}
								});
							}
							else{
								await writelog(getNowTime()+'fGenC0401: '+upfile+' '+error+'\r\nresponse: '+response+'\r\nbody: '+body);
							}
						});
					}
					else{
						await writelog(getNowTime()+'getCurToken: no token. '+body);
					}
				}
				else{
					await writelog(getNowTime()+'getCurToken: '+error+'\r\nresponse: '+response+'\r\nbody: '+body);
				}
			});
		}
		else if(upfile.substr(0,5)=='C0501'){
			var invarray=get501invdetail(invuploaddir+'/'+upfile);
			//console.log(invarray);
			var options={
				uri:zdnurl+'getCurToken',
				method:'POST',
				form:{
					"taxid":zdnid,
					"password":zdnpw
				},
				timeout:5000
			};
			request(options,async function (error,response,body) {
				if(!error&&response.statusCode==200) {
					var getdata=JSON.parse(body);
					if(typeof getdata['token']!=='undefined'){
						options={
							uri:zdnurl+'fGenC0501',
							method:'POST',
							form:invarray,
							headers:{
								"Accept":"application/json",
								"Authorization":"Bearer "+getdata['token']
							},
							timeout:5000
						};
						
						request(options,async function (error,response,body) {
							if(!error&&response.statusCode==200) {
								var getresponse=JSON.parse(body);
								//console.log(getresponse);
								if(typeof getresponse['success']!=='undefined'&&getresponse['success']){
									await writelog(getNowTime()+'upload seccess. : '+body,upfile);
									await writelog(getNowTime()+upfile+' upload done.(seccess)');
								}
								else if(typeof getresponse['success']!=='undefined'&&!getresponse['success']){
									await writelog(getNowTime()+'upload fail. : message => '+getresponse['message'],upfile);
									await writelog(getNowTime()+upfile+' upload done.(fail)');
								}
								else{
									await writelog(getNowTime()+'upload error. : '+body,upfile);
									await writelog(getNowTime()+upfile+' upload done.(ERROR)');
								}
								fs.rename(invuploaddir+'/'+upfile,completedir+'/'+upfile,async function(err){
									if(err){
										await writelog(getNowTime()+err);
									}
									else{
										await writelog(getNowTime()+upfile+' move success.');
									}
								});
							}
							else{
								await writelog(getNowTime()+'fGenC0501: '+upfule+' '+error+'\r\nresponse: '+response+'\r\nbody: '+body);
							}
						});
					}
					else{
						await writelog(getNowTime()+'getCurToken: no token. '+body);
					}
				}
				else{
					await writelog(getNowTime()+'getCurToken: '+error+'\r\nresponse: '+response+'\r\nbody: '+body);
				}
			});
		}
		else{
			await writelog(getNowTime()+upfile+' : file name is error.('+i+')');
		}
		setTimeout(async function(){
			loop(++i,maxfiles,files);
		},100);
	}
};

if(fs.existsSync(mainlogdir)){
}
else{
	fs.mkdirSync(mainlogdir);
}
if(fs.existsSync(invuploaddir)){
}
else{
	fs.mkdirSync(invuploaddir);
}
if(fs.existsSync(completedir)){
}
else{
	fs.mkdirSync(completedir);
}
if(fs.existsSync(logdir)){
}
else{
	fs.mkdirSync(logdir);
}
fs.readdir(invuploaddir,async function(err,files){
	await writelog(getNowTime()+'start check loop.');
	loop(0,files.length,files);
});