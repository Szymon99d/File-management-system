/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'codemirror/lib/codemirror.css';
import 'codemirror/theme/monokai.css';
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import $ from 'jquery';
import CodeMirror from 'codemirror';

function clearSidebar()
{
    $("#fileProperties").empty();
    $("#fileContent").empty();
}

function fileType(mimeType,path,fileContents)
{
    var saveFileBtn = "<button id='saveChangesBtn' class='btn btn-success w-100'>Save changes</button>";
    var display = "<hr><h3>Preview</h3>";
    if(mimeType.includes("image"))
    {
        display += "<div><img src='"+path+"' class='img-fluid' alt='image' /></div>";
        $("#fileContent").append(display);
    }
    else if(mimeType.includes("text") || mimeType === "application/x-empty")
    {
        display += "<textarea class='w-100 py-2 fileText' rows='15'></textarea>";
        $("#fileContent").append(display);
        var textEditor = CodeMirror.fromTextArea($(".fileText")[0],{lineNumbers:true,theme:"monokai"});
        textEditor.setValue(fileContents);
        $("#fileContent").append(saveFileBtn);
        
    }
    else if(mimeType.includes("pdf"))
    {
        display += "<embed src='"+path+"'/>";
        $("#fileContent").append(display);
    }
    else if(mimeType.includes("video"))
    {
        display +=  "<div class='video-fluid ratio ratio-1x1'><video controls>"
        +"<source src='"+path+"' type='"+mimeType+"'></video>";
        $("#fileContent").append(display); 
    }   
    else
    {
        var display = "<hr><h3>No preview</h3>";
        $("#fileContent").append(display);
    } 
}

$(function(){
    $(document).on('click','#saveChangesBtn',function(){
        var fileId = $("#fileId").val();
        var fileText = $(".CodeMirror")[0].CodeMirror.getValue();
        $.ajax({
            url: "/save-changes/"+fileId+"",
            method: "post",
            data: {"data":fileText},
            async: true,
            success: function(resp){
                
            },
            error: function(e){

            },
        });

    });

    $("#fileUpload").on('change',function(){
        var formData = new FormData();
        var countFiles = $("#fileUpload").prop("files").length;
        for(var i=0; i<countFiles; i++){
            
            formData.append("files[]",$("#fileUpload").prop("files")[i]);
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
                    +"<p class='px-1 file-name'>"+files[i]["fileName"]+"</p>";
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
            url: "/select-file/"+fileId+"",
            method: "post",
            async: true,
            success: function(resp)
            {
                clearSidebar();
                resp = JSON.parse(resp);
                var file = resp['name']+resp['extension'];
                var properties = " <div class='d-flex broder-2 justify-content-end'><button id='closeProp' class='btn btn-danger'>Close</button></div>"
                +"<div><h5>File: <input id='fileName' type='text' class='input-file-name' value='"+resp['name']+"'/></h5>"
                +"<h5>Owner: "+resp['owner']+"</h5>"
                +"<h5>Upload date: "+resp['uploadDate']+"</h5>"
                +"<h5>Extension: "+resp['extension']+"</h5>"
                +"<h5>Size: "+resp['size']+" KB</h5></div>";
                $("#fileProperties").append(properties);
                var fileMenu = "<div class='list-group'>"
                +"<a id='downloadFile' href='"+resp['path']+"' download='"+file+"'class='list-group-item list-group-item-action list-group-item-primary'>Download</a>"
                +"<button id='deleteFile' class='list-group-item list-group-item-action list-group-item-primary'>Delete</button>"
                +"<input id='fileId' type='number' value='"+resp['id']+"' class='visually-hidden'/>"
                +"</div>";
                $("#fileProperties").append(fileMenu);
                fileType(resp['mimeType'], resp['path'], resp['fileContents']);
            },
            error: function(e)
            {
                console.log(e);
            }
        });
    });

    $(document).on('click','#closeProp',function(){
        clearSidebar();
    });

    $(document).on('click',"#deleteFile",function(){
        var conf = confirm("Are you sure you want to delete this file?");
        if(conf)
        {
            var fileId = $("#fileId").val();
            $.ajax({
                url: "/delete-file/"+fileId+"",
                method: "post",
                async: true,
                success: function(resp)
                {
                    clearSidebar();
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
                    url: "/rename-file/"+fileId+"",
                    method: "post",
                    data: {"name":fileName},
                    dataType: "json",
                    async: true,
                    success: function(resp)
                    {
                        resp = JSON.parse(resp);
                        var newFile = "";
                        newFile = fileName+""+resp["fileExtension"];

                        $("#"+fileId).children("p").first().text(newFile);
                        
                        $("#downloadFile").attr('download',newFile);
                        $("#downloadFile").attr('href',resp["filePath"]);
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