<?php

require_once('component/search/search.class.php5');

class CSearchEx extends CSearch
{

  private $casSearchableComponent = array();
  private $cnSearchableComponent = 0;

  public function __construct()
  {
    $this->casSearchableComponent = CDependency::getComponentIdByInterface('searchable');
    $this->cnSearchableComponent = count($this->casSearchableComponent);

    return true;
  }

  public function getDefaultType()
  {
    return CONST_SEARCH_TYPE_SEARCH;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_SEARCH;
  }

  //====================================================================
  //  accessors
  //====================================================================


  //====================================================================
  //  interface
  //====================================================================

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    //if no component are available for search, dont return the search link
    if($this->cnSearchableComponent == 0)
      return array();

    $asActions = array();

    $asActions['all'][] = array('picture' => $this->getResourcePath().'/pictures/menu/search.png', 'url' => 'javascript:;');
    return $asActions;
  }


  public function getAjax()
  {
    $this->_processUrl();
    return json_encode($this->_ajaxSearch());
  }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();
    return '';
  }

  private function _ajaxSearch()
  {
    $sSearchWord = getValue('search');
    if(empty($sSearchWord))
      return array('notice' => 'Nothing to search.');

    if($this->cnSearchableComponent == 0)
       return array('error' => 'Oops, no component available for search.');


    //version 1, basic search:
    //search on all component
    $asResult = array();
    $asCss = array();

    foreach($this->casSearchableComponent as $sComponentId)
    {
      $oComponent = CDependency::getComponentByUid($sComponentId);
      $asSearchResult = $oComponent->search($sSearchWord);

      //if one search return anykind of special action, we stop searching and execute the action
      if(isset($asSearchResult['url']) || isset($asSearchResult['timedurl']) || isset($asSearchResult['reload']) || isset($asSearchResult['timedreload']) || isset($asSearchResult['notice']) || isset($asSearchResult['error']))
        return $asSearchResult;

      if(isset($asSearchResult['nb']) && isset($asSearchResult['data']) && $asSearchResult['nb'] > 0)
      {
        $asResult[] = $asSearchResult['data'];
      }
    }



    if(count($asResult) > 0)
    {
      $oPage = CDependency::getComponentByName('page');
      $sSearchResult = array('data' => implode('<br />', $asResult), 'cssfile' => $asCss);
      return $oPage->getAjaxExtraContent($sSearchResult);
    }
    else
      return array('notice' => 'No result found.');
  }

  
  
}