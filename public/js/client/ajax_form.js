var client_ajax_form = {
    settings: {
        processFormUrl: '',
        barangayAutocompleteUrl: '',
        deseaseAutocompleteUrl: '',
        encoderAutocompleteUrl: '',
        formChoices: null
    },
    init: function(){

        global.init();
        client_ajax_form.processForm();
        client_ajax_form.bindAutoComplete();
        client_ajax_form.addFamilyMember();
        client_ajax_form.remove();
        client_ajax_form.bindInput();

    },
    
    bindInput: function(){
        $('#client_form_civil_status').unbind('change').bind('change', function(){
            var _this = $(this);

            if(_this.val() == 'Others'){

                $('#' + _this.data('childelem')).parent().parent.removeClass('d-none');
            } else {
                $('#' + _this.data('childelem')).parent().addClass('d-none');

            }
        });


        $('#client_form_civil_status').unbind('change').bind('change', function(){
            var _this = $(this);

            if(_this.val() == 'Others'){

                $('#' + _this.data('childelem')).parent().removeClass('d-none');
            } else {
                $('#' + _this.data('childelem')).parent().addClass('d-none');

            }
        });

        $.each($(".caseTypeInput"),function(){
            var _this = $(this);
            _this.bind('click').bind('click',function(){
                var checkedInput = $(".caseTypeInput:checked");
                client_ajax_form.bindInputChildElem(_this, checkedInput, ['Others']);
            });
        });

        $.each($(".assistantTypeInput"),function(){
            var _this = $(this);
            _this.bind('click').bind('click',function(){
                var checkedInput = $(".assistantTypeInput:checked");
                client_ajax_form.bindInputChildElem(_this, checkedInput, ['AHC', 'FAM']);

            });
        });        
    },

    bindInputChildElem(_this, checkedInput, valArrayToSpecify){

        var checks = [];
        if(checkedInput.length){
            checkedInput.each(function(){
                checks.push(this.value);
            });
        }

        client_ajax_form.hideShowElem(_this, checks,valArrayToSpecify);
    },

    hideShowElem: function(_this, _array, valArrayToSpecify){

        var hasVal  = false;
        
        $.each(_array, function(i,v){
            if(valArrayToSpecify.includes(v)){
                hasVal = true;
            }
        });

        if(hasVal){
            
            $('#' + _this.data('childelem')).parent().parent().removeClass('d-none');
        } else {
            $('#' + _this.data('childelem')).parent().parent().addClass('d-none');

        }   
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
                url:  client_ajax_form.settings.processFormUrl,
                data: formData, 
                type: "post",
                cache: false,
                processData: false,
                contentType: false, 
                success: function(r){
                    if(r.success){
          
                        $.toaster({ message : r.msg, title : '', priority : 'success' });
                        $('.modal').modal('hide');
    
                        if(typeof client  != 'undefined'){
                            client.dataList.draw();
                        }
    
                    } else {
                        $.toaster({ message : r.msg, title : '', priority : 'danger' });
                        _this.find(':input[type=submit]').prop('disabled', false);

                    }
                }
            });
        });
    },
    addFamilyMember: function(){
        $.each($('.href-add'),function(){
            var _this = $(this);
            _this.unbind('click').bind('click',function(){
                var ctr = $('.memberCtr').length;
                ctr++;
                var _html = '';
                _html+= '<tr class="memberCtr"><td><a class="remove float-left" href="javascript:void(0);"/><i style="color:red;margin-right:5px;" class="fa fa-times" aria-hidden="true"></i></a>'+'<input style="width:85%;" type="text" class="form-control" name="client_form[familyMember]['+ctr+'][firstName]"/></td>';
                _html+= '<td><input type="text" class="form-control" name="client_form[familyMember]['+ctr+'][middleName]" /></td>';
                _html+= '<td><input type="text" class="form-control" name="client_form[familyMember]['+ctr+'][lastName]" /></td>';
                _html+= '<td style="width:120px;"><select class="form-control" name="client_form[familyMember]['+ctr+'][gender]" />'+client_ajax_form.getOptionElem('genders')+'</select></td>';
                _html+= '<td><select class="form-control" name="client_form[familyMember]['+ctr+'][familyRole]" />'+client_ajax_form.getOptionElem('patientRelationships')+'</select></td>';
                _html+= '<td style="width:90px;"><input type="text" class="form-control" name="client_form[familyMember]['+ctr+'][age]" /></td>';
                _html+= '<td style="min-width:150px;"><select class="form-control" name="client_form[familyMember]['+ctr+'][civilStatus]" />'+client_ajax_form.getOptionElem('civilStatuses')+'</select></td>';
                _html+= '<td><select class="form-control" name="client_form[familyMember]['+ctr+'][educationalAttainment]" />'+client_ajax_form.getOptionElem('educationalAttainments')+'</select></td>';
                _html+= '<td><input type="text" class="form-control" name="client_form[familyMember]['+ctr+'][occupation]" /></td>';
                _html+= '<td><input type="text" class="form-control" name="client_form[familyMember]['+ctr+'][monthlyIncome]" /></td>';
                $('#' + _this.data('target')).find('tbody').append(_html);
                global.mask();
                client_ajax_form.remove();
            
            });
        });
    },
    getOptionElem: function(selectType){
        var _html = '';


        $.each(client_ajax_form.settings.formChoices,function(i, obj) {

            if(i == selectType){
                $.each(client_ajax_form.settings.formChoices[selectType], function(i){
                    _html+= '<option value="'+i+'">'+i+'</option>';
                });
            }
        });

        return _html;
    },
    remove: function(){
        $.each($('.remove'),function(){
            var _this = $(this);
            _this.unbind('click').bind('click',function(){
                 _this.closest('tr').remove();     
            });
        });
    },

    bindAutoComplete: function(){
        global.autocomplete.bind(client_ajax_form.settings.barangayAutocompleteUrl, '#client_form_barangay_desc', '#client_form_barangay');   
        global.autocomplete.bind(client_ajax_form.settings.barangayAutocompleteUrl, '#client_form_barangay_address_desc', '#client_form_barangay_address');   
        global.autocomplete.bind(client_ajax_form.settings.deseaseAutocompleteUrl, '#client_form_desease_desc', '#client_form_desease');   
        global.autocomplete.bind(client_ajax_form.settings.encoderAutocompleteUrl, '#client_form_encoder_fullname', '#client_form_encoder');   

    }
}