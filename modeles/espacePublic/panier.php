<?php if (!defined('PLX_ROOT')) exit;
/*
Si vous réutilisez ce fichier dans votre thème, nous vous conseillons de noter la version actuelle de plxMyShop
version : 
*/
$plxPlugin = $d["plxPlugin"];
$plxPlugin->traitementPanier();
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
   <header>
    <?php
     $plxPlugin->lang('L_PUBLIC_BASKET');
     if ($afficheMessage) {
      echo '<br />'.$message;
      }
    ?>
   </header>
   <div id="shoppingCart">
    <?php
     $sessioncart="";
     $totalpricettc=0;
     $totalpoidg=0;
     $totalpoidgshipping = 0;
     $nprod=0;

     if (isset($_SESSION[$plxPlugin->plugName]['prods']) && $_SESSION[$plxPlugin->plugName]['prods']) { ?>

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
           $maxPnr = $plxPlugin->aProds[$pId]['iteminstock'] != '' ? 'max="'.$plxPlugin->aProds[$pId]['iteminstock'].'" ' : '';
         ?>
         <tr>
          <td><a href="<?php echo $plxPlugin->productRUrl($pId); ?>"><?php echo plxUtils::strCheck($plxPlugin->aProds[$pId]['name']); ?></a></td>
          <td class="nombre"><?php echo $plxPlugin->pos_devise($prixUnitaire);?></td>
          <td width="10%"><input type="number" name="nb[<?php echo $pId;?>]" value="<?php echo htmlspecialchars($nb);?>" min="0" <?php echo $maxPnr; ?>/></td>
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

   <p class="tal"><span class="startw"><?php $plxPlugin->lang('L_PUBLIC_MANDATORY_FIELD'); ?></span></p>

   <form id="formcart" method="POST" action="#panier">
     <?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsDebut')) # Hook Plugins ?>
     <fieldset>
      <legend>Your details</legend>
      <ol>
       <li>
        <label for=name><?php $plxPlugin->lang('L_PUBLIC_FIRSTNAME'); ?><span class='star'>*</span>&nbsp;:</label>
        <input type="text" name="firstname" id="firstname" value="" required="required" />
       </li>
       <li>
        <label for=firstname><?php $plxPlugin->lang('L_PUBLIC_LASTNAME'); ?><span class='star'>*</span>&nbsp;:</label>
        <input type="text" name="lastname" id="lastname" value="" required="required" />
       </li>
       <li>
        <label for=email><?php $plxPlugin->lang('L_PUBLIC_EMAIL'); ?><span class='star'>*</span>&nbsp;:</label>
        <input type="email" name="email" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,}" id="email" value="" required="required" />
       </li>
       <li>
        <label for=phone><?php $plxPlugin->lang('L_PUBLIC_TEL'); ?>&nbsp;:</label>
        <input type="text" name="tel" id="tel" value="">
       </li>
      </ol>
     </fieldset>

     <fieldset>
      <legend>Delivery address</legend>
      <ol>
       <li>
        <label for=address><?php $plxPlugin->lang('L_PUBLIC_ADDRESS'); ?><span class='star'>*</span>&nbsp;:</label>
        <input type="text" name="adress" id="adress" value="" required="required" />
       </li>
       <li>
        <label for=postcode><?php $plxPlugin->lang('L_PUBLIC_ZIP'); ?><span class='star'>*</span>&nbsp;:<br /></label>
        <input type="text" name="postcode" id="postcode" value="" required="required" />
       </li>
       <li>
        <label for=town><?php $plxPlugin->lang('L_PUBLIC_TOWN'); ?><span class='star'>*</span>&nbsp;:<br /></label>
        <input type="text" name="city" id="city" value=""  required="required">
       </li>
       <li>
        <label for=country><?php $plxPlugin->lang('L_PUBLIC_COUNTRY'); ?><span class='star'>*</span>&nbsp;:<br /></label>
        <input type="text" name="country" id="country" value="" required="required" />
       </li>
      <?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsMilieu')) # Hook Plugins to display the saving of details ?>
     </fieldset>

        <?php if($plxPlugin->getParam("delivery_date")){ ?>
     <fieldset>
      <legend>Delivery dates</legend>
      <ol>
       <li>
        <?php $plxPlugin->lang('L_PUBLIC_DELIVERYDATE'); ?><span class='star'>*</span>&nbsp;:<br />
        <?php plxUtils::printInput('deliverydate',$var['deliverydate'], 'text','',false,'classOrNot" required="required') ?>
       </li>  

       <?php
        #Creation of the time intervals based on the configuration
        $firstTime = strtotime($this->getParam("delivery_start_time"));
        $lastTime = strtotime($this->getParam("delivery_end_time"));
        $interval = $this->getParam("delivery_nb_timeslot")." hours";
        $time=$firstTime;
        $intervals['']="";
        while ($time < $lastTime) {
            $from = date('H:i', $time) . " - ";
            $time = strtotime($interval, $time);
            if ($time > $lastTime) {
                $to = date('H:i', $lastTime) . "<br>"; }
            else {
                $to =  date('H:i', $time) . "<br>";}
            $intervals[$from.$to]=$from.$to;
         } ?>
       <li>
        <?php $plxPlugin->lang('L_PUBLIC_DELIVERYTIME'); ?><span class='star'>*</span>&nbsp;:<br />
        <?php plxUtils::printSelect('delivery_interval',$intervals, 2,false,'classOrNot" required="required') ?>
       </li>
      </ol>
     </fieldset>
       <?php } ?>

     <fieldset>
      <legend>Optional</legend>
      <ol>
       <li>
        <label for="choixCadeau"><?php $plxPlugin->lang('L_PUBLIC_GIFT'); ?></label>
        <input type="checkbox" id="choixCadeau" name="choixCadeau"<?php echo (!isset($_POST["choixCadeau"])) ? '' : ' checked="checked"';?> />
        <label for="nomCadeau" class="conteneurNomCadeau ninety fl pl" id="conteneurNomCadeau"<?php echo (!isset($_POST["choixCadeau"])) ? '' : ' style="display:block;"';?>> <?php $plxPlugin->lang('L_PUBLIC_GIFTNAME'); ?>&nbsp;:
        <input type="text" name="nomCadeau" id="nomCadeau" value="<?php echo (!isset($_POST["nomCadeau"])) ? '' : htmlspecialchars($_POST['nomCadeau']);?>" /></label>
       </li>
       <li>
        <label for=comment><?php $plxPlugin->lang('L_PUBLIC_COMMENT'); ?></label>
        <textarea name="msg" id="msgCart" rows="3"></textarea>
        <textarea name="prods" id="prodsCart" rows="3"></textarea>
       </li>
      </ol>  
     </fieldset>

	 <fieldset>
      <legend> <?php $plxPlugin->lang('L_EMAIL_CUST_PAYMENT'); ?></legend>
      <input type="hidden" name="total" id="totalcommand" value="0" />
      <input type="hidden" name="shipping" id="shipping" value="0" />
      <input type="hidden" name="shipping_kg" id="shipping_kg" value="0" />
      <input type="hidden" name="idsuite" id="idsuite" value="0" />
      <input type="hidden" name="numcart" id="numcart" value="0" />

      <ol>
       <li>
        <fieldset>
         <legend> <?php $plxPlugin->lang('L_EMAIL_CUST_SELECT_PAYMENT'); ?></legend>
          <ol>
           <li>
        	 <?php
              $methodpayment = !isset($_SESSION[$plxPlugin->plugName]["methodpayment"]) ? "" : $_SESSION[$plxPlugin->plugName]["methodpayment"];
              #if amount of order is below paypal amount then remove from payment options
	          if ($totalpricettc <= $paypal_amount) {
		  	    unset($d["tabChoixMethodespaiement"][paypal]);
		       }
              foreach ($d["tabChoixMethodespaiement"] as $codeM => $m) { ?>
                <input id="<?php echo htmlspecialchars($codeM);?>" name=methodpayment type=radio value="<?php echo htmlspecialchars($codeM);?>" required="required">
                <label for="<?php echo htmlspecialchars($codeM);?>"><?php echo htmlspecialchars($m["libelle"]);?></label>
              <?php } ?>
           </li>
		  </ol>
        </fieldset>
       </li>
        <fieldset>
          <?php if ("" !== $plxPlugin->getParam("urlCGV")) {?>
           <label for="valideCGV">
            <span class='star'>*</span>
            <a href="<?php echo $plxPlugin->plxMotor->urlRewrite($plxPlugin->getParam("urlCGV"));?>"><?php echo htmlspecialchars((empty($plxPlugin->getParam('useLangCGVDefault')))?$plxPlugin->getParam('libelleCGV'):$plxPlugin->getLang('L_COMMANDE_LIBELLE_DEFAUT'));?></a>
           </label>
           <input type="checkbox" name="valideCGV" id="valideCGV"<?php echo (!isset($_POST["valideCGV"])) ? "" : " checked=\"checked\"";?>  required="required" />
          <?php } ?>
        </fieldset>
      </ol>
  </fieldset>

    <input type="submit" class="green" name="validerCommande" id="btnCart" value="<?php $plxPlugin->lang('L_PUBLIC_VALIDATE_ORDER'); ?>" /><br />
   </form>

   <?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierCoordsFin')) # Hook Plugins ?>
  </section>
 </div>
</div>

<script type='text/javascript' src='<?php echo $plxPlugin->plxMotor->racine . PLX_PLUGINS.$plxPlugin->plugName;?>/js/panier.js?v0131'></script>
<?php eval($plxPlugin->plxMotor->plxPlugins->callHook('plxMyShopPanierFin')) # Hook Plugins ?>
