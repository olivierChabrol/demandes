$(document).ready(function() {
//TODO -multifile
// PHP - on creer un nom de dossier random qu'on stock dans l'objet purchase order ou missionorder
// on recupere le nom de dossier random
// on envoie une requete ajax avec le/s Fichier et le nom de dossier random
// PHP - on a un script php qui uppload le fichier
// on affiche le fichier avec la possibilité de le delete
//on gere le delete


    // print files to be uploaded
    $('.file-to-upload.ajax').change(function() {
      var totalfiles=$('.file-to-upload')[0].files.length;
      var form_data = new FormData();
      form_data.append('dir',uploadDir);
      /*if(getid!=undefined)
      {
        form_data.append('id',getid);
      }*/
      for (var i = 0; i < totalfiles; i++) {
        form_data.append("files[]", $('.file-to-upload').prop('files')[i]);
      }

      var elem=$(this);

      $.ajax({
        type: "POST",
        url: "request_upload.php",
        cache: false,
        contentType: false,
        processData: false,
        data : form_data,
        datatype: 'json'
        })
        .done(function( data ) {
	    console.log(data);
            var files=eval(data);
            var htmlFileToUpload = printFileToUpload(files);
            elem.siblings('.files-to-upload-group').append(htmlFileToUpload);
        });

        //var htmlFileToUpload = printFileToUpload($(this).get(0).files);
        //$(this).parent().children('.files-to-upload-group').html(htmlFileToUpload);
    });


    $(".files-to-upload-group").on('click', '.fa-trash', function(event) {
      if(confirm('Êtes-vous sur de vouloir supprimer ce fichier ?'))
      {
        var data=new FormData();
        data.append('dir',uploadDir);
        data.append('file',event.target.id);

        var elem=$(this);

        $.ajax({
          type: "POST",
          url: "request_deletefile.php",
          cache: false,
          contentType: false,
          processData: false,
          data : data,
          datatype: 'json'
          })
          .done(function( data ) {
            var ret=eval(data);
            if(ret[0].msgtype=='valid')
            {
              elem.parents("div .filerow").remove();
            }
            else {
              console.log("ERREUR");
            }

          });

      }
    });

    function printFileToUpload(files) {
        var filesToUpload = '';

        for (var i = 0; i < files.length; ++i) {
            filesToUpload +='<div class="filerow"><i style="vertical-align: middle;" class="fa fa-upload text-info"></i>&nbsp;' + files[i].name;
            filesToUpload +='<a title="Supprimer ce fichier" >&nbsp; <i id="'+files[i].realname+'" class="fa fa-trash text-danger"></i></a></div>';
            //if (i+1 < files.length) {
                //filesToUpload += '<br>';
            //}
        }

        return filesToUpload;
    }



});

function sendError(message) {
    var errorBox =
        '<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex d-none">' +
        '<div class="flex-grow-1">' +
        '<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>' +
        '<strong class="text-danger">' + message + '</strong>' +
        '</div>' +
        '<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>' +
        '</button>' +
        '</div>';
    $('.request').prepend(errorBox);
}

function toggleElement(element, show, addClass = 'flex') {
    if (show) {
        $(element).removeClass('d-none');
        $(element).addClass('d-' + addClass);
    } else {
        $(element).removeClass('d-' + addClass);
        $(element).addClass('d-none');
    }
}
