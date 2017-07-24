<?php if (!defined('PLX_ROOT')) exit;

/**
 * Edition du code source d'un produit
 *
 * @package PLX
 * @author David L
 **/

# Liste des langues disponibles et prises en charge par le plugin
$aLangs = array($plxAdmin->aConf['default_lang']);

# Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
# On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
if($plxPlugin->aLangs) {
 $aLangs = $plxPlugin->aLangs;
}

# On édite le produit
if(!empty($_POST) AND isset($plxPlugin->aProds[$_POST['id']])) {
 $plxPlugin->editProduct($_POST);
 header('Location: plugin.php?p='.$plxPlugin->plugName.'&amp;prod='.$_POST['id']);
 exit;
} elseif(!empty($_GET['prod'])) { # On affiche le contenu de la page
 $id = plxUtils::strCheck(plxUtils::nullbyteRemove($_GET['prod']));
 if(!isset($plxPlugin->aProds[ $id ])) {
  plxMsg::Error(L_PRODUCT_UNKNOWN_PAGE);
  header('Location: plugin.php?p='.$plxPlugin->plugName);
  exit;
 }
 # On récupère le contenu
 foreach ($aLangs as $lang) {
  $content[$lang] = trim($plxPlugin->getFileProduct($id,$lang));
 }
 $image = $plxPlugin->aProds[$id]['image'];
 $pricettc = $plxPlugin->aProds[$id]['pricettc'];
 $pcat = $plxPlugin->aProds[$id]['pcat'];
 $poidg = $plxPlugin->aProds[$id]['poidg'];
 $title = $plxPlugin->aProds[$id]['name'];
 $url = $plxPlugin->aProds[$id]['url'];
 $active = $plxPlugin->aProds[$id]['active'];
 $stockmgmt = $plxPlugin->aProds[$id]['stock_mgmnt'];
 $iteminstock = $plxPlugin->aProds[$id]['iteminstock'];
 $noaddcart = $plxPlugin->aProds[$id]['noaddcart'];
 $notice_noaddcart = $plxPlugin->aProds[$id]['notice_noaddcart'];
 $title_htmltag = $plxPlugin->aProds[$id]['title_htmltag'];
 $meta_description = $plxPlugin->aProds[$id]['meta_description'];
 $meta_keywords = $plxPlugin->aProds[$id]['meta_keywords'];
 $template = $plxPlugin->aProds[$id]['template'];
} else { # Sinon, on redirige
 header('Location: products.php');
 exit;
}
# On récupère les templates du produit
$files = plxGlob::getInstance(PLX_ROOT.$plxAdmin->aConf['racine_themes'].$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
 foreach($array as $k=>$v)
  $aTemplates[$v] = $v;
}

$modProduit = ("1" !== $pcat);

if (!isset($_SESSION)) {// inutile?
 session_start();
}
$_SESSION[$plxPlugin->plugName]["cheminImages"] = realpath(PLX_ROOT . $plxPlugin->cheminImages);
$_SESSION[$plxPlugin->plugName]["urlImages"] = $plxAdmin->urlRewrite($plxPlugin->cheminImages);

?>
<p class="in-action-bar return-link plx<?php echo str_replace('.','-',@PLX_VERSION); echo $plxPlugin->aLangs?' multilingue':'';?>">
 <a href="plugin.php?p=<?php echo $plxPlugin->plugName.($modProduit ? '' : '&mod=cat');?>"><?php
  echo $plxPlugin->lang($modProduit ? 'L_PRODUCT_BACK_TO_PAGE' : 'L_CAT_BACK_TO_PAGE');
?></a>
</p>

<h3 id="pmsTitle" class="page-title">
 <?php $plxPlugin->lang($modProduit ? 'L_PRODUCT_TITLE' : 'L_CAT_TITLE');?>
 &laquo;<?php echo plxUtils::strCheck($title);?>&raquo;
</h3>
<script type="text/javascript">//surcharge du titre dans l'action bar
 var title = document.getElementById('pmsTitle');
 title.className += " hide";
 document.getElementsByClassName('inline-form')[0].firstChild.nextSibling.innerHTML = '<?php echo $plxPlugin->plugName; ?> - '+title.innerHTML;
</script>

<div class="grid">
 <div class="col sml-12 med-6">
  <p class="informationsShortcodeProduit"><?php $plxPlugin->lang('L_PRODUCTS_SHORTCODE'); ?>&nbsp;:<br/>
  <span class="code">[<?php echo $plxPlugin->shortcode.'&nbsp;'.$id;?>]</span></p>
 </div>
</div>

<?php eval($plxAdmin->plxPlugins->callHook('AdminProductTop')); // hook plugin ?>
<form action="plugin.php?p=<?php echo $plxPlugin->plugName; ?>" method="post" id="form_article">
 <div class="grid" id="tabContainer">
  <fieldset class="col sml-12">
   <?php plxUtils::printInput('prod', $_GET['prod'], 'hidden');?>
   <?php plxUtils::printInput('id', $id, 'hidden');?>
   <div class="tabs">
    <ul class="col sml-12">
     <li id="tabHeader_main" class="active"><?php $plxPlugin->lang('L_MAIN') ?></li>
<?php
     foreach($aLangs as $lang){
      echo '     <li id="tabHeader_'.$lang.'"><span class="myhide">'.L_CONTENT_FIELD.'</span> <sup>'.strtoupper($lang).'</sup></li>'.PHP_EOL;
     }
     $imgNoUrl = PLX_PLUGINS.$plxPlugin->plugName.'/images/none.png';
?>
    </ul>
   </div>
   <div class="grid tabscontent">
    <div class="tabpage" id="tabpage_main">
    <!-- Utilisation du selecteur d'image natif à PluXml -->
    <script type="text/javascript">
    function refreshImg() {
     var dta = document.getElementById('id_image').value;
     if(dta.trim()==='') {
      document.getElementById('id_image_img').innerHTML = '<img src="<?php echo $imgNoUrl ?>" alt="" />';
     } else {//console.log(document.getElementById('id_image_img').innerHTML);
      var link = dta.match(/^(https?:\/\/[^\s]+)/gi) ? dta : '<?php echo $plxAdmin->racine ?>'+dta;
      document.getElementById('id_image_img').innerHTML = '<img src="'+link+'" alt="" />';
     }
    }
    </script>
    <div class="grid gridthumb">
     <div class="col sml-12 med-5 label-centered">
      <label><?php $plxPlugin->lang('L_PRODUCTS_IMAGE_CHOICE') ?> <a title="<?php echo L_THUMBNAIL_SELECTION ?>" id="toggler_thumbnail" href="javascript:void(0)" onclick="mediasManager.openPopup('id_image', true)" style="outline:none; text-decoration: none"> +</a></label>
      <?php plxUtils::printInput('image',plxUtils::strCheck($image),'text','255-255',false,'full-width','','onKeyUp="refreshImg()"'); ?>
     </div>
     <div class="col sml-12 med-7">
      <div id="id_image_img">
<?php
       $imgUrl = PLX_ROOT.$plxPlugin->cheminImages.$image;
       $imgUrl = is_file($imgUrl)?$imgUrl:$imgNoUrl;
?>
       <img src="<?php echo $imgUrl ?>" alt="" />
      </div>
     </div>
    </div>
  <!-- Fin du selecteur d'image natif de PluXml -->
<?php if ($modProduit){ ?>
     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_pricettc"><?php $plxPlugin->lang('L_PRODUCTS_PRICE') ;?> (<?php echo trim($plxPlugin->getParam("devise"));?>)&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
       <?php plxUtils::printInput('pricettc',plxUtils::strCheck($pricettc),'text','0-255'); ?>
      </div>
     </div>
     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_poidg"><?php $plxPlugin->lang('L_PRODUCTS_WEIGHT') ;?>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
       <?php plxUtils::printInput('poidg',plxUtils::strCheck($poidg),'text','0-255'); ?>
      </div>
      <div class="col sml-12 med-5 label-centered">
       <label for="id_iteminstock"><?php $plxPlugin->lang('L_PRODUCTS_ITEM_INSTOCK') ;?>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
        <?php plxUtils::printInput('iteminstock',plxUtils::strCheck($iteminstock),'text','0-255'); ?>
      </div>
     </div>


     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_stockmgmt"><?php $plxPlugin->lang('L_PRODUCTS_BASKET_BUTTON') ;?></label>
      </div>
      <div class="col sml-12 med-7">
       <script type="text/javascript">function toggleStockmgmt(a){var b = document.getElementById('id_notice_noaddcart');var c = document.getElementById('config_notice_noaddcart');var d = document.getElementById('cartImg');if(a==1){b.setAttribute("placeholder","<?php echo $plxPlugin->getLang('L_NOTICE_NOADDCART').' ('.$plxPlugin->getLang('L_BY_DEFAULT').')';?>");c.classList.remove("hide");d.src = "";}else{b.removeAttribute("placeholder");c.classList.add("hide");d.src = "<?php echo PLX_PLUGINS.$plxPlugin->plugName.'/images/full.png'; ?>";}}</script>
       <?php plxUtils::printSelect('stockmgmt', array('1'=>L_YES,'0'=>L_NO), plxUtils::strCheck($stockmgmt), false,'" onChange="toggleStockmgmt(this.options[this.selectedIndex].value);'); ?>
      </div>
     </div>


     <div class="grid">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_noaddcart"><?php $plxPlugin->lang('L_PRODUCTS_BASKET_BUTTON') ;?>&nbsp;:<?php echo '<img id="cartImg" class="noaddcartImg" src="'.PLX_PLUGINS.$plxPlugin->plugName.'/images/'.(empty($noaddcart)?'full':'empty').'.png" />'; ?></label>
      </div>
      <div class="col sml-12 med-7">
       <script type="text/javascript">function toggleNoaddcart(a){var b = document.getElementById('id_notice_noaddcart');var c = document.getElementById('config_notice_noaddcart');var d = document.getElementById('cartImg');if(a==1){b.setAttribute("placeholder","<?php echo $plxPlugin->getLang('L_NOTICE_NOADDCART').' ('.$plxPlugin->getLang('L_BY_DEFAULT').')';?>");c.classList.remove("hide");d.src = "<?php echo PLX_PLUGINS.$plxPlugin->plugName.'/images/empty.png'; ?>";}else{b.removeAttribute("placeholder");c.classList.add("hide");d.src = "<?php echo PLX_PLUGINS.$plxPlugin->plugName.'/images/full.png'; ?>";}}</script>
       <?php plxUtils::printSelect('noaddcart', array('1'=>L_YES,'0'=>L_NO), plxUtils::strCheck($noaddcart), false,'" onChange="toggleNoaddcart(this.options[this.selectedIndex].value);'); ?>
      </div>
     </div>
     <div class="grid<?php echo $noaddcart?'':' hide'; ?>" id="config_notice_noaddcart">
      <div class="col sml-12 med-5 label-centered">
       <label for="id_notice_noaddcart"><?php $plxPlugin->lang('L_PRODUCTS_BASKET_NO_BUTTON') ;?>&nbsp;:</label>
      </div>
      <div class="col sml-12 med-7">
       <?php plxUtils::printInput('notice_noaddcart',plxUtils::strCheck($notice_noaddcart),'text','0-255', false, 'notice_noaddcart"'.($noaddcart?' placeholder="'.$plxPlugin->getLang('L_NOTICE_NOADDCART').' ('.$plxPlugin->getLang('L_BY_DEFAULT').')':'')); ?>
      </div>
     </div>
     <hr/>
     <?php $plxPlugin->lang('L_PRODUCTS_CATEGORIES');?>&nbsp;:<br/>
     <?php $listeCategories = explode(",", $plxPlugin->aProds[$id]["group"]);?>
     <?php foreach ($plxPlugin->aProds as $idCategorie => $p) {?>
<?php 
       if ("1" !== $p["pcat"]) {
        continue;
       }
?>
      <label for="categorie_<?php echo $idCategorie;?>">
       <input type="checkbox"
         name="listeCategories[]"
         value="<?php echo $idCategorie;?>"
         id="categorie_<?php echo $idCategorie;?>"
         <?php echo (!in_array($idCategorie, $listeCategories)) 
         ? "" : " checked=\"checked\"";?>
        />
       <?php echo plxUtils::strCheck($p["name"]); ?>
      </label>
     <?php } ?>
     <hr/>
<?php } else { ?>
     <?php plxUtils::printInput('pricettc',plxUtils::strCheck($pricettc),'hidden','0-255');?>
     <?php plxUtils::printInput('poidg',plxUtils::strCheck($poidg),'hidden','50-255');?>
     <?php plxUtils::printInput('noaddcart', plxUtils::strCheck($noaddcart),'hidden','0-255');?>
     <?php plxUtils::printInput('iteminstock',plxUtils::strCheck($iteminstock),'hidden','50-255');?>
     <?php plxUtils::printInput('notice_noaddcart',plxUtils::strCheck($notice_noaddcart),'hidden','50-255');?>
<?php } ?>
    <div class="grid">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_template"><?php $plxPlugin->lang('L_PRODUCTS_TEMPLATE_FIELD');?>&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printSelect('template', $aTemplates, $template);?>
     </div>
    </div>
    <div class="grid">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_title_htmltag"><?php $plxPlugin->lang('L_PRODUCT_TITLE_HTMLTAG');?>&nbsp;(<?php $plxPlugin->lang('L_OPTIONEL');?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printInput('title_htmltag',plxUtils::strCheck($title_htmltag),'text','0-255');?>
     </div>
    </div>
    <div class="grid">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_meta_description"><?php $plxPlugin->lang($modProduit?'L_PRODUCT_META_DESCRIPTION':'L_CAT_META_DESCRIPTION');?>&nbsp;(<?php $plxPlugin->lang('L_OPTIONEL');?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printInput('meta_description',plxUtils::strCheck($meta_description),'text','0-255'); ?>
     </div>
    </div>
    <div class="grid">
     <div class="col sml-12 med-5 label-centered">
      <label for="id_meta_keywords"><?php $plxPlugin->lang($modProduit?'L_PRODUCT_META_KEYWORDS':'L_CAT_META_KEYWORDS');?>&nbsp;(<?php $plxPlugin->lang('L_OPTIONEL');?>)&nbsp;:</label>
     </div>
     <div class="col sml-12 med-7">
      <?php plxUtils::printInput('meta_keywords',plxUtils::strCheck($meta_keywords),'text','0-255');?>
     </div>
    </div>
   </div><!-- fi tabpage_main -->

<!-- Content en multilingue -->
<?php foreach($aLangs as $lang) { ?>
   <div class="tabpage" id="tabpage_<?php echo $lang ?>" style="display:none;">
    <div class="grid">
     <div class="col sml-12">
      <label for="id_content_<?php echo $lang ?>"><?php echo L_CONTENT_FIELD ?>&nbsp;:</label>
       <?php
        if(!$plxPlugin->aLangs || $lang==$plxAdmin->aConf['default_lang'])
         plxUtils::printArea('content',plxUtils::strCheck($content[$lang]),140,30);
        else
         plxUtils::printArea('content_'.$lang,plxUtils::strCheck($content[$lang]),140,30);
?>
     </div>
    </div>
   </div>
<?php } ?>
<!-- Fin du content en multilingue -->
  </div><!-- fi tabpage id:tabscontent -->

  <p class="in-action-bar plx<?php echo str_replace('.','-',@PLX_VERSION); echo $plxPlugin->aLangs?' multilingue':'';?>">
   <?php echo plxToken::getTokenPostMethod() ?>
   <input type="submit" value="<?php $plxPlugin->lang($modProduit?'L_PRODUCT_UPDATE':'L_CAT_UPDATE');?>"/>
<?php
    if($active){ 
     $link = $plxAdmin->urlRewrite('index.php?product'.intval($id).'/'.$url);
     $codeTexte = $modProduit ? 'L_PRODUCT_VIEW_PAGE_ON_SITE' : 'L_CAT_VIEW_PAGE_ON_SITE';
     $texte = sprintf($plxPlugin->getLang($codeTexte), '<i class="myhide">'.plxUtils::strCheck($title).'</i>');
?>
    <br class="med-hide" /><a href="<?php echo $link;?>"><?php echo $texte;?></a>
<?php } ?>
  </p>
  </fieldset>
 </div><!-- fi tabContainer -->
</form>
<script type="text/javascript" src="<?php echo PLX_PLUGINS.$plxPlugin->plugName."/js/tabs.js" ?>"></script>
