$CMD='RUNDLL32 PRINTUI.DLL,PrintUIEntry /y /n 801'
RunWait(@ComSpec & " /c " & $CMD)
run('"C:\Program Files (x86)\Microsoft Office\Office12\WINWORD.EXE" c:\sample22.docx /mFilePrintDefault /mFileExit /q /n')
WinWait("Microsoft Office Word")
if WinExists("Microsoft Office Word") Then
   If not WinActivate("Microsoft Office Word","�`1���ȱi�j�p") Then WinActivate("Microsoft Office Word","�`1���ȱi�j�p")
	 ControlClick("Microsoft Office Word","�`1���ȱi�j�p","[CLASS:Button; INSTANCE:1]", "left")
EndIf