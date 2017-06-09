<?php if (!defined('PLX_ROOT')) exit;
/*
Si vous réutilisez ce fichier dans votre thème, nous vous conseillons de noter la version actuelle de plxMyShop
version : 
*/
$plxPlugin = $d["plxPlugin"];
$plxPlugin->traitementPanier();

$paypal_amount= $plxPlugin->getParam('payment_paypal_amount');

$afficheMessage = FALSE;
if ( isset($_SESSION[$plxPlugin->plugName]['msgCommand'])
 && !empty($_SESSION[$plxPlugin->plugName]['msgCommand'])
){
 $afficheMessage = TRUE;
 $message = $_SESSION[$plxPlugin->plugName]['msgCommand'];
 unset($_SESSION[$plxPlugin->plugName]['msgCommand']);
}
# Hook Plugins
eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierDebut'));
?>
<a id="panier"></a>
<div align="center" class="panierbloc">
 <div align="center" id="listproducts">
  <section align="center" class="productsect">
   <header><?php
     $plxPlugin->lang('L_PUBLIC_BASKET');
    if ($afficheMessage) {
     echo '<br />'.$message;
    }
   ?></header>
   <div id="shoppingCart">
<?php
     $sessioncart="";
     $totalpricettc=0;
     $totalpoidg=0;
     $totalpoidgshipping = 0;
     $nprod=0;
     if (isset($_SESSION[$plxPlugin->plugName]['prods']) && $_SESSION[$plxPlugin->plugName]['prods']) {
?>
       <form method="POST">
<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierFormProdsDebut')); # Hook Plugins ?>
        <table class="tableauProduitsPanier">
         <tr>
          <th><?php $plxPlugin->lang('L_PRODUCT'.(count($_SESSION[$plxPlugin->plugName]['prods'])>=2?'S':'')); ?></th>
          <th class="nombre"><?php $plxPlugin->lang('L_UNIT_PRICE'); ?></th>
          <th><?php $plxPlugin->lang('L_NUMBER'); ?></th>
          <th colspan="2" class="nombre"><?php $plxPlugin->lang('L_TOTAL_PRICE'); ?></th>
         </tr>
<?php   foreach ($_SESSION[$plxPlugin->plugName]['prods'] as $pId => $nb) {
           $prixUnitaire = (float) $plxPlugin->aProds[$pId]['pricettc'];
           $prixttc = $prixUnitaire * $nb;
           $poidg = (float) $plxPlugin->aProds[$pId]['poidg'] * $nb;
           $totalpricettc += $prixttc;
           $totalpoidg += $poidg;
           $nprod++;
?>
         <tr>
          <td><a href="<?php echo $plxPlugin->productRUrl($pId); ?>"><?php echo plxUtils::strCheck($plxPlugin->aProds[$pId]['name']); ?></a></td>
          <td class="nombre"><?php echo $plxPlugin->pos_devise($prixUnitaire);?></td>
          <td width="10%"><input type="number" name="nb[<?php echo $pId;?>]" value="<?php echo htmlspecialchars($nb);?>" min="0" /></td>
          <td class="nombre"><input type="submit" class="red" name="retirerProduit[<?php echo $pId;?>]" value="<?php echo htmlspecialchars($plxPlugin->getLang('L_DEL'));?>" /></td>
          <td class="nombre"><?php echo $plxPlugin->pos_devise($prixttc);?></td>
         </tr>
<?php   } // FIN foreach ($_SESSION[$plxPlugin->plugName]['prods'] as $pId => $nb) ?>
         <tr>
          <td class="nombre" colspan="3"><input type="submit" name="recalculer" value="<?php echo htmlspecialchars($plxPlugin->getLang('L_PANIER_RECALCULER'));?>" /></td>
          <td class="nombre"><?php $plxPlugin->lang('L_TOTAL_BASKET');?>&nbsp;:</td>
          <td class="nombre"><?php echo $plxPlugin->pos_devise($totalpricettc);?></td>
         </tr>
<?php
        $totalpoidgshipping = 0;
        if($plxPlugin->getParam("shipping_colissimo")){ ?>
         <tr>
          <td class="nombre" colspan="5"><?php $totalpoidgshipping = $plxPlugin->shippingMethod($totalpoidg, $totalpricettc); ?></td>
         </tr>
         <tr>
          <td class="nombre" colspan="4"><?php echo $plxPlugin->getLang('L_EMAIL_DELIVERY_COST').($plxPlugin->getParam("shipping_by_price") ? "" : ($totalpoidg?" ".$plxPlugin->getLang("L_FOR")." ".$totalpoidg."&nbsp;kg":""));?>&nbsp;:</td>
          <td class="nombre" id="spanshipping"><?php echo $plxPlugin->pos_devise($totalpoidgshipping);?></td>
         </tr>
         <tr class="msgyeah2">
          <td class="nombre" colspan="4"><?php echo htmlspecialchars($plxPlugin->getLang('L_TOTAL_BASKET').
           (($plxPlugin->getParam("shipping_colissimo"))?$plxPlugin->getLang('L_TOTAL_BASKET_PORT'):''));?>&nbsp;:</td>
          <td class="nombre" id='totalCart'><?php echo $plxPlugin->pos_devise($totalpricettc + $totalpoidgshipping);?></td>
         </tr>
<?php   } ?>
        </table>
<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierFormProdsFin')); # Hook Plugins ?>
        <noscript><p class="red"><?php $plxPlugin->lang('L_PUBLIC_NOJS'); ?></p></noscript>
       </form>
<?php
     } //fin isset($_SESSION[$plxPlugin->plugName]['prods']) && $_SESSION[$plxPlugin->plugName]['prods']
    if (0 === $nprod && !$afficheMessage) {?>
     <em><?php $plxPlugin->lang('L_PUBLIC_NOPRODUCT'); ?></em>
<?php } ?>
   </div>
   <form id="formcart" method="POST" action="#panier">
<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsDebut')) # Hook Plugins ?>
    <p class="tal"><span class="startw"><?php $plxPlugin->lang('L_PUBLIC_MANDATORY_FIELD'); ?></span></p>

    <p class="fifty fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_FIRSTNAME'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="firstname" id="firstname" value="" required="required" /></p>
    <p class="fifty fl tal"><?php $plxPlugin->lang('L_PUBLIC_LASTNAME'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="lastname" id="lastname" value="" required="required" /></p><br class="clear" />

    <p class="fifty fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_EMAIL'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="email" name="email" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,}" id="email" value="" required="required" /></p>

    <p class="fifty fl tal"><?php $plxPlugin->lang('L_PUBLIC_TEL'); ?>&nbsp;:<br />
    <input type="text" name="tel" id="tel" value=""></p><br class="clear" />

    <p class="ninety fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_ADDRESS'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="adress" id="adress" value="" required="required" /></p><br class="clear" />

    <p class="thirty fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_ZIP'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="postcode" id="postcode" value="" required="required" /></p>
    <p class="forty fl tal"><?php $plxPlugin->lang('L_PUBLIC_TOWN'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="city" id="city" value=""  required="required"></p>
    <p class="twenty fl tal"><?php $plxPlugin->lang('L_PUBLIC_COUNTRY'); ?><span class='star'>*</span>&nbsp;:<br />
    <input type="text" name="country" id="country" value="" required="required" /></p><br class="clear" />


<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsMilieu')) # Hook Plugins ?>

    <?php    if($plxPlugin->getParam("delivery_date")){ ?>
    <p class="fifty fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_DELIVERYDATE'); ?><span class='star'>*</span>&nbsp;:<br />
    <!-- <input type="text" name="deliverydate" id="datepicker" required="required" /></p> -->
    <?php plxUtils::printInput('deliverydate',$var['deliverydate'], 'text','',false,'classOrNot" required="required') ?></p>

<?php

#Creation of the time intervals based on the configuration
$firstTime = strtotime($this->getParam("delivery_start_time"));
$lastTime = strtotime($this->getParam("delivery_end_time"));
$interval = $this->getParam("delivery_nb_timeslot")." hours";
$time=$firstTime;
$intervals['']="";
while ($time < $lastTime) {
        $from = date('H:i', $time) . " - ";
        #$time = strtotime('+1 hours', $time);
        $time = strtotime($interval, $time);
        if ($time > $lastTime) {
            $to = date('H:i', $lastTime) . "<br>"; }
        else {
            $to =  date('H:i', $time) . "<br>";}
        $intervals[$from.$to]=$from.$to;
}
?>

<p class="fifty fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_DELIVERYTIME'); ?><span class='star'>*</span>&nbsp;:<br />
<!-- 
<select name="delivery_interval" id="delivery_interval" required="required">
    <?php foreach ($intervals as $interval) { ?>
    <option value="<?php echo $interval; ?>"><?php echo $interval; ?></option>
    <?php } ?>
</select>
-->
<?php plxUtils::printSelect('delivery_interval',$intervals, 2,false,'classOrNot" required="required') ?>
</p> <br class="clear" /><br class="clear" />

    <?php } ?>

    <p>
     <label for="choixCadeau">
      <input type="checkbox" id="choixCadeau" name="choixCadeau"<?php echo (!isset($_POST["choixCadeau"])) ? '' : ' checked="checked"';?> />
      <?php $plxPlugin->lang('L_PUBLIC_GIFT'); ?>
     </label>
    </p>

    <p class="conteneurNomCadeau ninety fl pl" id="conteneurNomCadeau"<?php echo (!isset($_POST["choixCadeau"])) ? '' : ' style="display:block;"';?>>
     <label for="nomCadeau"><?php $plxPlugin->lang('L_PUBLIC_GIFTNAME'); ?>&nbsp;:</label>
      <input type="text" name="nomCadeau" id="nomCadeau" value="<?php echo (!isset($_POST["nomCadeau"])) ? '' : htmlspecialchars($_POST['nomCadeau']);?>" />
    </p>
    <p class="ninety fl tal pl"><?php $plxPlugin->lang('L_PUBLIC_COMMENT'); ?><br />
    <textarea name="msg" id="msgCart" rows="3"></textarea></p><br class="clear" />
    <textarea name="prods" id="prodsCart" rows="3"></textarea>
    <input type="hidden" name="total" id="totalcommand" value="0" />
    <input type="hidden" name="shipping" id="shipping" value="0" />
    <input type="hidden" name="shipping_kg" id="shipping_kg" value="0" />
    <input type="hidden" name="idsuite" id="idsuite" value="0" />
    <input type="hidden" name="numcart" id="numcart" value="0" />
    <?php $plxPlugin->lang('L_EMAIL_CUST_PAYMENT'); ?>&nbsp;:&nbsp;&nbsp;
    <select name="methodpayment" id="methodpayment">
<?php
      $methodpayment = !isset($_SESSION[$plxPlugin->plugName]["methodpayment"]) ? "" : $_SESSION[$plxPlugin->plugName]["methodpayment"];
      #if amount of order is below paypal amount then remove from payment options
	  if ($totalpricettc <= $paypal_amount) {
		  	unset($d["tabChoixMethodespaiement"][paypal]);
		 }
      foreach ($d["tabChoixMethodespaiement"] as $codeM => $m) { ?>
      <option value="<?php echo htmlspecialchars($codeM);?>"<?php
       echo ($codeM !== $methodpayment) ? "" : ' selected="selected"';
      ?>><?php echo htmlspecialchars($m["libelle"]);?></option>
<?php } ?>
    </select>
    <br />
<?php if ("" !== $plxPlugin->getParam("urlCGV")) {?>
     <label for="valideCGV">
      <input type="checkbox" name="valideCGV" id="valideCGV"<?php echo (!isset($_POST["valideCGV"])) ? "" : " checked=\"checked\"";?>  required="required" />
      <span class='star'>*</span>
      <a href="<?php echo $plxPlugin->plxMotor->urlRewrite($plxPlugin->getParam("urlCGV"));?>"><?php echo htmlspecialchars((empty($plxPlugin->getParam('useLangCGVDefault')))?$plxPlugin->getParam('libelleCGV'):$plxPlugin->getLang('L_COMMANDE_LIBELLE_DEFAUT'));?></a>
     </label>
<?php } ?>
    <input type="submit" class="green" name="validerCommande" id="btnCart" value="<?php $plxPlugin->lang('L_PUBLIC_VALIDATE_ORDER'); ?>" /><br />
   </form>
<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsFin')) # Hook Plugins ?>
  </section>
 </div>
</div>
<?php $def_lng = $plxPlugin->plxMotor->aConf['default_lang']; ?>
<script type='text/javascript' src='<?php echo $plxPlugin->plxMotor->racine . PLX_PLUGINS.$plxPlugin->plugName;?>/js/panier.js?v0131'></script>
<script type='text/javascript' src='<?php echo $d["plxPlugin"]->plxMotor->racine . PLX_PLUGINS;?>plxMyShop/js/moment-<?php echo $def_lng!='en' ? 'with-locales' : ''; ?>.min.js'></script>
<script type='text/javascript' src='<?php echo $d["plxPlugin"]->plxMotor->racine . PLX_PLUGINS;?>plxMyShop/js/pikaday.js'></script>
<script type='text/javascript'>
function includeCSSfile(href) {
    var head_node = document.getElementsByTagName('head')[0];
    var link_tag = document.createElement('link');
    link_tag.setAttribute('rel', 'stylesheet');
    link_tag.setAttribute('type', 'text/css');
    link_tag.setAttribute('href', href);
    head_node.appendChild(link_tag);
}
includeCSSfile("<?php echo $d["plxPlugin"]->plxMotor->racine . PLX_PLUGINS;?>plxMyShop/css/pikaday.css")

var mindays= <?php echo $plxPlugin->getParam("delivery_nb_days"); ?>;
var today = new Date();
var nextdelivery = new Date();
nextdelivery.setDate(today.getDate() + mindays);
<?php echo $def_lng!='en' ? "moment.locale('".$def_lng."');" : ''; ?>
var picker_date = new Pikaday(
    {
        field: document.getElementById('id_deliverydate'),
        format: '<?php $plxPlugin->lang("L_FORMAT_PIKADAY"); ?>',
<?php if($def_lng!='en')$plxPlugin->lang("L_I18N_PIKADAY"); ?>
        firstDay: 1,
        minDate: nextdelivery,
        maxDate: new Date(<?php echo (date('Y')+3) ?>, 12, 31),
        yearRange: [<?php echo date('Y') ?>,<?php echo (date('Y')+3) ?>],
        onSelect: function() {
            var date = document.createTextNode(this.getMoment() + ' ');
        }
    }
);
</script>

<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierFin')) # Hook Plugins ?>
