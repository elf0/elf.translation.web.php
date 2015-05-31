//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
function AddTranslation(){
 //alert($("#inputAddTranslation").val());

 var form = $("#formTranslation");
 $.post("/translation/add.php", {l0: form.children("select[name='s']").val(), t0: form.children("input[id='inputSource']").val(),
  l1: form.children("select[name='d']").val(), t1: $("#inputAddTranslation").val()},
  function(data,status){
   $("#tableBody").html(data);
   $("#inputAddTranslation").val("");
 }).fail(function(xhr, statusText, errorThrown){
       $("#tableBody").html("<tr><td>Error(" + xhr.status + "): " + errorThrown + "</td><td>0</td><td>0</td></tr>");
 });
}

function Select_onSourceChange(){
 $("#inputSource").val("");
}

function Select_onDestinationChange(){
 $("#tableBody").html("");
}

$(document).ready(function(){
  var form = $("#formTranslation");
  /*$("#tableBody").load("top.txt", function(){
    $("a.good").click(function(){
      $(this).text(parseInt($(this).text()) + 1);
      var row = $(this).parent().parent();
      //alert(row.parentsUntil("table").parent().attr("class"));
      $.post("/vote", {t: row.parentsUntil("table").parent().attr("class"), i: row.attr("id"), b: 0});
    });

    $("a.bad").click(function(){
      $(this).text(parseInt($(this).text()) + 1);
      var row = $(this).parent().parent();
      //alert(row.parentsUntil("table").parent().attr("class"));
      $.post("/vote", {t: row.parentsUntil("table").parent().attr("class"), i: row.attr("id"), b: 1});
    });
  });*/
   

  form.submit(function(event){
    event.preventDefault();
    $("#tableBody").html("<tr><td>Waiting..</td><td>0</td><td>0</td></tr>");

//alert(form.children("select[name='s']").val() + " to " + form.children("select[name='d']").val() + ": " + form.children("input[id='inputSource']").val());

    $.post("/translation/translate.php", {l0: form.children("select[name='s']").val(), l1: form.children("select[name='d']").val(), t0: form.children("input[id='inputSource']").val()},
      function(data,status){
       $("#tableBody").html(data);
    }).fail(function(xhr, statusText, errorThrown){
       $("#tableBody").html("<tr><td>Error(" + xhr.status + "): " + errorThrown + "</td><td>0</td><td>0</td></tr>");
    });
  });
});
