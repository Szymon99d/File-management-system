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
        var formData = new FormData();
        var countFiles = $("#fileUpload").prop("files").length;
        console.log($("#fileUpload").prop("files")[0]);
        console.log(countFiles);
        for(var i=0; i<countFiles; i++){
            
            formData.append("files[]",$("#fileUpload").prop("files")[i]);
            console.log($("#fileUpload").prop("files")[i]);
        }
        $.ajax({
            url: "/upload-file",
            method: "post",
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            async: true,
            success: function(resp)
            {
                var files = JSON.parse(resp);
                for(var i=0; i<files.length; i++)
                {
                    var newFile = "<div id='"+files[i]['fileId']+"' class='px-2 file'>"
                    +"<i class='bi bi-file-text'></i>"
                    +"<p>"+files[i]["fileName"]+"."+files[i]["fileExtension"]+"</p>";
                    $("#fileBrowser").append(newFile);
                }
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
                var properties = "<hr> <div class='d-flex justify-content-end'><button id='closeProp' class='btn btn-danger'>Close</button></div>"
                +"<div><h5>File: <input id='fileName' type='text' class='input-file-name' value='"+resp['name']+"'/></h5>"
                +"<h5>Owner: "+resp['owner']+"</h5>"
                +"<h5>Extension: "+resp['extension']+"</h5>"
                +"<h5>Size: "+resp['size']+" KB</h5></div>";
                $("#fileProperties").append(properties);
                var fileMenu = "<div class='list-group'>"
                +"<a id='downloadFile' href='"+resp['path']+"' download='"+file+"'class='list-group-item list-group-item-action list-group-item-primary'>Download</a>"
                +"<button id='deleteFile' class='list-group-item list-group-item-action list-group-item-primary'>Delete</button>"
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

    $(document).on('click','#closeProp',function(){
        $("#fileProperties").empty();
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
    $(document).on('keydown',"#fileName",function(e){
        if(e.keyCode == 13)
        {
            if(!$("#fileName").val()=="")
            {
                var fileId = $("#fileId").val();
                var fileName = $("#fileName").val();
                $.ajax({
                    url: "/rename-file",
                    method: "post",
                    data: {"id":fileId,"name":fileName},
                    dataType: "json",
                    async: true,
                    success: function(resp)
                    {
                        console.log(resp);
                        console.log("#"+fileId);
                        $("#"+fileId).children("p").first().text(fileName+"."+resp);
                        var newFile = fileName+"."+resp;
                        var newPath = "files/"+newFile;
                        $("#downloadFile").attr('download',newFile);
                        $("#downloadFile").attr('href',newPath);
                    },
                    error: function(e)
                    {

                    }
                });
            }
            else
            {
                alert("Invalid file name!");
            }
        }
    });
});