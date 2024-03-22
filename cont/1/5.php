<?php
	Require_Once DIRNAME(__FILE__) . '/../../inc/DBConnection.php';

	If(!Defined('webSite')) Header('Location: /');
?>
					<div class="middleline justify ofh">
                        <p class="margin_b_10 hfont bold blue_dot_small padding_l_15 ">창업교육센터 규정</p>
                        <div class="margin_b_50 table_view1">
							<table border="0" cellspacing="0" cellpadding="0">
                                <colgroup>
                                    <col />
                                    <col class="w25" />
                                </colgroup>
                                <thead class="centers">
                                    <tr>
                                        <th>명칭</th>
                                        <th>첨부문서</th>
                                    </tr>
                                </thead>
                                <tbody class="centers">

<?php
	// 센터 규정 조회
	$DB->setProcName('SP_WEB_CENTER_RULE_LIST_SELECT');
	
	If ($DB->ExecuteProc()) {
		$result = $DB->get_fetch_assoc();
		If($result) {
			Foreach($result AS $LIST) {
?>
									<tr>
<?php
				If ($LIST['FILE_NAME'] != '') {
?>
										<td><?php Echo $LIST['RULE_NAME']; ?></td>
										<td><a href="/inc/download.php?type=ruleFile&ruleNo=<?php Echo $LIST['RULE_NO']; ?>" title="<?php Echo HtmlSpecialChars($LIST['FILE_NAME']); ?>">다운로드</a></td>
<?php
				} Else {
?>
										<td><?php Echo $LIST['RULE_NAME']; ?></td>
										<td> - </td>
<?php
				}
?>
									</tr>
<?php
			}
?>
                                </tbody>
                            </table>
<?php
		}
	}
?>
						</div>
					</div>