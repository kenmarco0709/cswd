var intake_sheet = {
    settings : {
    },
    init: function(){
        intake_sheet.downloadCsv();
    },
    downloadCsv: function(){
      
        $('#reportCsvBtn').unbind('click').bind('click',function(){
            var dateFrom = $('#dateFrom').val() != '' ? $('#dateFrom').val().replaceAll('/', '-') : 'null';
            var dateTo = $('#dateTo').val() != '' ? $('#dateTo').val().replaceAll('/', '-') : 'null';
            var status = $('#assistantTypes').val();

            var _this = $(this);

            _this.prop('href', global.settings.url + '/report/export_intake_sheet_csv/' + dateFrom + '/' + dateTo + '/' + status);
        })
     
    },
}