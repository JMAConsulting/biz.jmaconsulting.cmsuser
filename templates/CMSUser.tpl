{literal}
<script type="text/javascript">
CRM.$(function($) {
  var framework = '{/literal}{$config->userFramework}{literal}';
  var url = '{/literal}{$cmsURL}{literal}';
  var user = '{/literal}{$cmsUser}{literal}';
  var prev = $('#crm-record-log .col1').html();
  var html = "&nbsp; &nbsp;" + framework.replace('Drupal8', 'Drupal') + " User: " + "<a class='crm-popup' href='" + url + "'>" + user + "</a>";
  $('#crm-record-log .col1').html(prev + html)
});
</script>
{/literal}
