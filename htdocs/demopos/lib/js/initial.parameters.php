<?php
function parameter($initsetting,$buttons1,$buttons2,$itemdis,$printlisttag,$member){
	echo "<div class='initsetting' style='display:none;'>";
		echo "<input type='hidden' id='settime' value='".$initsetting['init']['settime']."'>";//設定時區
		echo "<input type='hidden' id='firlan' value='".$initsetting['init']['firlan']."'>";//主語言代碼
		echo "<input type='hidden' id='seclan' value='".$initsetting['init']['seclan']."'>";//次語言代碼
		echo "<input type='hidden' id='opentemp' value='".$initsetting['init']['opentemp']."'>";//1>>開啟暫結0>>關閉暫結
		echo "<input type='hidden' id='controltable' value='".$initsetting['init']['controltable']."'>";//1>>開啟桌控(必須開啟暫結)0>>關閉桌控
		echo "<input type='hidden' id='openpersoncount' value='".$initsetting['init']['openpersoncount']."'>";//0>>關閉用餐人數1>>開啟用餐人數
		echo "<input type='hidden' id='orderlocation' value='".$initsetting['init']['orderlocation']."'>";//內用、外帶、外送按鈕開啟與位置(e.g.2,1,0 第一個位置為'外帶'按鈕，第二個位置為'內用'按鈕，第三個位置為空)
		echo "<input type='hidden' id='ordertype' value='".$initsetting['init']['ordertype']."'>";//預設選取帳單類別(內用/來店/外送)
		echo "<input type='hidden' id='tabnum' value='".$initsetting['init']['tabnum']."'>";//1>>啟用內用桌號0>>停用內用桌號
		echo "<input type='hidden' id='salecash' value='".$initsetting['init']['salecash']."'>";//1>>開啟現金結帳功能0>>關閉現金結帳功能
		echo "<input type='hidden' id='menutype' value='".$initsetting['init']['menutype']."'>";//菜單類別(單/雙層)
		echo "<input type='hidden' id='menutyperow' value='".$initsetting['init']['menutyperow']."'>";//菜單類別之列數
		echo "<input type='hidden' id='menutypecol' value='".$initsetting['init']['menutypecol']."'>";//菜單類別之行數
		echo "<input type='hidden' id='menurow' value='".$initsetting['init']['menurow']."'>";//菜單項目之列數
		echo "<input type='hidden' id='menucol' value='".$initsetting['init']['menucol']."'>";//菜單項目之行數
		echo "<input type='hidden' id='groupchildtyperow' value='".$initsetting['init']['groupchildtyperow']."'>";//套餐選項類別之列數
		echo "<input type='hidden' id='groupchildtypecol' value='".$initsetting['init']['groupchildtypecol']."'>";//套餐選項類別之行數
		echo "<input type='hidden' id='groupchildrow' value='".$initsetting['init']['groupchildrow']."'>";//套餐選項之列數
		echo "<input type='hidden' id='groupchildcol' value='".$initsetting['init']['groupchildcol']."'>";//套餐選項之行數
		echo "<input type='hidden' id='tasterow' value='".$initsetting['init']['tasterow']."'>";//備註與加料每頁之列數
		echo "<input type='hidden' id='tastecol' value='".$initsetting['init']['tastecol']."'>";//備註與加料每頁之行數
		echo "<input type='hidden' id='publicseq' value='".$initsetting['init']['publicseq']."'>";//公開與專屬備註優先順序1>>公開優先2>>專屬優先
		echo "<input type='hidden' id='listprint' value='".$initsetting['init']['listprint']."'>";//出單類別(1>>出單2>>不出單3>>只出總單4>>只出標籤)
		echo "<input type='hidden' id='useoinv' value='".$initsetting['init']['useoinv']."'>";//是否啟用傳統發票(1/0)
		echo "<input type='hidden' id='oinv' value='".$initsetting['init']['oinv']."'>";//是否開立傳統發票(1/0)
		echo "<input type='hidden' id='useinv' value='".$initsetting['init']['useinv']."'>";//是否啟用電子發票(1/0)
		echo "<input type='hidden' id='inv' value='".$initsetting['init']['inv']."'>";//是否開立發票(1/0)
		echo "<input type='hidden' id='manyinv' value='".$initsetting['init']['manyinv']."'>";//是否開啟多張發票按鈕(1/0)
		echo "<input type='hidden' id='accuracytype' value='".$initsetting['init']['accuracytype']."'>";//折扣後之金額進位方式(四捨五入、無條件進位、無條件捨去)
		echo "<input type='hidden' id='accuracy' value='".$initsetting['init']['accuracy']."'>";//進位方式之精準度(精準度)
		echo "<input type='hidden' id='accuracyseq' value='".$initsetting['init']['accuracyseq']."'>";//折扣順序1>>帳單折扣以照單品與會員折扣後之價格2>>帳單折扣以照原始價格
		echo "<input type='hidden' id='itemaccuracyseq' value='".$initsetting['init']['itemaccuracyseq']."'>";//折扣順序1>>單品折扣以照依照加料後的價格2>>單品折扣以照原始價格
		echo "<input type='hidden' id='member' value='".$initsetting['init']['member']."'>";//是否使用會員折扣(1/0)
		echo "<input type='hidden' id='membernumber' value='".$initsetting['init']['membernumber']."'>";//會員折扣數(折數)
		echo "<input type='hidden' id='openchar' value='".$initsetting['init']['openchar']."'>";//是否開啟服務費(1/0)
		echo "<input type='hidden' id='charge' value='".$initsetting['init']['charge']."'>";//是否收取服務費(1/0)
		echo "<input type='hidden' id='chargenumber' value='".$initsetting['init']['chargenumber']."'>";//服務費之費率(%數)
		echo "<input type='hidden' id='chargeeq' value='".$initsetting['init']['chargeeq']."'>";//1>>以原價計算服務費2>>以折扣後之價格計算服務費
		echo "<input type='hidden' id='disbut1' value='".$initsetting['init']['disbut1']."'>";//是否開啟結帳畫面之預設折扣按鈕1(1/0)
		echo "<input type='hidden' id='disnum1' value='".$initsetting['init']['disnum1']."'>";//結帳畫面之預設折扣按鈕1(折數)
		echo "<input type='hidden' id='disbut2' value='".$initsetting['init']['disbut2']."'>";//是否開啟結帳畫面之預設折扣按鈕2(1/0)
		echo "<input type='hidden' id='disnum2' value='".$initsetting['init']['disnum2']."'>";//結帳畫面之預設折扣按鈕2(折數)
		echo "<input type='hidden' id='disbut3' value='".$initsetting['init']['disbut3']."'>";//是否開啟結帳畫面之預設折扣按鈕3(1/0)
		echo "<input type='hidden' id='disnum3' value='".$initsetting['init']['disnum3']."'>";//結帳畫面之預設折扣按鈕3(折數)
		echo "<input type='hidden' id='disbut4' value='".$initsetting['init']['disbut4']."'>";//是否開啟結帳畫面之預設折扣按鈕4(1/0)
		echo "<input type='hidden' id='disnum4' value='".$initsetting['init']['disnum4']."'>";//結帳畫面之預設折扣按鈕4(折數)
		echo "<input type='hidden' id='disbut5' value='".$initsetting['init']['disbut5']."'>";//是否開啟結帳畫面之預設折扣按鈕5(1/0)
		echo "<input type='hidden' id='disnum5' value='".$initsetting['init']['disnum5']."'>";//結帳畫面之預設折扣按鈕5(折數)
		echo "<input type='hidden' id='disbut6' value='".$initsetting['init']['disbut6']."'>";//是否開啟結帳畫面之預設折扣按鈕6(1/0)
		echo "<input type='hidden' id='disnum6' value='".$initsetting['init']['disnum6']."'>";//結帳畫面之預設折扣按鈕6(折數)
		echo "<input type='hidden' id='orderdis' value='".$initsetting['init']['orderdis']."'>";//是否開啟結帳畫面之帳單折扣按鈕(1/0)
		echo "<input type='hidden' id='orderdisnum' value='".$initsetting['init']['orderdisnum']."'>";//是否開啟結帳畫面之帳單折讓按鈕(1/0)
		echo "<input type='hidden' id='coupon1' value='".$initsetting['init']['coupon1']."'>";//是否開啟結帳畫面之不找零禮卷按鈕(1/0)
		echo "<input type='hidden' id='coupon2' value='".$initsetting['init']['coupon2']."'>";//是否開啟結帳畫面之找零禮卷按鈕(1/0)
		echo "<input type='hidden' id='moneybut' value='".$initsetting['init']['moneybut']."'>";//是否開啟結帳畫面之現金按鈕(1/0)
		echo "<input type='hidden' id='cashbut' value='".$initsetting['init']['cashbut']."'>";//是否開啟結帳畫面之信用卡按鈕(1/0)
		echo "<input type='hidden' id='changehint' value='".$initsetting['init']['changehint']."'>";//是否開啟開啟找零視窗(1/0)
		echo "<input type='hidden' id='openmember' value='".$initsetting['init']['openmember']."'>";//是否開啟開啟會員(1/0)
		echo "<input type='hidden' id='changeclose' value='".$initsetting['init']['changeclose']."'>";//找零視窗自動關閉倒數(秒)
		echo "<input type='hidden' id='openfloor' value='".$initsetting['init']['openfloor']."'>";//0>>關閉低銷1>>開啟低銷
		echo "<input type='hidden' id='comfloor' value='".$initsetting['init']['comfloor']."'>";//1>>以原價計算服務費2>>以折扣後計算服務費
		echo "<input type='hidden' id='secview' value='".$initsetting['init']['secview']."'>";//0>>關閉客戶顯示窗1>>開啟客戶顯示窗
		echo "<input type='hidden' id='cclasstime' value='".$initsetting['init']['cclasstime']."'>";//0>>手動切班1>>依照時間切班(參考class.ini)
		echo "<input type='hidden' id='autodis' value='".$initsetting['init']['autodis']."'>";//0>>關閉自動優惠1>>開啟自動優惠
		echo "<input type='hidden' id='cashcomm' value='";if(!isset($initsetting['init']['cashcomm']))echo "0";else echo $initsetting['init']['cashcomm'];echo "'>";//信用卡手續費(%;10%手續費填入10)
		echo "<input type='hidden' id='onlinemember' value='".$initsetting['init']['onlinemember']."'>";//0>>本地會員1>>網路會員
		echo "<input type='hidden' id='temptoinv' value='";if(!isset($initsetting['init']['temptoinv']))echo "0";else echo $initsetting['init']['temptoinv'];echo "'>";//0>>暫結不可以開立發票1>>暫結可以開立發票
		if($initsetting['init']['listprint']==1){
			echo "<input type='hidden' id='listprintname1' value='";if($buttons1!='-1')echo $buttons1['name']['23'];echo "'>";
			echo "<input type='hidden' id='listprintname2' value='";if($buttons2!='-1')echo $buttons2['name']['23'];echo "'>";
			echo "<input type='hidden' id='looptype' value='1'>";
		}
		else if($initsetting['init']['listprint']==2){
			echo "<input type='hidden' id='listprintname1' value='";if($buttons1!='-1')echo $buttons1['name']['24'];echo "'>";
			echo "<input type='hidden' id='listprintname2' value='";if($buttons2!='-1')echo $buttons2['name']['24'];echo "'>";
			echo "<input type='hidden' id='looptype' value='2'>";
		}
		else if($initsetting['init']['listprint']==3){
			echo "<input type='hidden' id='listprintname1' value='";if($buttons1!='-1')echo $buttons1['name']['25'];echo "'>";
			echo "<input type='hidden' id='listprintname2' value='";if($buttons2!='-1')echo $buttons2['name']['25'];echo "'>";
			echo "<input type='hidden' id='looptype' value='3'>";
		}
		else{
			echo "<input type='hidden' id='listprintname1' value='";if($buttons1!='-1')echo $buttons1['name']['26'];echo "'>";
			echo "<input type='hidden' id='listprintname2' value='";if($buttons2!='-1')echo $buttons2['name']['26'];echo "'>";
			echo "<input type='hidden' id='looptype' value='4'>";
		}
		//echo "<input type='hidden' id='bysaleday' value='";if(!isset($initsetting['init']['bysaleday']))echo "0";else echo $initsetting['init']['bysaleday'];echo "'>";//0>>帳單營收歸屬開單日1>>帳單營收歸屬結帳日
		echo "<input type='hidden' id='reserve' value='";if(!isset($initsetting['init']['reserve']))echo "0";else echo $initsetting['init']['reserve'];echo "'>";//0>>關閉預約單1>>開啟預約單
		echo "<input type='hidden' id='voidsale' value='";if(!isset($initsetting['init']['voidsale']))echo "0";else echo $initsetting['init']['voidsale'];echo "'>";//0>>開啟修改帳單1>>關閉修改帳單//0>>帳單註銷/作廢不必驗證1>>帳單註銷/作廢需驗證
		echo "<input type='hidden' id='voidpsw' value='";if(!isset($initsetting['init']['voidpsw']))echo "";else echo $initsetting['init']['voidpsw'];echo "'>";//作廢驗證密碼
		echo "<input type='hidden' id='invmoneytype' value='";if(!isset($initsetting['init']['invmoneytype']))echo "0";else echo $initsetting['init']['invmoneytype'];echo "'>";//0>>以應收金額為主1>>以發票金額(invsalemoney)為主(某些產品不開發票的店家使用)
		echo "<input type='hidden' id='creditcode' value='0'>";//2022/4/6 強制關閉，不紀錄信用卡後4碼，資料庫欄位轉移給nccc串接編號，以利後續刷退使用//0>>不輸入信用卡後4碼1>>輸入信用卡後4碼
		echo "<input type='hidden' id='notsale' value='";if(!isset($initsetting['init']['notsale']))echo "0";else echo $initsetting['init']['notsale'];echo "'>";//0>>關閉暫出結帳明細1>>開啟暫出結帳明細
		echo "<input type='hidden' id='openpay' value='";if(!isset($initsetting['init']['openpay']))echo "0";else echo $initsetting['init']['openpay'];echo "'>";//0>>關閉其他付款按鈕1>>開啟其他付款按鈕
		echo "<input type='hidden' id='subordertype' value='";if(!isset($initsetting['init']['subordertype']))echo $initsetting['init']['ordertype'];else echo $initsetting['init']['subordertype'];echo "'>";//1>>內用2>>外帶3>>外送(for submachine)

		if($itemdis=='-1'){
			echo "<input type='hidden' id='opensingledis' value='".$initsetting['init']['opensingledis']."'>";//0>>關閉"單一價"按鈕1>>開啟"單一價"按鈕
			echo "<input type='hidden' id='singledis' value='".$initsetting['init']['singledis']."'>";//單品折扣－單一價X元
		}
		else{
			echo "<input type='hidden' id='opensingledis' value='".$itemdis['item']['opensingledis']."'>";//0>>關閉"單一價"按鈕1>>開啟"單一價"按鈕
			echo "<input type='hidden' id='singledis' value='".$itemdis['item']['singledis']."'>";//單品折扣－單一價X元
			echo "<input type='hidden' id='opendis1' value='".$itemdis['item']['opendis1']."'>";//0>>關閉"快速折扣"按鈕1>>開啟"快速折扣"按鈕
			echo "<input type='hidden' id='dis1number' value='".$itemdis['item']['dis1number']."'>";//"快速折扣"之折數(e.g.9折填入90)
			echo "<input type='hidden' id='opendis2' value='".$itemdis['item']['opendis2']."'>";//0>>關閉"快速折讓"按鈕1>>開啟"快速折讓"按鈕
			echo "<input type='hidden' id='dis2number' value='".$itemdis['item']['dis2number']."'>";//"快速折讓"之金額
			if(isset($itemdis['memberpoint'])){//2020/4/14 單品會員點數兌換
				echo "<input type='hidden' id='usememberpoints' value='".$itemdis['memberpoint']['points']."'>";//一次兌換 N 點
				echo "<input type='hidden' id='usemembermaxmoney' value='".$itemdis['memberpoint']['maxmoney']."'>";//單品項一次最多折讓 M 元
				echo "<input type='hidden' id='usememberitemtime' value='".$itemdis['memberpoint']['itemtime']."'>";//單品項一次可使用 X 次優惠
				echo "<input type='hidden' id='usemembertype' value='".$itemdis['memberpoint']['type']."'>";//折讓規則 1>>原價計算2>>包含加料
			}
			else{
			}
		}

		echo "<input type='hidden' id='container1' value='";if(!isset($initsetting['init']['container1']))echo "8";else echo $initsetting['init']['container1'];echo "'>";//電子發票共通性載具－手機條碼驗證長度(預留政府往後更改長度)
		echo "<input type='hidden' id='container2' value='";if(!isset($initsetting['init']['container2']))echo "16";else echo $initsetting['init']['container2'];echo "'>";//電子發票自然人憑證驗證長度(預留政府往後更改長度)
		echo "<input type='hidden' id='weborder' value='";if(!isset($initsetting['init']['weborder']))echo "0";else echo $initsetting['init']['weborder'];echo "'>";//0>>關閉網路訂單下載按鈕1>>開啟網路訂單下載按鈕
		echo "<input type='hidden' id='webordersec' value='";if(!isset($initsetting['init']['webordersec']))echo "300";else echo $initsetting['init']['webordersec'];echo "'>";//檢查網路訂單的時間間隔(秒)
		echo "<input type='hidden' id='pointtree' value='";if(!isset($initsetting['init']['pointtree']))echo "0";else echo $initsetting['init']['pointtree'];echo "'>";//0>>停用"集點樹"功能1>>啟用"集點樹"功能
		echo "<input type='hidden' id='moneycost' value='";if(!isset($initsetting['init']['moneycost']))echo "0";else echo $initsetting['init']['moneycost'];echo "'>";//0>>關閉費用支出1>>開啟費用支出
		echo "<input type='hidden' id='maxtime' value='";if(!isset($initsetting['init']['maxtime']))echo "100";else echo $initsetting['init']['maxtime'];echo "'>";//用餐時間(分)
		echo "<input type='hidden' id='hinttime' value='";if(!isset($initsetting['init']['hinttime']))echo "100";else echo $initsetting['init']['hinttime'];echo "'>";//剩餘時間(分)
		echo "<input type='hidden' id='sechinttime' value='";if(!isset($initsetting['init']['sechinttime']))echo "100";else echo $initsetting['init']['sechinttime'];echo "'>";//二次提醒剩餘時間(分)
		echo "<input type='hidden' id='posdvr' value='";if(!isset($initsetting['init']['posdvr']))echo "0";else echo $initsetting['init']['posdvr'];echo "'>";//0>>關閉1>>開啟"錢都錄"借接API
		echo "<input type='hidden' id='quickcremember' value='";if(!isset($initsetting['init']['quickcremember']))echo "0";else echo $initsetting['init']['quickcremember'];echo "'>";//0>>不使用1>>使用快速新增會員
		echo "<input type='hidden' id='kvm' value='";if(!isset($initsetting['init']['kvm']))echo "0";else echo $initsetting['init']['kvm'];echo "'>";//0>>關閉廚房控餐1>>開啟廚房控餐
		echo "<input type='hidden' id='checkcontrol' value='";if(!isset($initsetting['init']['checkcontrol']))echo "1";else echo $initsetting['init']['checkcontrol'];echo "'>";//0>>不檢查桌控狀態1>>檢查桌控狀態
		echo "<input type='hidden' id='historypaper' value='";if(!isset($initsetting['init']['historypaper']))echo "0";else echo $initsetting['init']['historypaper'];echo "'>";//0>>停用"列印報表"功能1>>啟用"列印報表"功能
		echo "<input type='hidden' id='openindex' value='";if(!isset($initsetting['init']['openindex']))echo "0";else echo $initsetting['init']['openindex'];echo "'>";//0>>關閉員工登入1>>開啟員工登入
		echo "<input type='hidden' id='openpunch' value='";if(!isset($initsetting['init']['openpunch']))echo "0";else echo $initsetting['init']['openpunch'];echo "'>";//0>>關閉員工打卡1>>開啟員工打卡
		echo "<input type='hidden' id='controltabini' value='";if(!isset($initsetting['init']['controltabini']))echo "1";else echo $initsetting['init']['controltabini'];echo "'>";//0>>結帳號不清桌控1>>結帳後清桌控
		echo "<input type='hidden' id='quickorderbarlength' value='";if(!isset($initsetting['init']['quickorderbarlength']))echo "0";else echo $initsetting['init']['quickorderbarlength'];echo "'>";//barcode總長度，在開啟快點區的情況下，長度設定為0則不判斷barcode
		echo "<input type='hidden' id='quickorderstart' value='";if(!isset($initsetting['init']['quickorderstart']))echo "0";else echo $initsetting['init']['quickorderstart'];echo "'>";//產品編號起始位置(起始值為0)
		echo "<input type='hidden' id='quickorderlength' value='";if(!isset($initsetting['init']['quickorderlength']))echo "0";else echo $initsetting['init']['quickorderlength'];echo "'>";//產品編號長度
		echo "<input type='hidden' id='quickmoneystart' value='";if(!isset($initsetting['init']['quickmoneystart']))echo "0";else echo $initsetting['init']['quickmoneystart'];echo "'>";//價格起始位置(起始值為0)
		echo "<input type='hidden' id='quickmoneylength' value='";if(!isset($initsetting['init']['quickmoneylength']))echo "0";else echo $initsetting['init']['quickmoneylength'];echo "'>";//價格長度
		echo "<input type='hidden' id='accounting' value='";if(!isset($initsetting['init']['accounting']))echo "1";else echo $initsetting['init']['accounting'];echo "'>";//1>>以主機為主體2>>每台分機皆為主體
		echo "<input type='hidden' id='tastegroup' value='";if(!isset($initsetting['init']['tastegroup']))echo "0";else echo $initsetting['init']['tastegroup'];echo "'>";//0>>停用備註(加料)群組1>>啟用備註(加料)群組
		echo "<input type='hidden' id='ticket' value='";if(!isset($printlisttag['item']['ticket']))echo "1";else echo $printlisttag['item']['ticket'];echo "'>";//0>>不出票劵1>>暫結出票劵2>>結帳出票劵3>>結帳與暫結皆出票劵
		echo "<input type='hidden' id='ticketlisttype' value='";if(!isset($printlisttag['item']['ticketlisttype']))echo "1";else echo $printlisttag['item']['ticketlisttype'];echo "'>";//票劵於帳單類別出單
		echo "<input type='hidden' id='ourmempointmoney' value='";if(!isset($initsetting['init']['ourmempointmoney']))echo "0";else echo $initsetting['init']['ourmempointmoney'];echo "'>";//0>>不使用POS內建的會員點數(儲值金)1>>使用POS內建的會員點數(儲值金)
		echo "<input type='hidden' id='posexitmode' value='";if(!isset($initsetting['init']['posexitmode']))echo "1";else echo $initsetting['init']['posexitmode'];echo "'>";//1>>使用print離開2>>使用reg離開
		echo "<input type='hidden' id='intellapay' value='";if(!isset($initsetting['init']['intellapay']))echo "0";else echo $initsetting['init']['intellapay'];echo "'>";//0>>不使用1>>使用"英特拉"電子支付
		echo "<input type='hidden' id='easycard' value='";if(!isset($initsetting['init']['easycard']))echo "0";else echo $initsetting['init']['easycard'];echo "'>";//0>>關閉1>>開啟悠遊卡電子支付
		echo "<input type='hidden' id='linepay' value='";if(!isset($initsetting['init']['linepay']))echo "0";else echo $initsetting['init']['linepay'];echo "'>";//0>>關閉1>>開啟LinePay電子支付
		echo "<input type='hidden' id='creditcardpay' value='";if(!isset($initsetting['init']['creditcardpay']))echo "0";else echo $initsetting['init']['creditcardpay'];echo "'>";//0>>關閉1>>開啟線上信用卡支付
		echo "<input type='hidden' id='intellaother' value='";if(!isset($initsetting['init']['intellaother']))echo "0";else echo $initsetting['init']['intellaother'];echo "'>";//0>>關閉1>>開啟顧客自選支付
		echo "<input type='hidden' id='intellauser' value='";if(!isset($initsetting['init']['intellauser']))echo "0";else echo $initsetting['init']['intellauser'];echo "'>";//0>>關閉1>>開啟顧客被掃支付
		echo "<input type='hidden' id='itri' value='";if(!isset($initsetting['init']['itri']))echo "0";else echo $initsetting['init']['itri'];echo "'>";//0>>關閉"工研院商業獅"優惠卷1>>開啟"工研院商業獅"優惠卷
		echo "<input type='hidden' id='orderdis' value='";if(!isset($initsetting['init']['orderdis']))echo "1";else echo $initsetting['init']['orderdis'];echo "'>";//1>>開啟帳單折扣按鈕0>>關閉帳單折扣按鈕
		echo "<input type='hidden' id='orderdisnum' value='";if(!isset($initsetting['init']['orderdisnum']))echo "1";else echo $initsetting['init']['orderdisnum'];echo "'>";//1>>開啟帳單折讓按鈕0>>關閉帳單折讓按鈕
		echo "<input type='hidden' id='membertype' value='";if(!isset($initsetting['init']['membertype']))echo "1";else echo $initsetting['init']['membertype'];echo "'>";//0>>門市會員不共用1>>門市會員共用(網路會員專用)
		echo "<input type='hidden' id='webding' value='";if(!isset($initsetting['init']['webding']))echo "1";else echo $initsetting['init']['webding'];echo "'>";//0>>關閉網路訂單提醒音效1>>開啟網路訂單提醒音效
		echo "<input type='hidden' id='loopding' value='";if(!isset($initsetting['init']['loopding']))echo "0";else echo $initsetting['init']['loopding'];echo "'>";//0>>單次撥放網路訂單提醒音效1>>循環撥放網路訂單提醒音效
		echo "<input type='hidden' id='webmail' value='";if(!isset($initsetting['init']['webmail']))echo "1";else echo $initsetting['init']['webmail'];echo "'>";//0>>不寄送網路訂單提醒郵件1>>寄送網路訂單提醒郵件
		echo "<input type='hidden' id='webdownloadmachine' value='";if(!isset($initsetting['init']['webdownloadmachine']))echo "m1";else echo $initsetting['init']['webdownloadmachine'];echo "'>";//設定手動下載網路訂單的機號，其餘機號皆不可下載網路訂單
		echo "<input type='hidden' id='linklist' value='";if(!isset($initsetting['init']['linklist']))echo "0";else echo $initsetting['init']['linklist'];echo "'>";//0>>關閉1>>開啟外帶暫結對應內用帳單編號
		echo "<input type='hidden' id='intellaotherprint' value='";if(!isset($initsetting['init']['intellaotherprint']))echo "1";else echo $initsetting['init']['intellaotherprint'];echo "'>";//1>>英特拉消費者主掃qrcode顯示於客顯2>>英特拉消費者主掃qrcode列印於暫出明細單(一定列印)
		echo "<input type='hidden' id='outman' value='";if(!isset($initsetting['init']['outman']))echo "0";else echo $initsetting['init']['outman'];echo "'>";//0>>不紀錄外送人員1>>紀錄外送人員
		echo "<input type='hidden' id='faceidmember' value='";if(!isset($initsetting['init']['faceidmember']))echo "0";else echo $initsetting['init']['faceidmember'];echo "'>";//0>>關閉1>>使用會員臉部ID
		echo "<input type='hidden' id='reporttime' value='";if(!isset($initsetting['report']['reporttime']))echo "30";else echo $initsetting['report']['reporttime'];echo "'>";//定時回傳資料庫(秒)
		echo "<input type='hidden' id='opencashdraw' value='";if(!isset($initsetting['zdninv']['opencashdraw']))echo "1";else echo $initsetting['zdninv']['opencashdraw'];echo "'>";//0>>使用中鼎開立發票時，開立發票的情況不開啟錢櫃(因為中鼎同時也會送錢櫃指令)
		echo "<input type='hidden' id='openrfid' value='";if(!isset($initsetting['rfid']['open']))echo "0";else echo $initsetting['rfid']['open'];echo "'>";//0>>關閉RFID讀取點單1>>開啟RFID讀取點單(多卡)2>>開啟RFID讀取點單(單卡)
		echo "<input type='hidden' id='readrfidinterval' value='";if(!isset($initsetting['rfid']['timeout']))echo "0.5";else echo $initsetting['rfid']['timeout'];echo "'>";//於單卡讀取時，該秒數為讀取間隔

		echo "<input type='hidden' id='openpaypw' value='";if($member!=''&&isset($member['init']['openpaypw']))echo $member['init']['openpaypw'];else echo '0';echo "'>";//0>>停用會員交易密碼1>>開啟會員交易密碼
		echo "<input type='hidden' id='printedvoid' value='";if(!isset($initsetting['init']['printedvoid']))echo "0";else echo $initsetting['init']['printedvoid'];echo "'>";//刪除已出單品項0>>不用驗證1>>需要驗證
		echo "<input type='hidden' id='usenodejs' value='";if(!isset($initsetting['init']['usenodejs']))echo "0";else echo $initsetting['init']['usenodejs'];echo "'>";//0>>遵循舊有流程1>>套用nodejs流程
		echo "<input type='hidden' id='steplog' value='";if(!isset($initsetting['init']['steplog']))echo "0";else echo $initsetting['init']['steplog'];echo "'>";//0>>不紀錄操作流程1>>紀錄操作流程
		echo "<input type='hidden' id='foodpanda' value='";if(!isset($initsetting['init']['foodpanda']))echo "0";else echo $initsetting['init']['foodpanda'];echo "'>";//0>>關閉FoodPanda串接1>>開啟Foodpanda串接
		echo "<input type='hidden' id='quickclick' value='";if(!isset($initsetting['init']['quickclick']))echo "0";else echo $initsetting['init']['quickclick'];echo "'>";//0>>關閉QuickClick串接1>>開啟QuickClick串接
		echo "<input type='hidden' id='quickclicklisttype' value='";if(!isset($initsetting['init']['quickclicklisttype']))echo "0";else echo $initsetting['init']['quickclicklisttype'];echo "'>";//"0>>QuickClick為自動接單1>>QuickClick為手動接單"
		echo "<input type='hidden' id='nccc' value='";if(!isset($initsetting['init']['nccc']))echo "0";else echo $initsetting['init']['nccc'];echo "'>";//0>>關閉聯合信用卡中心刷卡機串接1>>開啟聯合信用卡中心刷卡機串接
		echo "<input type='hidden' id='nccceasycard' value='";if(!isset($initsetting['init']['nccceasycard']))echo "0";else echo $initsetting['init']['nccceasycard'];echo "'>";//0>>關閉聯合信用卡中心刷卡機悠遊卡串接1>>開啟聯合信用卡中心刷卡機悠遊卡串接
		echo "<input type='hidden' id='directlinepay' value='";if(!isset($initsetting['init']['directlinepay']))echo "0";else echo $initsetting['init']['directlinepay'];echo "'>";//0>>關閉直接linepay付款串接1>>開啟直接linepay串接
		echo "<input type='hidden' id='openubereats' value='";if(!isset($initsetting['ubereats']['openubereats']))echo "0";else echo $initsetting['ubereats']['openubereats'];echo "'>";//0>>關閉ubereats串接1>>開啟ubereats串接
		echo "<input type='hidden' id='yunlincoinsopen' value='";if(!isset($initsetting['yunlincoins']['open']))echo "0";else echo $initsetting['yunlincoins']['open'];echo "'>";//0>>關閉yunlincoins串接1>>開啟yunlincoins串接
		echo "<input type='hidden' id='jkos' value='";if(!isset($initsetting['init']['jkos']))echo "0";else echo $initsetting['init']['jkos'];echo "'>";//0>>關閉街口付款串接1>>開啟街口支付串接
		echo "<input type='hidden' id='pxpayplus' value='";if(!isset($initsetting['init']['pxpayplus']))echo "0";else echo $initsetting['init']['pxpayplus'];echo "'>";//0>>關閉全支付付款串接1>>開啟全支付支付串接
	echo "</div>";
}
?>