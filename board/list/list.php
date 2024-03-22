<?php
	If($MASTER[0]['USE_CATEGORY'] == 'Y') {
?>
								<p class="search" style="text-align:left;">
									<a href="?id=<?php Echo htmlspecialchars($BID); ?>&srhctgr=<?php Echo htmlspecialchars($SEARCH_TYPE); ?>&keyword=<?php Echo htmlspecialchars($SEARCH_KEYWORD); ?>" style="margin:0 10px;<?php If($SEARCH_CATEGORY == "") Echo ' font-weight:bold; color:#127458'; ?>">전체</a>
<?php
		Foreach($categoryArr AS $list){
			$cssStyle = "";
			Echo '<span style="color:#ccc">|</span>';
			If($list == $SEARCH_CATEGORY) $cssStyle = ' font-weight:bold; color:#127458';
			Echo '<a href="?id='.htmlspecialchars($BID).'&category='.UrlEncode(htmlspecialchars($list)).'&srhctgr='.htmlspecialchars($SEARCH_TYPE).'&keyword='.htmlspecialchars($SEARCH_KEYWORD).'" style="margin:0 10px;'.$cssStyle.'">'.htmlspecialchars($list).'</a>';
		}
?>
								</p>
<?php
	}

	$DB->setProcName('SP_WEB_BOARD_LIST_SELECT');
	$DB->bind_param('P_BOARD_ID', 						$BID,											_VARCHAR, 		20,			_PARAM_IN);
	$DB->bind_param('P_SEARCH_CATEGORY',			$SEARCH_CATEGORY,				_VARCHAR, 		200,			_PARAM_IN);
	$DB->bind_param('P_SEARCH_TYPE', 					$SEARCH_TYPE,							_VARCHAR, 		20,			_PARAM_IN);
	$DB->bind_param('P_SEARCH_KEYWORD', 			$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
	$DB->bind_param('P_START', 							0,												_INT, 				11,			_PARAM_IN);
	$DB->bind_param('P_END',	 							0,												_INT, 				11,			_PARAM_IN);

	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
	}
	$totalCount = IntVal($result[0]['COUNT']);

	$totalPage = Ceil($totalCount / $MASTER[0]['PAGE_LIST']);
	$page = ($page > $totalPage) ? $totalPage : $page;

	$start = (($page - 1) * $MASTER[0]['PAGE_LIST']) + 1;
	$end = ($page * $MASTER[0]['PAGE_LIST']);
	$curnum = $totalCount - ($start - 1);
?>
						<form method="get" name="srhfrm" action="?">
							<div class="search_box center">
								<input type="hidden" id="id" name="id" value="<?php Echo htmlspecialchars($BID); ?>" />
								<select name="srhctgr">
									<option value="title"<?php If($SEARCH_TYPE == 'title') Echo ' selected = "selected"'; ?>>제목</option>
									<option value="contents"<?php If($SEARCH_TYPE == 'contents') Echo ' selected = "selected"'; ?>>내용</option>
									<option value="writer"<?php If($SEARCH_TYPE == 'writer') Echo ' selected = "selected"'; ?>>작성자</option>
									<option value="all"<?php If($SEARCH_TYPE == 'all') Echo ' selected = "selected"'; ?>>전체</option>
								</select>
								<input type="text" id="keyword" name="keyword" value="<?php Echo htmlspecialchars($SEARCH_KEYWORD); ?>" maxlength="30" onkeypress="if(event.keyCode==13){ document.form.submit(); }" class="textbox" style="width:400px" />
								<input type="submit" value="검색" class="submit" />
							</div>
						</form>
						<div class="margin_t_20 margin_b_20 table_listA">
							<table>
								<thead>
									<tr>
										<th id="th_num" scope="col" width="60">번호</th>
<?php
	If($MASTER[0]['USE_CATEGORY'] == 'Y') {
?>
										<th id="th_subject" scope="col" width="120">분류</th>
<?php
	}
?>
										<th id="th_subject" scope="col">제목</th>
										<th id="th_writer" scope="col" width="90">작성자</th>
										<th id="th_date" scope="col" width="90">작성일자</th>
										<th id="th_read" scope="col" width="60">읽음</th>
									</tr>
								</thead>
								<tbody>
<?php
	If ( $totalCount > 0 ) {
		$DB->setProcName('SP_WEB_BOARD_LIST_SELECT');
		$DB->bind_param('BOARD_ID', 						$BID,											_VARCHAR, 		20,			_PARAM_IN);
		$DB->bind_param('SEARCH_CATEGORY',			$SEARCH_CATEGORY,				_VARCHAR, 		200,			_PARAM_IN);
		$DB->bind_param('SEARCH_TYPE', 					$SEARCH_TYPE,							_VARCHAR, 		20,			_PARAM_IN);
		$DB->bind_param('SEARCH_KEYWORD', 			$SEARCH_KEYWORD,					_VARCHAR, 		200,			_PARAM_IN);
		$DB->bind_param('START', 							$start,										_INT, 				11,			_PARAM_IN);
		$DB->bind_param('END',	 							$end,										_INT, 				11,			_PARAM_IN);

		If ($DB->ExecuteProc()) {
			$result = $DB->get_fetch_assoc();
		} Else location(2, '조회 오류입니다. 관리자에게 문의하십시오.', '/');

		Foreach ($result as $list) {
?>
									<tr>
										<td headers="th_num" class="center"><?php Echo (IntVal($list['NOTI']) == 1) ? '<b>공지</b>' : $curnum; ?></td>
<?php
			If($MASTER[0]['USE_CATEGORY'] == 'Y') {
?>
										<td headers="th_num" class="center"><?php Echo $list['CATEGORY']; ?></td>
<?php
			}
?>
										<td headers="th_subject" class="left">
<?php
			$LEVEL = '';
			If (IntVal($list['LVL']) > 0) {
				For($i = 0; $i <= IntVal($list['LVL']); $i++){
					$LEVEL .= "&nbsp;&nbsp;";
				}

				$LEVEL .= '<img src="/res/img/board/iconRe.gif" style="margin-top:2px;" />';
			}
?>
											<span><?php Echo $LEVEL; ?></span>
											<a href="?id=<?php Echo $BID; ?>&amp;mode=V&amp;idx=<?php Echo htmlspecialchars($list['IDX']); ?>&amp;srhctgr=<?php Echo htmlspecialchars($SEARCH_TYPE); ?>&amp;keyword=<?php Echo htmlspecialchars($SEARCH_KEYWORD); ?>&amp;p=<?php Echo htmlspecialchars($page); ?>"  class="ls_lnk">
<?php
			If (IntVal($list['NOTI']) == 1) {
?>
											<b><?php Echo (mb_strlen(htmlspecialchars($list['TITLE'])) > 66) ? mb_substr(htmlspecialchars($list['TITLE']), 0, 66, 'UTF-8') . '...' : htmlspecialchars($list['TITLE']); ?></b>
<?php
			} Else {
				If ($VIEW_LEVEL == 1 && $list['LEVEL'] == 0)  Echo '<img src="/res/img/board/secret.gif" style="margin-right:3px;" />';
?>
												<?php Echo (mb_strlen(htmlspecialchars($list['TITLE'])) > 40) ? mb_substr(htmlspecialchars($list['TITLE']), 0, 36, 'UTF-8') . '...' : htmlspecialchars($list['TITLE']); ?>
<?php
			}
?>
											</a>
										</td>
										<td headers="th_writer" class="center"><?php Echo htmlspecialchars($list['WRITER']); ?></td>
										<td headers="th_date" class="center"><?php Echo $list['CREATE_DT']; ?></td>
										<td headers="th_read" class="center"><?php Echo readNumColor($list['READ']); ?></td>
									</tr>
<?php
			$curnum--;
		}
	} Else {
?>
									<tr>
										<td headers="th_num" scope="col" class="center" colspan="<?php Echo ($MASTER[0]['USE_CATEGORY'] == 'Y') ? '6' : '5'; ?>"><b>등록된 글이 없습니다.</b></td>
									</tr>
<?php
	}
?>
								</tbody>
							</table>
						</div>
						<div class="paginate_complex">
<?php
	setPaging('', $param, $page, $totalPage, $MASTER[0]['PAGE_BLOCK']);
?>
						</div>

<?php
	If(isBoardAuth($_SESSION['HOME']['USER_AUTH'], $WRITE_LEVEL_ARRAY)){
?>
						<div id="boardBtn">
							<div class="fright">
								<a href="?id=<?php Echo $BID; ?>&mode=W&srhctgr=<?php Echo $SEARCH_TYPE; ?>&keyword=<?php Echo $SEARCH_KEYWORD; ?>">등록</a>
							</div>
						</div>
<?php
	}
?>
