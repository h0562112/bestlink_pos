<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <script src="./lib/api/ourmember/member_api.js"></script>
  <script src="../tool/jquery-1.12.4.js"></script>
  <title>Document</title>
  <script>
  $(document).ready(function(){
	  //api_point_money_ourmember(company,story,memno,paymoney,giftpoint,memberpoint,membermoney)
	  var res=api_point_money_ourmember('demo','demo0001','demo00015',-100,-2,0,0);
	  console.log(res);
	  //$('#res').html(res);
	  if(res[0]['state']=='success'){
		  $('#res').html('');
		  $.each(res[0],function(index,value){
			  $('#res').append(index+': '+value+'<br>');
		  });
	  }
	  else{
		  $('#res').html('');
		  $('#res').append('state: '+res[0]['state']+'<br>');
		  $('#res').append('message: '+res[0]['message']+'<br>');
	  }
  });
  </script>
 </head>
 <body>
  <div style="border:1px solid #000000;margin:4px;padding:4px;">
	company: demo<br>
	story: demo00012<br>
	memno: demo00011<br>
	paymoney: 0<br>
	memberpoint: 0<br>
	membermoney: 0
  </div>
  <div id='res' style="border:1px solid #000000;margin:4px;padding:4px;">
	
  </div>
 </body>
</html>
