<?php //error_reporting(E_ALL); ini_set("display_errors", 1); ?>
<?php
	Header("Content-Type: text/html; charset=UTF-8");

	Define("__ROOT__", $_SERVER['DOCUMENT_ROOT']);
	Define(webSite, '남서울*%*창업');
	Define(webPage, 'sub');
	Define(webBoard, 'board');

	Require_Once __ROOT__ . '/inc/DBConnection.php';
	mb_internal_encoding('UTF-8');

	$MODE = (!Empty($_GET['mode'])) ? $_GET['mode'] : Null;
	If (Empty($MODE)) $MODE = (!Empty($_POST['mode'])) ? $_POST['mode'] : Null;

	// 게시판 조회
	$BID = (!Empty($_GET['id'])) ? $_GET['id'] : Null;
	If (Empty($BID)) $BID = (!Empty($_POST['id'])) ? $_POST['id'] : Null;

	If (Empty($BID)) { location(2, '존재하지 않는 게시판입니다.', '/'); }

	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;

	// 검색
	$SEARCH_TYPE			= IsSet($_GET['srhctgr']) 			? 	Trim($_GET['srhctgr']) 			: 	'';
	$SEARCH_KEYWORD	= IsSet($_GET['keyword']) 			? 	Trim($_GET['keyword']) 			: 	'';
	$SEARCH_CATEGORY	= IsSet($_GET['category']) 			? 	Trim($_GET['category']) 			: 	'';

	// param 생성
	$arrParam = Array (
		'id' => $BID,
		'p' => $page,
		'srhctgr' => $SEARCH_TYPE,
		'keyword' => $SEARCH_KEYWORD,
		'category' => $SEARCH_CATEGORY
	);
	$param = getQueryString($arrParam);
	
	$DB->setProcName('SP_BOARD_MASTER_DETAIL_SELECT');
	$DB->bind_param('@P_BOARD_ID',		$BID,		_VARCHAR,	20,	_PARAM_IN);
	
	If ($DB->ExecuteProc()) {
		$MASTER = $DB->get_fetch_assoc();
		If ($MASTER[0]['BOARD_ID'] == "") location(2, '존재하지 않는 게시판입니다.', '/');
	}
	
	If($MASTER[0]['CODE'] == "") {
		location(2, '게시판의 메뉴번호가 설정되지 않았습니다. 관리자에게 문의하십시오.', '/');
	}
	$_GET['code'] = $MASTER[0]['CODE'];
	
	// 카테고리
	If($MASTER[0]['USE_CATEGORY'] == 'Y') {
		$categoryStr = $MASTER[0]['CATEGORY'];
		
		If($categoryStr == "") {
			$MASTER[0]['USE_CATEGORY'] = 'N';
		} Else {
			$categoryArr = trimSplit(',', $MASTER[0]['CATEGORY']);
		}
	}
	
	// 카테고리 검색 시 유효성 체크
	If(!In_Array($SEARCH_CATEGORY, Array($categoryArr))) $SEARCH_CATEGORY = "";

	$HIPASS = False;
	// 권한 세션 없을 시 비회원으로 적용, 최고관리자/관리자 권한 체크 통과
	If(!IsSet($_SESSION['HOME']['USER_AUTH']) || $_SESSION['HOME']['USER_AUTH'] == "") $_SESSION['HOME']['USER_AUTH'] = '9999';			// 비회원

	// 게시판 권한 설정 (배열로 변경)
	$WRITE_LEVEL_ARRAY 	= trimSplit(',', $MASTER[0]['WRITE_LEVEL']);
	$VIEW_LEVEL_ARRAY 		= trimSplit(',', $MASTER[0]['VIEW_LEVEL']);
	$REPLY_LEVEL_ARRAY 	= trimSplit(',', $MASTER[0]['REPLY_LEVEL']);
	$DELETE_LEVEL_ARRAY 	= trimSplit(',', $MASTER[0]['DELETE_LEVEL']);
	$EDITOR_LEVEL_ARRAY 	= trimSplit(',', $MASTER[0]['EDITOR_LEVEL']);

	Require_Once __ROOT__ . '/inc/code.php';
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
<?php
	// HTML Header
	Require_Once __ROOT__.'/inc/html.php';
?>
	</head>
	<body>
		<div id="commonLayout">
<?php
	// Header
	Require_Once __ROOT__.'/inc/head.php';
?>
			<div id="subVisual">
				<p><b>남서울대학교 <span class="lightseagreen">창업교육센터</span></b></p>
			</div>
			<div id="subLayout" class="ofh flefts">
<?php
	// Sub Navigation
	Require_Once __ROOT__.'/inc/navi_sub.php';
?>
				<div id="subContent">
					<div>
						<h3><?php Echo $_CODE['navi_end']; ?></h3>
						<p class="bfont">창업교육센터는 남서울대학교의 숭고한 정신을 깊이 새기어 건강한 사회, 더 행복한 세상을 만들어 가겠습니다.</p>
						<p class="navi"><?php Echo $_CODE['navi']; ?></p>
					</div>
					<div class="middleline">
<?php
	$skinPath = DIRNAME(__FILE__) . '/' . $MASTER[0]['BOARD_SKIN'];
	If( !is_dir($skinPath) ) { location(2, '스킨설정오류입니다. 관리자에게 문의하십시오.', '/'); }

	Switch ( $MODE ) {
		Case 'W' 		:
								$includePath 	= 	$skinPath . '/write.php';
								Break;
		Case 'V' 		:
								$includePath 	= 	$skinPath . '/view.php';
								Break;
		Case 'M' 		:
								$includePath 	= 	$skinPath . '/write.php';
								Break;
		Case 'R' 		:
								$includePath 	= 	$skinPath . '/write.php';
								Break;
		Case 'WR'	:
								$includePath 	= 	$skinPath . '/proc.php';
								Break;
		Case 'RR'		:
								$includePath 	= 	$skinPath . '/proc.php';
								Break;
		Case 'MR'	:
								$includePath 	= 	$skinPath . '/proc.php';
								Break;
		Case 'DR'		:
								$includePath 	= 	$skinPath . '/proc.php';
								Break;
		Case 'PW'		:
								$includePath 	= 	$skinPath . '/passwd.php';
								Break;
		Default 		:
								$includePath 	= 	$skinPath . '/list.php';
	}

	If( File_Exists($includePath) ) Include_Once $includePath;
	Else location(2, '스킨설정오류입니다. 관리자에게 문의하십시오.', '/');
?>
					</div>
				</div>
			</div>
<?php
	// Footer
	Require_Once __ROOT__ . '/inc/foot.php';
?>
		</div>
	</body>
</html>