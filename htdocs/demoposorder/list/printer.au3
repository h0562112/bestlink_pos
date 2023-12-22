#RequireAdmin
#include <WinAPI.au3>
#include <Word.au3>
$printer=IniRead("set.ini","basic","printer","")
$filedest=IniRead("set.ini","basic","dest","")
$printer=StringReplace($printer,'"','')
$filedest=StringReplace($filedest,'"','')
ConsoleWrite($printer&@CRLF&@ScriptDir & "\noread\"&$filedest&@CRLF)
_WinAPI_SetDefaultPrinter ( $printer )
Local $oWord = _Word_Create(False)
Local $oDoc = _Word_DocOpen($oWord, @ScriptDir & "\noread\"&$filedest, Default, Default, True)
If Not @error then
   _Word_DocPrint($oDoc,True)
   If Not @error Then
	  _Word_Quit($oWord)
	  ConsoleWrite("OK")
	  Exit
   EndIf
    _Word_Quit($oWord)
	SetError(1,1,"Can Not printer�I")
   Exit(1)
EndIf
_Word_Quit($oWord)
SetError(2,2,"No file�I")
Exit(2)
;~ WinWait("Microsoft Office Word")
;~ if WinExists("Microsoft Office Word") Then
;~    If not WinActivate("Microsoft Office Word","�`1���ȱi�j�p") Then WinActivate("Microsoft Office Word","�`1���ȱi�j�p")
;~ 	 ControlClick("Microsoft Office Word","�`1���ȱi�j�p","[CLASS:Button; INSTANCE:1]", "left")
;~ EndIf