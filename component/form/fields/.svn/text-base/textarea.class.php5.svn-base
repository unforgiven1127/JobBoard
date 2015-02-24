<?php

require_once('component/form/fields/field.class.php5');

class CTextarea extends CField
{
  protected $cbIsTinymce;

  public function __construct($psFieldName, $pasFieldParams = array())
  {
    parent::__construct($psFieldName, $pasFieldParams);

    if(isset($pasFieldParams['isTinymce']) && $pasFieldParams['isTinymce'])
      $this->cbIsTinymce = true;
    else
      $this->cbIsTinymce = false;
  }


  public function getDisplay()
  {

    //--------------------------------
    //fetching field parameters

    if(!isset($this->casFieldParams['id']))
      $this->casFieldParams['id'] = $this->csFieldName.'Id';

    //------------------------
    //add JScontrol classes
   if(isset($this->casFieldParams['required']) && !empty($this->casFieldParams['required']))
      $this->casFieldContol['jsFieldNotEmpty'] = '';



    if(isset($this->casFieldParams['label']))
    {
      $sLabel = $this->casFieldParams['label'];
      unset($this->casFieldParams['label']);
    }
    else
      $sLabel = '';


    if(isset($this->casFieldParams['value']))
    {
      $sValue = $this->casFieldParams['value'];
      unset($this->casFieldParams['value']);
    }
    else
      $sValue = '';

    //--------------------------------

    $sHTML = '';


    if(!empty($sLabel) && $this->isVisible())
      $sHTML.= '<div class="formLabel">'.$sLabel.'</div>';


    $sHTML.= '<div class="formField">';

    if($this->cbIsTinymce)
    {
      $oPage = CDependency::getComponentByName('page');
      //$oPage->addRequiredJsFile('/component/form/resources/js/tiny_mce/jquery.tinymce.js');

      //if in ajax, loaded by the mce itself with script_url option
      if(!$this->cbFieldInAjax)
      {
        //$oPage->addRequiredJsFile('/component/form/resources/js/tiny_mce/tiny_mce.js');
        $sExtraJs = '';
      }
      else
        $sExtraJs = '';
        //$sExtraJs = "script_url : '/component/form/resources/js/tiny_mce/tiny_mce.js', ";


      $sTinyJs = 'tinyMCE.init(
                  {
                    '.$sExtraJs.'
                    // General options
                    //mode : "textareas",
                    mode : "exact",
                    elements : "'.$this->csFieldName.'",
                    theme : "advanced",
                    plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker",

                    // Theme options
                    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,|,hr,removeformat,|,charmap,emotions,iespell,|,fullscreen,spellchecker",
                    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,cleanup,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                    theme_advanced_buttons3 : "",

                    theme_advanced_toolbar_location : "top",
                    theme_advanced_toolbar_align : "left",
                    theme_advanced_statusbar_location : "bottom",
                    theme_advanced_resizing : true,
                    theme_advanced_path : false,


                    // Example content CSS (should be your site CSS)
                    //content_css : "",

                    // Drop lists for link/image/media/template dialogs
                    template_external_list_url : "js/template_list.js",
                    external_link_list_url : "js/link_list.js",
                    external_image_list_url : "js/image_list.js",
                    media_external_list_url : "js/media_list.js",

                    // Replace values for the template plugin
                    template_replace_values : {
                            username : "Some User",
                            staffid : "991234"
                    },

                    setup : function(ed)
                    {
                      ed.onInit.add(function(ed, evt)
                      {
                        var dom = ed.dom;
                        var doc = ed.getDoc();

                        //jQuery("#" + ed.id + \'_tbl \'+\'.mceToolbar\').hide();
                        tinymce.dom.Event.add(doc.body, \'blur\', function(e)
                        {
                          //jQuery(\'#\' + ed.id + \'_tbl \'+\'.mceToolbar\').hide();
                          ed.save();
                        });
                      });
                    },
                    cleanup: true
                }); ';


      //$oPage->addCustomjs($sTinyJs);
      $sHTML.= '<script>'.$sTinyJs.'</script>';

      if(isset($this->casFieldParams['class']))
        $this->casFieldParams['class'].= 'tinymce hidden';
      else
        $this->casFieldParams['class'] = 'tinymce hidden';
    }




    $sHTML.= '<textarea name="'.$this->csFieldName.'" ';

    foreach($this->casFieldParams as $sKey => $vValue)
      $sHTML.= ' '.$sKey.'="'.$vValue.'" ';

    if(!empty($this->casFieldContol))
    {
      $sHTML.= ' jsControl="';
      foreach($this->casFieldContol as $sKey => $vValue)
        $sHTML.= $sKey.'@'.$vValue.'|';

      $sHTML.= '" ';
    }

    $sHTML.= ' >'.$sValue.'</textarea></div>';

    return $sHTML;
  }

}