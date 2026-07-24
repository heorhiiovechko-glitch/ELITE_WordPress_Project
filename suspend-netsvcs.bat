@echo off
:: Suspend ONLY svchost.exe (-k netsvcs). Does not kill it.
:: Right-click -> Run as administrator

net session >nul 2>&1
if errorlevel 1 (
    echo [!] Right-click this file and choose "Run as administrator".
    pause
    exit /b 1
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ErrorActionPreference='Stop';" ^
  "Add-Type @'" ^
  "using System;" ^
  "using System.Runtime.InteropServices;" ^
  "public class Native {" ^
  "  [DllImport(\"ntdll.dll\")] public static extern int NtSuspendProcess(IntPtr h);" ^
  "  [DllImport(\"kernel32.dll\", SetLastError=true)] public static extern IntPtr OpenProcess(uint a, bool b, int pid);" ^
  "  [DllImport(\"kernel32.dll\", SetLastError=true)] public static extern bool CloseHandle(IntPtr h);" ^
  "}'@;" ^
  "$p = Get-CimInstance Win32_Process -Filter \"Name='svchost.exe'\" |" ^
  "  Where-Object { $_.CommandLine -match '-k\s+netsvcs(\s|$)' } |" ^
  "  Select-Object -First 1;" ^
  "if (-not $p) { Write-Host '[!] netsvcs svchost not found.'; exit 1 }" ^
  "$h = [Native]::OpenProcess(0x1F0FFF, $false, [int]$p.ProcessId);" ^
  "if ($h -eq [IntPtr]::Zero) { Write-Host '[!] Failed to open PID' $p.ProcessId; exit 1 }" ^
  "$r = [Native]::NtSuspendProcess($h);" ^
  "[void][Native]::CloseHandle($h);" ^
  "if ($r -ne 0) { Write-Host '[!] Suspend failed, status:' $r; exit 1 }" ^
  "Write-Host '[OK] Suspended svchost.exe (netsvcs) PID' $p.ProcessId"

echo.
echo Use resume-netsvcs.bat to unsuspend later.
pause
