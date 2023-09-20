var client = {
    settings: {
        ajaxUrl: '',
        ajax_form_process: '',
        ajaxDeleteUrl:''
    },
    init: function() {
        client.initDataTable();
        client.initForm();
        client.bindInput();

        $('#uploadFile').unbind('change').bind('change',function(){
            $('#importClient').submit();

        });
    },

    initForm: function(){
        
        $('.href-modal').unbind('click').bind('click',function(){
            var _this = $(this);
            
            $('.modal').removeClass('modal-fullscreen');

            $('.modal').removeClass('modal-fullscreen');

            if(_this.hasClass("fullscreen")){
                $('.modal').addClass('modal-fullscreen');
            }

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
            client.initForm();
            client.bindInput();
        };

        client.dataList = $('#client-datalist').DataTable({
            'processing': true,
            'serverSide': true,
            "lengthChange": false,
            "pageLength": 20,
            'ajax': {
                'url': client.settings.ajaxUrl,
                'data': function(d) {
                    d.url = global.settings.url;
                    d.q = client.getURlParams() ? client.getURlParams() : '';
                }
            },
            'deferRender': true,
            'columnDefs': [
                { 'orderable': false, 'targets': 10 },
                { 'searchable': false, 'targets': 10 }
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
            },
            createdRow: function( row, data, dataIndex ) {

               if($( row ).find('td:eq(0)').find('input').length){
                    $(row).addClass('isLocked');
               }
            }
        });

        $('.content-container').removeClass('has-loading');
        $('.content-container-content').removeClass('hide');
    },

    getURlParams: function(){
        var  queryString = window.location.search;
        var urlParams = new URLSearchParams(queryString);
        return urlParams.get('q');
    }, 

    bindInput: function(){

        $.each($('.delete-btn'), function(){
            var _this = $(this);
            _this.bind('click').bind('click',function(){
                if(!confirm($(this).data('message'))) {
                    e.preventDefault();
                } else {
                   
                    $.ajax({
                        url:  client.settings.ajaxDeleteUrl,
                        data: { 
                            id: _this.data('id')
                        }, 
                        type: "post",
                        success: function(r){
                            if(r.success){
                  
                                $.toaster({ message : r.msg, title : '', priority : 'success' });
            
                                if(typeof client  != 'undefined'){
                                    client.dataList.draw();
                                }
            
                            } else {
                                $.toaster({ message : r.msg, title : '', priority : 'danger' });        
                            }
                        }
                    });
                }
            });
        });
    }
};