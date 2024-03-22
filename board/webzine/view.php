<?php
	// 게시판 조회
	$IDX = (!Empty($_GET['idx'])) ? IntVal($_GET['idx']) : Null;
	If (Empty($IDX)) { location(2, '존재하지 않는 게시글입니다.', '/'); }

	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;
	$srhctgr = $_GET['srhctgr'];
	$keyword = $_GET['keyword'];

	$IDX = mysql_real_escape_string($IDX);

	mysql_query("UPDATE `T_BOARD` SET `READ` = `READ` + 1 WHERE `IDX` = '{$IDX}' ");

	$sql  = "SELECT A.`TITLE`, A.`CONTENTS`, A.`WRITER`, A.`REGDATE`, A.`NOTI`, A.`READ`, B.`BOARD_ID`, B.`BOARD_NAME`, B.`NAV1` FROM `T_BOARD` A ";
	$sql .= "INNER JOIN `T_BOARD_CONFIG` B ON A.`BOARD_ID` = B.`BOARD_ID` ";
	$sql .= "WHERE A.`IDX` = '{$IDX}' ";
	$r = mysql_query($sql);
	$c = mysql_fetch_assoc($r);

	If ( !$c ) { location(2, '존재하지 않는 게시글입니다.', '/'); }
	$BID = $c['BOARD_ID'];
	$BOARD_NAME = $c['BOARD_NAME'];
	$NAV_1 = $c['NAV1'];
	$TITLE = htmlspecialchars($c['TITLE']);
	$WRITER = htmlspecialchars($c['WRITER']);
	$REGDATE = $c['REGDATE'];
	$READ = IntVal($c['READ']);
	$CONTENTS = $c['CONTENTS'];
	$NOTI = IntVal($c['NOTI']);
	mysql_free_result($r);
?>
      <div>
			<div id="CNPDBoardView">
				<h4><?php Echo $TITLE; ?></h4>
				<div class="info">
					<dl class="user">
						<dt>저자 : </dt>
						<dd><b><?php Echo $WRITER; ?></b> <span>(<?php Echo Date('Y-m-d', StrToTime($REGDATE)); ?>)</span></dd>
					</dl>
				</div>
<?php
	$sql = "SELECT `SEQ`, `FILE_NAME` FROM `T_BOARD_FILE` WHERE `IDX` = '{$IDX}' ORDER BY IDX ASC ";
	$r = mysql_query($sql);
	$fileCnt = mysql_num_rows($r);
	If ($fileCnt > 0) {
?>
				<div class="info">
					<!--dl class="user">
						<dt>첨부파일</dt>
					</dl-->
					<dl class="read">
<?php
		While($l = mysql_fetch_assoc($r)){
?>
					<dd style="display:block">
						<a href="download.php?idx=<?php Echo $IDX; ?>&amp;seq=<?php Echo $l['SEQ']; ?>"><?php Echo $l['FILE_NAME']; ?></a>
					</dd>
<?php
		}
	mysql_free_result($r);
?>
				</div>
<?php
	}
?>
			</div>
			<div id="CNPDBoardContent">
				<div id="CDIBoardContentData">
					<?php Echo nl2br($CONTENTS); ?>
				</div>
			</div>
			<div id="CNPDBoardBtn">
				<div class="fright">
					<a href="?id=<?php Echo $BID; ?>&amp;srhctgr=<?php Echo $srhctgr; ?>&amp;keyword=<?php Echo $keyword; ?>&amp;p=<?php Echo $page; ?>">목록</a> 
				</div>
			</div>
        </div>