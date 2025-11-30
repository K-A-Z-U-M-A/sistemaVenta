Set WshShell = WScript.CreateObject("WScript.Shell")
strDesktop = WshShell.SpecialFolders("Desktop")
Set oShellLink = WshShell.CreateShortcut(strDesktop & "\ABRIR SISTEMA.lnk")
oShellLink.TargetPath = "C:\Proyects\Sistema Abarrotes\sistemaAbarrotes\iniciar_silencioso.vbs"
oShellLink.WorkingDirectory = "C:\Proyects\Sistema Abarrotes\sistemaAbarrotes"
oShellLink.Description = "Iniciar Sistema de Abarrotes"
oShellLink.IconLocation = "%SystemRoot%\System32\SHELL32.dll,13"
oShellLink.Save
