#include <File.au3>
#include <Array.au3>
#include <WinAPI.au3>
#include <Word.au3>
#RequireAdmin
While 1
   Global $aFileList =_FileListToArray(@ScriptDir&"\noread","*.docx",$FLTA_FILES)
;~    _ArrayDisplay($aFileList)
   If Not @error Then
	  For $i=1 to $aFileList[0]
		 $filearray=StringSplit($aFileList[$i],"_")
		 IniWrite(@ScriptDir&"\set.ini",$filearray[1],"dest",$aFileList[$i])
;~ 		 ConsoleWrite($company&" "&$dept)
		 printer($filearray[1])
	  Next
	  Sleep(500)
   EndIf
WEnd

Func printer($dep)
   $printer=IniRead(@ScriptDir&"\set.ini",$dep,"printer","null")
   ConsoleWrite(" "&$printer&@CRLF)
;~    Exit
   $filedest=IniRead("set.ini",$dep,"dest","")
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
		 FileMove(@ScriptDir&"\noread\"&$aFileList[$i],@ScriptDir&"\read\"&$aFileList[$i],$FC_OVERWRITE)
;~ 		 Exit
	  EndIf
	   _Word_Quit($oWord)
	   SetError(1,1,"Can Not printer¡I")
;~ 	  Exit(1)
   EndIf
   _Word_Quit($oWord)
   SetError(2,2,"No file¡I")
;~    Exit(2)
EndFunc