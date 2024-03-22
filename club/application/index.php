<?php
	Header("Content-Type: text/html; charset=UTF-8");

	Define("__ROOT__", $_SERVER['DOCUMENT_ROOT']);
	Define(webSite, '남서울*%*창업');
	Define(webPage, 'sub');
	Define(webBoard, 'board');

	Require_Once __ROOT__ . '/inc/DBConnection.php';
	mb_internal_encoding('UTF-8');

	$_GET['code'] = '040300';

	If(!IsSet($_SESSION['HOME']['USER_NO']) || $_SESSION['HOME']['USER_NO'] == '') location(2, '로그인 후 이용하실 수 있습니다.', '/member/login.php');

	Require_Once __ROOT__ . '/inc/code.php';
	
	$USER_NO = $_SESSION['HOME']['USER_NO'];
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
<?php
	// HTML Header
	Require_Once __ROOT__.'/inc/html.php';
?>
		<link rel="stylesheet" type="text/css" media="all" href="/res/css/modules/jquery-ui/jquery-ui.min.css" />
		<link rel="stylesheet" type="text/css" media="all" href="/res/css/modules/jquery-ui/jquery-ui.theme.min.css" />
		<link rel="stylesheet" type="text/css" media="all" href="/res/css/parsley.css" />
		<script type="text/javascript" src="/res/js/modules/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/res/js/validator/parsley.remote.js"></script>
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

					<div class="middleline justify ofh">
<?php
	$MODE = ($_GET['mode'] != '') ? $_GET['mode'] : Null;
	If ($MODE == '') $MODE = ($_POST['mode'] != '') ? $_POST['mode'] : Null;

	If($MODE != 'WR') {
		$YEAR = ($_GET['year'] != '') ? $_GET['year'] : Null;
		$HAGGI = ($_GET['haggi'] != '') ? $_GET['haggi'] : Null;
		$TEAM_CD = ($_GET['teamCd'] != '') ? $_GET['teamCd'] : Null;
	}

	Switch ( $MODE ) {		// W: 신청폼, V: 신청내역, M: 수정폼, WR: 등록 로직, MR: 수정 로직, DR: 삭제 로직
		Case 'W' 		:
		Case 'M'		:
								$includePath 	= 	DIRNAME(__FILE__) . '/write.php';
								Break;
		Case 'V' 		:
								$includePath 	= 	DIRNAME(__FILE__) . '/view.php';
								Break;
		Case 'WR'		:
		Case 'MR'		:
								$includePath 	= 	DIRNAME(__FILE__) . '/proc.php';
								Break;
		Default 			:
								$includePath 	= 	DIRNAME(__FILE__) . '/list.php';
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