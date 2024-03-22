$(function(){
	// 입상 및 지원사업 선정경력 추가 이벤트
	$('.btnAddEntry').on('click', function(){
		var tbody = $('#winTable>table>tbody');
		newEntry1(tbody);
	});

	// 입상 및 지원사업 선정경력 삭제 이벤트
	$(document).on('click', '.btnDelEntry', function(){
		var tbody = $('#winTable>table>tbody');
		$(this).closest('tr').remove();

		var count = $('#winTable>table>tbody>tr').length;
		if (count < 1) newEntry1(tbody);
	});
	
	// 특허 출원여부 이벤트
	$('#IPR_APPLY_YN').on('change', function(){
		var isChecked = $(this).is(':checked');

		if(isChecked) {
			$('#IPR_ING_CNT').prop('readonly', false);
			$('#IPR_PATENT_CNT').prop('readonly', false);
		} else {
			$('#IPR_ING_CNT').prop('readonly', true);
			$('#IPR_PATENT_CNT').prop('readonly', true);
			$('#IPR_ING_CNT').val('');
			$('#IPR_PATENT_CNT').val('');
		}
	});

	var mode = $('#mode').val();
	
	// 초기설정
	if ( mode == 'WR' ) {
		$('#IPR_ING_CNT').prop('readonly', true);
		$('#IPR_PATENT_CNT').prop('readonly', true);
		$('#btnHeader').css('display', 'table-cell');			// 자바스크립트 사용시만 추가/삭제 이벤트 가능하도록 설정

		newEntry1($('#winTable>table>tbody'));
	}
	
	if ( mode == 'MR' ) {
		$('#btnHeader').css('display', 'table-cell');			// 자바스크립트 사용시만 추가/삭제 이벤트 가능하도록 설정
		$('.delButton').css('display', 'table-cell');			// 자바스크립트 사용시만 추가/삭제 이벤트 가능하도록 설정

		var i = 0;
		$('#winTable > table > tbody').find('tr').each(function(){
			$('#ENTRY1_ROW_'+i+'_AWARD_DT').datepicker({
				dateFormat: 'yy-mm-dd',
				changeYear: true,
				changeMonth: true,
				prevText: '이전 달',
				nextText: '다음 달',
				monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				dayNames: ['일','월','화','수','목','금','토'],
				dayNamesShort: ['일','월','화','수','목','금','토'],
				dayNamesMin: ['일','월','화','수','목','금','토'],
				showMonthAfterYear: true,
				yearSuffix: '년',
				showButtonPanel: true,
				currentText: '오늘 날짜',
				closeText: '닫기'
			});
			
			i++;
		});
	}

	$('#BIZ_START_DT').datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		prevText: '이전 달',
		nextText: '다음 달',
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		showMonthAfterYear: true,
		yearSuffix: '년',
		showButtonPanel: true,
		currentText: '오늘 날짜',
		closeText: '닫기'
	});
});

function newEntry1(tbody) {
	var maxId = 0;
	tbody.find('tr').each(function(){
		var iVal = parseInt(this.id.replace('ENTRY1_ROW_','')) + 1;
		maxId = iVal > maxId ? iVal : maxId;
	});
	var rowId = 'ENTRY1_ROW_' + maxId;

	var newRow = '';
	newRow += '<tr id="'+rowId+'">';
	newRow += '	<td class="center"><a href="#n" class="btnDelEntry" style="text-decoration:none"><img src="/res/img/sub/cont/98/minus.png" align="middle" alt="삭제" title="클릭하시면 해당 행이 삭제됩니다." /> (삭제)</a></td>';
	newRow += '	<td><input name="CONTEST_NM[]" id="'+rowId+'_CONTEST_NM" style="width: 98%;" type="text" value=""></td>';
	newRow += '	<td><input name="AWARD[]" id="'+rowId+'_AWARD" style="width: 98%;" type="text" value=""></td>';
	newRow += '	<td><input name="AWARD_DT[]" id="'+rowId+'_AWARD_DT" style="width: 98%;" class="center" type="text" maxlength="10" value=""></td>';
	newRow += '	<td><input name="AGENCY[]" id="'+rowId+'_AGENCY" style="width: 98%;" type="text" value=""></td>';
	newRow += '</tr>';

	$(newRow).appendTo(tbody);
	
	$('#'+rowId+'_AWARD_DT').datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		prevText: '이전 달',
		nextText: '다음 달',
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		showMonthAfterYear: true,
		yearSuffix: '년',
		showButtonPanel: true,
		currentText: '오늘 날짜',
		closeText: '닫기'
	});
}