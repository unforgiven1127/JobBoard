<?php
/*
 *
 * example of how to use the mail ccomponent:
 *
 *
 * $oMail = CDependency::getComponentByName('mail');

  $oMail->creatNewEmail();
  $oMail->setFrom('crm@bulbouscell.com', 'CRM notifyer');

  $oMail->addRecipient('sboudoux@bulbouscell.com', 'stef');
  $oMail->addCCRecipient('sboudoux@gmail.com', 'stef - CC- gmail');
  $oMail->addBCCRecipient('sboudoux@gmail.com', 'stef- bcc - gmail');

  $oResult = $oMail->send(' ohhhh subject', 'ahhhhhhh content', 'hiiiiiiiii text content');

  dump($oMail);

  dump($oResult);
  dump($oMail->getErrors());
 *
 *
 */

require_once('component/mail/mail.class.php5');
require_once('component/mail/phpmailer/class.phpmailer.php');

class CMailEx extends CMail
{
  private $coPhpMailer;
  private $casMailStatus;
  private $casError;


  public function __construct()
  {
    $this->coPhpMailer = new PHPMailer();

    //true for non required options (can be changed to false if errors)
    $this->casMailStatus = array('hasFrom' => false, 'hasRecipient' => false, 'hasContent' => false, 'hasCC' => true, 'hasBCC' => true);
    $this->casError = array();

    $this->coPhpMailer->IsSMTP();
    $this->coPhpMailer->IsHTML(true);
  }

  public function creatNewEmail()
  {
    $this->casMailStatus = array('hasFrom' => false, 'hasRecipient' => false, 'hasContent' => false, 'hasCC' => true, 'hasBCC' => true);
    $this->casError = array();
    $this->coPhpMailer = new PHPMailer();
    $this->coPhpMailer->IsSMTP();
    $this->coPhpMailer->IsHTML(true);
  }

  public function getErrors()
  {
    return $this->casError;
  }

  public function sendRawEmail($psSender, $psAddress, $psSubject, $psContent,$psCC='',$psBCC='')
  {
    $oHTML = CDependency::getComponentByName('display');

    $this->creatNewEmail();
    $this->addRecipient($psAddress);

    if(!empty($psSender))
      $this->setFrom($psSender);

    if(!empty($psCC))
      $this->addCCRecipient($psCC);

    if(!empty($psBCC))
      $this->addBCCRecipient($psBCC);

   return (bool)$this->send($psSubject, $this->getDefaultCrmTemplate($psContent));
  }

  public function addRecipient($pvRecipient, $psDisplayedName = '')
  {
    if(!assert('!empty($pvRecipient) && is_string($psDisplayedName)'))
      return 0;

    $nAddedRecipient = 0;

    if(is_array($pvRecipient))
    {
      foreach($pvRecipient as $vKey => $asRecipient)
      {
        if(isset($asRecipient['email']) && !empty($asRecipient['email']) && filter_var($asRecipient['email'], FILTER_VALIDATE_EMAIL) !== false)
        {
          if(!isset($asRecipient['name']))
            $asRecipient['name'] = $asRecipient['email'];

          $bAdded = $this->coPhpMailer->AddAddress($asRecipient['email'], $asRecipient['name']);
          if($bAdded)
          {
            $this->casMailStatus['hasRecipient'] = true;
            $nAddedRecipient++;
          }

        }
      }
    }
    else
    {

      if(filter_var($pvRecipient, FILTER_VALIDATE_EMAIL) === false)
        return 0;

      $bAdded = $this->coPhpMailer->AddAddress($pvRecipient, $psDisplayedName);
      if($bAdded)
      {
        $nAddedRecipient++;
        $this->casMailStatus['hasRecipient'] = true;
      }
    }
    return $nAddedRecipient;
  }

  public function addCCRecipient($pvRecipient, $psDisplayedName = '')
  {
    if(!assert('!empty($pvRecipient) && is_string($psDisplayedName)'))
      return 0;

    $nAddedRecipient = 0;

    if(is_array($pvRecipient))
    {
      foreach($pvRecipient as $vKey => $asRecipient)
      {
        if(isset($asRecipient['email']) && !empty($asRecipient['email']) && filter_var($asRecipient['email'], FILTER_VALIDATE_EMAIL) !== false)
        {
          if(!isset($asRecipient['name']))
            $asRecipient['name'] = $asRecipient['email'];

          $bAdded = $this->coPhpMailer->AddCC($asRecipient['email'], $asRecipient['name']);
          if($bAdded)
          {
            $this->casMailStatus['hasCC'] = true;
            $nAddedRecipient++;
          }
        }
      }
    }
    else
    {
      if(filter_var($pvRecipient, FILTER_VALIDATE_EMAIL) === false)
        return 0;

      $bAdded = $this->coPhpMailer->AddCC($pvRecipient, $psDisplayedName);
      if($bAdded)
      {
        $this->casMailStatus['hasCC'] = true;
        $nAddedRecipient++;
      }
    }

    return $nAddedRecipient;
  }


  public function addBCCRecipient($pvRecipient, $psDisplayedName = '')
  {
    if(!assert('!empty($pvRecipient) && is_string($psDisplayedName)'))
      return 0;

    $nAddedRecipient = 0;

    if(is_array($pvRecipient))
    {
      foreach($pvRecipient as $vKey => $asRecipient)
      {
        if(isset($asRecipient['email']) && !empty($asRecipient['email']) && filter_var($asRecipient['email'], FILTER_VALIDATE_EMAIL) !== false)
        {
          if(!isset($asRecipient['name']))
            $asRecipient['name'] = $asRecipient['email'];

          $bAdded = $this->coPhpMailer->AddBCC($asRecipient['email'], $asRecipient['name']);
          if($bAdded)
          {
            $this->casMailStatus['hasBCC'] = true;
            $nAddedRecipient++;
          }
        }
      }
    }
    else
    {
      if(filter_var($pvRecipient, FILTER_VALIDATE_EMAIL) === false)
        return 0;

      $bAdded = $this->coPhpMailer->AddBCC($pvRecipient, $psDisplayedName);
      if($bAdded)
      {
        $this->casMailStatus['hasBCC'] = true;
        $nAddedRecipient++;
      }
    }

    return $nAddedRecipient;
  }

  public function addAllRecipient($pasRecipient)
  {
    if(!assert('is_array($pasRecipient) && !empty($pasRecipient)'))
      return 0;

    $nAddedRecipient = 0;

    foreach($pasRecipient as $vKey => $asRecipient)
    {
      if(isset($asRecipient['type']) && isset($asRecipient['email']) && !empty($asRecipient['email']) && filter_var($asRecipient['email'], FILTER_VALIDATE_EMAIL) !== false)
      {
        if(!isset($asRecipient['type']) || empty($asRecipient['type']))
          $asRecipient['type'] = 'to';
        else
        {
          if(!in_array($asRecipient['type'], array('to', 'cc', 'bcc', 'ReplyTo')))
            return 0;
        }

        if(!isset($asRecipient['name']))
          $asRecipient['name'] = $asRecipient['email'];

        $bAdded = $this->coPhpMailer->AddAnAddress($asRecipient['type'], $asRecipient['email'], $asRecipient['name']);
        if($bAdded)
        {
          switch($asRecipient['type'])
          {
            case 'to': $this->casMailStatus['hasRecipient'] = true; break;
            case 'cc': $this->casMailStatus['hasCC'] = true; break;
            case 'bcc': $this->casMailStatus['hasBCC'] = true; break;
            case 'ReplyTo': $this->casMailStatus['hasReplyTo'] = true; break;
            default:
              $this->casMailStatus['ready'] = false; break;
          }

          $nAddedRecipient++;
        }
      }
    }

    return $nAddedRecipient;
  }

  public function setFrom($psRecipient, $psDisplayedName = '')
  {
    if(!assert('!empty($psRecipient) && is_string($psDisplayedName)'))
      return 0;

    if(filter_var($psRecipient, FILTER_VALIDATE_EMAIL) === false)
      return 0;

    $bAdded = $this->coPhpMailer->SetFrom($psRecipient, $psDisplayedName);
    if($bAdded)
    {
      $this->casMailStatus['hasFrom'] = true;
      return 1;
    }

    return 0;
  }

  public function isReady()
  {
    if(!$this->casMailStatus['hasFrom'])
    {
      $oLogin = CDependency::getComponentByName('login');
      $this->setFrom($oLogin->getUserEmail(), $oLogin->getUserName());

      $this->casMailStatus['hasFrom'] = true;
    }

    foreach($this->casMailStatus as $bStatus)
    {
      if(!$bStatus)
        return false;
    }

    return true;
  }

  /*
   * Will return the email pk after email sent and logged in DB
  */

  public function send($psSubject, $psContent, $psTextContent = '', $pasAttachement = array() )
  {
    if(!assert('!empty($psSubject) && !empty($psContent)'))
     return 0;

    $sEncoding = mb_check_encoding($psSubject);
    if($sEncoding != 'UTF8')
      $this->coPhpMailer->Subject = mb_convert_encoding($psSubject, 'utf8');
    else
      $this->coPhpMailer->Subject = $psSubject;

    $this->casMailStatus['hasSubject'] = true;

    $sEncoding = mb_check_encoding($psSubject);
    if($sEncoding != 'UTF8')
      $this->coPhpMailer->Body = mb_convert_encoding($psContent, 'utf8');
    else
     $this->coPhpMailer->Body = $psContent;

    $this->casMailStatus['hasContent'] = true;


    if(empty($psTextContent))
    {
      $psContent = str_ireplace(array('<br>','<br >','<br/>','<br />','</p>'), PHP_EOL, $psContent);
      $this->coPhpMailer->AltBody = strip_tags($psContent);
    }
    else
      $this->coPhpMailer->AltBody = strip_tags($psTextContent);


    if(!$this->isReady())
    {
      $this->casError[] = __LINE__.' - Mail is not setup properly. (isReady() = false ['.serialize($this->casMailStatus).']) ';
      return 0;
    }

    if(!empty($pasAttachement))
    {
      foreach($pasAttachement as $sFilePath)
      {
        if(!file_exists($sFilePath))
        {
          $this->casError[] = __LINE__.' - Can\'t find mail attachement file ['.$sFilePath.'] ';
          return 0;
        }

        $nFileSize = filesize($sFilePath);
        if( $nFileSize > CONST_PHPMAILER_ATTACHMENT_SIZE)
        {
          $this->casError[] = __LINE__.' - Attachement size too big. ('.$nFileSize.' > '.CONST_PHPMAILER_ATTACHMENT_SIZE.') ';
          return 0;
        }

        $this->coPhpMailer->AddAttachment($sFilePath);
      }
    }
    if(!$this->_send())
    {
      $this->casError[] = __LINE__.' - Error sending email ['.$this->coPhpMailer->ErrorInfo.'] ';
      return 0;
    }

    //TODO: log mail in DB
    return 1;
  }


  private function _send()
  {
    if(CONST_DEV_SERVER == 1)
    {
      //replace all recipeints by DEV_EMAIL
      $this->coPhpMailer->all_recipients = array(CONST_DEV_EMAIL => true);
      $this->coPhpMailer->ReplyTo = array(CONST_DEV_EMAIL => 'test email crm');
    }

    return (bool)$this->coPhpMailer->Send();
  }

  public function loadTemplate($psTemplateName, $pasTemplateVars=array())
  {

  }

  public function getDefaultCrmTemplate($psContent = '')
  {
    if(empty($psContent))
      $psContent = '||@content@||';

    $oHTML = CDependency::getComponentByName('display');

    switch(CONST_WEBSITE)
    {
      case 'talentAtlas':
        $sHeaderPic =  $oHTML->getPicture('http://www.talentatlas.com/media/picture/talentatlas/mail_header.png');
        $sHeaderLink = $oHTML->getLink($sHeaderPic, 'http://www.talentatlas.com');
        $sFooterLink = '';
        break;

      case 'bcm':
        $sHeaderPic =  $oHTML->getPicture('http://www.bulbouscell.com/images/header.jpg');
        $sHeaderLink = $oHTML->getLink($sHeaderPic,'https://bcm.bulbouscell.com');
        $sFooterLink = $sHeaderLink;
        break;

      case 'jobboard':
        $sHeaderPic =  '';
        $sHeaderLink =  '';
        $sFooterLink = $oHTML->getLink($sHeaderPic,'http://www.slate.co.jp/wp-content/themes/Slate/images/slate_logo.png');
        break;
    }

    $sContent = "<html><body style='padding: 15px; font-family: Verdana, Arial; font-size: 11px;'>";
    $sContent.= $oHTML->getBlocStart('', array('style' => 'width:650px;'));
    $sContent.= $sHeaderLink;
    $sContent.= $oHTML->getBlocEnd();
    $sContent.= $oHTML->getBlocStart('', array('style' => 'width:650px; border: 2px solid #999; min-height:300px; margin:8px 0; background-color:#fff;-webkit-border-radius: 10px ; -moz-border-radius: 10px; border-radius: 10px;'));
    $sContent.= $oHTML->getBlocStart('', array('style' => 'margin:15px;'));
    $sContent.= $psContent;
    $sContent.= $oHTML->getBlocEnd();
    $sContent.= $oHTML->getBlocEnd();
    $sContent.= $oHTML->getBlocStart('', array('style' => 'width:650px;'));
    $sContent.= $sFooterLink;
    $sContent.= $oHTML->getBlocEnd();
    $sContent.= "</body></html>";

    return $sContent;
  }


}