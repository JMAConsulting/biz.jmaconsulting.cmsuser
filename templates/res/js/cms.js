CRM.$(function($) {

var cid = getURLParam('crmId');
if (cid) {
  populateUserForm(cid);
}

function populateUserForm(cid) {
  CRM.api3('Contact', 'get', {
    "sequential": 1,
    "return": "first_name,last_name,email",
    "id": cid
  }).done(function(result) {
    $.each(result.values, function(key, element) {
      $('#edit-name').val(element.first_name + '.' + element.last_name);
      $('#edit-mail').val(element.email);
    });
  });
}

function getURLParam(sParam) {
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split('&');
  for (var i = 0; i < sURLVariables.length; i++) {
    var sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] == sParam) {
      return sParameterName[1];
    }
  }
}

});
