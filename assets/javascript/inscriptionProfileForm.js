$(document).ready(function(){
 
    (function(window, $, Routing){
        //the modal is an object hier, and we give the function to this dom element
        //expresso: delegate selector of jquery
        window.ProfileForm = function($wrapper){
            this.$wrapper = $wrapper;
            this.$wrapper.on(
                'submit',
                '.inscription-profile-form',
                this.handleFormSubmit.bind(this)
            );
            //!difference with profile js, the class name
            this.$wrapper.on(
                'change',
                '.inscription-input',
                this.handleInputFile.bind(this)
            );
            
            this.url = Routing.generate('inscription_test');
        }
      
        $.extend(ProfileForm.prototype, {
            handleFormSubmit : function(e) {
                console.log('heere');

                e.preventDefault();
                //expresso: formData manuellement vs automatique
                // var fileInput = document.getElementById('user_photo_inscription');
                // var file = fileInput.files[0];
                // let formData = new FormData();
                // formData.append('file', file);
                let form = e.currentTarget;
                let formData = new FormData(form);

                var self = this;
                $.ajax({
                    url: self.url,
                    method: 'POST',
                    contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                    processData: false, 
                    data: formData,
                    success: function(data){
                        const imgUrl = data.data;
                        self._showProfileImage(imgUrl);
                        console.log(imgUrl);
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
                console.log(  this.$wrapper.find('.avatarImg'));
                //pas besoin de reload
                //location.reload();
            },

            handleInputFile: function(e) {
                console.log('hi');
                const inputFileEle = e.target;
                const inputFile = inputFileEle.files[0];
                $(inputFileEle).parent()
                        .find('.custom-label')
                        .html(inputFile.name);
            }
        } )
        
    })(window, jQuery, Routing)

    var $wrapper = $('#inscription-avatar-container');

    var profileForm = new ProfileForm($wrapper);
});
