var case_type = {
    settings: {
        ajaxUrl: '',
        ajax_form_process: ''
    },
    init: function() {
        case_type.initDataTable();
        case_type.initForm();
    },

    initForm: function(){
        
        $('.href-modal').unbind('click').bind('click',function(){
            var _this = $(this);
            
            $('.modal').removeClass('modal-fullscreen');

            
            $.ajax({
                url: _this.data('url'),
                type: 'POST',
                data: { id: _this.data('id'), action: _this.data('action')},
                beforeSend: function(){
                $(".modal-content").html('');
                    
                },
                success: function(r){
                    if(r.success){
                
                        $(".modal-content").html(r.html);
                        $('#modal').modal('show');
                    }
                }
            });
        });
        
    },
    initDataTable: function() {

        var callBack = function() {
            case_type.initForm();
        };

        case_type.dataList = $('#case_type-datalist').DataTable({
            'processing': true,
            'serverSide': true,
            "lengthChange": false,
            "pageLength": 20,
            'ajax': {
                'url': case_type.settings.ajaxUrl,
                'data': function(d) {
                    d.url = global.settings.url;
                }
            },
            'deferRender': true,
            'columnDefs': [
                { 'orderable': false, 'targets': 2 },
                { 'searchable': false, 'targets': 2 }
            ],
            drawCallback: function() {
                callBack();
            },
            responsive: {
                details: {
                    renderer: function( api,rowIdx ) {
                        return global.dataTableResponsiveCallBack(api, rowIdx, callBack);
                    }
                }
            }
        });

        $('.content-container').removeClass('has-loading');
        $('.content-container-content').removeClass('hide');
    }
};