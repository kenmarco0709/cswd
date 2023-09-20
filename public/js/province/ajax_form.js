var province_ajax_form = {
    settings: {
        processFormUrl: ''
    },
    init: function(){

        global.init();
        province_ajax_form.processForm();
        console.log('swaaaaa');

    },
    
    processForm: function(){
        
        $('.close-modal').unbind('click').bind('click',function(){
            $('.modal').modal('hide');
        });
        
        $('#form').submit(function(e){
            e.preventDefault();
            _this = $(this);
            _this.find(':input[type=submit]').prop('disabled', true);
            var formData = new FormData(_this[0]);

            $.ajax({
                url:  province_ajax_form.settings.processFormUrl,
                data: formData, 
                type: "post",
                cache: false,
                processData: false,
                contentType: false, 
                success: function(r){
                    if(r.success){
          
                        $.toaster({ message : r.msg, title : '', priority : 'success' });
                        $('.modal').modal('hide');
    
                        if(typeof province  != 'undefined'){
                            province.dataList.draw();
                        }
    
                    } else {
                        $.toaster({ message : r.msg, title : '', priority : 'danger' });
                    }
                }
            });
        });
    },
}