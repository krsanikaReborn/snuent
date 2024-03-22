<?php
	// 게시판 조회
	$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
	If (Empty($IDX)) { location(2, '존재하지 않는 게시글입니다.', '/'); }

	$DB->setProcName('SP_WEB_BOARD_DETAIL_SELECT');
	$DB->bind_param('IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$Result = $DB->get_fetch_assoc();
		
		If ($Result[0]['IDX'] == "") location(2, '존재하지 않는 게시글입니다.', '/');

		// BOARD
		$MODE = $MODE . 'R';  // MR 또는 RR로 변경
		$TITLE = htmlspecialchars($Result[0]['TITLE']);
		$CATEGORY = htmlspecialchars($Result[0]['CATEGORY']);
		$CONTENTS = $Result[0]['CONTENTS'];
		$WRITER = htmlspecialchars($Result[0]['WRITER']);
		$REGDATE = $Result[0]['CREATE_DT'];
		$LVL = IntVal($Result[0]['LVL']);
		$READ = IntVal($Result[0]['READ']);
		$NOTI = IntVal($Result[0]['NOTI']);
		$CREATE_ID = $Result[0]['CREATE_ID'];
	} Else location(2, '조회 오류입니다. 관리자에게 문의하십시오', '/');

	// 권한 체크
	If(!isBoardAuth($_SESSION['HOME']['USER_AUTH'], $VIEW_LEVEL_ARRAY)) {
		If(Empty($_POST['passwd'])) {
			Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=V'\">";
			Exit;
		} Else {
			If ($_POST['passwd'] != $Result[0]['PASSWD']){
				Echo "<script>alert('비밀번호가 일치하지 않습니다.');</script>";
				Echo "<meta http-equiv=\"refresh\" content=\"0; url='?id={$BID}&amp;mode=PW&amp;idx={$IDX}&amp;rmode=V'\">";
			}
		}
	}

	$DB->setProcName('SP_BOARD_READ_UPDATE');
	$DB->bind_param('IDX', 			$IDX,				_INT, 		11,			_PARAM_IN);
	$DB->ExecuteProc();
?>
		<div>
			<div id="CNPDBoardView">
				<h4><?php Echo $TITLE; ?></h4>
<?php
	If($MASTER[0]['USE_CATEGORY'] == 'Y') {
?>
				<div class="info">
					<dl class="user">
						<dt>분류 : </dt>
						<dd><b><?php Echo $CATEGORY; ?></b></dd>
					</dl>
				</div>
<?php
	}
?>
				<div class="info">
					<dl class="user">
						<dt>작성 : </dt>
						<dd><b><?php Echo $WRITER; ?></b> <span>(<?php Echo $REGDATE; ?>)</span></dd>
					</dl>
					<dl class="read">
						<dt>읽음 : </dt>
						<dd><b><span style="color:#000;"><?php Echo $READ; ?></span></b></dd>
					</dl>
				</div>
<?php
	$fileArray = Array();
	$DB->setProcName('SP_BOARD_FILE_LIST_SELECT');
	$DB->bind_param('IDX', 						$IDX,								_INT, 		20,			_PARAM_IN);
	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();

		If($result) {
?>
				<div class="info">
					<dl class="user">
						<dt>첨부파일</dt>
					</dl>
					<dl class="read">
<?php
			$i = 0;
			Foreach($result as $fileList) {
				$fileArray[$i] = $fileList;
?>
						<dd style="display:block">
							<a href="download.php?idx=<?php Echo $IDX; ?>&amp;seq=<?php Echo $fileList['SEQ']; ?>"><?php Echo $fileList['FILE_REALNAME']; ?></a>
						</dd>
<?php
				$i++;
			}
?>
					<dl>
				</div>
<?php
		}
	}
?>
			</div>

			<div id="CNPDBoardContent">
<?php
	If ($fileArray) {
		Foreach($fileArray AS $l) {
			$array = Explode('.', $l['FILE_REALNAME']);
			$fe = Array_Pop($array);
			If(In_Array(strToLower($fe), Array('jpeg', 'jpg', 'bmp', 'png', 'gif'))) {
?>
				<div class="center" style="margin-bottom:20px;">
					<img src="getImage2.php?idx=<?php Echo $IDX; ?>&amp;seq=<?php Echo $l['SEQ']; ?>" style="width:100%" />
				</div>
<?php
			}
		}
	}
?>
				<div id="CDIBoardContentData">
					<?php Echo nl2br($CONTENTS); ?>
				</div>
			</div>
			<div id="boardBtn">
				<div class="fright">
<?php
	// 답글
	If ($NOTI != 1 && In_Array($_SESSION['HOME']['USER_AUTH'], $REPLY_LEVEL_ARRAY)) Echo "<a href=\"?id={$BID}&mode=R&idx={$IDX}&{$queryString}\" alt=\"\">답글</a> ";
	// 수정
	//If ($HIPASS == True || ($_SESSION['HOME_USER_NO'] != "" && $_SESSION['HOME_USER_NO'] == $CREATE_ID)) 
	Echo "<a href=\"?id={$BID}&mode=M&idx={$IDX}&{$queryString}\" alt=\"\">수정</a> ";
	// 삭제
	//If($HIPASS == True || ($_SESSION['HOME_USER_NO'] != "" && $_SESSION['HOME_USER_NO'] == $CREATE_ID) || In_Array($_SESSION['HOME_USER_NO'], $DELETE_LEVEL_ARRAY))
	Echo "<a href=\"?id={$BID}&mode=DR&idx={$IDX}\" alt=\"\">삭제</a> ";
?>
					<a href="?id=<?php Echo $BID; ?>&amp;srhctgr=<?php Echo $SEARCH_TYPE; ?>&amp;keyword=<?php Echo $SEARCH_KEYWORD; ?>&amp;p=<?php Echo $page; ?>">목록</a> 
				</div>
			</div>
        </div>