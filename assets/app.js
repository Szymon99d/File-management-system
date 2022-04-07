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
                var newFile = "<div id='"+resp['fileId']+"' class='px-2 file'>"
                +"<i class='bi bi-file-text'></i>"
                +"<p>"+resp["fileName"]+"."+resp["fileExtension"]+"</p>";
                $("#fileBrowser").append(newFile);
            },
            error: function(e){
                console.log(e);
            }
        });
    });

    $(document).on("click",".file",function(){
        var fileId = $(this).attr("id");
        $.ajax({
            url: "/select-file",
            method: "post",
            data: {"id":fileId},
            async: true,
            success: function(resp)
            {
                $("#fileProperties").empty();
                resp = JSON.parse(resp);
                var file = resp['name']+"."+resp['extension'];
                var path = "files/"+file;
                var properties = "<hr> <h4>File: "+resp['name']+"</h4>"
                +"<h5>Owner: "+resp['owner']+" KB</h5>"
                +"<h5>Extension: "+resp['extension']+"</h5>"
                +"<h5>Size: "+resp['size']+" KB</h5>";
                $("#fileProperties").append(properties);
                var fileMenu = "<div class='list-group'>"
                +"<a href='"+path+"' download='"+file+"'class='list-group-item list-group-item-action'>Download</a>"
                +"<a href='#' class='list-group-item list-group-item-action'>Rename</a>"
                +"<button id='deleteFile' class='list-group-item list-group-item-action'>Delete</button>"
                +"<input id='fileId' type='number' value='"+resp['id']+"' class='visually-hidden'/>"
                +"</div>";
                $("#fileProperties").append(fileMenu);

            },
            error: function(e)
            {
                console.log(e);
            }
        });
    });

    $(document).on('click',"#deleteFile",function(){
        var conf = confirm("Are you sure you want to delete this file?");
        if(conf)
        {
            var fileId = $("#fileId").val();
            $.ajax({
                url: "/delete-file",
                method: "post",
                data: {"id":fileId},
                async: true,
                success: function(resp)
                {
                    $("#fileProperties").empty();
                    $("#"+fileId).remove();
                },
                error: function(e){
                    console.log(e);
                },
            });
        }
    });
});