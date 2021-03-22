$(document).ready(function(){
 
    (function(window, $){
        //the modal is an object hier, and we give the function to this dom element
        //expresso: delegate selector of jquery
        window.AvatarModal = function($wrapper){
            this.$wrapper = $wrapper;
            this.$wrapper.on(
                'submit',
                '.profile-avatar-form',
                this.handleFormSubmit.bind(this)
            );
            this.$wrapper.on(
                'change',
                '.custom-file-input',
                this.handleInputFile.bind(this)
            );

            // this.$wrapper.find('.profile-avatar-form').on(
            //     'submit',
            //     this.handleFormSubmit.bind(this)
            // );
            // this.$wrapper.find('.custom-file-input').on(
            //     'change',
            //     this.handleInputFile.bind(this)
            // );

        }

        $.extend(AvatarModal.prototype, {
            handleFormSubmit : function(e) {
                e.preventDefault();
                //expresso
                //observer la différence entre les deux, one is jquery init, lautre est form dom elem
                $form = $(e.currentTarget);
                //1er way to send file/image via ajax
                //$wholeForm = new FormData($form[0]);
                let wholeForm = new FormData($form[0]);
              
                //2er way to send the file/image, pay attention, must name the formData key = user_photo[avatar]
                // var formData = new FormData();
                // let blob = $('input[type=file]')[0].files[0];
                // let token = $('#user_photo__token').val();
                // formData.append('user_photo[avatar]', blob);
                // formData.append('user_photo[_token]', token);

                $url = $form[0].action;

                $avatarModal = $(this.$wrapper);
                //expresso:
                var self = this;
                $.ajax({
                    url: $url,
                    method: 'POST',
                    contentType: false,
                    processData:false,
                    cache:false,
                    data: wholeForm,
                    success: function(data){
                        //todo the data contain the image url, and attach the new photo to profile
                        $avatarModal.modal('hide');
                        const imgUrl = `{{asset(${data})}}`;
                        self._showProfileImage(imgUrl);
                    },
                    error:function(jqXHR)
                    {
                       var errorData = JSON.parse(jqXHR.responseText);
                       self._mapErrorsToForm(errorData);
                    }
                })
            },

            _mapErrorsToForm: function(errorData){
                console.log(errorData);
                var errorMessage = errorData.error['avatar']
                var errorSpan = '<span class="invalid-feedback d-block"><span class="d-block"><span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message"> ' + errorMessage + ' </span></span></span>';
                this.$wrapper.find('.form-error').html(errorSpan);
            },

            _showProfileImage: function(imageUrl){
                this.$wrapper.find('.avatarImg').attr("src", imageUrl);
                location.reload();
            },

            handleInputFile: function(e) {
                const inputFileEle = e.target;
                const inputFile = inputFileEle.files[0];
                $(inputFileEle).parent()
                        .find('.custom-file-label')
                        .html(inputFile.name);
            }
        } )
        
    })(window, jQuery)
    
    //? pourquoi $, 
    //! just to rappelle que c'est un jquery object
    var $wrapper = $('#avatarModal');
    var avatarModal = new AvatarModal($wrapper);
});



