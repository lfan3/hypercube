import { Controller } from 'stimulus';
const $ = require('jquery');

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    updateImage() {
        var avatar = $("#avatarToUpload")[0].files[0];
        var fd = new FormData();
        // fd.append('user', 'abc');
        // for (var val of fd.values())
        // {
        //     console.log(val);
        // }

        fd.append("avatar", avatar);

        $.ajax({  
             url:        '/profile/edit/avatar/123',  
             type:       'POST',   
             data:   fd,
             processData: false,  // indique à jQuery de ne pas traiter les données
             contentType: false,  // indique à jQuery de ne pas configurer le contentType
             async: true, 

             success: function(data, status) {  
                console.log("success");
                console.log(data.name);
                $("#testId").html(data.name);
                console.log($("#testId"));
             },  
             error : function(xhr, textStatus, errorThrown) {  
                alert('Ajax request failed.');  
             } 
        });
    }

}