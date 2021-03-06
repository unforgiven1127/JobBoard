<?php

require_once('component/project/project.class.php5');

class CProjectEx extends CProject
{
  private $casTaskType = array('Design', 'Editorial', 'IT', 'Management', 'Sales', 'Other');
  private $casTaskTypeColor = array('Design' => '#00A058', 'Editorial' => '#1A64A5', 'IT' => '#8E21BC', 'Sales' => '#EA893A', 'Other' => '#777', 'Management' => '#888');
  private $casTaskTypeBgColor = array('Design' => '#EDFCF5', 'Editorial' => '#EDF5FC', 'IT' => '#F8F2FC', 'Sales' => '#FFF9F4', 'Other' => '#f6f6f6', 'Management' => '#888');
  private $casDisplayableType = array('image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/bmp', 'image/x-windows-bmp', 'image/pjpeg');

  public function __construct()
  {
    return true;
  }

  public function getDefaultType()
  {
    return CONST_PROJECT_TYPE_PROJECT;
  }

  public function getDefaultAction()
  {
    return CONST_ACTION_LIST;
  }

  //====================================================================
  //  accessors
  //====================================================================


  //====================================================================
  //  interface
  //====================================================================

  public function getPageActions($psAction = '', $psType = '', $pnPk = 0)
  {
    $asActions = array();
    $oRight = CDependency::getComponentByName('right');
    $oLogin = CDependency::getComponentByName('login');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),$this->getPk());
    if(!empty($pnPk))
      $bEdit = $this->_canEdit($pnPk,$oLogin->getUserPk());

    switch($psType)
    {
      case CONST_PROJECT_TYPE_PROJECT:

        /*@var $oPage CPageEx */
        $oPage = CDependency::getComponentByName('page');
        $asActions['ppav'] = array();
        $asActions['ppal'] = array();
        $asActions['ppaa'] = array();
        $asActions['ppae'] = array();
        $asActions['ppad'] = array();

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            if(!empty($pnPk))
            {
              $asActions['ppaa'][] = array('picture' => $this->getResourcePath().'/pictures/menu/task_add.png','title'=>'Add task', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK, 0, array('prjpk' => $pnPk)));
              $asActions['ppaa'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_add.png','title'=>'Add project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_PROJECT));
              $asActions['ppav'][] = array('picture' => $this->getResourcePath().'/pictures/menu/wall_view.png','title'=>'View Project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW_DETAILED, CONST_PROJECT_TYPE_PROJECT, $pnPk));
              $asActions['ppav'][] = array('picture' => $this->getResourcePath().'/pictures/menu/gallery.png','title'=>'View Project in gallery', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW_DETAILED, CONST_PROJECT_TYPE_PROJECT, $pnPk, array('diaporama' => 1)));
              if($sAccess && $bEdit)
                $asActions['ppad'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_delete.png','title'=>'Delete Project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_DELETE, CONST_PROJECT_TYPE_PROJECT, $pnPk));
              if($bEdit)
              $asActions['ppae'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_edit.png','title'=>'Edit Project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_EDIT, CONST_PROJECT_TYPE_PROJECT,$pnPk));
              $asActions['ppal'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_list.png','title'=>'Projects list', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_PROJECT_TYPE_PROJECT));
            }
            break;

            case CONST_ACTION_ADD:
            case CONST_ACTION_EDIT:
              $asActions['ppal'][] = array('picture' => CONST_PICTURE_MENU_LIST,'title'=>'Projects List', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_PROJECT_TYPE_PROJECT));
            break;

            case CONST_ACTION_VIEW_DETAILED:
            if(!empty($pnPk))
            {
              $asActions['ppaa'][] = array('picture' => $this->getResourcePath().'/pictures/menu/task_add.png','title'=>'Add task', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK, 0, array('prjpk' => $pnPk, 'back' => 'wall')));
              $asActions['ppaa'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_add.png','title'=>'Add project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_PROJECT));
              $asActions['ppav'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project.png','title'=>'View project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $pnPk));
              $asActions['ppav'][] = array('picture' => $this->getResourcePath().'/pictures/menu/gallery.png','title'=>'View Project in gallery', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_VIEW_DETAILED, CONST_PROJECT_TYPE_PROJECT, $pnPk, array('diaporama' => 1)));
              if($sAccess && $bEdit)
              $asActions['ppad'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_delete.png','title'=>'Delete project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_DELETE, CONST_PROJECT_TYPE_PROJECT, $pnPk));
              if($bEdit)
              $asActions['ppae'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_edit.png','title'=>'Edit project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_EDIT, CONST_PROJECT_TYPE_PROJECT,$pnPk));
              $asActions['ppal'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_list.png','title'=>'Projects List', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_PROJECT_TYPE_PROJECT));
            }
            break;

            case CONST_ACTION_LIST:
              $asActions['ppaa'][] = array('picture' => $this->getResourcePath().'/pictures/menu/project_add.png','title'=>'Add project', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_PROJECT));
            break;
        }
      break;

      case CONST_PROJECT_TYPE_TASK:

        /*@var $oPage CPageEx */
        $oPage = CDependency::getComponentByName('page');

        //always displayed: list, add
        $asActions['ppal'][] = array('picture' => CONST_PICTURE_MENU_LIST,'title'=>'List  tasks', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK));
        $asActions['ppaa'][] = array('picture' => CONST_PICTURE_MENU_ADD,'title'=>'Add a task', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK));

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:

            $nProjectPk = getValue('prjpk');
            if(!empty($pnPk) && !empty($nProjectPk))
            {
              if($sAccess)
              $asActions['ppad'][] = array('picture' => '/media/picture/menu/bin.png','title'=>'Delete this task', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_DELETE, CONST_PROJECT_TYPE_TASK, $pnPk, array('prjpk' => $nProjectPk)));
              $asActions['ppae'][] = array('picture' => '/media/picture/menu/edit.png','title'=>'Edit this task', 'url' => $oPage->getUrl($this->_getUid(), CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK,$pnPk, array('prjpk' => $nProjectPk)));
            }
            break;
        }
      break;
    }
    return $asActions;
  }


  public function getAjax()
  {
    $this->_processUrl();
    switch($this->csType)
    {
      case CONST_PROJECT_TYPE_PROJECT:
          switch($this->csAction)
          {
            case CONST_ACTION_SAVEADD:
             return json_encode($this->_getProjectSaveEdit(0));
              break;

            case CONST_ACTION_SAVEEDIT:
             return json_encode($this->_getProjectSaveEdit($this->cnPk));
              break;

            case CONST_ACTION_DELETE:
             return json_encode($this->_getProjectDelete($this->cnPk));
              break;

           case CONST_ACTION_VALIDATE:
             return json_encode($this->_getProjectSaveTaskOrder());
              break;

           default:
            case CONST_ACTION_LIST:
              return json_encode($this->_getAjaxProjectList());
               break;
          }
          break;

        case CONST_PROJECT_TYPE_TASK:
          switch($this->csAction)
          {
            case CONST_ACTION_SAVEADD:
             return json_encode($this->_getTaskSaveEdit(0));
              break;

            case CONST_ACTION_SAVEEDIT:
             return json_encode($this->_getTaskSaveEdit($this->cnPk));
              break;

            case CONST_PROJECT_ACTION_UPDATE:
             return json_encode($this->_getTaskUpdate($this->cnPk));
              break;

            case CONST_ACTION_DONE:
             return json_encode($this->_getTaskChangeStatus($this->cnPk));
              break;

            case CONST_ACTION_DELETE:
              return json_encode($this->_getTaskDelete($this->cnPk));
              break;

           default:
            case CONST_ACTION_LIST:
              return json_encode($this->_getAjaxTaskList());
               break;
            }
          break;

        case CONST_PROJECT_TYPE_ATTACHMENT:
          switch($this->csAction)
          {
            case CONST_ACTION_ADD:
              return json_encode($this->_getAttachFileForm($this->cnPk));
              break;
            }
          break;

       case CONST_PROJECT_TYPE_ACTOR:

        switch($this->csAction)
        {
            case CONST_ACTION_SAVEEDIT:
            return json_encode($this->_getSaveProjectActors());
            break;
        }
        break;

      }
    }

    /**
     * Cron Job function to notify about project expiry date
     * @return boolean
     */

    public function getCronJob()
    {
     $oHTML = CDependency::getComponentByName('display');

     $sHTML = $oHTML->getText('Project cron');
     $sHTML.= $oHTML->getCarriageReturn(2);

     echo $sHTML;

      //check if there are projects and tasks close to the ending date
      $nHour = date('H');
      if(((date('D') == 'Mon') && ($nHour > 5 && $nHour < 6)) || getValue('forcecron') == 'project' || getValue('custom_uid') == '456-789')
      {
        $this->_notifyExpirationDate();
      }
      return true;
    }

  //====================================================================
  //  public methods
  //====================================================================

  public function getHtml()
  {
    $this->_processUrl();

    switch($this->csType)
    {
      case CONST_PROJECT_TYPE_PROJECT:

        switch($this->csAction)
        {
          case CONST_ACTION_VIEW:
            return $this->_getProjectView($this->cnPk);
            break;

          case CONST_ACTION_ADD:
            return $this->_getProjectForm(0);
            break;

          case CONST_ACTION_EDIT:
            return $this->_getProjectForm($this->cnPk);
            break;

          case CONST_ACTION_VIEW_DETAILED:
            return $this->_getProjectGraphicalView($this->cnPk);
            break;

          default:
          case CONST_ACTION_LIST:
            return $this->_getProjectList();
            break;
        }
        break;

      case CONST_PROJECT_TYPE_TASK:

        switch($this->csAction)
        {
          case CONST_ACTION_ADD:
            return $this->_getTaskForm(0);
            break;

          case CONST_ACTION_EDIT:
          case CONST_ACTION_VIEW:
            return $this->_getTaskForm($this->cnPk);
            break;

          case CONST_ACTION_LIST:
            return $this->_getTaskList();
          break;
          }
        break;

      case CONST_PROJECT_TYPE_ATTACHMENT:

        switch($this->csAction)
        {
          case CONST_ACTION_SAVEADD:
          case CONST_ACTION_SAVEEDIT:
            return $this->_getAttachFileSave($this->cnPk);
            break;
        }
        break;

     case CONST_PROJECT_TYPE_ACTOR:

        switch($this->csAction)
        {
            case CONST_ACTION_EDIT:
            return $this->_getAddProjectActors();
            break;
        }
        break;

    }
  }

  private function _getSaveProjectActors()
  {
     $oDB = CDependency::getComponentByName('database');
     $oPage = CDependency::getComponentByName('page');

     $asProjectActors = getValue('project_actors');
     $sDate = date('Y-m-d');

     foreach($asProjectActors as $sProjectActors)
     {
       $sQuery = 'SELECT * from project_user where loginfk = '.$sProjectActors.'';
       $oDbResult = $oDB->ExecuteQuery($sQuery);
       $bRead = $oDbResult->readFirst();
       if(!$bRead)
       {
        $sQuery = 'INSERT INTO project_user(loginfk,date) VALUES ('.$sProjectActors.',"'.$sDate.'")' ;
        $oDbResult = $oDB->ExecuteQuery($sQuery);
       }
     }

     $sURL = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_ACTOR);
     return (array('notice'=>'Project actors has been added.', 'timedUrl' => $sURL));
 }

  // Get the function to add the project actors

  private function _getAddProjectActors()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $oPage->addCssFile($this->getResourcePath().'css/project.css');

    $sHTML= $oHTML->getBlocStart();

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('projectActorFormData');
    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_SAVEEDIT, CONST_PROJECT_TYPE_ACTOR);
    $oForm->setFormParams('', true, array('submitLabel' => 'Save ', 'action' => $sURL));

    $oForm->addField('misc', '', array('type' => 'text', 'text' => '<span class="h4">Add the project actors: </span><br /><br /><br />'));
    $oForm->addField('select', 'project_actors[]', array('label' => 'Project Actor', 'multiple' => 'multiple'));
    $oForm->setFieldControl('project_actors[]', array('jsFieldNotEmpty' => ''));

    $asProjectMembers = $this->_getProjectUserList();

    $asProjectActors = $oLogin->getUserList();
    foreach($asProjectActors as $asProjectData)
    {
      if(in_array($asProjectData['loginpk'],$asProjectMembers))
       $oForm->addOption('project_actors[]', array('value'=>$asProjectData['loginpk'],'label' => $asProjectData['firstname'].' '.$asProjectData['lastname'], 'selected' => 'selected'));
      else
       $oForm->addOption('project_actors[]', array('value'=>$asProjectData['loginpk'],'label' => $asProjectData['firstname'].' '.$asProjectData['lastname']));
     }

    $oForm->addField('misc', '', array('type'=> 'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   *Function to get the users who can be assigned a task in project
   * @return array;
   */

  private function _getProjectUserList()
  {
     $oDB = CDependency::getComponentByName('database');

     $sQuery = 'SELECT * FROM project_user WHERE 1';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oDbResult->readFirst();
     $asProjectMembers = array();

     if($bRead)
     {
      while($bRead)
       {
         $asProjectMembers[] = $oDbResult->getFieldValue('loginfk');
         $bRead = $oDbResult->readNext();
       }
     }
     return  $asProjectMembers;

   }

  /**
   * Display the project tabs
   * @return string
   */

  private function _getProjectList()
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $oPage->addCssFile($this->getResourcePath().'css/project.css');
    $sHTML = $oHTML->getTitleLine('Project list', $this->getResourcePath().'pictures/project_48.png');

    $asTabs = array();
    $asTabs[] = array('tabtitle' => 'In Progress','tabOptions'=>array('tabId'=>'','class'=>'tab_display tab_selected','onclick' => '$(\'#currentPrjId\').fadeIn();$(\'.tabs_list >li\').removeClass(\'tab_selected\');$(this).addClass(\'tab_selected\'); $(\'#oldPrjId\').fadeOut();'),'content' => $this->_getProjectResultList(false),'contentOptions'=>array('contentId'=>'currentPrjId','class'=>'display_tab','style'=>'display:block;'));
    $asTabs[] = array('tabtitle' => 'Job Done','tabOptions'=>array('tabId'=>'','class'=>'tab_display','onclick' => '$(\'#currentPrjId\').fadeOut(); $(\'#oldPrjId\').fadeIn();$(\'.tabs_list >li\').removeClass(\'tab_selected\');$(this).addClass(\'tab_selected\');'),'content' => $this->_getProjectResultList(true),'contentOptions'=>array('contentId'=>'oldPrjId','class'=>'display_tab hidden','style'=>'display:none;'));

    $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
    $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));
    $sHTML.= $oHTML->getTabs('', $asTabs);

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getCarriageReturn(2);

    return $sHTML;
   }

   /**
    * Get the projects according to the status
    * @param integer $pnStatus
    * @return array of project data
    */

  private function _getProjects($pbCurrentProject)
  {
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oPager = CDependency::getComponentByName('pager');

    $nUserPk = $oLogin->getUserPk();

    $sCountQuery = 'SELECT count(*) as nCount FROM project as p ';
    $sCountQuery.= ' LEFT JOIN project_actors as pa ON (pa.projectfk = p.projectpk AND pa.loginfk = '.$nUserPk.' AND pa.type = "view")';
    $sCountQuery.= ' WHERE p.status = '.(int)$pbCurrentProject.' AND p.is_public = 1 OR p.creatorfk = '.$nUserPk.' OR p.ownerfk = '.$nUserPk.' OR pa.loginfk = '.$nUserPk.' ';
    $sCountQuery.= ' ORDER BY p.date_end, p.date_create';

    $oDbResult = $oDB->ExecuteQuery($sCountQuery);
    $bRead = $oDbResult->readFirst();

    $nNbResult = $oDbResult->getFieldValue('nCount',CONST_PHP_VARTYPE_INT);

    $sQuery = 'SELECT *, if(p.date_end < NOW(), 1, 0) as finishing,status as finished FROM project as p ';
    $sQuery.= ' LEFT JOIN project_actors as pa ON (pa.projectfk = p.projectpk AND pa.loginfk = '.$nUserPk.' AND pa.type = "view")';
    $sQuery.= ' WHERE p.status = '.(int)$pbCurrentProject.' AND (p.is_public = 1 OR p.creatorfk = '.$nUserPk.' OR p.ownerfk = '.$nUserPk.' OR pa.loginfk = '.$nUserPk.' ) ';
    $sQuery.= ' ORDER BY p.date_end, p.date_create';

    $oPager->initPager();
    $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    $asProjectData = array();

    if($bRead)
    {
      while($bRead)
      {
        $asProjectData[] = $oDbResult->getData();
        $bRead = $oDbResult->readNext();
       }
     }
     return array('nbresult' => $nNbResult, 'data' => $asProjectData);
  }

  /**
   * List all the current running projects
   * @param array $pasProjectData
   * @param array $pasUser
   * @return string
   */
  private function _getProject($pbCurrentProject)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPager = CDependency::getComponentByName('pager');
    $oLogin = CDependency::getComponentByName('login');

    $asProjectData = $this->_getProjects($pbCurrentProject);

    //Get the list of every potential actors
    $asUser = $oLogin->getUserList(0,false);

    $sHTML ='';
    $sHTML.= $oHTML->getBlocStart('');

    foreach($asProjectData['data'] as $asProjectDatas )
    {
      $sHTML.= $this->_getProjectListRow($asProjectDatas, $asUser);
    }

    $nNbResult = $asProjectData['nbresult'];
    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_PROJECT,0,array('status'=>$pbCurrentProject));
    $asPagerUrlOption = array('ajaxTarget' => 'cprojectContainer_'.$pbCurrentProject);

    if($nNbResult > 0)
      $sHTML.= $oPager->getDisplay($nNbResult, $sURL,$asPagerUrlOption);

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Display the project informations on list
   * @param array $pasProjectData
   * @param array $pasUserData
   * @return boolean
   */

  private function _getProjectListRow($pasProjectData, $pasUserData)
  {
    if(!assert('is_array($pasProjectData) && !empty($pasProjectData)'))
      return false;

    if(!assert('is_array($pasUserData) && !empty($pasUserData)'))
      return false;

    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oHTML = CDependency::getComponentByName('display');

    $oRight = CDependency::getComponentByName('right');
    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),$this->getPk());

    $nProjectPk = (int)$pasProjectData['projectpk'];
    $sUrl = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);

    $sHTML = $oHTML->getBlocStart('', array('style' => 'border-bottom: 1px solid #999; padding-bottom:5px; margin-bottom:5px;'));

    if($pasProjectData['finishing'] && !$pasProjectData['finished'])
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:35%; float:left;  padding: 0 5px;'));
    else
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:45%; float:left;  padding: 0 5px;'));

    $sHTML.= $oHTML->getText('Project #'.$nProjectPk.': ', array('class' => 'strong'));
    $sHTML.= $oHTML->getLink($pasProjectData['title'], $sUrl, array('class' => 'project_name'));
    if(!empty($pasProjectData['description']))
    {
       $sHTML.= $oHTML->getCarriageReturn(2);
       $sHTML.= $oHTML->getText('Description: ', array('class' => 'strong'));
       $sHTML.= $oHTML->getCarriageReturn();
       $sHTML.= $oHTML->getText($pasProjectData['description'], array(), 65);
    }
    $sHTML.= $oHTML->getBlocEnd();

    if($pasProjectData['finishing'] && !$pasProjectData['finished'])
    {
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:9%; float:left;  padding: 0 5px;'));
      $sHTML.= $oHTML->getText('Attention required', array('class' => 'strong progress_slow'));
      $sHTML.= $oHTML->getBlocEnd();
     }

    $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:45%; float:left; padding: 0 5px;'));
    if($pasProjectData['date_start'] > date('Y-m-d'))
      $sHTML.= $oHTML->getText('Starting on '.$nProjectPk.': '.$pasProjectData['date_start']);
    else
      $sHTML.= $oHTML->getText('Started on'.$nProjectPk.': '.$pasProjectData['date_start']);

    $sHTML.= $oHTML->getCarriageReturn();

    if($pasProjectData['date_end'] > date('Y-m-d'))
    {
      $sClass = $this->_getPriorityLevelCss($pasProjectData['date_end']);
      $sHTML.= $oHTML->getText('Ending on ');
      $sHTML.= $oHTML->getText($pasProjectData['date_end'], array('class' => $sClass));
    }
    else
    {
      $sHTML.= $oHTML->getText('Ended on ');
      $sHTML.= $oHTML->getText($pasProjectData['date_end'], array('class' => 'project_past'));
    }

    $sHTML.= $oHTML->getCarriageReturn();
    $sClass = $this->_getProgressClass($pasProjectData['progress']);
    $sHTML.= $oHTML->getText('Progress: '.$pasProjectData['progress'].'%', array('class' => $sClass));
    $sHTML.= $oHTML->getCarriageReturn();
    if(!empty($pasUserData[$pasProjectData['creatorfk']]))
      $sHTML.= $oHTML->getText('Created by '.$oLogin->getUserNameFromData($pasUserData[$pasProjectData['creatorfk']]));
    else
      $sHTML.= $oHTML->getText('Created by Administrator');
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:8%; float:left;'));
    $sHTML.= $oHTML->getPicture(CONST_PICTURE_VIEW, 'View project', $sUrl);
    $sHTML.= $oHTML->getSpace(2);

    $sUrl = $oPage->getUrl('project', CONST_ACTION_VIEW_DETAILED, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);
    $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/wall.png', 'Wall view', $sUrl);
    $sHTML.= $oHTML->getSpace(2);

    $bEdit = $this->_canEdit($nProjectPk,$oLogin->getUserPk());
    if($bEdit)
    {
      $sUrl = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);
      $sHTML.= $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit project', $sUrl);
      $sHTML.= $oHTML->getSpace(2);

      if($sAccess)
      {
        $sUrl = $oPage->getAjaxUrl('project', CONST_ACTION_DELETE, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);
        $sPicture = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete project');
        $sHTML.= $oHTML->getLink($sPicture, $sUrl, array('onclick' => 'if(!window.confirm(\'Delete this project and all linked tasks ?\')){ return false; }'));
      }
    }
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to check whether the person can edit or not
   * @param integer $pnProjectPk
   * @param integer $nUserPk
   */

  private function _canEdit($pnProjectPk,$pnUserPk)
  {
    if(!assert('is_integer($pnProjectPk) && !empty($pnProjectPk)'))
      return false;

    if(!assert('is_integer($pnUserPk) && !empty($pnUserPk)'))
      return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM project as p ';
    $sQuery.= ' LEFT JOIN project_actors as pa ON (pa.projectfk = p.projectpk AND pa.loginfk = '.$pnUserPk.' AND pa.type = "edit")';
    $sQuery.= ' WHERE (p.is_edit_public = 1 OR p.creatorfk = '.$pnUserPk.' OR p.ownerfk = '.$pnUserPk.' OR pa.loginfk = '.$pnUserPk.' ) AND p.projectpk = '.$pnProjectPk.'';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if($bRead)
      return true;
    else
      return false;
    }

  /**
   * Display all the information about the project
   * @param integer $pnPK
   * @return string
   */

  private function _getProjectView($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return '';

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $nUserPk = $oLogin->getUserPk();
    $sQuery = 'SELECT * FROM project as p WHERE p.projectpk = '.$pnPK.' LIMIT 1';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('This project is not accessible or has been deleted.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

    //stock project data for later displaying
    $asProjectData = $oResult->getData();

    //define what to display in function of the access rights
    $nUserPk = $oLogin->getUserPk();
    $nCreatorfk = $oResult->getFieldValue('creatorfk');
    $nOwnerfk = $oResult->getFieldValue('ownerfk');
    $bIsPublic = (bool)$oResult->getFieldValue('is_public', false);
    $bAccessFull = false;
    $bAccessView = false;

    if($bIsPublic || ($nUserPk == $nCreatorfk && $nUserPk == $nCreatorfk))
      $bAccessFull = true;
    else
    {
      //not owner or creator, we have to check the link table
      $sQuery = 'SELECT * FROM project_actors as pa WHERE pa.projectfk = '.$pnPK.' AND pa.loginfk = '.$nUserPk;
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      if(!$bRead)
      {
        $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
        $sHTML.= $oHTML->getText(__LINE__.' Sorry you don\'t have access to this project.');
        $sHTML.= $oHTML->getBlocEnd();
        return $sHTML;
      }

      $sActorType = $oResult->getFieldValue('type');
      if(in_array($sActorType, array('*', 'manage')))
        $bAccessFull = true;
      else
        $bAccessView = true;
    }

    if(!$bAccessView && !$bAccessFull)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText(__LINE__.' Sorry you don\'t have access to this project.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
      }

    // =============================================
    //start displaying the project

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/project.css'));

    $sTitle = $oHTML->getBlocStart();
    $sTitle.= $oHTML->getText($asProjectData['title'], array('class' =>'h1')).'<br /><br />';
    $sTitle.= $oHTML->getText($asProjectData['description'], array('style' => 'color:#666;'));
    $sTitle.= $oHTML->getBlocEnd();

    $sHTML = $oHTML->getTitleLine($sTitle, $this->getResourcePath().'pictures/project_48.png', array('isHtml' => true));
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'projectDetails'));

    $nPorjectPk = (int)$asProjectData['projectpk'];
    $asUser = $oLogin->getUserList(0,false,true);

    $sHTML.= $oHTML->getText('Project created on ');
    $sHTML.= getFormatedDate('Y-m-d',$asProjectData['date_create']);
    $sHTML.= $oHTML->getText(' by ');
    if(!empty($asUser[$asProjectData['creatorfk']]))
       $sHTML.= $oLogin->getUserNameFromData($asUser[$asProjectData['creatorfk']]);
    else
       $sHTML.= $oHTML->getText('Administrator');

    $sHTML.= $oHTML->getCarriageReturn();
    if($asProjectData['creatorfk'] != $asProjectData['ownerfk'])
    {
      $sHTML.= $oHTML->getText('Currently in charge of the project ').
      $sHTML.= $oLogin->getUserNameFromData($asUser[$asProjectData['ownerfk']],true);
      $sHTML.= $oHTML->getCarriageReturn(2);
     }
    else
      $sHTML.= $oHTML->getCarriageReturn();

    $sHTML.= $oHTML->getText('Start on ');
    $sHTML.= getFormatedDate('Y-m-d',$asProjectData['date_start']);
    $sHTML.= $oHTML->getCarriageReturn();
    $sHTML.= $oHTML->getText('End on ');
    $sHTML.= getFormatedDate('Y-m-d',$asProjectData['date_end']);

    $sHTML.= $oHTML->getCarriageReturn();
    $sClass = $this->_getProgressClass($asProjectData['progress']);
    $sHTML.= $oHTML->getText('Progress: ');
    $sHTML.= $oHTML->getText($asProjectData['progress'].'%', array('class' => $sClass));
    $sHTML.= $oHTML->getCarriageReturn(2);

    $sHTML.= $oHTML->getCarriageReturn();

    // =============================================
    // =============================================
    //start displaying the project's tasks

    $sHTML.= $oHTML->getTitle('Project Tasks', 'h2', false);

    $sQuery = 'SELECT *, GROUP_CONCAT(loginfk SEPARATOR ",") as actors FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK;
    $sQuery.= ' GROUP BY pt.taskfk ORDER BY t.type  ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    $sURL = $oPage->getUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK, 0, array('prjpk'=>$nPorjectPk));
    if(!$bRead)
    {
      $sHTML.= $oHTML->getText('No task available for this project.');
      $sHTML.= $oHTML->getCarriageReturn(2);
      $sHTML.= $oHTML->getLink('Add a task now', $sURL);
    }
    else
    {
      $anTaskByType = array();
      $anDoneByType = array();
      $nCount = 0;
      $nTotal = 0;
      while($bRead)
      {
        if($oResult->getFieldValue('status', CONST_PHP_VARTYPE_INT) == 0)
        {
          if(isset($anTaskByType[$oResult->getFieldValue('type')]))
            $anTaskByType[$oResult->getFieldValue('type')]++ ;
          else
             $anTaskByType[$oResult->getFieldValue('type')] = 1;

          $nTotal+= $oResult->getFieldValue('progress', CONST_PHP_VARTYPE_FLOAT);
          $nCount++;
        }
        else
        {
          if(isset($anDoneByType[$oResult->getFieldValue('type')]))
            $anDoneByType[$oResult->getFieldValue('type')]++ ;
          else
             $anDoneByType[$oResult->getFieldValue('type')] = 1;

          $nTotal+= $oResult->getFieldValue('progress', CONST_PHP_VARTYPE_FLOAT);
          $nCount++;
         }
        $bRead = $oResult->readNext();
      }

      if($nCount > 0 && $nTotal > 0)
        $nTotal = round(($nTotal/$nCount), 2);

      $sClass = $this->_getProgressClass($nTotal);

      $sHTML.= $oHTML->getText('Overall task progress: ');
      $sHTML.= $oHTML->getText($nTotal.'%', array('class' => $sClass));
      $sHTML.= $oHTML->getSpace(10);
      $sHTML.= $oHTML->getLink('Show/Hide done tasks', 'javascript:;', array('onclick' =>'$(\'.taskRowDone\').fadeToggle(); '));
      $sHTML.= $oHTML->getCarriageReturn(3);

      $asTabs = array();
      $asTabs[] = array('tabtitle' => 'All('.array_sum($anTaskByType).'/'.array_sum($anDoneByType).')','tabOptions'=>array('tabId'=>'allTabId','class'=>'tab_display tab_selected'),'contentOptions'=>array('contentId'=>'allId','class'=>'display_tab','style'=>'display:block;'),'content'=>$this->_getTabs('',$pnPK,$asUser));
      foreach($this->casTaskType as $sType)
      {
        if(!isset($anTaskByType[$sType]))
          $anTaskByType[$sType] = 0;

        if(!isset($anDoneByType[$sType]))
          $anDoneByType[$sType] = 0;

        if(($anTaskByType[$sType] + $anDoneByType[$sType]) > 0)
         $asTabs[] = array('tabtitle' => $sType.' ('.$anTaskByType[$sType].'/'.$anDoneByType[$sType].')','tabOptions'=>array('tabId'=>$sType.'TabId','class'=>'tab_display','style' => 'color:'.$this->casTaskTypeColor[$sType].';'),'contentOptions'=>array('contentId'=>''.$sType.'Id','class'=>'display_tab hidden','style'=>'display:none;'),'content'=>$this->_getTabs($sType,$pnPK,$asUser));
      }

      $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
      $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));
      $sHTML.= $oHTML->getTabs('', $asTabs);
      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

     //include slider lib
      $oPage->addRequiredJsFile(CONST_PATH_JS_SLIDER);
      $oPage->addCssFile(CONST_PATH_CSS_SLIDER);

     }

    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }

  /**
   * Display the tabs content
   * @param string $psType
   * @param integer $pnPK
   * @param array $pasUser
   * @return string
   */

  private function _getTabs($psType='',$pnPK,$pasUser)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return '';

    if(!assert('is_array($pasUser) && !empty($pasUser)'))
      return '';

    $oRight = CDependency::getComponentByName('right');
    $oLogin = CDependency::getComponentByName('login');

    $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),$this->getPk());

    $bEdit = $this->_canEdit($pnPK,$oLogin->getUserPk());

    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT *, GROUP_CONCAT(loginfk SEPARATOR ",") as actors FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK;
    $sQuery.= ' GROUP BY pt.taskfk ORDER BY t.type ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();
    $sHTML = '';
    if($psType!='')
     {
      $sHTML.= $oHTML->getBlocStart($psType.'Id');
        $nLineNb = 1;
        $nCount = 0;
          while($bRead)
          {
            $asTask = $oResult->getData();
            if($asTask['type'] == $psType)
            {
               if(($nLineNb%2) == 0)
                $sExtraClass = ' taskRowEven ';
              else
                $sExtraClass = '';

              if($asTask['status'])
                 $sExtraClass.= ' taskRowDone ';

               $sHTML.= $oHTML->getBlocStart('', array('class' => 'projectView_taskrow '.$sExtraClass, 'style' => 'border-left: 10px solid; border-left-color:'.$this->casTaskTypeBgColor[$psType].';'));
               $asTaskActor = explode(',', $asTask['actors']);

               $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk'=>$pnPK));
               $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowShortCell'));
               $sHTML.= $oHTML->getText($nLineNb);
               $sHTML.= $oHTML->getBlocEnd();

               $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowWhoWhen'));
               if(isset($asUser[(int)$asTask['creatorfk']]))
               {
                 $sHTML.= $oHTML->getText('Created by ');
                 $sHTML.= $oHTML->getText($asUser[(int)$asTask['creatorfk']]['firstname'], array('style' => 'font-weight: bold;'));
                 $sHTML.= $oHTML->getCarriageReturn();
               }
               $sHTML.= $oHTML->getNiceTime($asTask['date_created']);
               $sHTML.= $oHTML->getBlocEnd();

               $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowProgress'));

               $sjavascript = "
                $('#taskCursor_".$asTask['taskfk']." div' ).slider({
                value:".(int)$asTask['progress'].",
                min: 0,
                max: 100,
                step: 1,
                slide: function( event, ui ) {
                $('#taskProgress_".$asTask['taskfk']."').html(ui.value);
                    }
                });

                $('#taskCursor_".$asTask['taskfk']."').fadeToggle();";
                $sHTML.= $oHTML->getLink($asTask['progress'], 'javascript:;', array('id' =>'taskProgress_'.$asTask['taskfk'], 'onclick'=>$sjavascript));
                $sHTML.= $oHTML->getText('%');

                $sHTML.= $oHTML->getBlocStart('taskCursor_'.$asTask['taskfk'], array('class' => 'sliderContainer hidden'));
                $sHTML.= $oHTML->getBlocStart('',  array('class' => 'sliderInner'));
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getSpanStart('',  array('class' => 'sliderSave'));
                $sURL = $oPage->getAjaxUrl('project', CONST_PROJECT_ACTION_UPDATE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'htmlid' => '#taskCursor_'.$asTask['taskfk']));
                $sPic = $oHTML->getPicture(CONST_PICTURE_SAVE);

                $sJavascript = "var sNewProgress = $('#taskProgress_".$asTask['taskfk']."').html();";
                $sJavascript.= "AjaxRequest('".$sURL."&progress='+sNewProgress,'#body'); ";

                $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
                $sHTML.= $oHTML->getSpanEnd();

                $sHTML.= $oHTML->getBlocEnd();
                $sHTML.= $oHTML->getBlocEnd();

                $sURL = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'],  array('prjpk' => $pnPK));
                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowDescription'));
                $sHTML.= $oHTML->getLink('<strong>'.substr($asTask['title'], 0, 100).'</strong>', $sURL);
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowUsers'));
                $sHTML.= $oHTML->getText(' => Affected to ');

                foreach($asTaskActor as $vKey => $nLoginPk)
                    $asTaskActor[$vKey] = $pasUser[$nLoginPk]['firstname'].' '.$pasUser[$nLoginPk]['lastname'];

                $sHTML.= implode(', ', $asTaskActor).' ';
                $sHTML.= $oHTML->getBlocEnd();
                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowAction'));
                $sPicId = 'taskPicId_'.$asTask['taskfk'];
                if($asTask['status'])
                {
                   $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'taskdone' => 0, 'htmlid' => $sPicId));
                   $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_OK, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_INACTIVE, 'imgHidden' => CONST_PICTURE_CHECK_OK));
                 }
                 else
                 {
                   $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'taskdone' => 1, 'htmlid' => $sPicId));
                   $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_INACTIVE, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_OK, 'imgHidden' => CONST_PICTURE_CHECK_INACTIVE));
                 }
                $sHTML.= $oHTML->getLink($sPic, $sURL);

                if($bEdit)
                {
                  $sURL = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK));
                  $sPic= $oHTML->getPicture(CONST_PICTURE_EDIT);
                  $sHTML.= $oHTML->getLink($sPic, $sURL).' ';

                  $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_ATTACHMENT, 0, array('taskpk' => (int)$asTask['taskfk']));
                  $sPic= $oHTML->getPicture($this->getResourcePath().'pictures/attachment.png', 'Attach a file');
                  $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => 'AjaxPopup(\''.$sURL.'\', \'#body\');'));

                  if($sAccess)
                  {
                    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DELETE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk']);
                    $sPic= $oHTML->getPicture(CONST_PICTURE_DELETE);
                    $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this task and all its attachments ?\')){ return false; }'));
                  }
                }

                $sHTML.= $oHTML->getBlocEnd();
                $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocEnd();
                $nCount++;
                $nLineNb++;
               }
              $bRead = $oResult->readNext();
            }
          }
          else
          {
            $sHTML.= $oHTML->getBlocStart('allId');
            $nLineNb = 1;
            $nCount = 0;
            while($bRead)
            {
                $asTask = $oResult->getData();
                if(($nLineNb%2) == 0)
                   $sExtraClass = ' taskRowEven ';
                else
                   $sExtraClass = '';

                if($asTask['status'])
                   $sExtraClass.= ' taskRowDone ';

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'projectView_taskrow '.$sExtraClass));
                $asTaskActor = explode(',', $asTask['actors']);

                $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk'=>$pnPK));

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowShortCell'));
                $sHTML.= $oHTML->getText($nLineNb);
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowWhoWhen'));
                if(isset($asUser[(int)$asTask['creatorfk']]))
                {
                    $sHTML.= $oHTML->getText('Created by ');
                    $sHTML.= $oHTML->getText($asUser[(int)$asTask['creatorfk']]['firstname'], array('style' => 'font-weight: bold;'));
                    $sHTML.= $oHTML->getCarriageReturn();
                }
                $sHTML.= $oHTML->getNiceTime($asTask['date_created']);
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowProgress'));

                $sjavascript = "
                $('#taskCursor_".$asTask['taskfk']." div' ).slider({
                value:".(int)$asTask['progress'].",
                min: 0,
                max: 100,
                step: 1,
                slide: function( event, ui ) {
                $('#taskProgress_".$asTask['taskfk']."').html(ui.value);
                      }
                  });

                $('#taskCursor_".$asTask['taskfk']."').fadeToggle();";
                $sHTML.= $oHTML->getLink($asTask['progress'], 'javascript:;', array('id' =>'taskProgress_'.$asTask['taskfk'], 'onclick'=>$sjavascript));
                $sHTML.= $oHTML->getText('%');

                $sHTML.= $oHTML->getBlocStart('taskCursor_'.$asTask['taskfk'], array('class' => 'sliderContainer hidden'));

                $sHTML.= $oHTML->getBlocStart('',  array('class' => 'sliderInner'));
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getSpanStart('',  array('class' => 'sliderSave'));
                $sURL = $oPage->getAjaxUrl('project', CONST_PROJECT_ACTION_UPDATE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'htmlid' => '#taskCursor_'.$asTask['taskfk']));
                $sPic = $oHTML->getPicture(CONST_PICTURE_SAVE);

                $sJavascript = "var sNewProgress = $('#taskProgress_".$asTask['taskfk']."').html();";
                $sJavascript.= "AjaxRequest('".$sURL."&progress='+sNewProgress,'#body'); ";

                $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
                $sHTML.= $oHTML->getSpanEnd();

                $sHTML.= $oHTML->getBlocEnd();
                $sHTML.= $oHTML->getBlocEnd();

                $sURL = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'],  array('prjpk' => $pnPK));
                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowDescription'));
                $sHTML.= $oHTML->getLink('<strong>'.substr($asTask['title'], 0, 100).'</strong>', $sURL);
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowUsers'));
                $sHTML.= $oHTML->getText(' => Affected to ');

                foreach($asTaskActor as $vKey => $nLoginPk)
                    $asTaskActor[$vKey] = $pasUser[$nLoginPk]['firstname'].' '.$pasUser[$nLoginPk]['lastname'];

                $sHTML.= implode(', ', $asTaskActor).' ';
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowAction'));
                $sPicId = 'taskPicId_'.$asTask['taskfk'];
                if($asTask['status'])
                {
                    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'taskdone' => 0, 'htmlid' => $sPicId));
                    $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_OK, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_INACTIVE, 'imgHidden' => CONST_PICTURE_CHECK_OK));
                }
                else
                {
                    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK, 'taskdone' => 1, 'htmlid' => $sPicId));
                    $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_INACTIVE, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_OK, 'imgHidden' => CONST_PICTURE_CHECK_INACTIVE));
                 }
                $sHTML.= $oHTML->getLink($sPic, $sURL);

                $sURL = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk'], array('prjpk' => $pnPK));
                $sPic= $oHTML->getPicture(CONST_PICTURE_EDIT);
                $sHTML.= $oHTML->getLink($sPic, $sURL).' ';

                $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_ATTACHMENT, 0, array('taskpk' => (int)$asTask['taskfk']));
                $sPic= $oHTML->getPicture($this->getResourcePath().'pictures/attachment.png', 'Attach a file');
                $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => 'AjaxPopup(\''.$sURL.'\', \'#body\');'));

                if($sAccess)
                {
                   $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DELETE, CONST_PROJECT_TYPE_TASK, (int)$asTask['taskfk']);
                   $sPic= $oHTML->getPicture(CONST_PICTURE_DELETE);
                   $sHTML.= ' '.$oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this task and all its attachments ?\')){ return false; }'));
                }
                $sHTML.= $oHTML->getBlocEnd();

                $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
                $sHTML.= $oHTML->getBlocEnd();
                $sHTML.= $oHTML->getBlocEnd();

                $nCount++;
                $nLineNb++;
                $bRead = $oResult->readNext();
            }
          }
         $sHTML.= $oHTML->getBlocEnd();
       return $sHTML;
   }

   /**
    * Display the project graphical view
    * @param integer $pnPK
    * @return string
    */

  private function _getProjectGraphicalView($pnPK)
  {
    if(!assert('is_integer($pnPK) && !empty($pnPK)'))
      return '';

    if(getValue('diaporama'))
      return $this->_getMagazineView($pnPK);

    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/project.css'));

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $nUserPk = $oLogin->getUserPk();
    $sQuery = 'SELECT * FROM project as p WHERE p.projectpk = '.$pnPK.' LIMIT 1';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('This project is not accessible or has been deleted.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

    //stock project data for later displaying
    $asProjectData = $oResult->getData();
    $asUser = $oLogin->getUserList();

    // =============================================
   //start displaying the project's tasks

    $sQuery = 'SELECT *, GROUP_CONCAT(loginfk SEPARATOR ",") as actors ';
    $sQuery.= ' FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK;
    $sQuery.= ' GROUP BY pt.taskfk  ORDER BY position, t.date_end, t.date_start, progress DESC ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('No task linked to this project.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }
    //------------------------------------------
    $sQuery = 'SELECT ta.*, t.progress ';
    $sQuery.= ' FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' INNER JOIN task_attachment as ta ON (t.taskpk = ta.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK.' ';
    $sQuery.= ' GROUP BY ta.task_attachmentpk ';
    $sQuery.= ' ORDER BY ta.taskfk, ta.date_upload DESC ';

    $oAttachResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oAttachResult->readFirst();

    $asAttachmentData = array();
    $nCount = 0;
    $nTotal = 0;
    while($bRead)
    {
      $nTaskFk = $oAttachResult->getFieldValue('taskfk', CONST_PHP_VARTYPE_INT);
      $nAttachmentPk = $oAttachResult->getFieldValue('task_attachmentpk', CONST_PHP_VARTYPE_INT);

      //calculating overall progress
      if(!isset($asAttachmentData[$nTaskFk]))
      {
        $nTotal+= (int)$oAttachResult->getFieldValue('progress');
        $nCount++;
       }

      $nParentFk = $oAttachResult->getFieldValue('parentfk', CONST_PHP_VARTYPE_INT);
      if(!$nParentFk)
        $asAttachmentData[$nTaskFk]['full'][$nAttachmentPk] = $oAttachResult->getData();
      else
        $asAttachmentData[$nTaskFk]['thumb'][$nParentFk] = $oAttachResult->getData();

      $bRead = $oAttachResult->readNext();
     }

    if($nCount > 0 && $nTotal > 0)
       $nTotal = round(($nTotal/$nCount), 2);

    $sHTML = '';
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskWallView'));

    //-----------------------------------------------
    //Menus / Filters
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskWallFilter'));

    $sClass = $this->_getProgressClass($nTotal);
    $sHTML.= $oHTML->getSpace(10);
    $sHTML.= $oHTML->getText('Overall progression: ');
    $sHTML.= $oHTML->getText($nTotal.'%', array('class' => $sClass));

    $sHTML.= $oHTML->getSpace(10);

    //javascript filter
    $sHTML.= $oHTML->getLink('All', 'javascript:;', array('class' => 'projectWallfilters', 'onclick' => '$(\'.taskFilterClass\').fadeIn();'));
    foreach($this->casTaskType as $sType)
    {
        $sHTML.= ' - '.$oHTML->getLink($sType, 'javascript:;', array('class' => 'projectWallfilters', 'onclick' => '$(\'.taskFilterClass\').hide(); $(\'.'.$sType.'\').fadeIn();'));
     }

    $sHTML.= ' >> '. $oHTML->getLink('File view', 'javascript:;', array('id' => 'filterViewFile', 'class' => 'projectWallfilters hidden', 'onclick' => '$(\'.isTextContent\').hide(); $(\'.attachment_container\').fadeIn(); $(this).hide(); $(\'#filterViewData\').show();'));
    $sHTML.= '    '. $oHTML->getLink('Data view', 'javascript:;', array('id' => 'filterViewData','class' => 'projectWallfilters', 'onclick' => '$(\'.attachment_container\').hide(); $(\'.isTextContent\').fadeIn();$(this).hide(); $(\'#filterViewFile\').show();'));

    $sHTML.= $oHTML->getBlocEnd();

    $oPage->addRequiredJsFile(CONST_PATH_JS_DRAGDROP);
    $oPage->addRequiredJsFile($this->getResourcePath().'js/project.js');

    $sJavascrip = "$(document).ready(function()";
    $sJavascrip.= "{";
    $sJavascrip.= "  initWallDragAndDrop();";
    $sJavascrip.= "});";
    $oPage->addCustomJs($sJavascrip);

    $sHTML.= '<form name="taskOrderForm" id="taskOrderFormId" style="display:none;" > ';
    $sHTML.= '<input type="hidden" name="projectfk" value="'.$pnPK.'" /> ';
    $sHTML.= '<input type="hidden" id="initOrder" name="initOrder" value="" /> ';
    $sHTML.= '<input type="hidden" id="currentOrder" name="currentOrder" value="" />';
    $sHTML.= '</form>';

    //-----------------------------------------------
    //Global view container
    $sHTML.= $oHTML->getBlocStart('', array('style' => 'position:relative'));
    $sHTML.= $oHTML->getListStart('taskWallDropZone', array('class' => 'taskWallContainer'));

    $bRead = $oResult->readFirst();
    while($bRead)
    {
      $asTask = $oResult->getData();
      $asTaskActor = explode(',', $asTask['actors']);
      $nTaskPk = (int)$asTask['taskpk'];

      $sHTML.= $oHTML->getListItemStart('Id_'.$nTaskPk, array('pk' => $nTaskPk, 'class' => 'taskWallTask taskFilterClass shadow '.$asTask['type'], 'style' => 'background-color:'.$this->casTaskTypeBgColor[$asTask['type']]));
      //-----------------------------------------------
      //Display the short task reduced view
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'isTextContent taskWallTaskContainer hidden'));

      $sURLView = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $pnPK));
      $sTitle = '<strong>'.$oHTML->getSpacedText(14, $asTask['title'], array('style' => 'color:'.$this->casTaskTypeColor[$asTask['type']]), 29).'</strong>';
      $sHTML.= $oHTML->getLink($sTitle, $sURLView, array('style' => 'color:'.$this->casTaskTypeColor[$asTask['type']]));

      $sHTML.= $oHTML->getCarriageReturn();
      $sHTML.= $oHTML->getText('Progress:');
      $sHTML.= $asTask['progress'].'%';
      $sHTML.= $oHTML->getCarriageReturn(2);

      $sHTML.= $oHTML->getText('Actors: ');
      $nCount = 0;
      foreach($asTaskActor as $vKey => $nLoginPk)
      {
        if($nCount == 5)
        {
          $sHTML.= $oHTML->getLink(' ...','javascript:;', array('style' => 'font-weight:bold; font-size:1.2em;', 'onclick' => '$(\'#taskView_'.$vKey.'\').slideToggle();'));
          $sHTML.= $oHTML->getSpanStart('taskView_'.$vKey, array('class' => 'hidden'));
        }

        $sHTML.= $oHTML->getCarriageReturn();
        $sHTML.= $oHTML->getText($asUser[$nLoginPk]['firstname'].' '.$asUser[$nLoginPk]['lastname']);

        $nCount++;
      }

      if($nCount >= 5)
        $sHTML.= $oHTML->getSpanEnd();
        $sHTML.= $oHTML->getBlocEnd();

      //-----------------------------------------------
      //Display the uploaded pictures

        $sHTML.= $oHTML->getBlocStart('', array('class' => ' attachment_container '));

        if(empty($asAttachmentData[$nTaskPk]))
          $sHTML.= $oHTML->getPicture($this->getResourcePath().'/pictures/no_attachment.png').'  ';
        else
          $sHTML.= $this->_getAttachmentToDisplay($nTaskPk, $asAttachmentData[$nTaskPk]);

        $sHTML.= $oHTML->getBlocEnd();
       //-----------------------------------------------
       // Display footer with actions
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskWallTaskFooter'));

      $sPic = $oHTML->getPicture(CONST_PICTURE_EDIT);
      $sHTML.= $oHTML->getLink($sPic, $sURLView);
      $sHTML.= $oHTML->getSpace(4);

      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_ATTACHMENT, 0, array('taskpk' => $nTaskPk));
      $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/attachment.png', 'Attach a file');
      $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => 'AjaxPopup(\''.$sURL.'\', \'#body\');'));
      $sHTML.= $oHTML->getSpace(4);

      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DELETE, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $pnPK));
      $sPic = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete task');
      $sHTML.= $oHTML->getLink($sPic, $sURL, array('onclick' => 'if(!window.confirm(\'Delete this task and all its attachments ?\')){ return false; }'));

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getListItemEnd();
      $bRead = $oResult->readNext();
    }

    $sHTML.= $oHTML->getListItemStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getListItemEnd();

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('style' => 'padding-left:10px;'));
    foreach($this->casTaskType as $sType)
      $sHTML.= $oHTML->gettext($sType.'  ', array('style' => 'color:'.$this->casTaskTypeColor[$sType].'; background-color:'.$this->casTaskTypeBgColor[$sType].';'));

    $sHTML.= $oHTML->getSpace(10);
    $sHTML.= $oHTML->getLink('Save new task order', 'javascript:;', array('onclick' => ' saveTaskOrder(true); '));
    $sHTML.= $oHTML->getBlocEnd($sType);
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to display the attachments
   * @param integer $pnTaskPk
   * @param array $pasAttachmentData
   * @return string
   */

  private function _getAttachmentToDisplay($pnTaskPk, $pasAttachmentData)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    if(!assert('is_array($pasAttachmentData)') || empty($pasAttachmentData))
      return  $oHTML->getPicture($this->getResourcePath().'/pictures/no_attachment.png').'  ';

    $sJavascript = '$(document).ready(function(){ $(".wallPictureLink").bind("click", function(){ $(this).fadeOut(function(){  if($(this).next("a.wallPictureLink").html()) { $(this).next().fadeIn(); } else {$(this).parent().find(".firstWallPic").fadeIn(); }  }); }); });';
    $oPage->addCustomJs($sJavascript);

    $sHTML = '';
    $nNbAttachment = count($pasAttachmentData);
    $asFullFile = array();
    $nCount = 0;

    foreach($pasAttachmentData['full'] as $nAttachPk => $asFullAttachment)
    {
      $sFullSizeLink = getRelativeUploadPath($asFullAttachment['file_path']);
      $sDescription =  $asFullAttachment['description'];
      $asFullFile[$nAttachPk]['filename'] = $asFullAttachment['file_name'];
      $asFullFile[$nAttachPk]['filepath'] = $sFullSizeLink;

      if($nCount == 0)
        $sClass = ' firstWallPic ';
      else
        $sClass = ' hidden ';

      if(isset($pasAttachmentData['thumb'][$nAttachPk]))
      {
        $asAttachment = $pasAttachmentData['thumb'][$nAttachPk];
        $sPic = $oHTML->getPicture(getRelativeUploadPath($asAttachment['file_path']), $sDescription, '', array('class' => 'wallPicture') ).'  ';
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('class' => 'wallPictureLink '.$sClass, 'fullsizeurl' => $sFullSizeLink));
      }
      else
      {
        $asAttachment = $pasAttachmentData['full'][$nAttachPk];
        $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/unknown_attachment.png', $sDescription, '', array('class' => 'wallPicture') ).'  ';
        $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('class' => 'wallPictureLink '.$sClass, 'fullsizeurl' => $sFullSizeLink));
      }
      $nCount++;
    }

    $nNbPicture = count($pasAttachmentData['full']);
    $sPic = $oHTML->getPicture($this->getResourcePath().'pictures/attachment.png', 'Attachments');
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'PictureGalleryText'));
    $sHTML.= $oHTML->getLink($nNbPicture.' '.$sPic, 'javascript:;', array('onclick' => '$(\'#wallDownloadLinks_'.$pnTaskPk.'\').slideToggle();'));

    $sHTML.= $oHTML->getBlocStart('wallDownloadLinks_'.$pnTaskPk, array('class' => 'wallDownloadListContainer hidden '));
    foreach($asFullFile as $nAttachmentFk => $asFileData)
    {
      $sHTML.= $oHTML->getLink($asFileData['filename'], CONST_CRM_DOMAIN.$asFileData['filepath'], array('target' => '_blank'));
      $sHTML.= $oHTML->getCarriageReturn();
    }
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  private function _getAjaxProjectList()
  {
     $sStatus = getValue('status');
     $sData = $this->_getProject($sStatus);

     return array('data' => mb_convert_encoding($sData, 'utf8'), 'action' => '$(\'body\').scrollTop(100);');
  }

  private function _getProjectResultList($psStatus)
  {
     //check if it is not empty
     $oHTML = CDependency::getComponentByName('display');

      $sHTML = $oHTML->getBlocStart('cprojectContainer_'.$psStatus);
      $sHTML.= $this->_getProject((boolean)$psStatus);
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
  }

  /**
   * Get the Form to add or edit Project
   * @param integer projectpk $pnPk
   * @return string
   */

  private function _getProjectForm($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return '';

    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/project.css'));
    $oPage->addRequiredJsFile($this->getResourcePath().'js/project.js');

    $oLogin = CDependency::getComponentByName('login');
    $nUserPk = $oLogin->getUserPk();
    $asUser = $oLogin->getUserList(0,true,false);
    $asViewers = array();
    $asEditors = array();
    if(empty($pnPk))
    {
      $asFieldValue = array('title'=>'','description'=>'', 'date_start'=>date('Y-m-d'), 'date_end'=>'', 'progress'=>0, 'creatorfk'=>$nUserPk, 'ownerfk'=>$nUserPk, 'status' => 0);
      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_SAVEADD, CONST_PROJECT_TYPE_PROJECT);
      $sTitle = $oHTML->getText('Add a project');

    }
    else
    {
      $oDB = CDependency::getComponentByName('database');
      $sQuery = 'SELECT prj.*,GROUP_CONCAT(prav.loginfk SEPARATOR ",") as viewers,GROUP_CONCAT(prae.loginfk SEPARATOR ",") as editors FROM project as prj LEFT JOIN project_actors as prav ON (prav.projectfk = prj.projectpk AND prav.type = "view") LEFT JOIN project_actors as prae ON (prae.projectfk = prj.projectpk AND prae.type = "edit")  WHERE projectpk = '.$pnPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the project you want to edit. It may have been deleted.');

      $asFieldValue = $oDbResult->getData();
      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_SAVEEDIT, CONST_PROJECT_TYPE_PROJECT, $pnPk);
      $sTitle = $oHTML->getText('Edit this project');

      $sViewers = $asFieldValue['viewers'];
      $asViewers = explode(',',$sViewers);

      $sEditors = $asFieldValue['editors'];
      $asEditors = explode(',',$sEditors);
    }

    $sHTML= $oHTML->getBlocStart();

    //div including the form
    $sHTML.= $oHTML->getBlocStart('projectFormId');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('projectFormData');
    $oForm->setFormParams('', true, array('submitLabel' => 'Save project', 'action' => $sURL));
    $oForm->setFormDisplayParams(array('style' => ''));

    $oForm->addField('misc', '', array('type' => 'text', 'text'=>$sTitle, 'class' => 'h2'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('input', 'title', array('label'=>'title', 'class' => '', 'value' => $asFieldValue['title'], 'required' =>  true));
    $oForm->setFieldControl('title', array('jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

    $oForm->addField('textarea', 'description', array('label'=>'Description', 'value' => $asFieldValue['description']));
    $oForm->setFieldControl('description', array('jsFieldMaxSize' => 2048));

    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('input', 'date_start', array('type'=>'date', 'label'=>'Starting on', 'class' => '', 'value' => $asFieldValue['date_start']));
    $oForm->setFieldControl('date_start', array('jsFieldDate' => ''));

    $oForm->addField('input', 'date_end', array('type'=>'date', 'label'=>'Ending on', 'class' => '', 'value' => $asFieldValue['date_end']));
    $oForm->setFieldControl('date_end', array('jsFieldDate' => '', 'jsFieldGreaterThan' => 'form[name=date_start]'));

    $asVisibility = array(0 =>array('label'=>'Private'),1=>array('label'=>'Public'),2=>array('label'=>'Custom'));
    $oForm->addField('select', 'visibility', array('label' => 'Visibility ','onchange'=>'showHideUserList(this.value);'));

    if(isset($asFieldValue['is_public']))
      $nPublic = $asFieldValue['is_public'];
    else
      $nPublic = '-1';

    foreach($asVisibility as $nKey => $asVisible)
    {
      if($nKey == $nPublic)
        $oForm->addOption('visibility', array('value'=> $nKey, 'label' => $asVisible['label'],'selected'=>'selected'));
      else
        $oForm->addOption('visibility', array('value'=> $nKey, 'label' => $asVisible['label']));
    }

    if(isset($asFieldValue['is_public']) && $asFieldValue['is_public'] == 2)
      $sClass = '';
    else
      $sClass = 'hidden';

    $oForm->addField('select', 'users[]', array('label' => 'Shared with ', 'multiple' => 'multiple'));
    $oForm->setFieldDisplayParams('users[]', array('class'=>$sClass.' userList'));

    foreach($asUser as $asUserData)
    {
      if(in_array($asUserData['loginpk'],$asViewers))
        $oForm->addOption('users[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
      else
        $oForm->addOption('users[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
    }

    $oForm->addField('select', 'user_editors[]', array('label' => ' Can be edited by ', 'multiple' => 'multiple'));
    $oForm->setFieldControl('user_editors[]', array('jsFieldNotEmpty' => ''));

    if(isset($asFieldValue['is_edit_public']) && $asFieldValue['is_edit_public'] == 1)
      $oForm->addOption('user_editors[]', array('value'=>0, 'label' => '--Everyone--','selected'=>'selected'));
    else
      $oForm->addOption('user_editors[]', array('value'=>0, 'label' => '--Everyone--'));

    foreach($asUser as $asUserData)
    {
      if(in_array($asUserData['loginpk'], $asEditors))
        $oForm->addOption('user_editors[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
      else
        $oForm->addOption('user_editors[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
    }

    $oForm->addField('input', 'progress', array('label'=>'Progress (%)', 'class' => '', 'value' => $asFieldValue['progress']));
    $oForm->setFieldControl('progress', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxValue' => 100));

    if(!empty($asFieldValue['status']))
      $oForm->addField('checkbox', 'status', array('label'=> 'Finished','class'=>'checkBoxTop','checked' => 'checked','value'=>1));
     else
      $oForm->addField('checkbox', 'status', array('label'=> 'Finished','class'=>'checkBoxTop','value'=>1));

     $oForm->addField('hidden', 'creatorfk', array( 'value' => $asFieldValue['creatorfk']));
     $oForm->addField('hidden', 'ownerfk', array( 'value' => $asFieldValue['ownerfk']));

    $sHTML.= $oForm->getDisplay();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

  /**
   * Function to save the project
   * @param integer $pnPk
   * @return array
   */

  private function _getProjectSaveEdit($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return array('error' => 'No project found.');

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');

    //control posted datas
    if(empty($_POST))
    {
      assert('false; // save project without post data');
      return array('error' => 'No project data. Sorry, can\'t save the project.');
     }

    $sTitle = getValue('title');
    $sDesc = getValue('description');
    $sDateStart = getValue('date_start');
    $sDateEnd = getValue('date_end');
    $nProgress = (float)getValue('progress', 0);
    $nStatus = (int)getValue('status', 0);

    $nCreatorfk = (int)getValue('creatorfk', 0);
    $nOwnerfk = (int)getValue('ownerfk', 0);
    $nVisibility = (int)getValue('visibility', 0);
    $asUsers = getValue('users');
    $asEditors = getValue('user_editors');

    if(in_array('0', $asEditors))
      $nEditPublic = 1;
    else
      $nEditPublic = 0;

    if(empty($sTitle))
      return array('error' => 'Title is required.');

    if($nVisibility == 2 && empty($asUsers))
      return array('error' => 'Select the users to be shared with.');

    if(!empty($sDateStart) && !empty($sDateEnd) && $sDateStart > $sDateEnd)
      return array('error' => 'The project starting date can\'t be after the ending date.');

    if($nProgress < 0 || $nProgress > 100)
      return array('error' => 'Progress is a number between 0 and 100%.');

    if(!empty($pnPk))
    {
      $sQuery = 'SELECT * FROM project WHERE projectpk = '.$pnPk;
      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead)
        return array('error' => __LINE__.' - Couldn\'t find the project you want to edit. It may have been deleted.');

      $sQuery = 'UPDATE project SET title = '.$oDB->dbEscapeString($sTitle).', ';
      $sQuery.= ' description = '.$oDB->dbEscapeString($sDesc).', ';
      $sQuery.= ' date_start = '.$oDB->dbEscapeString($sDateStart).', ';
      $sQuery.= ' date_end = '.$oDB->dbEscapeString($sDateEnd).', ';
      $sQuery.= ' progress = '.$oDB->dbEscapeString($nProgress).', ';
      $sQuery.= ' is_public = '.$oDB->dbEscapeString($nVisibility).',';
      $sQuery.= ' is_edit_public = '.$oDB->dbEscapeString($nEditPublic).',';
      $sQuery.= ' status = '.$oDB->dbEscapeString($nStatus).' ';
      $sQuery.= ' WHERE projectpk = '.$pnPk;

      $oResult = $oDB->ExecuteQuery($sQuery);
      $nProjectPk = $pnPk;

      //Remove all the actors existing and replace with the posted values
      $sQuery = 'DELETE FROM project_actors where projectfk = '.$nProjectPk.'';
      $oDB->ExecuteQuery($sQuery);

      if($nVisibility == 2)
      {
       if(!empty($asUsers))
       {
        foreach($asUsers as $sUserKey)
        {
          $asQuery[] = '('.$nProjectPk.', '.(int)$sUserKey.',"view")';
         }

        if(!empty($asQuery))
        {
          $sQuery = 'INSERT INTO `project_actors` (`projectfk`, `loginfk`, `type`) VALUES '.implode(', ',$asQuery);
          $oResult = $oDB->ExecuteQuery($sQuery);
          if(!$oResult)
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
        }
       }
      }

      if($nEditPublic == 0)
      {
       if(!empty($asUsers))
       {
        foreach($asEditors as $sUserKey)
        {
         $asMyQuery[] = '('.$nProjectPk.', '.(int)$sUserKey.',"edit")';
        }
       }

       if(!empty($asMyQuery))
       {
         $sQuery = 'INSERT INTO `project_actors` (`projectfk`, `loginfk`, `type`) VALUES '.implode(', ',$asMyQuery);
         $oResult = $oDB->ExecuteQuery($sQuery);
         if(!$oResult)
           return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
        }
      }
    }
    else
    {
      $sQuery = 'INSERT INTO project (title, description, date_start, date_end, progress,creatorfk, ownerfk, is_public,status,date_create,is_edit_public) ';
      $sQuery.= ' VALUES('.$oDB->dbEscapeString($sTitle).', '.$oDB->dbEscapeString($sDesc).', ';
      $sQuery.= $oDB->dbEscapeString($sDateStart).', '.$oDB->dbEscapeString($sDateEnd).', ';
      $sQuery.= $oDB->dbEscapeString($nProgress).', '.$oDB->dbEscapeString($nCreatorfk).', ';
      $sQuery.= $oDB->dbEscapeString($nOwnerfk).', '.$oDB->dbEscapeString($nVisibility).', ';
      $sQuery.= $oDB->dbEscapeString($nStatus).', "'.date('Y-m-d H:i:s').'",'.$oDB->dbEscapeString($nEditPublic).')';

      $oResult = $oDB->ExecuteQuery($sQuery);
      $nProjectPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);

      $sQuery = 'DELETE FROM project_actors where projectfk = '.$nProjectPk.'';
      $oDB->ExecuteQuery($sQuery);

      if($nVisibility == 2)
      {
       foreach($asUsers as $sUserKey)
       {
         $asQuery[] = '('.$nProjectPk.', '.(int)$sUserKey.',"view")';
        }

       if(!empty($asQuery))
       {
        $sQuery = 'INSERT INTO `project_actors` (`projectfk`, `loginfk`, `type`) VALUES '.implode(', ',$asQuery);
        $oResult = $oDB->ExecuteQuery($sQuery);
        if(!$oResult)
            return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
        }
      }

      if($nEditPublic == 0)
      {
       foreach($asEditors as $sUserKey)
        {
         $asEditQuery[] = '('.$nProjectPk.', '.(int)$sUserKey.',"edit")';
        }

       if(!empty($asEditQuery))
       {
         $sEditQuery = 'INSERT INTO `project_actors` (`projectfk`, `loginfk`, `type`) VALUES '.implode(', ',$asEditQuery);
         $oResult = $oDB->ExecuteQuery($sEditQuery);
         if(!$oResult)
          return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save users');
         }
       }
     }

    if(!$oResult)
      return array('error' => __LINE__.' - Couldn\'t save the project');

    $oPage = CDependency::getComponentByName('page');
    $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);
    return array('notice' => 'Project saved.', 'url' => $sURL);
  }

  /**
   * Function to delete the project
   * @param integer $pnPk
   * @return array
   */

  private function _getProjectDelete($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    $oDB = CDependency::getComponentByName('database');
    $sQuery = 'SELECT * FROM project WHERE projectpk = '.$pnPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk.'. The project may have already been deleted. Try reloading the page.');

    //DELETE Tasks
    if(!$this->_getProjectAttachmentDelete($pnPk))
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    $sQuery = 'DELETE FROM task WHERE taskpk IN(SELECT taskfk FROM  project_task WHERE projectfk = '.$pnPk.') ';
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    $sQuery = 'DELETE FROM project_task WHERE projectfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    //DELETE project managers
    $sQuery = 'DELETE FROM project_actors WHERE projectfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    //DELETE project
    $sQuery = 'DELETE FROM project WHERE projectpk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the project #'.$pnPk);

    $oPage = CDependency::getComponentByName('page');
    $sUrl = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_PROJECT);

    return array('notice' => 'Project deleted.', 'url' => $sUrl);
  }

 /**
  * Function to save the project tasks order
  * @return array
  */

  private function _getProjectSaveTaskOrder()
  {
    $sProjectPk = (int)getValue('projectfk', 0);
    $sTaskOrder = getValue('currentOrder', '');

    if(empty($sProjectPk) || empty($sTaskOrder))
      return array('error' => __LINE__.' - Missing parameters');

    $asTaskOrder = explode(';', $sTaskOrder);
    foreach($asTaskOrder as $nkey => $sTaskId)
    {
      if(empty($sTaskId))
        unset($asTaskOrder[$nkey]);
     }
    if(empty($asTaskOrder))
       return array('error' => __LINE__.' - Missing parameters');

    $asTaskPosition = array_flip($asTaskOrder);
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $sQuery = 'SELECT * FROM project_task WHERE projectfk = '.$sProjectPk.' AND taskfk IN('.implode(',', $asTaskOrder).') ';
    $sQuery.= ' GROUP BY taskfk ';

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
     return array('error' => __LINE__.' - Couldn\'t get the tasks or the project. ['.$sQuery.']');

    while($bRead)
    {
      $ntaskFk = (int)$oDbResult->getFieldValue('taskfk');
      $asQuery[] = 'UPDATE project_task SET position = "'.(int)$asTaskPosition[$ntaskFk].'" WHERE taskfk = '.$ntaskFk.' AND projectfk = '.$sProjectPk;
      $bRead = $oDbResult->readNext();
     }

    if(count($asTaskOrder) != count($asQuery))
      return array('error' => __LINE__.' - Task count doesn\'t match.[field: '.count($asTaskOrder).' / query:'.count($asQuery).']');

    foreach($asQuery as $sQuery)
    {
      $oDbResult = $oDB->ExecuteQuery($sQuery);
    }

    return array('message' => 'New task order saved.');
  }

  //====================================================================================================
  //====================================================================================================
  //TASK

  /**
   * Function to get the list of the tasks
   * @param boolean $pbMyTask
   * @param boolean $pbManagingTask
   * @return string
   */

  private function _getAjaxTaskList()
  {
     $sMyTask = getValue('mytask');
     $sManageTask = getValue('managetask');

     $sData = $this->_getTaskResultList($sMyTask,$sManageTask);

     return array('data' => mb_convert_encoding($sData, 'utf8'), 'action' => '$(\'body\').scrollTop(100);');
  }


  private function _getTaskResultList($psMyTask,$psManageTask)
  {
      if(empty($psMyTask))
         $psMyTask = 0;
      if(empty($psManageTask))
         $psManageTask = 0;

      $sHTML = $this->_getTasks((boolean)$psMyTask,(boolean)$psManageTask);
      return $sHTML;
  }


  private function _getTaskList($pbMyTask = true, $pbManagingTask = false)
  {
    //TODO: Dont have time to get tabs content in Ajax, so for now everything is displayed at one.

    $pbMyTask = true;
    $pbManagingTask = true;
    $bProjectList = false;
    //----------------------------------------------------

    $sOrder = getValue(CONST_PROJECT_TASK_SORT_PARAM, '');
    if(empty($sOrder) || $sOrder == 'date')
       $sSort = ' t.date_end, t.date_created ';
    else
    {
      $bProjectList = true;
      $sSort = ' p.projectpk, t.date_end, t.date_created ';
     }

    /* @var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/project.css');

    $sHTML = '';
    $sHTML = $oHTML->getTitleLine('My task List', $this->getResourcePath().'/pictures/project_48.png');
    $sHTML.= $oHTML->getCarriageReturn();

    $sPicture = $oHTML->getpicture(CONST_PICTURE_ADD);
    $sHTML.= $oHTML->getLink($sPicture.' Add a task', 'javascript:;', array('onclick' => '$(\'#quickTaskFormId\').fadeToggle();'));

    $sHTML.= $this->_getQuickTaskForm();
    $sHTML.= $oHTML->getSpace(20);
    $sPicture = $oHTML->getpicture(CONST_PICTURE_SORT);

    if(empty($sOrder) || $sOrder == 'date')
    {
       $sURL = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK, 0, array(CONST_PROJECT_TASK_SORT_PARAM => 'project'));
       $sHTML.= $oHTML->getLink($sPicture.' Sort by project', $sURL);
     }
     else
     {
        $sURL = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK, 0, array(CONST_PROJECT_TASK_SORT_PARAM => 'date'));
        $sHTML.= $oHTML->getLink($sPicture.' Sort by date', $sURL);
      }

     $sHTML.= $oHTML->getSpace(10);
     $sHTML.= $oHTML->getLink('Show/Hide done tasks', 'javascript:;', array('onclick' =>'$(\'.taskDone\').fadeToggle(); '));
     $sHTML.= $oHTML->getCarriageReturn(2);

     $asTabs = array();
     $asTabs[] = array('tabtitle' => 'My todo list','tabOptions'=>array('tabId'=>'','class'=>'tab_display tab_selected'),'content' => $this->_getTasks($pbMyTask=true,$pbManagingTask=false),'contentOptions'=>array('contentId'=>'taskListMyId','class'=>'display_tab projectContainer','style'=>'display:block;'));
     $asTabs[] = array('tabtitle' => 'Tasks I\'m following','tabOptions'=>array('tabId'=>'','class'=>'tab_display'),'content' => $this->_getTasks($pbMyTask=false,$pbManagingTask=true),'contentOptions'=>array('contentId'=>'taskListOtherId','class'=>'display_tab hidden projectContainer','style'=>'display:none;'));

     $sHTML.= $oHTML->getBlocStart('', array('class' =>'bottom_container'));
     $sHTML.= $oHTML->getBlocStart('',  array('class' => 'tabs_container'));
     $sHTML.= $oHTML->getTabs('', $asTabs);
     $sHTML.= $oHTML->getBlocEnd();
     $sHTML.= $oHTML->getBlocEnd();
     $sHTML.= $oHTML->getCarriageReturn(2);

    return $sHTML;
  }

  /**
   * Function to get all the task and display them passing into another function
   * @param boolean $pbMyTask
   * @param boolean $pbManagingTask
   * @return string
   */

  private function _getTasks($pbMyTask,$pbManagingTask)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oPager = CDependency::getComponentByName('pager');
    $oPage->addCssFile($this->getResourcePath().'css/project.css');

    $nUserPk = $oLogin->getUserPk();
    $bProjectList = false;

    $sOrder = getValue(CONST_PROJECT_TASK_SORT_PARAM, '');
    if(empty($sOrder) || $sOrder == 'date')
      $sSort = ' t.date_end, t.date_created ';
    else
     {
       $bProjectList = true;
       $sSort = ' p.projectpk, t.date_end, t.date_created ';
     }

     $sHTML = '';
     $sHTML.= $oHTML->getBlocStart('taskContainer');
     $sHTML.= $oHTML->getBlocStart('', array('class'=>'homePageContainer','style' =>'padding: 0px;background-color:#FFFFFF;width: 100%;'));
     $sHTML.= $oHTML->getListStart('', array('class' => 'ablistContainer'));

     $sHTML.= $oHTML->getListItemStart('', array('class' => 'ablistHeader'));
     $sHTML.= $this->_getProjectTaskHeader();
     $sHTML.= $oHTML->getListItemEnd();


     if($pbMyTask)
     {
     //Select the task affected to me

     $sCountQuery = 'SELECT count(distinct taskpk) as nCount';
     $sCountQuery.= ' FROM task as t ';
     $sCountQuery.= ' INNER JOIN login as l ON (l.loginpk = t.creatorfk) ';
     $sCountQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
     $sCountQuery.= ' LEFT JOIN login as l2 ON (l2.loginpk = pt.loginfk) ';
     $sCountQuery.= ' LEFT JOIN project as p ON (p.projectpk = pt.projectfk) ';
     $sCountQuery.= ' WHERE t.taskpk IN (SELECT taskfk FROM project_task as pt WHERE loginfk = '.$nUserPk.') ';

     $oDbResult = $oDB->ExecuteQuery($sCountQuery);
     $bRead = $oDbResult->readFirst();
     $nNbResult = $oDbResult->getFieldValue('nCount',CONST_PHP_VARTYPE_INT);

     $sQuery = 'SELECT t.*, pt.projectfk, p.title as project_title, p.description as project_description ';
     $sQuery.= ', l.lastname, l.firstname, GROUP_CONCAT(CONCAT(l2.firstname," ", l2.lastname) SEPARATOR ", ") as name ';
     $sQuery.= ' FROM task as t ';
     $sQuery.= ' INNER JOIN login as l ON (l.loginpk = t.creatorfk) ';
     $sQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
     $sQuery.= ' LEFT JOIN login as l2 ON (l2.loginpk = pt.loginfk) ';
     $sQuery.= ' LEFT JOIN project as p ON (p.projectpk = pt.projectfk) ';
     $sQuery.= ' WHERE t.taskpk IN (SELECT taskfk FROM project_task as pt WHERE loginfk = '.$nUserPk.') ';
     $sQuery.= ' GROUP BY pt.taskfk ';
     $sQuery.= ' ORDER BY '.$sSort;

     $oPager->initPager();
     $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();

     $oResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oResult->readFirst();

     if(!$bRead)
     {
       $sHTML.= $oHTML->getBlocStart('', array('class' => 'notice2'));
       $sHTML.= $oHTML->getText('No task available.');
       $sHTML.= $oHTML->getBlocEnd();

       $sHTML.= $this->_getQuickTaskForm(true);
     }
     else
     {
       $asUser = $oLogin->getUserList();
       $nCount = 1;
       $anDisplayedTask = array();
       $nProject = 0;
       while($bRead)
       {
          $asTaskData = $oResult->getData();
          $nTaskPk = (int)$asTaskData['taskpk'];
          $anDisplayedTask[] = $nTaskPk;

          if($bProjectList && $asTaskData['projectfk'] != $nProject)
          {
            $nProject = $asTaskData['projectfk'];
            $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskList_projectTitle'));
            $sHTML.= $oHTML->getTitle('Project: '.$asTaskData['project_title'], 'h4', true);
            $sHTML.= $oHTML->getBlocEnd();
           }

           $sHTML.= $oHTML->getListItemStart();
           $sHTML.= $this->_getTaskListRow($asTaskData, $nUserPk, true, !$bProjectList);
           $sHTML.= $oHTML->getListItemEnd();
           $bRead = $oResult->readNext();
         $nCount++;
         }
      }
    }

   if($pbManagingTask)
    {
      //Select the task assigned from me

      $sCountQuery = 'SELECT count(*) as nCount FROM task as t ';
      $sCountQuery.= ' INNER JOIN login as l ON (l.loginpk = t.creatorfk) ';
      $sCountQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
      $sCountQuery.= ' LEFT JOIN login as l2 ON (l2.loginpk = pt.loginfk) ';
      $sCountQuery.= ' LEFT JOIN project as p ON (p.projectpk = pt.projectfk) ';
      $sCountQuery.= ' WHERE (t.creatorfk = '.$nUserPk.' OR p.ownerfk = '.$nUserPk.') ';

      $oDbResult = $oDB->ExecuteQuery($sCountQuery);
      $bRead = $oDbResult->readFirst();
      $nNbResult = $oDbResult->getFieldValue('nCount',CONST_PHP_VARTYPE_INT);

      $sQuery = 'SELECT t.*, pt.projectfk, p.title as project_title, p.description as project_description ';
      $sQuery.= ', l.lastname, l.firstname, GROUP_CONCAT(CONCAT(l2.firstname," ", l2.lastname) SEPARATOR ", ") as name ';
      $sQuery.= ' FROM task as t ';
      $sQuery.= ' INNER JOIN login as l ON (l.loginpk = t.creatorfk) ';
      $sQuery.= ' LEFT JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
      $sQuery.= ' LEFT JOIN login as l2 ON (l2.loginpk = pt.loginfk) ';
      $sQuery.= ' LEFT JOIN project as p ON (p.projectpk = pt.projectfk) ';
      $sQuery.= ' WHERE (t.creatorfk = '.$nUserPk.' OR p.ownerfk = '.$nUserPk.') ';

      if(!empty($anDisplayedTask))
        $sQuery.= ' AND pt.taskfk NOT IN ('.implode(',', $anDisplayedTask).') ';

      $sQuery.= ' GROUP BY pt.taskfk ';
      $sQuery.= ' ORDER BY '.$sSort;

      $oPager->initPager();
      $sQuery.= ' LIMIT '.$oPager->getSqlOffset().','.$oPager->getLimit();
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oResult->readFirst();

      if(!$bRead)
      {
        $sHTML.= $oHTML->getBlocStart('', array('class' => 'notice2'));
        $sHTML.= $oHTML->getText('No task available.');
        $sHTML.= $oHTML->getBlocEnd();

        $sUrl = $oPage->getUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK);
        $sHTML.=  $this->_getQuickTaskForm(true);
      }
      else
      {
        $nCount = 1;
        $nProject = 0;
        $anDisplayedTask = array();
        while($bRead)
        {
          $asTaskData = $oResult->getData();
          $nTaskPk = (int)$asTaskData['taskpk'];
          $anDisplayedTask[] = $nTaskPk;

          if($bProjectList && $asTaskData['projectfk'] != $nProject)
          {
            $nProject = $asTaskData['projectfk'];
            $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskList_projectTitle'));
            $sHTML.= $oHTML->getTitle('Project: '.$asTaskData['project_title'], 'h4', true);
            $sHTML.= $oHTML->getBlocEnd();
           }

         $sHTML.= $oHTML->getListItemStart();
         $sHTML.= $this->_getTaskListRow($asTaskData, $nUserPk, false, !$bProjectList);
         $sHTML.= $oHTML->getListItemEnd();

         $bRead = $oResult->readNext();
         $nCount++;
          }
        }
      }

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK,0,array('mytask'=>$pbMyTask,'managetask'=>$pbManagingTask));
    $asPagerUrlOption = array('ajaxTarget' => 'taskContainer');

    if($nNbResult > 0)
      $sHTML.= $oPager->getDisplay($nNbResult, $sURL,$asPagerUrlOption);

     $sHTML.= $oHTML->getBlocEnd();

     return $sHTML;
  }

  /**
   * Display the Header with different columns in the header
   * @return string
   */

  private function _getProjectTaskHeader()
  {
      $oHTML = CDependency::getComponentByName('display');

      $sHTML = $oHTML->getBlocStart('', array('class' =>'list_row '));
      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_row_data'));

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:9%;'));
      $sHTML.= $oHTML->getText('Start/End Date');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:10%;'));
      $sHTML.= $oHTML->getText('Completion Percentage ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'width:10%;'));
      $sHTML.= $oHTML->getText('Description');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell','style' => 'min-width: 31%;'));
      $sHTML.= $oHTML->getText('Project');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell ','style' => 'min-width: 7%;'));
      $sHTML.= $oHTML->getText('Assigned ');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'list_cell ','style' => 'min-width:7%;float:right;'));
      $sHTML.= $oHTML->getText('Action');
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
      $sHTML.= $oHTML->getBlocEnd();

      $sHTML.= $oHTML->getBlocEnd();
      $sHTML.= $oHTML->getBlocEnd();

      return $sHTML;
   }

   /**
    * Display the tasks on the list
    * @param array $pasTaskData
    * @param integer $pnUserPk
    * @param boolean $pbUsersTask
    * @param boolean $pbDisplayProject
    * @return string
    */

  private function _getTaskListRow($pasTaskData, $pnUserPk, $pbUsersTask = true, $pbDisplayProject = true)
  {
   $oRight = CDependency::getComponentByName('right');
   $oLogin = CDependency::getComponentByName('login');

   $sAccess = $oRight->canAccess($this->_getUid(),CONST_ACTION_DELETE,$this->getType(),$this->getPk());

    if(!assert('is_array($pasTaskData) && !empty($pasTaskData)'))
      return '';
    if(!assert('is_integer($pnUserPk) && !empty($pnUserPk)'))
      return '';

    $nTaskPk = (int)$pasTaskData['taskpk'];
    $nProjectFk = (int)$pasTaskData['projectfk'];

    $bEdit = $this->_canEdit($nProjectFk,$oLogin->getUserPk());

    /* @var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');

    $oPage->addRequiredJsFile(CONST_PATH_JS_SLIDER);
    $oPage->addCssFile(CONST_PATH_CSS_SLIDER);

    $sHTML  = '';

    if($pasTaskData['status'])
       $sExtraClass= 'taskDone ';
    else
       $sExtraClass= ' ';

    $sHTML.= $oHTML->getListItemStart('');
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'myTaskRow '.$sExtraClass.''));

    $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:200px; float:left;  padding: 0 5px;'));
    $sHTML.= $oHTML->getText('Created : ');
    $sHTML.= $oHTML->getNiceTime($pasTaskData['date_start'], 0, true);
    $sHTML.= $oHTML->getCarriageReturn();

    $sClass = $this->_getPriorityLevelCss($pasTaskData['date_end']);
    $sHTML.= $oHTML->getText('Deadline : ');
    $sHTML.= $oHTML->getSpanStart('', array('class' => $sClass));
    $sHTML.= $oHTML->getNiceTime($pasTaskData['date_end'], 0, true);
    $sHTML.= $oHTML->getSpanEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocStart('', array('class' => 'taskRowProgress', 'style' => 'width:85px; float:left;'));

    $sjavascript = "
    $('#taskCursor_".$nTaskPk." div' ).slider({
          value:".(int)$pasTaskData['progress'].", min: 0, max: 100, step: 1,
          slide: function( event, ui ) { $('#taskProgress_".$nTaskPk."').html(ui.value); }
        });
      $('#taskCursor_".$nTaskPk."').fadeToggle(); ";

    $sHTML.= $oHTML->getLink($pasTaskData['progress'], 'javascript:;', array('id' =>'taskProgress_'.$nTaskPk, 'onclick'=>$sjavascript));
    $sHTML.= $oHTML->getText('%');

    $sHTML.= $oHTML->getBlocStart('taskCursor_'.$nTaskPk, array('class' => 'sliderContainer hidden'));

    $sHTML.= $oHTML->getBlocStart('',  array('class' => 'sliderInner'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getSpanStart('',  array('class' => 'sliderSave'));
    $sURL = $oPage->getAjaxUrl('project', CONST_PROJECT_ACTION_UPDATE, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk, 'htmlid' => '#taskCursor_'.$nTaskPk));
    $sPic = $oHTML->getPicture(CONST_PICTURE_SAVE);
    $sJavascript = "var sNewProgress = $('#taskProgress_".$nTaskPk."').html();";
    $sJavascript.= "AjaxRequest('".$sURL."&progress='+sNewProgress,'#body'); ";
    $sHTML.= $oHTML->getLink($sPic, 'javascript:;', array('onclick' => $sJavascript));
    $sHTML.= $oHTML->getSpanEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sUrl = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk));
    $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:300px; float:left;  padding: 0 5px;'));
    $sHTML.= $oHTML->getText('Title: ', array('class' => 'strong'));
    $sHTML.= $oHTML->getLink($pasTaskData['title'], $sUrl, array('class' => 'task_name'));
    $sHTML.= $oHTML->getCarriageReturn();

    $sHTML.= $oHTML->getText('Description: ', array('class' => 'strong'));
    $sHTML.= $oHTML->getText($pasTaskData['description'], array(), 65);

    $sHTML.= $oHTML->getBlocEnd();

    if($pbDisplayProject)
    {
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:200px; float:left;  padding: 0 5px;'));
      $sUrl = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectFk);
      $sHTML.= $oHTML->getLink($pasTaskData['project_title'], $sUrl, array('class' => 'task_name'));

      $sHTML.= $oHTML->getBlocEnd();
    }

    if($pbUsersTask)
    {
      //display who the task is affected to
      if($pasTaskData['creatorfk'] != $pnUserPk)
      {
        $sHTML.= $oHTML->getBlocStart('', array('style' => 'float:left; max-width: 200px; padding: 0 5px;'));
        $sHTML.= $oHTML->getText('By : ', array('class' => 'strong'));
        $sHTML.= $oHTML->getText($pasTaskData['firstname'].' '.$pasTaskData['lastname']);
      }
      else
      {
        $sHTML.= $oHTML->getBlocStart('', array('style' => 'float:left; max-width: 200px; padding: 0 5px;'));
        $sHTML.= $oHTML->getText('personal task', array('class' => 'fontItalic'));
       }

       $sHTML.= $oHTML->getBlocEnd();
    }
    else
    {
      //display who gave you the task
      $sHTML.= $oHTML->getBlocStart('', array('style' => 'float:left; max-width: 200px; padding: 0 5px;'));
      $sHTML.= $oHTML->getText('To: ', array('class' => 'strong'));
      $sHTML.= $oHTML->getText($pasTaskData['name']);
      $sHTML.= $oHTML->getBlocEnd();
     }

     $sHTML.= $oHTML->getBlocStart('', array('style' => 'width:85px; float:right;'));

     $sPicId = 'taskPicId_'.$nTaskPk;
     if($pasTaskData['status'])
     {
       $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk, 'taskdone' => 0, 'htmlid' => $sPicId));
       $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_OK, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_INACTIVE, 'imgHidden' => CONST_PICTURE_CHECK_OK));
     }
     else
     {
       $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_DONE, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk, 'taskdone' => 1, 'htmlid' => $sPicId));
       $sPic = $oHTML->getPicture(CONST_PICTURE_CHECK_INACTIVE, '', '', array('id' =>$sPicId, 'imgDisplay' => CONST_PICTURE_CHECK_OK, 'imgHidden' => CONST_PICTURE_CHECK_INACTIVE));
     }

     $sHTML.= $oHTML->getLink($sPic, $sURL);

     $sUrl = $oPage->getUrl('project', CONST_ACTION_EDIT, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk));
     $sHTML.= $oHTML->getPicture(CONST_PICTURE_VIEW, 'View task', $sUrl);
     $sHTML.= $oHTML->getSpace(2);

     if($bEdit)
     {
      $sHTML.= $oHTML->getPicture(CONST_PICTURE_EDIT, 'Edit task', $sUrl);
      $sHTML.= $oHTML->getSpace(2);

     if($sAccess)
     {
        $sUrl = $oPage->getAjaxUrl('project', CONST_ACTION_DELETE, CONST_PROJECT_TYPE_TASK, $nTaskPk, array('prjpk' => $nProjectFk));
        $sPicture = $oHTML->getPicture(CONST_PICTURE_DELETE, 'Delete task');
        $sHTML.= $oHTML->getLink($sPicture, $sUrl, array('onclick' => 'if(!window.confirm(\'Delete this task ?\')){ return false; }'));
      }
    }
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('', array('class' => 'floatHack'));
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getListItemEnd('');

    return $sHTML;
  }

  /**
   * Function to display the form to add the task
   * @param boolean $pbDisplay
   * @return string
   */

  private function _getQuickTaskForm($pbDisplay = false)
  {
    /* @var $oHTML CDisplayEx */
    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $nUserPk = $oLogin->getUserPk();

    $sQuery = 'SELECT *, if(p.date_end < NOW(), 1, 0) as finished FROM project as p ';
    $sQuery.= ' LEFT JOIN project_actors as pa ON (pa.projectfk = p.projectpk AND pa.loginfk = '.$nUserPk.' )';
    $sQuery.= ' WHERE p.is_public = 1 OR p.creatorfk = '.$nUserPk.' OR p.ownerfk = '.$nUserPk.' OR pa.loginfk = '.$nUserPk.' ';
    $sQuery.= ' ORDER BY finished, p.title';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
      return '';

    $sHTML = '';
    if($pbDisplay)
      $sHTML.= $oHTML->getBlocStart('quickTaskFormId');
    else
      $sHTML.= $oHTML->getBlocStart('quickTaskFormId', array('class' => 'hidden'));

    $sUrl = $oPage->getUrl('project', CONST_ACTION_ADD, CONST_PROJECT_TYPE_TASK);
    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('taskAddFormData');
    $oForm->setFormParams('', false, array('submitLabel' => 'Create task', 'action' => $sUrl));
    $oForm->setFormDisplayParams(array('noCancelButton' => 'true'));
    $sFormId = $oForm->getFormId();
    $oForm->addField('select', 'prjpk', array('label' => 'Create a task for: ', 'id' => 'projectfkId'));

    while($bRead)
    {
      $nProjectPk = $oResult->getFieldValue('projectpk');
      $oForm->addOption('prjpk', array('label' => '#'.$nProjectPk.': '.$oResult->getFieldValue('title'), 'value' => $nProjectPk));
      $bRead = $oResult->readNext();
     }

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    return $sHTML;
  }


  private function _getTaskView($pnPK)
  {
    return 'task view ';
  }


  private function _getTaskForm($pnPk)
  {
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oLogin = CDependency::getComponentByName('login');
    $oPage->addCssFile(array($this->getResourcePath().'css/project.css'));

    $nProjectPK = (int)getValue('prjpk', 0);
    if(empty($nProjectPK))
      return $oHTML->getErrorMessage(__LINE__.' - An error occured, can\'t find the project you want to add a task to.');

    $bIsCreator = false;

    //--------------------------------------------------------
    // TODO:
    //check if project exists and if user has rights to create a task (rights on project)
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM project WHERE projectpk = '.$nProjectPK;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return $oHTML->getErrorMessage(__LINE__.' - An error occured, can\'t find the project you want to add a task to.');

    $asProjectData = $oDbResult->getData();
    $nUserPk = $oLogin->getUserPk();

    if(empty($pnPk))
    {
      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_SAVEADD, CONST_PROJECT_TYPE_TASK);
      $asFieldValue = array('title'=>'','description'=>'', 'type' => '', 'date_start'=>date('Y-m-d'),'date_end'=>'','progress'=>'0','creatorfk'=>$nUserPk, 'date_created'=>'', 'affected_to' =>$nUserPk);

      $sTitle = 'Add a task to project #'.$nProjectPK;
      $asActors = array();
    }
    else
    {
      $sQuery = 'SELECT *, GROUP_CONCAT(loginfk) as actors FROM project_task as pt ';
      $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk AND t.taskpk = '.$pnPk.') ';
      $sQuery.= ' WHERE pt.taskfk = '.$pnPk.' ';
      $sQuery.= ' GROUP BY taskfk ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the task you want to edit. It may have been deleted.');

      if($nUserPk == $oDbResult->getFieldValue('creatorfk'))
        $bIsCreator = true;

      $asFieldValue = $oDbResult->getData();
      $asActors = explode(',', $asFieldValue['actors']);

      if(!empty($asFieldValue['date_end']))
        $asFieldValue['date_end'] = getFormatedDate('Y-m-d', $asFieldValue['date_end']);

      if(!empty($asFieldValue['date_start']))
        $asFieldValue['date_start'] = getFormatedDate('Y-m-d', $asFieldValue['date_start']);

      $sURL = $oPage->getAjaxUrl('project', CONST_ACTION_SAVEEDIT, CONST_PROJECT_TYPE_TASK, $pnPk);
      $sTitle = 'Edit this task for project #'.$nProjectPK;
    }

    $sHTML= $oHTML->getBlocStart();

    //div including the form
    $sHTML.= $oHTML->getBlocStart('taskFormId');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('taskFormData');
    $oForm->setFormParams('', true, array('submitLabel' => 'Save task', 'action' => $sURL));
    $oForm->setFormDisplayParams(array('style' => ''));

    $oForm->addField('misc', '', array('type' => 'text', 'text'=> $sTitle, 'class' => 'h2'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('misc', '', array('type'=> 'text', 'text' => $asProjectData['title'].'<hr />', 'class' => 'fontOk'));
    $oForm->addField('misc', '', array('type'=>'br'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $oLogin = CDependency::getComponentByName('login');
    $asUser = $this->_getProjectUsersList();

    //if(empty($pnPk) || $bIsCreator)
    if(1)
    {
      $oForm->addField('input', 'title', array('label'=>'title', 'class' => '', 'value' => $asFieldValue['title']));
      $oForm->setFieldControl('title', array('jsFieldNotEmpty' => '', 'jsFieldMaxSize' => 255));

      $oForm->addField('textarea', 'description', array('label'=>'Description', 'value' => $asFieldValue['description']));
      $oForm->setFieldControl('description', array('jsFieldMaxSize' => 2048));

      $oForm->addField('select', 'type', array('label' => 'Type'));
      $oForm->setFieldControl('type', array('jsFieldNotEmpty' => ''));

      foreach($this->casTaskType as $sType)
      {
        if($sType == $asFieldValue['type'])
          $oForm->addOption('type', array('value'=>$sType, 'label' => $sType, 'selected' => 'selected'));
        else
          $oForm->addOption('type', array('value'=>$sType, 'label' => $sType));
      }

      $oForm->addField('misc', '', array('type'=>'br'));
      $oForm->addField('input', 'date_start', array('type'=>'date', 'label'=>'Starting on', 'class' => '', 'value' => $asFieldValue['date_start']));
      $oForm->addField('input', 'date_end', array('type'=>'date', 'label'=>'Ending on', 'class' => '', 'value' => $asFieldValue['date_end']));
      $oForm->setFieldControl('date_end', array('jsFieldDate' => '', 'jsFieldGreaterThan' => '[name=date_start]'));

      $oForm->addField('select', 'affected_to[]', array('label' => 'Affected to', 'multiple' => 'multiple'));
      $oForm->setFieldControl('affected_to[]', array('jsFieldNotEmpty' => ''));

      foreach($asUser as $asUserData)
      {
        if(in_array($asUserData['loginpk'], $asActors))
          $oForm->addOption('affected_to[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
        else
          $oForm->addOption('affected_to[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
      }
    }
    else
    {
      $oForm->addField('input', 'title', array('label'=>'title', 'class' => '', 'value' => $asFieldValue['title'], 'readonly' => 'readonly'));
      $oForm->addField('textarea', 'description', array('label'=>'Description', 'value' => $asFieldValue['description'], 'readonly' => 'readonly'));

      $oForm->addField('select', 'type', array('label' => 'Type'));
      foreach($this->casTaskType as $sType)
        $oForm->addOption('type', array('value'=>$sType, 'label' => $sType));

      $oForm->addField('misc', '', array('type'=>'br'));

      $oForm->addField('input', 'date_start', array('type'=>'date', 'label'=>'Starting on', 'class' => '', 'value' => $asFieldValue['date_start'], 'readonly' => 'readonly'));
      $oForm->addField('input', 'date_end', array('type'=>'date', 'label'=>'Ending on', 'class' => '', 'value' => $asFieldValue['date_end'], 'readonly' => 'readonly'));

      $oForm->addField('select', 'affected_to[]', array('label' => 'Affected to', 'multiple' => 'multiple', 'readonly' => 'readonly'));
      foreach($asUser as $asUserData)
      {
        if(in_array($asUserData['loginpk'], $asActors))
          $oForm->addOption('affected_to[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname'], 'selected' => 'selected'));
        else
          $oForm->addOption('affected_to[]', array('value'=>$asUserData['loginpk'], 'label' => $asUserData['firstname'].' '.$asUserData['lastname']));
       }
    }
    $oForm->addField('input', 'progress', array('label'=>'Progress (%)', 'class' => '', 'value' => $asFieldValue['progress']));
    $oForm->setFieldControl('progress', array('jsFieldTypeIntegerPositive' => '', 'jsFieldMaxValue' => 100));

    $sGoBackTo = getValue('back');
    $oForm->addField('hidden', 'back', array( 'value' => $sGoBackTo));

    $oForm->addField('hidden', 'creatorfk', array( 'value' => $asFieldValue['creatorfk']));
    $oForm->addField('hidden', 'projectfk', array('value' => $nProjectPK));
    $oForm->addField('misc', '', array('type'=>'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }


  private function _getProjectUsersList()
  {
     $oDB = CDependency::getComponentByName('database');

     $sQuery = 'SELECT * FROM project_user as prj INNER JOIN login as lg ON lg.loginpk = prj.loginfk ';
     $oDbResult = $oDB->ExecuteQuery($sQuery);
     $bRead = $oDbResult->readFirst();
     $asResult = array();
     if($bRead)
     {
         while($bRead)
         {
             $asResult[] = $oDbResult->getData();
             $bRead = $oDbResult->readNext();
         }
     }
     return $asResult;
  }


  private function _getTaskSaveEdit($pnPk = 0)
  {
    if(!assert('is_integer($pnPk)'))
      return array();

    $oLogin = CDependency::getComponentByName('login');

    //control posted datas
    if(empty($_POST))
    {
      assert('false; // save tqask without post data');
      return array('error' => 'No task data. Sorry, can\'t save the task.');
    }

    $sTitle = getValue('title');
    $sDesc = getValue('description');
    $sType = getValue('type');
    $sDateStart = getValue('date_start');
    $sDateEnd = getValue('date_end');
    $nProgress = (float)getValue('progress', 0);

    $nCreatorfk = (int)getValue('creatorfk', 0);
    $anAffectedTo = getValue('affected_to', array());
    $nProjectfk = (int)getValue('projectfk', 0);

    $asFile = getValue('attachment', array());

    if(empty($nProjectfk))
      return array('error' => 'Can\'t find the project.');

    if(empty($anAffectedTo))
      return array('error' => 'The task has to be affected at least to one person.');

    if(empty($sTitle))
      return array('error' => 'Title is required.');

    if(!empty($sDateStart) && !empty($sDateEnd) && $sDateStart > $sDateEnd)
      return array('error' => 'The task starting date can\'t be after the ending date.');

    if($nProgress < 0 || $nProgress > 100)
      return array('error' => 'Progress is a number between 0 and 100%.');

    $oDB = CDependency::getComponentByName('database');

    if(!empty($pnPk))
    {
      $sQuery = 'SELECT *, GROUP_CONCAT(loginfk) as actors FROM project_task as pt ';
      $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk AND t.taskpk = '.$pnPk.') ';
      $sQuery.= ' WHERE projectfk = '.$nProjectfk;
      $sQuery.= ' GROUP BY taskfk ';

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();

      if(!$bRead)
        return array('error' => __LINE__.' - Couldn\'t find the task you want to edit. It may have been deleted.');

      $sQuery = 'UPDATE task SET title = '.$oDB->dbEscapeString($sTitle).', ';
      $sQuery.= ' description = '.$oDB->dbEscapeString($sDesc).', ';
      $sQuery.= ' date_start = '.$oDB->dbEscapeString($sDateStart, 'NULL').', ';
      $sQuery.= ' date_end = '.$oDB->dbEscapeString($sDateEnd, 'NULL').', ';
      $sQuery.= ' progress = '.$oDB->dbEscapeString($nProgress).', ';
      $sQuery.= ' type = '.$oDB->dbEscapeString($sType).' ';
      $sQuery.= ' WHERE taskpk = '.$pnPk;

      $oResult = $oDB->ExecuteQuery($sQuery);
      if(!$oResult)
        return array('error' => __LINE__.' - Couldn\'t save the task');

      $nTaskPK = $pnPk;
    }
    else
    {
      $sQuery = 'INSERT INTO task (title, description, date_start, date_end, progress, type, creatorfk, date_created) ';
      $sQuery.= ' VALUES('.$oDB->dbEscapeString($sTitle).', '.$oDB->dbEscapeString($sDesc).', ';
      $sQuery.= $oDB->dbEscapeString($sDateStart, 'NULL').', '.$oDB->dbEscapeString($sDateEnd, 'NULL').', ';
      $sQuery.= $oDB->dbEscapeString($nProgress).', '.$oDB->dbEscapeString($sType).', '.$oDB->dbEscapeString($nCreatorfk).', "'.date('Y-m-d H:i:s').'") ';

      $oResult = $oDB->ExecuteQuery($sQuery);

      if(!$oResult)
        return array('error' => __LINE__.' - Couldn\'t save the task');

      $nTaskPK = $oResult->getFieldValue('pk');
    }

    $nPosition = 9999;
    //====================================================
    //affecting the task to users
    if(!empty($pnPk))
    {
      $sQuery = 'SELECT position FROM project_task WHERE projectfk = '.$nProjectfk.' AND taskfk = '.$pnPk.' AND loginfk = '.$oLogin->getUserPk();
      $oResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return array('error' => __LINE__.' - Couldn\'t clean task actors');

      $nPosition = $oDbResult->getFieldValue('position', CONST_PHP_VARTYPE_INT);

      $sQuery = 'DELETE FROM project_task WHERE projectfk = '.$nProjectfk.' AND taskfk = '.$pnPk;
      $oResult = $oDB->ExecuteQuery($sQuery);
      if(!$oResult)
        return array('error' => __LINE__.' - Couldn\'t clean task actors');
    }

    $sQuery = 'INSERT INTO project_task (taskfk, projectfk, loginfk, date_affected, position) ';
    $sQuery.= ' VALUES ';

    foreach($anAffectedTo as $nLoginfk)
      $asQuery[] = ' ('.$nTaskPK.', '.$nProjectfk.', '.(int)$nLoginfk.', "'.date('y-m-d H:i:s').'", "'.$nPosition.'" ) ';

    $sQuery.= implode(',', $asQuery);
    $oResult = $oDB->ExecuteQuery($sQuery);

    if(!$oResult)
      return array('error' => __LINE__.' - Couldn\'t affect the task to users.');

    //All done: redirect the user
    $oPage = CDependency::getComponentByName('page');

    $sGoBackTo = getValue('back', '');
    if($sGoBackTo == 'wall')
       $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW_DETAILED, CONST_PROJECT_TYPE_PROJECT, $nProjectfk);
    else
       $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectfk);

    return array('notice' => 'Task saved.', 'url' => $sURL);
  }


  private function _getTaskChangeStatus($pnTaskPk)
  {
    if(!assert('is_integer($pnTaskPk) && !empty($pnTaskPk)'))
      return array('error' => __LINE__.' - Couldn\'t find the task in the database.');

    $nStatus = (int)getValue('taskdone', 0);
    $sHtmlId = getValue('htmlid', '');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM task WHERE taskpk = '.$pnTaskPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
       return array('error' => __LINE__.' - Couldn\'t find the task in the database.');

    $sQuery = 'UPDATE task SET status = '.$oDB->dbEscapeString($nStatus).', date_status_change = "'.date('Y-m-d H:i:s').'" WHERE taskpk = '.$pnTaskPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - Couldn\'t update the task status.');

    return array('action' => 'toggleImage(\'#'.$sHtmlId.'\')');
  }


  private function _getTaskUpdate($pnTaskPk)
  {
    if(!assert('is_integer($pnTaskPk) && !empty($pnTaskPk)'))
      return array('error' => __LINE__.' - Couldn\'t find the task in the database.');

    $sNewProgress = getValue('progress', null);
    if(!is_string($sNewProgress))
      return array('error' => __LINE__.' - New value incorrect.');

    $sHtmlId = getValue('htmlid', '');
    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM task WHERE taskpk = '.$pnTaskPk;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
       return array('error' => __LINE__.' - Couldn\'t find the task in the database.');

    $sQuery = 'UPDATE task SET progress = '.$oDB->dbEscapeString($sNewProgress).' WHERE taskpk = '.$pnTaskPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
       return array('error' => __LINE__.' - Couldn\'t update the task progress.');

    return array('action' => '$(\''.$sHtmlId.'\').fadeOut(); ');
  }


  private function _getTaskDelete($pnPk)
  {
    if(!assert('is_integer($pnPk) && !empty($pnPk)'))
       return array('error' => __LINE__.' - An error occured, can\'t delete the task #'.$pnPk);

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM task as t ';
    $sQuery.= ' INNER JOIN project_task as pt ON (pt.taskfk = t.taskpk) ';
    $sQuery.= ' WHERE taskpk = '.$pnPk.' ';
    $sQuery.= ' GROUP BY taskpk ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return array('error' => __LINE__.' - An error occured, can\'t delete the task #'.$pnPk.'. The task may have already been deleted. Try reloading the page.');

    $nProjectFk = $oDbResult->getFieldValue('projectfk', CONST_PHP_VARTYPE_INT);

    if(!$this->_getAttachmentDelete($pnPk))
      return array('error' => __LINE__.' - An error occured, can\'t delete the task attachments ');

    //Finally delete task
    $sQuery = 'DELETE FROM project_task WHERE taskfk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the task #'.$pnPk);

    //Finally delete task
    $sQuery = 'DELETE FROM task WHERE taskpk = '.$pnPk;
    $oResult = $oDB->ExecuteQuery($sQuery);
    if(!$oResult)
      return array('error' => __LINE__.' - An error occured, can\'t delete the task #'.$pnPk);

    $oPage = CDependency::getComponentByName('page');
  //  $sUrl = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectFk);

    return array('notice' => 'Task deleted.', 'reload' => 1);
  }

  private function _getPriorityLevelCss($psDate)
  {
    if($psDate < date('Y-m-d'))
      return 'project_past';

    $nNow = time();
    $n2DaysAgo = $nNow +(3600*24*2);
    $s2DaysAgo = date('Y-m-d', $n2DaysAgo);

    if($psDate < $s2DaysAgo)
      return 'project_urgent';

    $nWeekAgo = $nNow +(3600*24*7);
    $sWeekAgo = date('Y-m-d', $nWeekAgo);

    if($psDate < $sWeekAgo)
      return 'project_close';

    return 'project_fine';
  }

  private function _getProgressClass($psPercentage)
  {
    if(empty($psPercentage))
      return 'progress_slow';

    $nPercentage = (int)$psPercentage;

    if($nPercentage < 40)
      return 'progress_low';

    if($nPercentage < 80)
      return 'progress_average';

    return 'progress_high';
  }

  //====================================================================================================
  //====================================================================================================
  //====================================================================================================
  //====================================================================================================
  //ATTACHEMENT

  private function _getAttachFileForm($pnPk = 0)
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/project.css');

    $nTaskPk = getValue('taskpk', 0);
    if(empty($nTaskPk))
      return array('error' => $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the task you\'re trying to update.'));

    if(empty($pnPk))
    {
      $asFieldValue = array('title'=>'','attachment'=>'');
      $sURL = $oPage->getUrl('project', CONST_ACTION_SAVEADD, CONST_PROJECT_TYPE_ATTACHMENT);
      $sTitle = 'Add an attachment to task #'.$nTaskPk;
    }
    else
    {
      $oDB = CDependency::getComponentByName('database');

      $sQuery = 'SELECT * FROM task_attachment as ta ';
      $sQuery.= ' INNER JOIN task as t ON (t.taskpk = ta.taskfk) ';
      $sQuery.= ' WHERE task_attachmentpk = '.$pnPk;

      $oDbResult = $oDB->ExecuteQuery($sQuery);
      $bRead = $oDbResult->readFirst();
      if(!$bRead)
        return array('error' => $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the task you\'re trying to update.'));

      $asFieldValue = $oDbResult->getData();

      $sURL = $oPage->getUrl('project', CONST_ACTION_SAVEEDIT, CONST_PROJECT_TYPE_ATTACHMENT, $pnPk);
      $sTitle = 'Edit this attachment';
    }

    $sHTML= $oHTML->getBlocStart();

    //div including the form
    $sHTML.= $oHTML->getBlocStart('attachmentFormId');

    /* @var $oForm CFormEx */
    $oForm = $oHTML->initForm('attachmentFormData');
    $oForm->setFormParams('', false, array('submitLabel' => 'Save attachment', 'action' => $sURL));
    $oForm->setFormDisplayParams(array('noCancelButton' => 1,'style' => ''));

    $oForm->addField('misc', '', array('type' => 'text', 'text'=>$sTitle, 'class' => 'h2'));
    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('input', 'description', array('label'=>'Description', 'value' => $asFieldValue['title']));
    $oForm->setFieldControl('description', array('jsFieldMaxSize' => 255));

    $oForm->addField('misc', '', array('type'=>'br'));

    $oForm->addField('input', 'attachment[]', array('type' => 'file', 'label'=>'File', 'value' => $asFieldValue['attachment']));
    $oForm->addField('hidden', 'MAX_FILE_SIZE', array('value' => (25*1024*1024)));

    $oForm->addField('hidden', 'taskfk', array('value' => $nTaskPk));
    $oForm->addField('misc', '', array('type'=>'br'));

    $sHTML.= $oForm->getDisplay();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    return $oPage->getAjaxExtraContent(array('data' => $sHTML));
  }

  private function _getAttachFileSave()
  {
    /* @var $oHTML CDisplayEx */
    /* @var $oPage CPageEx */
    $oHTML = CDependency::getComponentByName('display');
    $oLogin = CDependency::getComponentByName('login');
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile($this->getResourcePath().'css/project.css');
    $oDB = CDependency::getComponentByName('database');

    //checking other parameters
    $sDescription = getValue('description', '');
    $nTaskFk = (int)getValue('taskfk', 0);
    if(empty($nTaskFk))
      return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the task you\'re trying to update.');

    //Check that the task and project still exist before treating the files
    $sQuery = 'SELECT * FROM task as t ';
    $sQuery.= ' INNER JOIN project_task as tp ON (tp.taskfk = t.taskpk) ';
    $sQuery.= ' WHERE taskpk = '.$nTaskFk;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();
    if(!$bRead)
      return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t find the task you\'re trying to update (#'.$nTaskPk.').');

    $nProjectPk = $oDbResult->getFieldValue('projectfk', CONST_PHP_VARTYPE_INT);

    //Checking the file upload
    if(!isset($_FILES) || !isset($_FILES['attachment']) || !isset($_FILES['attachment']['tmp_name']))
      return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save the file.');

    $asSavedFile = array();

    //Ready for multiple files
    foreach($_FILES['attachment']['tmp_name'] as $nKey => $sTmpFileName)
    {
      $sFileName = $_FILES['attachment']['name'][$nKey];

      //security control it s not a file pushed by a nasty script
      if(!is_uploaded_file($sTmpFileName))
        return $oHTML->getErrorMessage(__LINE__.' - Something weird is happening.');

      //checkExtension / mime /  filesize ...
      $oFinfo = finfo_open(FILEINFO_MIME_TYPE);
      $sMimeType = finfo_file($oFinfo, $sTmpFileName);

      if(filesize($sTmpFileName) > (25*1024*1024))
         return $oHTML->getErrorMessage(__LINE__.' - Sorry, the file is too big.');

      $sNewPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$nTaskFk.'/';
      $sNewName = date('YmdHis').'_'.$oLogin->getUserPk().'_'.uniqid('task'.$nTaskFk.'_').'_'.$sFileName;

      if(!is_dir($sNewPath) && !makePath($sNewPath))
        return $oHTML->getErrorMessage(__LINE__.' - Destination folder doesn\'t exist.('.$sNewPath.')');

      if(!is_writable($sNewPath))
        return $oHTML->getErrorMessage(__LINE__.' - Can\'t write in the destination folder.');

      if(!move_uploaded_file($sTmpFileName, $sNewPath.$sNewName))
        return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

      $sQuery = 'INSERT INTO `task_attachment` (`taskfk` ,`description` ,`mime_type` ,`file_name` ,`file_path` ,`date_upload`) ';
      $sQuery.= 'VALUES ('.$oDB->dbEscapeString($nTaskFk).', '.$oDB->dbEscapeString($sDescription, NULL).', '.$oDB->dbEscapeString($sMimeType).', ';
      $sQuery.= $oDB->dbEscapeString($sFileName).', '.$oDB->dbEscapeString($sNewPath.$sNewName).', '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).') ';

      $oResult = $oDB->ExecuteQuery($sQuery);

      if(!$oResult)
        return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t move the uploaded file.');

      $nAttachmentPk = $oResult->getFieldValue('pk', CONST_PHP_VARTYPE_INT);
      $asSavedFile[$nAttachmentPk] = $sNewPath.$sNewName;
    }

    $this->_addConvertedFiles($nTaskFk,  $asSavedFile);
    $this->_addConvertedFiles($nTaskFk,  $asSavedFile, true);
    $sURL = $oPage->getUrl('project', CONST_ACTION_VIEW, CONST_PROJECT_TYPE_PROJECT, $nProjectPk);

    return $oHTML->getRedirection($sURL, 2500, count($asSavedFile).' files saved.');
  }

  private function _addConvertedFiles($pnTaskFk, $pasFile, $pbBigSize = false)
  {
    if(!assert('is_integer($pnTaskFk) && !empty($pnTaskFk) && is_array($pasFile)') || empty($pasFile))
       return false;

    foreach($pasFile as $nAttachmentFk => $sFileFullPath)
    {
      if(!file_exists($sFileFullPath))
      {
        assert('false; // file ['.$sFileFullPath.'] doesn\'t exist.');
        return false;
      }

      $asFile = pathinfo($sFileFullPath);
      if(!isset($asFile['extension']))
        return true;

      $asPicture = array();
      switch(strtolower($asFile['extension']))
      {
        case 'pdf':
        case 'html':
        case 'xhtml':
        case 'txt':
        case 'csv':
        case 'png':
        case 'jpg':
        case 'jpeg':
        case 'gif':

          if($pbBigSize)
          {
            $sCommandLine = escapeshellcmd('/usr/bin/convert -density 300 -resize 100% ');
            $sCommandLine.= escapeshellarg($sFileFullPath).' '.escapeshellarg($asFile['dirname'].'/'.$asFile['filename'].'_full.png');

            $asPicture['filename'] = $asFile['filename']."_full.png ";
            $asPicture['filepath'] = $asFile['dirname']."/".$asFile['filename']."_full.png";
          }
          else
          {
            $sCommandLine = escapeshellcmd('/usr/bin/convert -density 300 -resize 190x190 ');
            $sCommandLine.= escapeshellarg($sFileFullPath).' '.escapeshellarg($asFile['dirname'].'/'.$asFile['filename'].'_thumb.png');

            $asPicture['filename'] = $asFile['filename']."_thumb.png ";
            $asPicture['filepath'] = $asFile['dirname']."/".$asFile['filename']."_thumb.png";
          }
          break;

          default: return true;
      }

      if(empty($asPicture))
         return true;

      $sLastLine = exec(escapeshellcmd($sCommandLine), $asCmdResult, $nCmdResult);
      if($nCmdResult != 0 || !empty($sLastLine))
      {
        assert('false; // couldn\'t generate thumb.');
        return false;
       }

      if($pbBigSize)
        $sType = 'fullsize';
      else
        $sType = 'thumb';

      $oDB = CDependency::getComponentByName('database');
      $sQuery = 'INSERT INTO `task_attachment` (`taskfk` ,`description`, `type`, `mime_type` ,`file_name` ,`file_path` ,`date_upload`, parentfk) ';
      $sQuery.= 'VALUES ('.$oDB->dbEscapeString($pnTaskFk).', "auto-generated", "'.$sType.'", "image/png", ';
      $sQuery.= $oDB->dbEscapeString($asPicture['filename']).', '.$oDB->dbEscapeString( $asPicture['filepath']).', '.$oDB->dbEscapeString(date('Y-m-d H:i:s')).', '.$nAttachmentFk.') ';

      $oResult = $oDB->ExecuteQuery($sQuery);

      if(!$oResult)
        return $oHTML->getErrorMessage(__LINE__.' - Couldn\'t save thumb ['.$asPicture['filepath'].'].');

      return true;
    }
  }

  private function _getAttachmentDelete($pnTaskPK)
  {
     if(!assert('is_integer($pnTaskPK) && !empty($pnTaskPK)'))
        return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM task_attachment WHERE taskfk = '.$pnTaskPK;
    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
       return true;

    if(empty($_SERVER['DOCUMENT_ROOT']))
       return false;

    $sAttchFolderPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$pnTaskPK;

    $sCommandLine = escapeshellcmd('rm -R ').escapeshellarg($sAttchFolderPath);
    $sLastLine = exec(escapeshellcmd($sCommandLine), $asCmdResult, $nCmdResult);

    if($nCmdResult != 0 || !empty($sLastLine))
    {
      assert('false; // couldn\'t delete attachment folder.');
      return false;
    }

    $sQuery = 'DELETE FROM task_attachment WHERE taskfk = '.$pnTaskPK;
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if(!$oDbResult)
      return false;

    return true;
  }

  private function _getProjectAttachmentDelete($pnProjectPk)
  {
    if(!assert('is_integer($pnProjectPk) && !empty($pnProjectPk)'))
       return false;

    $oDB = CDependency::getComponentByName('database');

    $sQuery = 'SELECT * FROM project_task as pt ';
    $sQuery.= '  INNER JOIN task_attachment as ta on (ta.taskfk = pt.taskfk) ';
    $sQuery.= '  WHERE pt.projectfk = '.$pnProjectPk;

    $oDbResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oDbResult->readFirst();

    if(!$bRead)
      return true;

    if(empty($_SERVER['DOCUMENT_ROOT']))
      return false;

    $anTaskFk = array();
    while($bRead)
    {
      $nTaskPK = (int)$oDbResult->getFieldValue('taskfk', 0);
      if(!empty($nTaskPK))
      {
        $anTaskFk[] = $nTaskPK;

        $sAttchFolderPath = $_SERVER['DOCUMENT_ROOT'].CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$nTaskPK;

        $sCommandLine = escapeshellcmd('rm -R ').escapeshellarg($sAttchFolderPath);
        $sLastLine = exec(escapeshellcmd($sCommandLine), $asCmdResult, $nCmdResult);

        if($nCmdResult != 0 || !empty($sLastLine))
           assert('false; // couldn\'t delete attachment folder. ['.$sAttchFolderPath.']');
       }
      $bRead = $oDbResult->readNext();
    }

    $sQuery = 'DELETE FROM task_attachment WHERE taskfk IN ('.implode(',', $anTaskFk).') ';
    $oDbResult = $oDB->ExecuteQuery($sQuery);

    if(!$oDbResult)
      return false;

    return true;
  }


  private function _getMagazineView($pnPK)
  {
    $oPage = CDependency::getComponentByName('page');
    $oPage->addCssFile(array($this->getResourcePath().'css/project.css'));

    $oHTML = CDependency::getComponentByName('display');
    $oDB = CDependency::getComponentByName('database');
    $oLogin = CDependency::getComponentByName('login');

    $nUserPk = $oLogin->getUserPk();
    $sQuery = 'SELECT * FROM project as p WHERE p.projectpk = '.$pnPK.' LIMIT 1';
    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('This project is not accessible or has been deleted.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
     }

    //stock project data for later displaying
    $asProjectData = $oResult->getData();
    $asUser = $oLogin->getUserList(0,false,true);

    $sQuery = 'SELECT *, GROUP_CONCAT(loginfk SEPARATOR ",") as actors ';
    $sQuery.= ' FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK;
    $sQuery.= ' GROUP BY pt.taskfk  ORDER BY position, t.date_end, t.date_start, progress DESC ';

    $oResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oResult->readFirst();

    if(!$bRead)
    {
      $sHTML = $oHTML->getBlocStart('', array('class' => 'notice2'));
      $sHTML.= $oHTML->getText('No task linked to this project.');
      $sHTML.= $oHTML->getBlocEnd();
      return $sHTML;
    }

    $sJavascript = "
    $(document).ready(function()
    {
        var galleries = jQuery('.ad-gallery').adGallery({
            loader_image: '/component/project/resources/pictures/ad-gallery/loader.gif',
            width: 800,
            height: 600,
            thumb_opacity: 0.6,
            start_at_index: 0,
            description_wrapper: jQuery('#descriptions'),
            animate_first_image: false,
            animation_speed: 400,
            display_next_and_prev: true,
            display_back_and_forward: true,
            scroll_jump: 0,
            slideshow: {
            enable: true,
            autostart: true,
            speed: 5000,
            start_label: 'Start',
            stop_label: 'Stop',
            stop_on_scroll: true,
            countdown_prefix: '(',
            countdown_sufix: 's)',
            },
            effect: 'slide-hori',
            enable_keyboard_move: true,
            cycle: true,
            callbacks: {
            init: function() {
                this.preloadAll();
                this.preloadImage(0);
                this.preloadImage(1);
                this.preloadImage(2);
            },
            afterImageVisible: function() {
                var context = this;
                this.loading(true);
                this.preloadImage(this.current_index + 1,
                function() {
                    context.loading(false);
                }
                );
            },
            beforeImageVisible: function(new_image, old_image) {
                // Do something wild!
             }
            }
         });
        });
     ";

    $oPage->addCssFile($this->getResourcePath().'/css/ad-gallery.css');
    $oPage->addRequiredJsFile($this->getResourcePath().'js/ad-gallery.js');
    $oPage->addCustomJs($sJavascript);

    //------------------------------------------
    $sQuery = 'SELECT DISTINCT(pt.taskfk), ta.*, t.progress ';
    $sQuery.= ' FROM project_task as pt ';
    $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk) ';
    $sQuery.= ' INNER JOIN task_attachment as ta ON (t.taskpk = ta.taskfk) ';
    $sQuery.= ' WHERE pt.projectfk = '.$pnPK.' AND ta.parentfk > 0 ';
    $sQuery.= ' ORDER BY position, date_upload DESC ';

    $oAttachResult = $oDB->ExecuteQuery($sQuery);
    $bRead = $oAttachResult->readFirst();
    $asPicture = array();
    while($bRead)
    {
      $nTaskFk = $oAttachResult->getFieldValue('taskfk', CONST_PHP_VARTYPE_INT);
      if(!isset($asPicture[$nTaskFk][$oAttachResult->getFieldValue('type')]))
        $asPicture[$nTaskFk][$oAttachResult->getFieldValue('type')] = $oAttachResult->getFieldValue('file_name');

      $bRead = $oAttachResult->readNext();
    }
    $asAttachmentData = array();

    $sHTML = $oHTML->getBlocStart('', array('class' => 'magazineViewContainer shadow'));

    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ad-gallery'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ad-image-wrapper'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ad-controls'));
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ad-nav'));
    $sHTML.= $oHTML->getBlocStart('',array('class'=>'ad-thumbs'));
    $sHTML.= $oHTML->getListStart('',array('class'=>'ad-thumb-list'));

    foreach($asPicture as $nTaskFk => $asPics)
    {
      $sPicture =  $oAttachResult->getFieldValue('file_name');
      $sThumbPicture =  $oAttachResult->getFieldValue('file_name');

      $sHTML.= $oHTML->getListItemStart('');
      $sHTML.= '<a href="'.CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$nTaskFk.'/'.$asPics['fullsize'].'">
                <img src="'.CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$nTaskFk.'/'.$asPics['thumb'].'" longdesc="'.CONST_PATH_UPLOAD_DIR.'project/task_attachment/'.$nTaskFk.'/'.$asPics['fullsize'].'" alt="Attachment" />
                </a>';
      $sHTML.= $oHTML->getListItemEnd('');
      $bRead = $oAttachResult->readNext();
    }

    $sHTML.= $oHTML->getListEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();
    $sHTML.= $oHTML->getBlocEnd();

    $sHTML.= $oHTML->getBlocEnd();

    return $sHTML;
  }

   private function _notifyExpirationDate()
   {
     $sLastWeek = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')-7, date('Y')));
     $sNexWeek = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+7, date('Y')));
     $sToday = date('Y-m-d');

     //select all the non finished task of the non finished projects
     $sQuery = 'SELECT p.*, pt.*, t.creatorfk as task_creator, t.date_end as task_end, t.title as task_title, t.description as task_description, t.status as task_done, t.date_status_change FROM project as p';
     $sQuery.= ' INNER JOIN project_task as pt ON (pt.projectfk = p.projectpk) ';
     $sQuery.= ' INNER JOIN task as t ON (t.taskpk = pt.taskfk AND t.date_end > "'.$sLastWeek.'") ';
     $sQuery.= ' WHERE p.status = 0 AND p.date_end > "'.$sLastWeek.'" ORDER BY t.date_end ASC';

     $oDB = CDependency::getComponentByName('database');
     $oDbResult = $oDB->executeQuery($sQuery);
     $bRead = $oDbResult->readFirst();

     if(!$bRead)
       return false;

     $asToNotify = array();
     $abRecentlyFinished = array();
     while($bRead)
     {
       $nLoginFk = $oDbResult->getFieldValue('loginfk', CONST_PHP_VARTYPE_INT);
       $nTaskCreator = $oDbResult->getFieldValue('task_creator', CONST_PHP_VARTYPE_INT);
       $nProjectCreator = $oDbResult->getFieldValue('creatorfk', CONST_PHP_VARTYPE_INT);
       $nProjectOwner = $oDbResult->getFieldValue('ownerfk', CONST_PHP_VARTYPE_INT);

       $nTaskPk = $oDbResult->getFieldValue('taskfk', CONST_PHP_VARTYPE_INT);
       $nProjectPk = $oDbResult->getFieldValue('projectpk', CONST_PHP_VARTYPE_INT);
       $sTaskEndDate = $oDbResult->getFieldValue('task_end');
       $bTaskDone = $oDbResult->getFieldValue('task_done', CONST_PHP_VARTYPE_BOOL);
       $sProjectEndDate = $oDbResult->getFieldValue('date_end');

       //define is we have project notifications
       if($sProjectEndDate < $sToday)
       {
         $asToNotify[$nProjectCreator]['p_expired'][$nProjectPk] = 'Project #'.$nProjectPk.' has ended on '.$sProjectEndDate;
         $asToNotify[$nProjectOwner]['p_expired'][$nProjectPk] = 'Project #'.$nProjectPk.' has ended on '.$sProjectEndDate;
       }
       elseif($sProjectEndDate < $sNexWeek)
       {
         $asToNotify[$nProjectCreator]['p_expiration'][$nProjectPk] = 'Project #'.$nProjectPk.' has ended on '.$sProjectEndDate;
         $asToNotify[$nProjectOwner]['p_expiration'][$nProjectPk] = 'Project #'.$nProjectPk.' has ended on '.$sProjectEndDate;
       }

       if($bTaskDone)
       {
         //log task recently finished. If all task recently finished, notify project can be closed
         $sDateStatusChange = $oDbResult->getFieldValue('date_status_change');
         if(!empty($sDateStatusChange) && $sDateStatusChange > $sLastWeek)
           $abRecentlyFinished[$nProjectPk][$nTaskPk] = true;
         else
           $abRecentlyFinished[$nProjectPk][$nTaskPk] = false;
       }
       else
       {
         $abRecentlyFinished[$nProjectPk][$nTaskPk] = false;

         //define is we have taks notifications
         if(!empty($nTaskPk))
         {
           if($sTaskEndDate < $sToday)
           {
             $sTaskTitle = $oDbResult->getFieldValue('task_title');
             $asToNotify[$nLoginFk]['t_expired'][$nTaskPk] = 'Task #'.$nTaskPk.' has ended on '.$sTaskEndDate.': '.$sTaskTitle;
             $asToNotify[$nTaskCreator]['t_expired'][$nTaskPk] = 'Task #'.$nTaskPk.' has ended on '.$sTaskEndDate.': '.$sTaskTitle;
           }
           elseif($sTaskEndDate < $sNexWeek)
           {
             $sTaskTitle = $oDbResult->getFieldValue('task_title');
             $asToNotify[$nLoginFk]['t_expiration'][$nTaskPk] = 'Task #'.$nTaskPk.' will end on '.$sTaskEndDate.': '.$sTaskTitle;
             $asToNotify[$nTaskCreator]['t_expiration'][$nTaskPk] = 'Task #'.$nTaskPk.' will end on '.$sTaskEndDate.': '.$sTaskTitle;
           }
         }
       }
       $bRead = $oDbResult->readNext();
     }

     if(empty($asToNotify))
       return false;

     $oPage = CDependency::getComponentByName('page');
     $oLogin = CDependency::getComponentByName('login');
     $oMail = CDependency::getComponentByName('mail');
     $oMailComponent = CDependency::getComponentUidByName('mail');

     $asUsers = $oLogin->getUserList(0,true,false);

     $sURL = $oPage->getUrl('project', CONST_ACTION_LIST, CONST_PROJECT_TYPE_TASK, 0, array(CONST_PROJECT_TASK_SORT_PARAM => 'project'));

     if(!empty($oMailComponent))
     {
      foreach($asToNotify as $nLoginFk => $asNotify)
      {
        $asUserData = $asUsers[$nLoginFk];
        if(!empty($asUserData['email']))
        {
          $oMail->creatNewEmail();
          $oMail->setFrom('crm@bulbouscell.com', 'CRM notifier');
          $sRecipientName = $oLogin->getUserNameFromData($asUserData);
          $oMail->addRecipient($asUserData['email'], $sRecipientName);

          $sContent = 'Dear '.$sRecipientName.', <br /><br />';
          $sContent.= 'Access BCM for more informations (<a href="'.$sURL.'">My tasks list</a>). <br /><br />';
          $sContent.= 'Project notifications are as follows: <br />';

          foreach($asNotify as $sType => $asContent)
          {
            switch($sType)
            {
              case 't_expiration': $sContent.= '<br /><br /><strong>Tasks close to deadline : </strong><br /><br />'.implode('<br />', $asContent); break;
              case 't_expired': $sContent.= '<br /><br /><strong>Tasks recently expired : </strong><br /><br />'.implode('<br />', $asContent); break;

              case 'p_expiration':
              case 'p_expired':

                if($sType == 'p_expiration')
                  $sContent.= '<br /><br /><strong>Projects close to deadline : </strong><br /><br />';
                else
                  $sContent.= '<br /><br /><strong>Projects recently expired : </strong><br /><br />';

                foreach($asContent as $nProjectPk => $sProjectContent)
                {
                   $sContent.= $sProjectContent.'<br />';

                   if($this->_isArrayAllTrue($abRecentlyFinished[$nProjectPk]))
                      $sContent.= '-=[ Note ]=- All this project\'s tasks seems to be completed. <br />';
                }
               break;
             }
           }

          $sContent = $oMail->getDefaultCrmTemplate($sContent);

         $oResult = $oMail->send('BCM notifier: Tasks and projects summary', $sContent);
        }
      }
     }
     return true;
   }


   private function _isArrayAllTrue($pasArray)
   {
     if(!assert('is_array($pasArray)') || empty($pasArray))
       return false;

     foreach($pasArray as $vValue)
     {
       if(!$vValue)
         return false;
     }
     return true;
   }

  public function setLanguage($psLanguage)
  {
    require_once('language/language.inc.php5');
    if(isset($gasLang[$psLanguage]))
      $this->casText = $gasLang[$psLanguage];
    else
      $this->casText = $gasLang[CONST_DEFAULT_LANGUAGE];
  }
}