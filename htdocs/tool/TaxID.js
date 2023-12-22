/*2019/10/22
** 檢查統編是否符合國稅局提供的格式
** 共八位，全部為數字型態
** 各數字分別乘以 1,2,1,2,1,2,4,1
** 例：統一編號為 53212539
** 
** Step1 將統編每位數乘以一個固定數字固定值
**   5   3   2   1   2   5   3   9
** x 1   2   1   2   1   2   4   1
** ================================
**   5   6   2   2   2  10  12   9
** 
** Step2 將所得值取出十位數及個位數
** 十位數 個位數
**   0      5
**   0      6
**   0      2
**   0      2
**   0      2
**   1      0
**   1      2
**   0      9
** 並將十位數與個位數全部結果值加總
** 
** Step3 判斷結果
** 第一種:加總值取10的餘數為0
** 第二種:加總值取9的餘數等於9而且統編的第6碼為7
*/

function check_TaxID(ID){
	var istrue=1;
	var ban=ID;
	var value=0;
	var t=[1,2,1,2,1,2,4,1];//主要加權
	var temp=0;
	for(var i=0;i<8;i++){
		/*Step1*/
		temp=parseInt(ban.substr(i,1))*t[i];
		/*Step2*/
		if(parseInt(temp)>=10){
			value=parseInt(value)+parseInt(temp.toString().substr(0,1))+parseInt(temp.toString().substr(1,1));
		}
		else{
			value=parseInt(value)+parseInt(temp);
		}
	}
	/*Step3*/
	if(value%10==0){//第一種
	}
	else if(parseInt(ban.substr(6,1))==7&&(value+1)%10==0){//第二種
	}
	else{
		istrue=0;
	}

	return istrue;
}