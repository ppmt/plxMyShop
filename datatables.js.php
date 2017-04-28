<script src="../../plugins/plxMyShop/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="../../plugins/plxMyShop/js/featherlight-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../../plugins/plxMyShop/js/jquery.dataTables-1.10.15.min.js" type="text/javascript"></script>
<script src="../../plugins/plxMyShop/js/dataTables.responsive-1.0.0.min.js" type="text/javascript"></script>
<?php /*
<!--
<script type="text/javascript" src="//code.jquery.com/jquery-latest.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.js" charset="utf-8"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/responsive/1.0.0/js/dataTables.responsive.min.js"></script>
-->
//js 4 cdn css exemple
 "<link rel='stylesheet' href='//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css' type='text/css' media='screen' />"+
 "<link rel='stylesheet' href='//cdn.datatables.net/responsive/1.0.0/css/dataTables.responsive.css' type='text/css' media='screen' />"+
 "<link rel='stylesheet' href='//cdn.rawgit.com/noelboss/featherlight/1.7.2/release/featherlight.min.css' type='text/css' />"
*/
?>
<script type="text/javascript" class="init">
$(document).ready(function(){
 $("head link[rel='stylesheet']").last().after("<style>"+
 ".dataTables_wrapper{position: static !important;}"+
 ".lightbox { display: none; }.featherlight-iframe .featherlight-content {overflow-y: auto !important;width:92%;height:92%;}iframe.featherlight-inner{width: 100%;height: 100%;}"+
 "</style>"+
 "<link rel='stylesheet' href='../../plugins/plxMyShop/css/jquery.dataTables-1.10.15.min.css' type='text/css' media='screen' />"+
 "<link rel='stylesheet' href='../../plugins/plxMyShop/css/dataTables.responsive-1.0.0.css' type='text/css' media='screen' />"+
 "<link rel='stylesheet' href='../../plugins/plxMyShop/css/featherlight-1.7.2.min.css' type='text/css' />");
 var table = $('#myShop-table').DataTable({// DataTable
  "order": [[ 1, "desc" ]],
  "language":{
   "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/<?php $plxPlugin->lang('L_DATABLEJS'); ?>.json"
  }
 });

 table.columns().every(function(){// Apply the search
 //console.log('table.columns.every',this.value);
  if($(this).text()!=''){
   var that = this;
   $('input',this.footer()).on('keyup change',function(){
    if(that.search() !== this.value){
     that
      .search(this.value)
      .draw();
    }
   });
  }
 });
});
</script>