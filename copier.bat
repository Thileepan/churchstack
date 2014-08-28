@echo off
SET CURR_PATH=%~dp0
SET CURR_BATCH_FILE_NAME=%~n0%~x0
REM //Check the number of arguments and break if less than the expected count
IF "%~1"=="" (
	echo.
	echo Syntax Error
	echo Expected Syntax : %CURR_BATCH_FILE_NAME% ^<source^> ^<target^> ^[^<exclusion_list_file^>^]
	EXIT /B
)

IF "%~2"=="" (
	echo.
	echo Syntax Error
	echo Expected Syntax : %CURR_BATCH_FILE_NAME% ^<source^> ^<target^> ^[^<exclusion_list_file^>^]
	EXIT /B
)

SET "SOURCE_FOLDER_PATH=%~1"
SET "TARGET_FOLDER_PATH=%~2"
REM SET "EXCLUSION_LIST_FILE="

IF NOT EXIST "%SOURCE_FOLDER_PATH%" (
	echo.
	echo ERROR : source folder not found
	EXIT /B
)

IF NOT "%~3"=="" (
	SET "EXCLUSION_LIST_FILE=%~3"
) ELSE (
	SET "EXCLUSION_LIST_FILE="
)

IF NOT "%EXCLUSION_LIST_FILE%"=="" (
	IF NOT EXIST "%EXCLUSION_LIST_FILE%" (
		echo.
		echo ERROR : Exclusion List File not found : %EXCLUSION_LIST_FILE%
		EXIT /B
	)
	
	SET "COMMAND_TO_RUN=xcopy "%SOURCE_FOLDER_PATH%" "%TARGET_FOLDER_PATH%" /H /S /E /W /Y /V /I /Q  /EXCLUDE:%EXCLUSION_LIST_FILE%"
) ELSE (
	SET "COMMAND_TO_RUN=xcopy "%SOURCE_FOLDER_PATH%" "%TARGET_FOLDER_PATH%" /H /S /E /W /Y /V /I /Q"
)

%COMMAND_TO_RUN%

echo.
SET /P ANSWER=Do you want to zip the target folder (Y/N)? 
IF /i {%ANSWER%}=={y} (goto :ZIP_IT
) ELSE IF /i {%ANSWER%}=={yes} (goto :ZIP_IT
) ELSE GOTO EOF

:ZIP_IT
	ECHO Set objArgs = WScript.Arguments > _zipIt.vbs
	ECHO InputFolder = objArgs(0) >> _zipIt.vbs
	ECHO ZipFile = objArgs(1) >> _zipIt.vbs
	ECHO CreateObject("Scripting.FileSystemObject").CreateTextFile(ZipFile, True).Write "PK" ^& Chr(5) ^& Chr(6) ^& String(18, vbNullChar) >> _zipIt.vbs
	ECHO Set objShell = CreateObject("Shell.Application") >> _zipIt.vbs
	ECHO Set source = objShell.NameSpace(InputFolder).Items >> _zipIt.vbs
	ECHO objShell.NameSpace(ZipFile).CopyHere(source) >> _zipIt.vbs
	ECHO wScript.Sleep 2000 >> _zipIt.vbs

	CScript  _zipIt.vbs  %~f2  %~f2.zip
	DEL _zipIt.vbs
::

:EOF
	EXIT /B