<?php
	// URL 접근 거부
	If(!Defined('webSite')) Header('Location: /');

	/********************************************************************************************************
	* POST 처리
	********************************************************************************************************/
	$Valid = New Validation();
	$Form = New form();

	$_POST = $Form->clean($Valid->validate('', $_POST, Array('filter' => 'trim')));

	$REG_HAGBEON			= $_SESSION['HOME']['USER_NO'];
	
	$UploadDir 					= '/NonApplication/';

	// 등록
	If($MODE == 'WR') {

		$TEAM_CD					= '';
		$REG_GROUP				= '';
		$REG_DEPART				= '';
		$REG_STATE					= '';
		$REG_TEL_NO				= '';
		$REG_EMAIL					= '';
		$PROFESSOR_NM			= '';

		If($_FILES['FILE']['name'] == "") {
			location(2, '제출문서를 첨부하세요!', '/club/application/');
		}

		Try {
			$YEAR							= $Valid->validate('년도',					$_POST['YEAR'],									Array('filter' => 'onlynum', 'validate' => 'required|max_len,4'));
			$HAGGI						= $Valid->validate('학기',					$_POST['HAGGI'],								Array('validate' => 'required|max_len,6'));
			$REG_NAME					= $Valid->validate('성명',					$_POST['REG_NAME'],						Array('validate' => 'required|max_len,30'));
			$REG_GRADE				= $Valid->validate('학년',					$_POST['REG_GRADE'],						Array('validate' => 'required|max_len,1'));
			$REG_MAJOR				= $Valid->validate('소속학과',				$_POST['REG_MAJOR'],					Array('validate' => 'required|max_len,100'));
			$REG_HP_NO					= $Valid->validate('연락처',					$_POST['REG_HP_NO'],						Array('filter' => 'onlynum', 'validate' => 'required|max_len,14'));

			$CLUB_NM					= $Valid->validate('팀명',					$_POST['CLUB_NM'],						Array('validate' => 'required|max_len,150'));
			$ITEM_NM					= $Valid->validate('창업아이템명',		$_POST['ITEM_NM'],							Array('validate' => 'max_len,300'));
			$CORE_TECH				= $Valid->validate('핵심기술',				$_POST['CORE_TECH'],					Array('validate' => 'max_len,300'));
			$BIZ_NAME					= $Valid->validate('기업명',					$_POST['BIZ_NAME'],						Array('validate' => 'max_len,150'));
			$BIZ_NUM						= $Valid->validate('사업자등록번호',		$_POST['BIZ_NUM'],							Array('validate' => 'max_len,15'));
			$BIZ_START_DT				= $Valid->validate('창업일',					$_POST['BIZ_START_DT'],					Array('validate' => 'max_len,10'));
			$BIZ_TYPE					= $Valid->validate('업종',					$_POST['BIZ_TYPE'],						Array('validate' => 'max_len,150'));
			$BIZ_SALES					= $Valid->validate('매출',					$_POST['BIZ_SALES'],						Array('validate' => 'max_len,12'));
			$BIZ_EMP_CNT				= $Valid->validate('고용인원',				$_POST['BIZ_EMP_CNT'],					Array('validate' => 'max_len,12'));
			$IPR_APPLY_YN				= $Valid->validate('출원여부',				$_POST['IPR_APPLY_YN'],					Array('filter' => 'ifelse,Y:N'));
			$IPR_ING_CNT				= $Valid->validate('출원중',					$_POST['IPR_ING_CNT'],					Array('validate' => 'max_len,5'));
			$IPR_PATENT_CNT		= $Valid->validate('등록완료',				$_POST['IPR_PATENT_CNT'],			Array('validate' => 'max_len,5'));
			$IPR_PATENT_NAME		= $Valid->validate('발명의명칭',			$_POST['IPR_PATENT_NAME'],			Array('validate' => 'max_len,1000'));

			$CONTEST_NM				= $Valid->validate('대회명',					$_POST['CONTEST_NM'],					Array('validate' => 'max_len,1000'));
			$AWARD						= $Valid->validate('수상내역',				$_POST['AWARD'],							Array('validate' => 'max_len,1000'));
			$AWARD_DT					= $Valid->validate('수상일자',				$_POST['AWARD_DT'],						Array('validate' => 'max_len,10'));
			$AGENCY						= $Valid->validate('시행기관',				$_POST['AGENCY'],							Array('validate' => 'max_len,1000'));
		} Catch (Exception $e) {
			location(2, HtmlSpecialChars($e->getMessage()), '/club/application/');
		}

		$CREATE_ID		= 'HOME_'.$_SESSION['HOME']['USER_NO'];

		// 첨부파일
		If ($_FILES['FILE']['name'] != "") {
			Try {
				$fileName = Date('YmdHms') . '_' . generate_state();
				$FILE_NAME = UploadPathFile($_FILES['FILE'], $fileName, $UploadDir);
				$REALFILE_NAME = $_FILES['FILE']['name'];
			} Catch(Exception $e) {
				location(2, $e->getMessage(), '/club/application/');
			}
		}
		
		

		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_SAVE');
		$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_USER_NO',							$USER_NO,					_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_CLUB_NM',						$CLUB_NM,					_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_ITEM_NM',							$ITEM_NM,					_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_CORE_TECH',					$CORE_TECH,				_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_REG_HAGBEON',				$REG_HAGBEON,			_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_REG_NAME',						$REG_NAME,					_VARCHAR,	30,		_PARAM_IN);
		$DB->bind_param('P_REG_GROUP',					$REG_GROUP,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_DEPART',					$REG_DEPART,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_MAJOR',					$REG_MAJOR,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_GRADE',						$REG_GRADE,				_VARCHAR,	3,			_PARAM_IN);
		$DB->bind_param('P_REG_STATE',						$REG_STATE,				_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_REG_TEL_NO',					$REG_TEL_NO,				_VARCHAR,	14,		_PARAM_IN);
		$DB->bind_param('P_REG_HP_NO',						$REG_HP_NO,				_VARCHAR,	14,		_PARAM_IN);
		$DB->bind_param('P_REG_EMAIL',						$REG_EMAIL,				_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_PROFESSOR_NM',				$PROFESSOR_NM,			_VARCHAR,	30,		_PARAM_IN);
		$DB->bind_param('P_BIZ_NAME',						$BIZ_NAME,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_BIZ_NUM',							$BIZ_NUM,					_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_BIZ_START_DT',					$BIZ_START_DT,			_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_BIZ_TYPE',						$BIZ_TYPE,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_BIZ_SALES',						$BIZ_SALES,					_VARCHAR,	12,		_PARAM_IN);
		$DB->bind_param('P_BIZ_EMP_CNT',					$BIZ_EMP_CNT,			_VARCHAR,	12,		_PARAM_IN);
		$DB->bind_param('P_IPR_APPLY_YN',					$IPR_APPLY_YN,			_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_IPR_ING_CNT',					$IPR_ING_CNT,				_INT,			3,			_PARAM_IN);
		$DB->bind_param('P_IPR_PATENT_CNT',			$IPR_PATENT_CNT,		_INT,			3,			_PARAM_IN);
		$DB->bind_param('P_IPR_PATENT_NAME',			$IPR_PATENT_NAME,	_VARCHAR,	2000,		_PARAM_IN);
		$DB->bind_param('P_FILE_NAME',						$FILE_NAME,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_REALFILE_NAME',				$REALFILE_NAME,			_VARCHAR,	255,		_PARAM_IN);
		$DB->bind_param('P_CREATE_ID',						$CREATE_ID,					_VARCHAR,	50,		_PARAM_IN);
		$DB->bind_param('V_RETURN_NO',					'',									_VARCHAR,	20,		_PARAM_OUT);

		If($DB->ExecuteProc()) {
			$TEAM_CD = $DB->output['V_RETURN_NO'];

			For($i = 0; $i < Count($CONTEST_NM); $i++) {
				If($CONTEST_NM[$i] != '') {
					$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_CAREER_SAVE');
					$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
					$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
					$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
					$DB->bind_param('P_CONTEST_NM',					$CONTEST_NM[$i],		_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_AWARD',							$AWARD[$i],					_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_AWARD_DT',						$AWARD_DT[$i],			_VARCHAR,	10,		_PARAM_IN);
					$DB->bind_param('P_AGENCY',							$AGENCY[$i],				_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_CREATE_ID',						$CREATE_ID,					_VARCHAR,	20,		_PARAM_IN);

					$DB->ExecuteProc();
					/*
					If(!$DB->ExecuteProc()){
						$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_DELETE');
						$DB->bind_param('P_YEAR',							$YEAR,							_VARCHAR,	4,			_PARAM_IN);
						$DB->bind_param('P_HAGGI',							$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
						$DB->bind_param('P_TEAM_CD',					$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
						$DB->ExecuteProc();

						location(2, HtmlSpecialChars($e->getMessage()), '/club/application/');
					}
					*/
				}
			}

			location(2, '동아리 등록이 완료되었습니다.', '/club/application/');
		} Else {
			location(2, '오류가 발생하였습니다. 관리자에게 문의하십시오.', '/club/application/');
		}
	}


	// 수정
	If($MODE == 'MR') {
		$REG_GROUP				= '';
		$REG_DEPART				= '';
		$REG_STATE					= '';
		$REG_TEL_NO				= '';
		$REG_EMAIL					= '';
		$PROFESSOR_NM			= '';

		Try {
			$YEAR							= $Valid->validate('년도',					$_POST['YEAR'],									Array('filter' => 'onlynum', 'validate' => 'required|max_len,4'));
			$HAGGI						= $Valid->validate('학기',					$_POST['HAGGI'],								Array('validate' => 'required|max_len,6'));
			$TEAM_CD					= $Valid->validate('팀관리번호',		$_POST['TEAM_CD'],							Array('validate' => 'required|max_len,10'));
			$REG_NAME					= $Valid->validate('성명',					$_POST['REG_NAME'],						Array('validate' => 'required|max_len,30'));
			$REG_GRADE				= $Valid->validate('학년',					$_POST['REG_GRADE'],						Array('validate' => 'required|max_len,1'));
			$REG_MAJOR				= $Valid->validate('소속학과',				$_POST['REG_MAJOR'],					Array('validate' => 'required|max_len,100'));
			$REG_HP_NO					= $Valid->validate('연락처',					$_POST['REG_HP_NO'],						Array('filter' => 'onlynum', 'validate' => 'required|max_len,14'));

			$CLUB_NM					= $Valid->validate('팀명',					$_POST['CLUB_NM'],						Array('validate' => 'required|max_len,150'));
			$ITEM_NM					= $Valid->validate('창업아이템명',		$_POST['ITEM_NM'],							Array('validate' => 'max_len,300'));
			$CORE_TECH				= $Valid->validate('핵심기술',				$_POST['CORE_TECH'],					Array('validate' => 'max_len,300'));
			$BIZ_NAME					= $Valid->validate('기업명',					$_POST['BIZ_NAME'],						Array('validate' => 'max_len,150'));
			$BIZ_NUM						= $Valid->validate('사업자등록번호',		$_POST['BIZ_NUM'],							Array('validate' => 'max_len,15'));
			$BIZ_START_DT				= $Valid->validate('창업일',					$_POST['BIZ_START_DT'],					Array('validate' => 'max_len,10'));
			$BIZ_TYPE					= $Valid->validate('업종',					$_POST['BIZ_TYPE'],						Array('validate' => 'max_len,150'));
			$BIZ_SALES					= $Valid->validate('매출',					$_POST['BIZ_SALES'],						Array('validate' => 'max_len,12'));
			$BIZ_EMP_CNT				= $Valid->validate('고용인원',				$_POST['BIZ_EMP_CNT'],					Array('validate' => 'max_len,12'));
			$IPR_APPLY_YN				= $Valid->validate('출원여부',				$_POST['IPR_APPLY_YN'],					Array('filter' => 'ifelse,Y:N'));
			$IPR_ING_CNT				= $Valid->validate('출원중',					$_POST['IPR_ING_CNT'],					Array('validate' => 'max_len,5'));
			$IPR_PATENT_CNT		= $Valid->validate('등록완료',				$_POST['IPR_PATENT_CNT'],			Array('validate' => 'max_len,5'));
			$IPR_PATENT_NAME		= $Valid->validate('발명의명칭',			$_POST['IPR_PATENT_NAME'],			Array('validate' => 'max_len,1000'));

			$CONTEST_NM				= $Valid->validate('대회명',					$_POST['CONTEST_NM'],					Array('validate' => 'max_len,1000'));
			$AWARD						= $Valid->validate('수상내역',				$_POST['AWARD'],							Array('validate' => 'max_len,1000'));
			$AWARD_DT					= $Valid->validate('수상일자',				$_POST['AWARD_DT'],						Array('validate' => 'max_len,10'));
			$AGENCY						= $Valid->validate('시행기관',				$_POST['AGENCY'],							Array('validate' => 'max_len,1000'));
		} Catch (Exception $e) {
			location(2, HtmlSpecialChars($e->getMessage()), '/club/application/');
		}

		$CREATE_ID		= 'HOME_'.$_SESSION['HOME']['USER_NO'];

		// 첨부파일
		If ($_FILES['FILE']['name'] != "" || $_POST['DEL_FILE'] == 'Y') {
			// 등록된 기존파일을 조회한다.
			$DB->setProcName('SP_NON_MAJOR_CLUB_APPLICATION_FILE_SELECT');
			$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
			$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
			$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);

			If($DB->ExecuteProc()) {
				$Result = $DB->get_fetch_assoc();

				// 기존파일이 있다면, 삭제한다.
				If($Result[0]['FILE_NAME'] != '') {
					DeletePathFile($Result[0]['FILE_NAME']);

					$DB->setProcName('SP_NON_MAJOR_CLUB_APPLICATION_FILE_DELETE');
					$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
					$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
					$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);

					$DB->bind_param('V_RETURN_NO',					'',									_VARCHAR,	20,		_PARAM_OUT);

					$DB->ExecuteProc();
				}
			}
			

			If($_FILES['FILE']['name'] != "") {
				// 신규파일 등록
				$fileName = Date('YmdHms') . '_' . generate_state();
				$FILE_NAME = UploadPathFile($_FILES['FILE'], $fileName, $UploadDir);
				$REALFILE_NAME = $_FILES['FILE']['name'];
			}
		}

		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_SAVE');
		$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
		$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
		$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_USER_NO',							$USER_NO,					_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_CLUB_NM',						$CLUB_NM,					_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_ITEM_NM',							$ITEM_NM,					_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_CORE_TECH',					$CORE_TECH,				_VARCHAR,	600,		_PARAM_IN);
		$DB->bind_param('P_REG_HAGBEON',				$REG_HAGBEON,			_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_REG_NAME',						$REG_NAME,					_VARCHAR,	30,		_PARAM_IN);
		$DB->bind_param('P_REG_GROUP',					$REG_GROUP,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_DEPART',					$REG_DEPART,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_MAJOR',					$REG_MAJOR,				_VARCHAR,	100,		_PARAM_IN);
		$DB->bind_param('P_REG_GRADE',						$REG_GRADE,				_VARCHAR,	3,			_PARAM_IN);
		$DB->bind_param('P_REG_STATE',						$REG_STATE,				_VARCHAR,	20,		_PARAM_IN);
		$DB->bind_param('P_REG_TEL_NO',					$REG_TEL_NO,				_VARCHAR,	14,		_PARAM_IN);
		$DB->bind_param('P_REG_HP_NO',						$REG_HP_NO,				_VARCHAR,	14,		_PARAM_IN);
		$DB->bind_param('P_REG_EMAIL',						$REG_EMAIL,				_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_PROFESSOR_NM',				$PROFESSOR_NM,			_VARCHAR,	30,		_PARAM_IN);
		$DB->bind_param('P_BIZ_NAME',						$BIZ_NAME,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_BIZ_NUM',							$BIZ_NUM,					_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_BIZ_START_DT',					$BIZ_START_DT,			_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_BIZ_TYPE',						$BIZ_TYPE,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_BIZ_SALES',						$BIZ_SALES,					_VARCHAR,	12,		_PARAM_IN);
		$DB->bind_param('P_BIZ_EMP_CNT',					$BIZ_EMP_CNT,			_VARCHAR,	12,		_PARAM_IN);
		$DB->bind_param('P_IPR_APPLY_YN',					$IPR_APPLY_YN,			_VARCHAR,	10,		_PARAM_IN);
		$DB->bind_param('P_IPR_ING_CNT',					$IPR_ING_CNT,				_INT,			3,			_PARAM_IN);
		$DB->bind_param('P_IPR_PATENT_CNT',			$IPR_PATENT_CNT,		_INT,			3,			_PARAM_IN);
		$DB->bind_param('P_IPR_PATENT_NAME',			$IPR_PATENT_NAME,	_VARCHAR,	2000,		_PARAM_IN);
		$DB->bind_param('P_FILE_NAME',						$FILE_NAME,					_VARCHAR,	300,		_PARAM_IN);
		$DB->bind_param('P_REALFILE_NAME',				$REALFILE_NAME,			_VARCHAR,	255,		_PARAM_IN);
		$DB->bind_param('P_CREATE_ID',						$CREATE_ID,					_VARCHAR,	50,		_PARAM_IN);
		$DB->bind_param('V_RETURN_NO',					'',									_VARCHAR,	20,		_PARAM_OUT);

		If($DB->ExecuteProc()) {
			// 기존 데이터 삭제
			$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_CAREER_DELETE');
			$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
			$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
			$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
			$DB->ExecuteProc();
			
			// 신규 데이터 등록
			For($i = 0; $i < Count($CONTEST_NM); $i++) {
				If($CONTEST_NM[$i] != '') {
					$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_CAREER_SAVE');
					$DB->bind_param('P_YEAR',								$YEAR,							_VARCHAR,	4,			_PARAM_IN);
					$DB->bind_param('P_HAGGI',								$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
					$DB->bind_param('P_TEAM_CD',						$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
					$DB->bind_param('P_CONTEST_NM',					$CONTEST_NM[$i],		_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_AWARD',							$AWARD[$i],					_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_AWARD_DT',						$AWARD_DT[$i],			_VARCHAR,	10,		_PARAM_IN);
					$DB->bind_param('P_AGENCY',							$AGENCY[$i],				_VARCHAR,	2000,		_PARAM_IN);
					$DB->bind_param('P_CREATE_ID',						$CREATE_ID,					_VARCHAR,	20,		_PARAM_IN);

					$DB->ExecuteProc();
					/*
					If(!$DB->ExecuteProc()){
						$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_APPLICATION_DELETE');
						$DB->bind_param('P_YEAR',							$YEAR,							_VARCHAR,	4,			_PARAM_IN);
						$DB->bind_param('P_HAGGI',							$HAGGI,						_VARCHAR,	6,			_PARAM_IN);
						$DB->bind_param('P_TEAM_CD',					$TEAM_CD,					_VARCHAR,	10,		_PARAM_IN);
						$DB->ExecuteProc();

						location(2, HtmlSpecialChars($e->getMessage()), '/club/application/');
					}
					*/
				}
			}

			location(2, '수정되었습니다.', '/club/application/?mode=V&year='.$YEAR.'&haggi='.UrlEncode($HAGGI).'&teamCd='.$TEAM_CD);
		} Else {
			location(2, '오류가 발생하였습니다. 관리자에게 문의하십시오.', '/club/application/');
		}

	}

	Exit;
?>