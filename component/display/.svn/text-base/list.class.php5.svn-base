<?php

class CList
{
  private $cnLine = 25;
  private $cnResults = 0;
  private $csPagerUrl = '';
  private $csHasHeader = true;

  private $casColumns = array();
  private $casSortableCol = array();
  private $casCellParam = array();

  private $casLineAction = array();
  private $casGlobalAction = array();


  private $casListData = array();



  public function __construct($pnLineToDisplay = 0, $pnTotalResult = 0, $psPagerUrl = '', $pbHasHeader = true)
  {
    if(!empty($pnLineToDisplay))
      $this->cnLine = $pnLineToDisplay;


    $this->cnResults = $pnTotalResult;

    if(!empty($psPagerUrl))
      $this->csPagerUrl = $psPagerUrl;

    $this->csHasHeader = $pbHasHeader;
  }

  public function setColParams($pasColumns)
  {

    foreach($pasColumns as $sColName => $asColParams)
    {

      if(!isset($asColParams['label']))
        assert('false; // a column has no label');

      $this->casColumns[$sColName]['label'] = $asColParams['label'];


      if(isset($asColParams['sortUrl']) && !empty($asColParams['sortUrl']))
      {
        $this->casSortableCol[$sColName]['sort'] = $asColParams['sortUrl'];
      }
      elseif(  (isset($asColParams['sortAscUrl']) && !empty($asColParams['sortAscUrl']))
             ||(isset($asColParams['sortDescUrl']) && !empty($asColParams['sortDescUrl']))  )
      {
        $this->casSortableCol[$sColName]['sortAsc'] = $asColParams['sortAscUrl'];
        $this->casSortableCol[$sColName]['sortDesc'] = $asColParams['sortDescUrl'];
      }


      if(isset($asColParams['param']))
        $this->casCellParam = $asColParams['param'];
    }

  }


  /**
   * receive the data, and try to define the header automatically
   * @param array $pasData: data to display
   */
  public function autoLoadParam($pasData, $pnCall = 0)
  {
    if(!assert('is_array($pasJavascript)') || empty($pasJavascript))
      return false;

    foreach($pasData as $sKey => $vData)
    {
      if(is_array($vData))
      {
        if($pnCall == 0)
          $this->autoLoadParam($vData, 1);
        else
          return false;
      }
      else
      {
        $this->casColumns[$sKey]['label'] = $sKey;
      }
    }

    return true;
  }


  public function setLineAction($pasAction)
  {
    if(!assert('is_array($pasAction)') || empty($pasAction))
      return 0;

    $nCount = 0;
    foreach($pasAction as $asActionData)
    {
      if(isset($asActionData['url']) && isset($asActionData['urlParam']) && isset($asActionData['label']))
      {
        $this->casLineAction[$nCount]['url'] = $asActionData['url'];
        $this->casLineAction[$nCount]['urlParam'] = $asActionData['urlParam'];
        $this->casLineAction[$nCount]['label'] = $asActionData['label'];

        $nCount++;
      }
    }

    return $nCount;
  }


  public function setGlobalAction($pasAction)
  {
    if(!assert('is_array($pasAction)') || empty($pasAction))
      return 0;

    $nCount = 0;
    foreach($pasAction as $sActionName => $asActionData)
    {
      if(isset($asActionData['url']) && isset($asActionData['urlParam']))
      {
        $this->casGlobalAction[$nCount]['url'] = $asActionData['url'];
        $this->casGlobalAction[$nCount]['urlParam'] = $asActionData['urlParam'];

        if(isset($asActionData['picture']))
          $this->casGlobalAction[$nCount]['picture'] = $asActionData['picture'];

        if(isset($asActionData['label']))
          $this->casGlobalAction[$nCount]['label'] = $asActionData['label'];

        $nCount++;
      }
    }

    return $nCount;
  }


  public function AddRow($pvKey = '', $pasRowData, $panRowAction = array())
  {

    if(empty($pvKey))
    {
      $asInfo = array('data' => $pasRowData, 'action' => $panRowAction);
      $this->casListData[] = $asInfo;
    }
    else
    {
      if(isset($this->casListData[$pvKey]))
        asssert('false; // rows with same key');

      $this->casListData[$pvKey]['data'] = $pasRowData;
      $this->casListData[$pvKey]['action'] = $panRowAction;
    }

    return true;
  }



  public function getDisplay()
  {
    /* @var $oPage  CPageEx */
    /* @var $oHTML  CDisplayEx */

    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $sTbody = '<tbody>';


    foreach($this->casListData as $nRowKey => $asRowData)
    {
      $sTbody.= '<tr>';

      if(count($this->casGlobalAction))
        $sTbody.= '<td><input type="checkbox" name="" Value="" /></td>';


      foreach($asRowData['data'] as $vKey => $sData)
      {
        $sTbody.= '<td>';
        $sTbody.= $oHTML->getBlocStart('', $this->casCellParam);
        $sTbody.= $sData;
        $sTbody.= $oHTML->getBlocEnd();
        $sTbody.= '</td>';
      }

      if(!empty($asRowData['action']))
      {
        $bHasAction = true;
        $sTbody.= '<td>';
        $sHTML.= '<td>';

        //dump($this->casLineAction);
        $sTbody.= $oHTML->getBlocStart();
        foreach($this->casLineAction as $sKey => $asAction)
        {
          $sTbody.= '<a href="'.$asAction['url'].'&'.$asAction['urlParam'].'=zeub" >';
          $sTbody.= $asAction['label'];
          $sTbody.= '</a> ';
        }

        $sTbody.= $oHTML->getBlocEnd();
        $sTbody.= '</td>';
      }

      $sTbody.= '</tr>';
    }

    $sTbody.= '</tbody>';




    $sHTML = '';
    $sHTML.= $oHTML->getFittingBlocStart('', array('class' => 'listContainer'));
    $sHTML.= $oHTML->getFittingBlocStart('', array('class' => 'listInnerContainer'));

    if(count($this->casGlobalAction))
    {
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'listGlobalAction'));
      $sHTML.= 'global action ';
      $sHTML.= $oHTML->getBlocEnd();
    }


    $sHTML.= '<table cellspacing="0" cellpadding="0" class="listTable">';

    if($this->csHasHeader)
    {
      $sHTML.= '<thead>';
      $sHTML.= '<tr>';

      if(count($this->casGlobalAction))
        $sHTML.= '<td>#</td>';

      $nCount = 0;
      foreach($this->casColumns as $sColdName => $colData)
      {
        if(isset($colData['sortUrl']))
        {
          $sHTML.= '<td><a href="javascript:;">'.$colData['label'].'</a></td>';
        }
        elseif(isset($colData['sortAscUrl']) || isset($colData['sortDescUrl']))
        {
          $sHTML.= '<td>'.$colData['label'].'<a href="javascript:;">+</a><a href="javascript:;">-</a></td>';
        }
        else
          $sHTML.= '<td>'.$colData['label'].'</td>';

        $nCount++;

        if($nCount >= $this->cnLine)
          break;

      }

      if($bHasAction)
      {
        $sHTML.= '<td>Actions</td>';
      }

      $sHTML.= '</tr>';
      $sHTML.= '</thead>';
    }



    $sHTML.= $sTbody.'</table>';

    /* TODO: Pager */
    if(!empty($this->csPagerUrl) && !empty($this->cnResults) && $this->cnResults > $this->cnLine)
    {
      $nCurrentPage = getValue('pgn', 1);

      $nbPages = floor($this->cnResults / $this->cnLine);
      if( ($this->cnResults - ($nbPages * $this->cnLine)) > 0)
        $nbPages++;

      $anPage = array();
      for($nCount=-4; $nCount <= 4; $nCount++)
      {
        if( ($nCurrentPage+$nCount) > 0 && count($anPage) < 6)
          $anPage[] = $nCurrentPage+$nCount;
      }


      $sHTML.= $oHTML->getBlocStart('', array('class' => 'pagerContainer'));
      $sHTML.= $this->cnResults.' results      ';

      if($nCurrentPage != 1)
      {
        $sHTML.= '<<  <  ' ;
      }

      foreach($anPage as $nPageNumber)
      {
        if($nPageNumber != $nCurrentPage)
          $sHTML.= ' <a href="'.$this->csPagerUrl.'&pgn='.$nPageNumber.'">'.$nPageNumber.'</a>';
        else
          $sHTML.= ' '.$nPageNumber;

      }


      $sHTML.= ' <select name=""><option value="10">10</option><option value="25">25</option><option value="50">50</option>';
      $sHTML.= ' <option value="100">100</option><option value="'.$this->cnResults.'">All</option></select> nb / page';

      $sHTML.= $oHTML->getBlocEnd();
    }

    $sHTML.= $oHTML->getFittingBlocEnd();
    $sHTML.= $oHTML->getFittingBlocEnd();

    return $sHTML;
  }



}
?>
