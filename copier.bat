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
