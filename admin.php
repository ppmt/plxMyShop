<?php if (!defined('PLX_ROOT')) exit;
/**
 * Edition des produits
 * @package PLX
 * @author    David L
 **/

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Control de l'accès à la page en fonction du profil de l'utilisateur connecté
$plxAdmin->checkProfil(PROFIL_ADMIN, PROFIL_MANAGER);

# On édite les produits
$fakeget=$onglet='';
if(!empty($_POST)){
 $plxPlugin->editProducts($_POST, true);
 if (isset($_POST['prod']) && !empty($_POST['prod'])){
  $plxPlugin->editProduct($_POST);
  $fakeget='&prod='.$_POST['prod'];
 } else {
  $plxPlugin->editProducts($_POST);
  $fakeget=(isset($_GET['mod']) && !empty($_GET['mod'])?'&mod='.$_GET['mod']:'');
 }
 header('Location: plugin.php?p='.$plxPlugin->plugName.$fakeget);
 exit;
}

$dir = PLX_ROOT.(!$plxPlugin->getParam('racine_commandes')?'data/commandes/':$plxPlugin->getParam('racine_commandes'));
if (isset($_GET['kill']) && !empty($_GET['kill']) && is_file($dir.$_GET['kill'])){
 unlink($dir.$_GET['kill']);
 header('Location: plugin.php?p='.$plxPlugin->plugName.'&mod=cmd');
}

if ((isset($_GET['prod']) && !empty($_GET['prod'])) || (isset($_POST['prod']) && !empty($_POST['prod'])))
 include(dirname(__FILE__).'/template/editionProduitAdmin.php');
else {
# On inclut le header
//include(dirname(__FILE__).'/top.php');
?>
<script type="text/javaScript">
function checkBox(obj){
 obj.value = (obj.checked==true) ? '1': '0';
}
</script>
<?php
 if (!isset($_GET["mod"])){
  $onglet = "produits";
  $titre = $plxPlugin->getLang("CREATE_PRODUCTS");
 }elseif("cat" === $_GET["mod"]){
  $onglet = "categories";
  $titre = $plxPlugin->getLang("CREATE_CATS");
 }elseif("cmd" === $_GET["mod"]){
  $onglet = "commandes";
  $titre = $plxPlugin->getLang("LIST_ORDERS");
 }
?>

<h2 id="pmsTitle" class="page-title"><?php echo plxUtils::strCheck($titre);?></h2>
<script type="text/javascript">//surcharge du titre dans l'admin
 var title = document.getElementById('pmsTitle');
 title.className += " hide";
 document.getElementsByClassName('inline-form')[0].firstChild.nextSibling.innerHTML = '<?php echo $plxPlugin->plugName;?> - '+title.innerHTML;
</script>
<p class="in-action-bar plx<?php echo str_replace('.','-',@PLX_VERSION); echo defined('PLX_MYMULTILINGUE')?' multilingue':'';?>"><?php $plxPlugin->menuAdmin($onglet);?></p>

<form action="plugin.php?p=<?php echo $plxPlugin->plugName.(isset($_GET['mod']) && $_GET['mod']=='cat'?"&amp;mod=cat":""); ?>" method="post" id="form_products">
 <?php if (!isset($_GET['mod']) || (isset($_GET['mod']) && $_GET['mod']!='cmd')): ?>
  <p>
   <?php echo plxToken::getTokenPostMethod() ?>
   <?php plxUtils::printSelect('selection', array( '' =>L_FOR_SELECTION, 'delete' =>L_DELETE), '', false, '', 'id_selection') ?>
   <input class="button submit" type="submit" name="submit" value="<?php echo L_OK ?>" onclick="return confirmAction(this.form, 'id_selection', 'delete', 'idProduct[]', '<?php echo L_CONFIRM_DELETE ?>')" />
   <input class="button update" type="submit" name="update" value="<?php $plxPlugin->lang('L_ADMIN_MODIFY') ?> <?php echo (isset($_GET['mod']) && $_GET['mod']=='cat'?$plxPlugin->getlang('L_CATEGORIES'):$plxPlugin->getlang('L_PRODUCTS')); ?>" />
  </p>
 <?php endif; ?>
  <div class="scrollable-table">
   <table id="myShop-table" class="table full-width listeCategoriesProduitsAdmin liste<?php echo (isset($_GET['mod']) && $_GET['mod']=='cat'?"Categories":"Produits");?>Admin display responsive no-wrap" width="100%">
    <thead>
     <tr>
<?php if (!isset($_GET['mod']) || (isset($_GET['mod']) && $_GET['mod']!='cmd')): ?>
      <th><input type="checkbox" onclick="checkAll(this.form, 'idProduct[]')" /></th>
      <th><?php $plxPlugin->lang('L_PRODUCTS_ID') ?></th>
      <th></th>
      <th><?php $plxPlugin->lang('L_PRODUCTS_TITLE') ?></th>
      <th><?php $plxPlugin->lang('L_PRODUCTS_URL') ?></th>
 <?php if (isset($_GET['mod']) && $_GET['mod']=='cat'){ ?>
      <th><?php $plxPlugin->lang('L_CATEGORIE_ACTIVE') ?></th>
 <?php } else { ?>
      <th><?php $plxPlugin->lang('L_PRODUIT_ACTIF') ?></th>
 <?php } ?>
      <th><?php $plxPlugin->lang('L_PRODUCTS_ORDER') ?></th>
 <?php if (isset($_GET['mod']) && $_GET['mod']=='cat'){ ?>
      <th><?php $plxPlugin->lang('L_PRODUCTS_MENU')?></th>
 <?php } else { ?>
      <th><?php $plxPlugin->lang('L_PRODUCTS_PRICE')?></th>
      <th><?php $plxPlugin->lang('L_PRODUCTS_WEIGHT')?></th>
 <?php } ?>
      <th><?php $plxPlugin->lang('L_PRODUCTS_ACTION') ?></th>
 <?php else: ?>
      <th><?php $plxPlugin->lang('L_DATE') ?></th>
      <th><?php $plxPlugin->lang('L_PAIEMENT') ?></th>
      <th><?php $plxPlugin->lang('L_MONTANT') ?></th>
      <th><?php $plxPlugin->lang('L_ACTIONS') ?></th>
<?php endif; ?>
     </tr>
    </thead>
   <tbody>
<?php
    # Initialisation de l'ordre
    $num = 0;
    # Si on a des produits
 if($plxPlugin->aProds){
  foreach($plxPlugin->aProds as $k=>$v){ # Pour chaque produit
   $url=$v['url'];
   if ((isset($_GET['mod']) && $_GET['mod']=='cat' && $v['pcat']!=1)||(isset($_GET['mod']) && $_GET['mod']=='cmd'))continue;
   if (!isset($_GET['mod']) && $v['pcat']==1)continue;

   $ordre = ++$num;
   $selected = $v['pcat']==1 ? ' checked="checked"' : '';
   $valued = $v['pcat']==1 ? '1' : '0';
   $noaddcartImg = ($v['pcat']!=1 ? '<img class="noaddcartImg" src="'.PLX_PLUGINS.$plxPlugin->plugName.'/images/'.(empty($v['noaddcart'])?'full':'empty').'.png" />' : '');
   $noaddcartTit = (empty($v['noaddcart'])?'':PHP_EOL.htmlspecialchars($plxPlugin->getLang('L_PRODUCTS_BASKET_BUTTON')));
   echo '
   <tr class="line-'.($num%2).'">
    <td><input type="checkbox" name="idProduct[]" value="'.$k.'" /><input type="hidden" name="productNum[]" value="'.$k.'" /></td>
    <td><a href="plugin.php?p='.$plxPlugin->plugName.'&amp;prod='.$k.'" title="'.$plxPlugin->getLang('L_PRODUCTS_SRC_TITLE').$noaddcartTit.'">'.$k.$noaddcartImg.'</a>
    <input type="hidden" name="'.$k.'_pcat" value="'.$valued.'"'.$selected.' onclick="checkBox(this);" />
   </td>'.PHP_EOL;
?>
   <td>
<?php
   $image = $v["image"];
   echo '<a href="plugin.php?p='.$plxPlugin->plugName.'&amp;prod='.$k.'" title="'.$plxPlugin->getLang('L_PRODUCTS_SRC_TITLE').$noaddcartTit.'"><img class="product_image" src="'.($image!=""?PLX_ROOT.$plxPlugin->cheminImages.$image:PLX_PLUGINS.$plxPlugin->plugName.'/images/none.png').'" /></a>';
?>
   </td>
<?php
   echo '<td>'.PHP_EOL;
   plxUtils::printInput($k.'_name', plxUtils::strCheck($v['name']), 'text', '20-255');
   echo '</td><td>'.PHP_EOL;
   plxUtils::printInput($k.'_url', $v['url'], 'text', '12-255');
   echo '</td><td>'.PHP_EOL;
   plxUtils::printSelect($k.'_active', array('1'=>L_YES,'0'=>L_NO), $v['active']);
   echo '</td><td>'.PHP_EOL;
   plxUtils::printInput($k.'_ordre', $ordre, 'text', '2-3');
   echo '</td>'.PHP_EOL;

   if ($v['pcat']==1){
    echo '<td>';
    plxUtils::printSelect($k.'_menu', array('oui'=>L_DISPLAY,'non'=>L_HIDE), $v['menu']);
    echo '</td>'.PHP_EOL;
   } else {
    echo '<td class="nombre">';
    if ($v["pricettc"] > 0){
     echo $plxPlugin->pos_devise($v["pricettc"]);
    }
    echo '</td>'.PHP_EOL;
    echo '<td class="nombre">';
    if ($v["poidg"] > 0){
     echo $v["poidg"];
    }
    echo '</td>'.PHP_EOL;
   }

   if(!plxUtils::checkSite($v['url'])){
    echo '<td>';
    echo '<a href="plugin.php?p='.$plxPlugin->plugName.'&amp;prod='.$k.'" title="'.$plxPlugin->getLang('L_PRODUCTS_SRC_TITLE').$noaddcartTit.'">'.$plxPlugin->getLang('L_PRODUCTS_SRC').'</a>';
    if($v['active']){
     echo '&nbsp;-&nbsp;<a href="'.$plxAdmin->urlRewrite('index.php?product'.intval($k).'/'.$url).'" title="'.sprintf($plxPlugin->getLang('L_VIEW_ONLINE'), plxUtils::strCheck($v['name'])).'">'.L_VIEW.'</a>';
    }
    echo '</td></tr>'.PHP_EOL;
   }
   elseif($url[0]=='?')
    echo '</td><td>b <a href="'.$plxAdmin->urlRewrite('index.php?product'.intval($k).'/'.$url).'" title="'.plxUtils::strCheck($v['name']).'">'.L_VIEW.'</a></td></tr>'.PHP_EOL;
   else
    echo '</td><td>c <a href="'.$plxAdmin->urlRewrite('index.php?product'.intval($k).'/'.$url).'" title="'.plxUtils::strCheck($v['name']).'">'.L_VIEW.'</a></td></tr>'.PHP_EOL;
  }
  # On récupère le dernier identifiant
  $a = array_keys($plxPlugin->aProds);
  rsort($a);
 } else {
  $a['0'] = 0;
 }
 $new_productid = str_pad($a['0']+1, 3, "0", STR_PAD_LEFT);
 if (!isset($_GET['mod']) || (isset($_GET['mod']) && $_GET['mod']!='cmd')): ?>
  <tr class="new">
   <td>&nbsp;<?php echo '<input type="hidden" name="productNum[]" value="'.$new_productid.'" />'; ?></td>
   <td><?php echo (isset($_GET['mod']) && $_GET['mod']=='cat'?$plxPlugin->getlang('L_NEW_CATEGORY'):$plxPlugin->getlang('L_NEW_PRODUCT')); ?></td>
   <td><input title="<?php $plxPlugin->lang('L_CAT') ?><?php echo '" type="hidden" name="'.$new_productid.'_pcat" value="'.(isset($_GET['mod']) && $_GET['mod']=='cat'?'1':'0').'" '.(isset($_GET['mod']) && $_GET['mod']=='cat'?'checked':'').' onclick="checkBox(this);" /></td>'.PHP_EOL;
    echo '<td>';
    plxUtils::printInput($new_productid.'_name', '', 'text', '20-255');
    plxUtils::printInput($new_productid.'_template', $plxPlugin->getParam('template'), 'hidden');
    echo '</td><td>'.PHP_EOL;
    plxUtils::printInput($new_productid.'_url', '', 'text', '12-255');
    echo '</td><td>'.PHP_EOL;
    plxUtils::printSelect($new_productid.'_active', array('1'=>L_YES,'0'=>L_NO), '0');
    echo '</td><td>'.PHP_EOL;
    plxUtils::printInput($new_productid.'_ordre', ++$num, 'text', '2-3');
    echo '</td>'.PHP_EOL;
    if (isset($_GET['mod']) && $_GET['mod']=='cat'){
     echo '<td>';
     plxUtils::printSelect($new_productid.'_menu', array('oui'=>L_DISPLAY,'non'=>L_HIDE), '0');
     echo '</td>'.PHP_EOL;
    } else {
     echo '<td colspan="3">&nbsp;</td>'.PHP_EOL;
    }
?>
  </tr>
<?php else:

 $dh  = opendir($dir);
 $filescommande= array();
 while (false !== ($filename = readdir($dh))){
  if (is_file($dir.$filename) && $filename!='.' && $filename!='..' && $filename!='index.html'){
   $filescommande[] = $filename;
  }
 }
 rsort($filescommande);
 while (list ($key, $val) = each ($filescommande) ){
  $namearray=explode('_',$val);
  $date=implode('/',explode('-',$namearray[0]));
  echo '<tr>'.PHP_EOL.
   '   <td id="dateTime">'.$date.' - '.str_replace('-',':',$namearray[1]).'</td>'.PHP_EOL.
   '   <td>'.$namearray[2].'</td>'.PHP_EOL.
   '   <td class="nombre">'.$plxPlugin->pos_devise((float)$namearray[3]+(float)preg_replace('/.html/','',$namearray[4])).'</td>'.PHP_EOL.
   '   <td><a onclick="if(confirm(\''.$plxPlugin->getlang('L_ADMIN_CONFIRM_DELETE').'\')) return true; else return false;" href="plugin.php?p='.$plxPlugin->plugName.'&amp;mod=cmd&amp;kill='.$val.'">'.$plxPlugin->getlang('L_ADMIN_ORDER_DELETE').'</a> - <a target="_blank" href="'.$dir.$val.'" data-featherlight-target="'.$dir.$val.'" data-featherlight="iframe" data-featherlight-iframe-allowfullscreen="true">'.$plxPlugin->getlang('L_ADMIN_ORDER_VIEW').'</a></td>'.PHP_EOL.
   '</tr>';
 };

 endif; ?>
    </tbody>
   </table>
  </div>
</form>
<?php }
if($onglet=='commandes')
 include('datatables.js.php');