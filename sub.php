<?php
	If ((Function_Exists("session_status") && session_status() != PHP_SESSION_ACTIVE) || !session_id()) session_start();
	Define(webSite, '한기대*%*공학');
	Define(webPage, 'sub');

	Require_Once DIRNAME(__FILE__).'/inc/code.php';
?>
<!DOCTYPE html>
<html lang="ko">
	<head>
<?php
	// HTML Header
	Require_Once DIRNAME(__FILE__).'/inc/html.php';
?>
	</head>
	<body>
		<div id="commonLayout">
<?php
	// Header
	Require_Once DIRNAME(__FILE__).'/inc/head.php';
?>
			<div id="subVisual">
				<p><b>남서울대학교 <span class="lightseagreen">창업교육센터</span></b></p>
			</div>
			<div id="subLayout" class="ofh flefts">
<?php
	// Sub Navigation
	Require_Once DIRNAME(__FILE__).'/inc/navi_sub.php';
?>
				<div id="subContent">
					<div>
						<h3><?php Echo $_CODE['navi_end']; ?></h3>
						<p class="bfont">창업교육센터는 남서울대학교의 숭고한 정신을 깊이 새기어 건강한 사회, 더 행복한 세상을 만들어 가겠습니다.</p>
						<p class="navi"><?php Echo $_CODE['navi']; ?></p>
					</div>
<?php
	If(File_Exists($_CODE['uri'])) Include_Once $_CODE['uri'];
	Else Exit;
?>
				</div>
			</div>
<?php
	// Footer
	Require_Once DIRNAME(__FILE__).'/inc/foot.php';
?>
		</div>
	</body>
</html>