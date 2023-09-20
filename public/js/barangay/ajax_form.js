var barangay_ajax_form = {
    settings: {
        processFormUrl: '',
        cityAutocompleteUrl: ''
    },
    init: function(){

        global.init();
        barangay_ajax_form.processForm();
        barangay_ajax_form.bindAutoComplete();


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
                url:  barangay_ajax_form.settings.processFormUrl,
                data: formData, 
                type: "post",
                cache: false,
                processData: false,
                contentType: false, 
                success: function(r){
                    if(r.success){
          
                        $.toaster({ message : r.msg, title : '', priority : 'success' });
                        $('.modal').modal('hide');
    
                        if(typeof barangay  != 'undefined'){
                            barangay.dataList.draw();
                        }
    
                    } else {
                        $.toaster({ message : r.msg, title : '', priority : 'danger' });
                    }
                }
            });
        });
    },

    bindAutoComplete: function(){
        global.autocomplete.bind(barangay_ajax_form.settings.cityAutocompleteUrl, '#barangay_form_city_desc', '#barangay_form_city');   

    }
}