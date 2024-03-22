<?php
	$page = (IsSet($_GET['p']) && !Empty($_GET['p'])) ? IntVal($_GET['p']) : 1;

	$srhctgr = $_GET['srhctgr'];
	$keyword = $_GET['keyword'];
	$lv = null;
	$filePath = "/files/" . $BID . "/";

	if(!empty($keyword)) {
		switch($srhctgr) {
			case 'subject':
				$lv = 'and `TITLE` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
			case 'contents':
				$lv = 'and `CONTENTS` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
			case 'author':
				$lv = 'and (`WRITER` like \'%'.mysql_real_escape_string($keyword).'%\'';
			break;
		}
	}
?>
      <div style="width:100%;">
                        <form method="get" name="srhfrm" action="?">
							<p class="search">
								<input type="hidden" id="id" name="id" value="<?php Echo htmlspecialchars($BID); ?>" />
								<input type="image" src="/res/img/board/btn_search.jpg" alt="검색" onclick="this.form.submit(); return false;" />
								<input type="text" id="keyword" name="keyword" value="<?php Echo htmlspecialchars($keyword); ?>" maxlength="30" onkeypress="if(event.keyCode==13){ document.form.submit(); }" />
								<select name="srhctgr">
									<option value="subject"<?php If($srhctgr == 'subject') Echo ' selected = "selected"'; ?>>제목</option>
									<option value="contents"<?php If($srhctgr == 'contents') Echo ' selected = "selected"'; ?>>내용</option>
									<!--option value="author"<?php If($srhctgr == 'author') Echo ' selected = "selected"'; ?>>작성자</option-->
								</select>
							</p>
						</form>
						<div style="clear:both"></div>
						
						<div class="photo_list">
<?php
	$r = mysql_query("SELECT COUNT(`IDX`) FROM `T_BOARD` WHERE BOARD_ID = '{$BID}' {$lv} ");
	$c = mysql_fetch_row($r);
	$totalCount = IntVal($c[0]);

	mysql_free_result($r);

	$total_page = ceil($totalCount / $pages);
	$start = ($page - 1) * $pages;
	$num = $totalCount - $start;

	$r = mysql_query("SELECT `IDX`, `TITLE`, `WRITER`, `READ`, `FILE_NAME` FROM `T_BOARD` WHERE BOARD_ID = '{$BID}' {$lv} ORDER BY `IDX` DESC LIMIT {$start}, {$pages} ");

	If ( $totalCount > 0 ) {
		$i = 1;
		$lineOut = 3;

		While ( $list = mysql_fetch_assoc($r) ) {
			$checkClass = "";
			If($i % $lineOut == 0) {
				If($i == $totalCount || $i % $lineOut == 0) $checkClass = " chk";
			}
			If($i == 1 || $i % ($lineOut + 1) == 0) {
?>
								<div class="set_wrap ofh flefts">
<?php
			}
?>
									<a href="?id=<?php Echo $BID; ?>&mode=V&idx=<?php Echo $list['IDX']; ?>&p=<?php Echo $page; ?>" class="photo_set<?php Echo $checkClass; ?>">
										<p style="width:170px; height:110px;" class="ofh center">
<?php
			If (!Empty($list['FILE_NAME'])) {
?>
											<img src="getImage.php?idx=<?php Echo $list['IDX']; ?>&temp=m_thumb_" width="170" height="110" alt="" />
<?php
			} Else Echo '<img src="/cont/2/noimg.png" alt="" width="170" height="110" />';
?>
										</p>
										<p class="subject"><?php Echo (mb_strlen($list['TITLE']) > 26) ? mb_substr($list['TITLE'], 0, 22, 'UTF-8') . '...' : $list['TITLE']; ?></p>
									</a>
<?php
			If($i == $totalCount || $i % $lineOut == 0) {
?>
								</div>
<?php
			}

			$i++;
		}

	} Else {
?>
								<div><p style="text-align:center; width:100%; padding-top:40px; height:60px; font-weight:bold;">등록된 내역이 없습니다.</p></div>
<?php
	}

	mysql_free_result($r);
?>
							</div>
						</div>
						
						<br /><br />
						
						<div id="CNPDBoardPage">
							<p>
<?php
								$param = "id={$BID}&amp;srhctgr={$srhctgr}&amp;keyword={$keyword}";
								setPaging($param, $page, $total_page, $block);
?>
							</p>
						</div>