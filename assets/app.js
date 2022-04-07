/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import $ from 'jquery';

$(function(){
    $("#fileUpload").on('change',function(){
        var uploadForm = $("#uploadForm");
        var formData = new FormData(uploadForm[0]);
        $.ajax({
            url: "/upload-file",
            method: "post",
            data: formData,
            processData: false,
            contentType: false,
            async: true,
            success: function(resp)
            {
                var newFile = "<div class='px-2 file'>"
                +"<i class='bi bi-file-text'></i>"
                +"<p>"+resp["fileName"]+"."+resp["fileExtension"]+"</p>"
                +"<p>Size: "+resp["fileSize"]+"Kb </p>";
                $("#fileBrowser").append(newFile);
            },
            error: function(e){
                console.log(e);
            }
        });
    });
});