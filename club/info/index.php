<?php
	Header("Content-Type: text/html; charset=UTF-8");

	Define("__ROOT__", $_SERVER['DOCUMENT_ROOT']);
	Define(webSite, '남서울*%*창업');
	Define(webPage, 'sub');
	Define(webBoard, 'board');

	Require_Once __ROOT__ . '/inc/DBConnection.php';
	mb_internal_encoding('UTF-8');

	$_GET['code'] = '040200';
	$pageSize = 10;
	$pageBlock = 10;
	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;

	Require_Once __ROOT__ . '/inc/code.php';
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
	$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_LIST_SELECT');
	$DB->bind_param('P_CLUB_NM', 							$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
	$DB->bind_param('P_START', 								0,												_INT, 				11,			_PARAM_IN);
	$DB->bind_param('P_END',	 								0,												_INT, 				11,			_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}
	$totalCount = IntVal($result[0]['COUNT']);
	$totalPage = Ceil($totalCount / $pageSize);
	$page = ($page > $totalPage) ? $totalPage : $page;

	$start = (($page - 1) * $pageSize) + 1;
	$end = ($page * $pageSize);
	$curnum = $totalCount - ($start - 1);
?>
						<div class="margin_t_20 margin_b_20 table_listA">
							<table>
								<thead>
									<tr>
										<th id="th_num" scope="col" width="60">번호</th>
										<th id="th_year" scope="col" width="60">년도</th>
										<th id="th_haggi" scope="col" width="60">학기</th>
										<th id="th_clubNm" scope="col" >동아리명</th>
										<th id="th_memCnt" scope="col" width="60">인원</th>
										<th id="th_remark" scope="col" width="200">비고</th>
									</tr>
								</thead>
								<tbody>
<?php
	If ( $totalCount > 0 ) {
		$DB->setProcName('SP_WEB_NON_MAJOR_CLUB_LIST_SELECT');
		$DB->bind_param('P_CLUB_NM', 							$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
		$DB->bind_param('P_START', 								$start,											_INT, 				11,			_PARAM_IN);
		$DB->bind_param('P_END',	 									$end,											_INT, 				11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$result = $DB->get_fetch_assoc();
		} Else location(2, '조회 오류입니다. 관리자에게 문의하십시오.', '/');

		Foreach ($result as $list) {
?>
									<tr>
										<td headers="th_num"><?php Echo $curnum; ?></td>
										<td headers="th_year"><?php Echo $list['YEAR']; ?></td>
										<td headers="th_haggi"><?php Echo $list['HAGGI']; ?></td>
										<td headers="th_clubNm"><?php Echo $list['CLUB_NM']; ?></td>
										<td headers="th_memCnt"><?php Echo $list['MEM_CNT']; ?>명</td>
										<td headers="th_remark" class="left">&nbsp;</td>
									</tr>
<?php
			$curnum--;
		}
	} Else {
?>
									<tr>
										<td headers="th_num" scope="col" colspan="9"><b>개설된 창업동아리가 없습니다.</b></td>
									</tr>
<?php
	}
?>
								</tbody>
							</table>
						</div>
						<div class="paginate_complex">
<?php
	setPaging('', $param, $page, $totalPage, $pageBlock);
?>
						</div>
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