<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');

	If($MODE == 'WR') {
		
		/********************************************************************************************************
		* POST 처리
		********************************************************************************************************/
		$Valid = New Validation();
		$Form = New form();

		$_POST = $Form->clean($Valid->validate('', $_POST, Array('filter' => 'trim')));

		Try {
			$YEAR							= $Valid->validate('년도',					$_POST['YEAR'],									Array('validate' => 'required|max_len,4'));
			$HAGGI						= $Valid->validate('학기',					$_POST['HAGGI'],								Array('validate' => 'required|max_len,6'));
			$TEAM_CD					= $Valid->validate('팀관리번호',		$_POST['TEAM_CD'],							Array('validate' => 'required|max_len,10'));

			$REG_USER_NO			= $Valid->validate('학번',					$_POST['REG_USER_NO'],					Array('filter' => 'removecomma', 'validate' => 'max_len,20'));
			$REG_GRADE				= $Valid->validate('학년',					$_POST['REG_GRADE'],						Array('filter' => 'removecomma', 'validate' => 'max_len,3'));
			$REG_CHECK_YN			= $Valid->validate('학번 체크여부',	$_POST['REG_CHECK_YN'],				Array('filter' => 'ifelse,Y:N'));
		} Catch (Exception $e) {
			location(2, HtmlSpecialChars($e->getMessage()), '/club/member/?mode=W');
		}

		$stat = False;

		$REG_USER_NO_LIST = "";
		$REG_GRADE_LIST = "";

		// 데이터 생성
		For($i = 0; $i < Count($REG_USER_NO); $i++) {
			If($REG_USER_NO[$i] != '' && $REG_CHECK_YN[$i] == 'Y') {
				$stat = True;

				$REG_USER_NO_LIST .= $REG_USER_NO[$i] . ',';
				$REG_GRADE_LIST .= $REG_GRADE[$i] . ',';

			}
		}

		If(!$stat) {
			location(2, '학번은 필수 입력입니다.', '/club/member/?mode=W');
		}

		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_MEMBER_SAVE');
		$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
		
		$DB->bind_param('P_USER_NO_LIST',				$REG_USER_NO_LIST,	_VARCHAR,	8000,	_PARAM_IN);
		$DB->bind_param('P_REG_GRADE_LIST',			$REG_GRADE_LIST,		_VARCHAR,	8000,	_PARAM_IN);
		
		$DB->bind_param('P_CREATE_ID',						$USER_NO,					_VARCHAR,	50,		_PARAM_IN);
		$DB->bind_param('V_RETURN_NO',					'',									_VARCHAR,	20,		_PARAM_OUT);

		If($DB->ExecuteProc()) {
			$TEAM_CD = $DB->output['V_RETURN_NO'];
			
			location(2, '등록되었습니다.', '/club/member/');

		} Else {

			location(2, $DB->get_error(), '/club/member/?mode=W');

		}
	}
?>