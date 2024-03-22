<?php
	Header("Content-Type: text/html; charset=UTF-8");

	Define("__ROOT__", $_SERVER['DOCUMENT_ROOT']);
	Define(webSite, '남서울*%*창업');
	Define(webPage, 'sub');
	Define(webBoard, 'board');

	Require_Once __ROOT__ . '/inc/DBConnection.php';
	mb_internal_encoding('UTF-8');
	
	$stat = False;
	$response = new stdClass();
	
	$Valid = New Validation();

	Try {
		$YEAR						= $Valid->validate('년도',					$_POST['YEAR'],					Array('validate' => 'required|max_len,4'));
		$HAGGI					= $Valid->validate('학기',					$_POST['HAGGI'],				Array('validate' => 'required|max_len,6'));
		$TEAM_CD				= $Valid->validate('팀 관리번호',		$_POST['TEAM_CD'],			Array('validate' => 'required|max_len,10'));
		$USER_NO				= $Valid->validate('학번',					$_POST['USER_NO'],			Array('validate' => 'required|max_len,20'));
	} Catch (Exception $e) {
		$response->process = false;
		$response->message = $e->getMessage();
		Echo urldecode(json_encode($response));
		Exit;
	}
	
	// 수강생정보 조회
	$DB->setProcName('SP_WEB_USER_SELECT_AJAX');
	$DB->bind_param('P_YEAR', 				$YEAR,									_VARCHAR,	4,			_PARAM_IN);
	$DB->bind_param('P_HAGGI', 			$HAGGI,								_VARCHAR,	6,			_PARAM_IN);
	$DB->bind_param('P_TEAM_CD', 		$TEAM_CD,							_VARCHAR,	10,		_PARAM_IN);
	$DB->bind_param('P_USER_NO', 		$USER_NO,							_VARCHAR,	20,		_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = $DB->get_fetch_assoc();

		If($Result) {
			$stat = True;

			$response->process = true;
			$response->NAME = $Result[0]['NAME'];
			$response->MAJOR = $Result[0]['SOSOG_NM'];
		}
	} Else {
		$response->process = false;
		$response->message = $DB->get_error();
	}

	If(!$stat) $response->process = false;

	Echo urldecode(json_encode($response));
	Exit;
?>