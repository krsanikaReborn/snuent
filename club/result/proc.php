<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');
	
	/********************************************************************************************************
	* POST 처리
	********************************************************************************************************/
	$Valid = New Validation();
	$Form = New form();

	$_POST = $Form->clean($Valid->validate('', $_POST, Array('filter' => 'trim')));

	// 파일 경로
	$UploadDir = '/ActivityResult/';
	$stat = False;

	// index.php 에서 조회한 값
	$REG_HAGBEON			= $_SESSION['HOME']['USER_NO'];
	$REG_NAME					= $REG_NAME;
	$REG_MAJOR				= $REG_MAJOR;
	$REG_GRADE				= $REG_GRADE;

	Try {
		$TEAM_CD						= $Valid->validate('팀 관리번호',										$_POST['TEAM_CD'],							Array('validate' => 'required|max_len,10'));
		$RESULT							= $Valid->validate('창업활동 계획대비 실행결과',				$_POST['RESULT'],							Array('validate' => 'required'));
		$NON_RESULT					= $Valid->validate('학기 계획대비 미 실행 상황',					$_POST['NON_RESULT'],					Array('validate' => 'required'));
		$PLAN_NOTE					= $Valid->validate('창업 활동 성과 및 방학 중 활동 계획',	$_POST['PLAN_NOTE'],						Array('validate' => 'required'));
		$FILE_DESCRIPTION1		= $Valid->validate('문서명1',												$_POST['FILE_DESCRIPTION1'],			Array('validate' => 'max_len,100'));
		$FILE_DESCRIPTION2		= $Valid->validate('문서명2',												$_POST['FILE_DESCRIPTION2'],			Array('validate' => 'max_len,100'));
		$FILE_DESCRIPTION3		= $Valid->validate('문서명3',												$_POST['FILE_DESCRIPTION3'],			Array('validate' => 'max_len,100'));
		$FILE_DESCRIPTION4		= $Valid->validate('문서명4',												$_POST['FILE_DESCRIPTION4'],			Array('validate' => 'max_len,100'));
		$FILE_DESCRIPTION5		= $Valid->validate('문서명5',												$_POST['FILE_DESCRIPTION5'],			Array('validate' => 'max_len,100'));
	} Catch (Exception $e) {
		location(2, $e->getMessage(), '/club/result/');
	}

	$CREATE_ID		= 'HOME_'.$_SESSION['HOME']['USER_NO'];
	
	
	// 해당 동아리의 활동결과보고서를 조회한다.
	$DB->setProcName('SP_WEB_ACTIVITY_REPORT_DETAIL_SELECT');
	$DB->bind_param('P_YEAR',		 			$YEAR,														_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',		 			$HAGGI,													_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD', 			$TEAM_CD,												_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_USER_NO',			$USER_NO,												_VARCHAR,	20,		_PARAM_IN);

	$view_stat = false;
	
	If ($DB->ExecuteProc()) {
		$Result = Array();
		$Result = $DB->get_fetch_assoc();

		If($Result) {
			$view_stat = true;

			$SUBMIT_YN		= $Result[0]['REGIST_YN'];			// 제출여부
			$LEADER_YN		= $Result[0]['LEADER_YN'];			// 팀장여부

			$REG_NAME			= $Result[0]['REG_NAME'];
			$REG_MAJOR		= $Result[0]['REG_MAJOR'];
			$REG_GRADE		= $Result[0]['REG_GRADE'];

		}
	}

	If(!$view_stat) location(2, '활동결과보고서 조회에 실패하였습니다. 관리자에게 문의하십시오.', '/club/result/');

	// 제출여부
	If($SUBMIT_YN == 'Y') location(2, '활동결과보고서를 이미 제출한 동아리입니다.', '/club/result/');
	
	// 팀장여부
	If($LEADER_YN != 'Y') location(2, '활동결과보고서는 팀장만 제출할 수 있습니다.', '/club/result/');

	$DB->setProcName('SP_WEB_ACTIVITY_REPORT_SAVE');
	$DB->bind_param('P_YEAR',								$YEAR,								_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI',								$HAGGI,							_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD',						$TEAM_CD,						_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_RESULT',							$RESULT,							_VARCHAR,	8000,	_PARAM_IN);
	$DB->bind_param('P_NON_RESULT',					$NON_RESULT,				_VARCHAR,	8000,	_PARAM_IN);
	$DB->bind_param('P_PLAN_NOTE',						$PLAN_NOTE,					_VARCHAR,	8000,	_PARAM_IN);
	$DB->bind_param('P_REG_HAGBEON',				$REG_HAGBEON,				_VARCHAR,	20,		_PARAM_IN);
	$DB->bind_param('P_REG_NAME',						$REG_NAME,					_VARCHAR,	30,		_PARAM_IN);
	$DB->bind_param('P_REG_MAJOR',					$REG_MAJOR,					_VARCHAR,	100,		_PARAM_IN);
	$DB->bind_param('P_REG_GRADE',					$REG_GRADE,					_VARCHAR,	3,			_PARAM_IN);
	$DB->bind_param('P_CREATE_ID',						$CREATE_ID,					_VARCHAR,	50,		_PARAM_IN);
	$DB->bind_param('V_RETURN_NO',					'',										_VARCHAR,	20,		_PARAM_OUT);

	If($DB->ExecuteProc()) {
		$RETURN_NO = $DB->output['V_RETURN_NO'];
		$stat = True;
	} Else {
		location(2, HtmlSpecialChars($DB->get_error()), '/club/result/');
	}

	// 파일업로드
	$fileUpload = True;

	For($i = 1; $i <= 5; $i++) {

		// 첨부파일 업로드 ( 1부터 5까지 동적변수를 생성하여 처리한다. )
		If ($_FILES['FILE_'.$i]['name'] != "") {
			${'fileName'.$i} = Date('YmdHms') . '_' . generate_state();

			Try {
				${'FILE_PATH'.$i} = UploadPathFile($_FILES['FILE_'.$i], ${'fileName'.$i}, $UploadDir);
				${'FILE'.$i} = fileInfo(${'FILE_PATH'.$i});

				${'FILE_NAME'.$i} = $_FILES['FILE_'.$i]['name'];
				${'REALFILE_NAME'.$i} = ${'FILE'.$i}['fileInfo']['basename'];
				${'FILE_TYPE'.$i} = ${'FILE'.$i}['fileInfo']['mime'];
				${'FILE_SIZE'.$i} = ${'FILE'.$i}['fileInfo']['filesize'];

				// 파일 등록
				$DB->setProcName('SP_WEB_ACTIVITY_REPORT_FILE_SAVE');
				$DB->bind_param('P_YEAR',							$YEAR,										_VARCHAR,	4,			_PARAM_IN);
				$DB->bind_param('P_HAGGI',							$HAGGI,									_VARCHAR,	6,			_PARAM_IN);
				$DB->bind_param('P_TEAM_CD',					$TEAM_CD,								_VARCHAR,	10,		_PARAM_IN);
				$DB->bind_param('P_FILE_NAME',					${'FILE_NAME'.$i},					_VARCHAR,	200,		_PARAM_IN);
				$DB->bind_param('P_REALFILE_NAME',			${'REALFILE_NAME'.$i},			_VARCHAR,	500,		_PARAM_IN);
				$DB->bind_param('P_FILE_PATH',					${'FILE_PATH'.$i},					_VARCHAR,	600,		_PARAM_IN);
				$DB->bind_param('P_FILE_SIZE',					${'FILE_SIZE'.$i},						_INT,			11,		_PARAM_IN);
				$DB->bind_param('P_FILE_TYPE',					${'FILE_TYPE'.$i},					_VARCHAR,	100,		_PARAM_IN);
				$DB->bind_param('P_FILE_DESCRIPTION',		${'FILE_DESCRIPTION'.$i},		_VARCHAR,	1000,	_PARAM_IN);
				$DB->bind_param('P_CREATE_ID',					$CREATE_ID,							_VARCHAR,	50,		_PARAM_IN);

				If(!$DB->ExecuteProc()) { $fileUpload = False; }
			} Catch(Exception $e) {
				$fileUpload = False;
			}
		}
	}
	
	If($stat) {
		If(!$fileUpload) {
			location(2, '활동결과보고서가 제출되었으나 파일업로드에 실패하였습니다. 관리자에게 문의하십시오.', '/club/result/');
		}
		Else {
			location(2, '활동결과보고서가 제출되었습니다.', '/club/result/');
		}
	}

	Exit;
?>