//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
function AddTranslation(){
 var form = $("#formTranslation");
 $.post("/translation/add.php", {l0: form.children("select[name='l0']").val(), t0: form.children("input[id='inputL0']").val(),
  l1: form.children("select[name='l1']").val(), t1: $("#inputL1").val()},
  function(data,status){
   $("#tableBody").html(data);
   $("#inputL1").val("");
 }).fail(function(xhr, statusText, errorThrown){
       $("#tableBody").html("<tr><td>Error(" + xhr.status + "): " + errorThrown + "</td><td>0</td><td>0</td></tr>");
 });
}

function vote(obj, l0, l1, id, bad){
 $.post("/translation/vote.php", {l0: l0, l1: l1, id: id, bad: bad});
 $(obj).text(parseInt($(obj).text()) + 1);
}

function Select_onL0Change(){
 $("#inputL0").val("");
}

function Select_onL1Change(){
 $("#tableBody").html("");
}

$(document).ready(function(){
  var form = $("#formTranslation");
  form.submit(function(event){
    event.preventDefault();
    $("#tableBody").html("<tr><td>Waiting..</td><td>0</td><td>0</td></tr>");

    $.post("/translation/translate.php", {l0: form.children("select[name='l0']").val(), l1: form.children("select[name='l1']").val(), t0: form.children("input[id='inputL0']").val()},
      function(data,status){
       $("#tableBody").html(data);
    }).fail(function(xhr, statusText, errorThrown){
       $("#tableBody").html("<tr><td>Error(" + xhr.status + "): " + errorThrown + "</td><td>0</td><td>0</td></tr>");
    });
  });
});
