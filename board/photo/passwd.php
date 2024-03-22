<?php
	$BID = $_GET['id'];
	$MODE = $_GET['mode'];
	$RETURN_MODE = $_GET['rmode'];
	$IDX = $_GET['idx'];

	If(Empty($IDX)) { location(2, "삭제되었거나 존재하지 않는 게시글입니다.", "/board/?id={$BID}"); }
	If ($RETURN_MODE == 'V') {
		$MODE_MESSAGE = '열람';
	} Else If ($RETURN_MODE == 'M') {
		$MODE_MESSAGE = '수정';
	} Else If ($RETURN_MODE == 'DR') {
		$MODE_MESSAGE = '삭제';
	}
?>
						<div id="CDIBoardWrite">
							<form method="post" name="pform" id="pform" action="?id=<?php Echo htmlspecialchars($BID); ?>&idx=<?php Echo htmlspecialchars($IDX); ?>&mode=<?php Echo htmlspecialchars($RETURN_MODE); ?>">
							<table class="board3">
								<col width="150" />
								<col />
								<thead>
									<tr>
										<th colspan="2">이 글은 비밀번호확인 후 <?php Echo $MODE_MESSAGE; ?>하실 수 있습니다.</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><b><label for="passwd">비밀번호확인</label></b></td>
										<td class="left"><input type="password" name="passwd" id="passwd" size="25" /></td>
									</tr>
								</tbody>
							</table>

							<div id="boardBtn">
								<div>
									<input type="submit" value="확인" style="cursor:pointer" /> &nbsp;  <a href="?id=<?php Echo htmlspecialchars($BID); ?>&mode=V&idx=<?php Echo htmlspecialchars($IDX); ?>">이전</a>
								</div>
							</div>
						</form>
					</div>