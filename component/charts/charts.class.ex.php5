<?php

require_once('component/charts/charts.class.php5');

class CChartsEx extends CCharts
{

  private $casAvailableType = array('line', 'bar', 'column' /*'arearange', 'areasplinerange', 'columnrange', 'pie'*/);
  private $csChartType = '';
  private $csChartTitle = '';
  private $csAxisTitleX = '';
  private $csAxisTitleY = '';
  private $csToolTip = '';
  private $cnLegendPosX = '';
  private $cnLegendPosY = '';

  private $casAxis = array();
  private $casChartData = array();

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();
    return $asActions;
  }

  // Normal functions
  public function getHtml()
  {
    $this->_processUrl();
    switch($this->csType)
    {
    }
  }

  //Ajax function

  public function getAjax()
  {
    $this->_processUrl();

    switch($this->csType)
    {
    }
  }


  public function createChart($psType, $psChartTitle = '', $psAxisTitleY = '', $psAxisTitleX = '')
  {
    if(!in_array($psType, $this->casAvailableType))
    {
      assert('false; // chart type does not exist');
      return false;
    }

    $this->csChartType = $psType;
    $this->csChartTitle = $psChartTitle;
    $this->csAxisTitleX = $psAxisTitleX;
    $this->csAxisTitleY = $psAxisTitleY;

    $this->csLegendDirection = 'horizontal';
    $this->cnLegendPosX = 0;
    $this->cnLegendPosY = 0;
    $this->csToolTip = '';
    $this->casAxis = array();
    $this->casChartData = array();

    return true;
  }

  public function setChartLegendPosition($psDirection, $pnPosX = 0, $pnPosY = 0)
  {
    if(!assert('is_numeric($pnPosX) && is_numeric($pnPosY)'))
      return false;

    if(strtolower($psDirection) == 'vertical')
      $this->csLegendDirection = 'vertical';
    else
      $this->csLegendDirection = 'horizontal';

    $this->cnLegendPosX = $pnPosX;
    $this->cnLegendPosY = $pnPosY;
    return false;
  }

  public function setToolTip($psJs)
  {
    if(!assert('!empty($psJs)'))
      return false;

    $this->csToolTip = $psJs;
    return true;
  }


  public function setChartAxis($pasData)
  {
    if(!assert('is_array($pasData) && !empty($pasData)'))
      return false;

    $this->casAxis = $pasData;
    return true;
  }

  public function setChartData($psStreamName, $pasData)
  {
    if(!assert('is_array($pasData) && !empty($pasData)'))
      return false;

    $nValues = count($this->casAxis);
    if(count($pasData) != $nValues)
    {
      assert('false; // nb of elements are different between axis and this set of data');
      return false;
    }

    foreach($pasData as $vValue)
    {
      if(!is_numeric($vValue))
      {
        assert('false; // a value in this set of data is not a number');
        return false;
      }
    }

    $this->casChartData[] = array('label' => $psStreamName, 'data' => $pasData);
    return true;
  }

  public function getChartDisplay($pbAjax = false, $psId = '', $pnMarginRight = 0, $pnMarginBottom = 25)
  {
    if(!assert('is_numeric($pnMarginRight) && is_numeric($pnMarginBottom)'))
      return '';

    //check if everythings is good:
    if(empty($this->casAxis))
    {
      assert('false; // chart need a axis set of values ');
      return '';
    }

    $sHTML = '';
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    if(!$pbAjax)
    {
      $oPage->addRequiredJsFile($this->getResourcePath().'js/highcharts.js');
    }

    if(!empty($psId))
      $sChartId = $psId;
    else
      $sChartId = uniqid();

    $asSeries = array();
    foreach($this->casChartData as $asSerie)
    {
      $asSeries[] = "{name: '".addslashes($asSerie['label'])."', data: [".implode(',', $asSerie['data'])."]}";
    }

    $sJavascript = "
      var chart1; // globally available
      $(document).ready(function()
      {
        chart1 = new Highcharts.Chart({
         chart: {
            renderTo: '".$sChartId."',
            type: '".$this->csChartType."',
            marginRight: ".$pnMarginRight.",
            marginBottom: ".$pnMarginBottom."
         },
         title: {
            text: '".$this->csChartTitle."'
         },
         xAxis: {
           title: {text: '".$this->csAxisTitleX."'},
            categories: ['".implode("', '", $this->casAxis)."']

         },
         yAxis: {
            title: {text: '".$this->csAxisTitleY."'}
         },
         /*tooltip: {
                formatter: function() { return ''+ this.series.name +'<br/>'+ this.x +': '+ this.y +'°C'; }
         },*/
         legend:
         {
            layout: '".$this->csLegendDirection."',
            align: 'right',
            verticalAlign: 'top',
            x: ".$this->cnLegendPosX.",
            y: ".$this->cnLegendPosY.",
            borderWidth: 0,
            backgroundColor: '#fff',
            floating: true,
            shadow: false
         },
         series: [".implode(',', $asSeries)." ]
        });
      });
      ";

    if(!$pbAjax)
      $oPage->addCustomJs($sJavascript);
    else
    {
      $sHTML.= '<script>function initChart(){ '.$sJavascript.' } </script>';
    }

    $sHTML.= $oHTML->getBlocStart($sChartId, array('style' => 'width: 100%; height: 200px;'));
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  public function includeChartsJs()
  {
    $oPage = CDependency::getComponentByName('page');
    $oPage->addRequiredJsFile($this->getResourcePath().'js/highcharts.js');
    return true;
  }

}
