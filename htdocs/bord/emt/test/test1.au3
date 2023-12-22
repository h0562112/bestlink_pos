$CMD='RUNDLL32 PRINTUI.DLL,PrintUIEntry /y /n 801'
RunWait(@ComSpec & " /c " & $CMD)
run('"C:\Program Files (x86)\Microsoft Office\Office12\WINWORD.EXE" c:\sample22.docx /mFilePrintDefault /mFileExit /q /n')
WinWait("Microsoft Office Word")
if WinExists("Microsoft Office Word") Then
   If not WinActivate("Microsoft Office Word","節1的紙張大小") Then WinActivate("Microsoft Office Word","節1的紙張大小")
	 ControlClick("Microsoft Office Word","節1的紙張大小","[CLASS:Button; INSTANCE:1]", "left")
EndIf