var city_ajax_form = {
    settings: {
        processFormUrl: '',
        provinceAutocompleteUrl: ''
    },
    init: function(){

        global.init();
        city_ajax_form.processForm();
        city_ajax_form.bindAutoComplete();


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
                url:  city_ajax_form.settings.processFormUrl,
                data: formData, 
                type: "post",
                cache: false,
                processData: false,
                contentType: false, 
                success: function(r){
                    if(r.success){
          
                        $.toaster({ message : r.msg, title : '', priority : 'success' });
                        $('.modal').modal('hide');
    
                        if(typeof city  != 'undefined'){
                            city.dataList.draw();
                        }
    
                    } else {
                        $.toaster({ message : r.msg, title : '', priority : 'danger' });
                    }
                }
            });
        });
    },

    bindAutoComplete: function(){
        global.autocomplete.bind(city_ajax_form.settings.provinceAutocompleteUrl, '#city_form_province_desc', '#city_form_province');   

    }
}