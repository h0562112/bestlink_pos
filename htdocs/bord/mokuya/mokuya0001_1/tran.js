var index=1;
var c;
var disPoacityNumber=1;
var showPoacityNumber=0;
/*function tranImg(time,divID){
	window.setInterval("tranMethod('"+divID+"')", time*1000);
}
function tranMethod(divID){
	this.disPoacityNumber=1;
	this.showPoacityNumber=0;
	this.c=document.getElementById(divID).childNodes;
	if(this.index==c.length-2){
		var st=this.index;
		var en=1;
		var tr1=window.setInterval("disPic("+st+")",50);
		var tr2=window.setInterval("showPic("+en+")",50);
	}
	else{
		var st=this.index;
		var en=this.index+2;
		var tr1=window.setInterval("disPic("+st+")",50);
		var tr2=window.setInterval("showPic("+en+")",50);
	}
	window.setTimeout("window.clearInterval("+tr1+")",1200);
	window.setTimeout("window.clearInterval("+tr2+")",1200);
	window.setTimeout(change,300);
	//document.getElementById('text').innerHTML="index="+this.index;
}*/
function change(){
	this.index=(this.index+2)%(c.length-1);
}
/*function disPic(picIndex){
	this.disPoacityNumber=this.disPoacityNumber-0.05;
	if(this.disPoacityNumber<=0){
		this.c[picIndex].style.opacity=0;
		this.c[picIndex].style.zIndex=4;
	}
	else{
		this.c[picIndex].style.opacity=this.disPoacityNumber;
	}
	if(this.disPoacityNumber<=0){
		this.disPoacityNumber=0;
	}
}
function showPic(picIndex){
	this.showPoacityNumber=this.showPoacityNumber+0.05;
	if(this.showPoacityNumber>=1){
		this.c[picIndex].style.opacity=1;
		this.c[picIndex].style.zIndex=5;
	}
	else{
		this.c[picIndex].style.opacity=this.showPoacityNumber;
	}
	if(this.showPoacityNumber>=1){
		this.showPoacityNumber=1;
	}
}*/