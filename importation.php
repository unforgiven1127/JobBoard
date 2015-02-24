<?php
/*
$oPgCx = pg_connect("dbname=bccrm user=bccrm password=bccrm");
if(!$oPgCx)
  exit('no pg connection');
*/
/*
$oMyCx = mysql_connect("127.0.0.1", 'root', '');
$bConnected = mysql_select_db('bccrm_migration');
if(!$bConnected)
  exit('no mysql connection');
*/
//======================================================================================
//======================================================================================
/*
$asConversion['candi_regist'] = 'date_create';
$asConversion['candi_update'] = 'date_update';
$asConversion['candi_rno'] = 'contactpk';
$asConversion['candi_fname'] = 'lastname';
$asConversion['candi_lname'] = 'firstname';

$asConversion['candi_sex'] = 'courtesy';  //need conversion
$asCourtesy[1] = 'mr';
$asCourtesy[2] = 'ms';

$asConversion['candi_cid'] = 'followerfk';  //==> need a correspondance table cons <-> login
$asUsers[1] = 1;
*/

//candi_occupation  -> profil position
//candi_dept
//candi_title
//candi_industry  -> industry

function escapeString($psString)
{
    $sString = trim($psString);
    $sString = mysql_real_escape_string($sString);
    $sString = str_replace("\r\n", "\n", $sString);
    $encoding = mb_detect_encoding( $sString, "auto" );
    $sString= mb_convert_encoding( $sString, "UTF-8", $encoding);
   
    return $sString;
}

if(!isset($_GET['step']) || empty($_GET['step']))
{
  $nStep = 0;
  exit('add ?step=1 to 14');
}
else
  $nStep = (int)$_GET['step'];

// 1. create companies
if($nStep === 1)
{
  $sQuery = 'SELECT * FROM company_tbl where trim(company_name) <> \'\' ' ;

  $oResult = pg_query($sQuery);
  
  while($asData = pg_fetch_assoc($oResult))
  {
      if($asData['company_last_update']!='')
        $sLastUpdate= $asData['company_last_update'];
      else
        $sLastUpdate= date('Y-m-d H:i:s');
      
      $sQuery = 'select * from login where status=1 and lower(id) like "'.strtolower(trim($asData['company_creator'])).'"';
      $oDbResult = mysql_query($sQuery) or die($sQuery);
      $sCreator = 0;
      while($asRecords = mysql_fetch_array($oDbResult))
      { 
          $sCreator = $asRecords['loginpk'];
       
       }
      if($sCreator==0)
           $sCreator=1;
      
      $asMysqlQuery[] = '("'.$asData['company_id'].'", "'.escapeString($asData['company_name']).'", "'.escapeString($asData['company_address']).'", "'.escapeString($asData['company_added']).'", '.$sCreator.', '.$sCreator.', "'.$sLastUpdate.'", "1")';
  }
  pg_free_result($oResult);
  $nNbCompany = count($asMysqlQuery);
  $nNbQuery = ceil($nNbCompany/200);

  for($nCount = 0; $nCount < $nNbQuery; $nCount++)
  {
    $asCompanyToAdd = array_slice($asMysqlQuery, ($nCount*200), 200);
    $sQuery = 'INSERT INTO company  (externalkey, company_name,address_1,date_create, creatorfk, followerfk,date_update,updated_by) VALUES ';
    $sQuery.= implode(',', $asCompanyToAdd);

    $oInsertResult = mysql_query($sQuery)or die($sQuery.mysql_error());

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">+200,Company</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 2)
{
  // 2. create contacts
  $sQuery = '';
  $sQuery = 'SELECT * FROM candidate_tbl ';

  $oResult = pg_query($sQuery);
  while($asData = pg_fetch_assoc($oResult))
  {
      if($asData['candi_sex']==1)
       $asSex = 'mr';
      if($asData['candi_sex']==2)
        $asSex = 'ms';
         
      if($asData['candi_update']!='')
        $sLastUpdate= $asData['candi_update'];
      else
        $sLastUpdate= date('Y-m-d H:i:s');
      
      $sQuery = 'select * from login where status=1 and lower(id) like "'.strtolower(trim($asData['candi_cid'])).'"';
      $oDbResult = mysql_query($sQuery) or die($sQuery);
      $sCreator=0;
      while($asRecords = mysql_fetch_array($oDbResult))
      { 
          $sCreator = $asRecords['loginpk'];
       
       }
       if($sCreator==0)
           $sCreator=1;
      
    $asMyQuery[] = '("'.$asData['candi_rno'].'","'.$asSex.'","'.date('Y-m-d',strtotime($asData['candi_birth'])).'", "'.escapeString($asData['candi_fname']).'", "'.escapeString($asData['candi_lname']).'", "'.mysql_real_escape_string($asData['candi_regist']).'", "'.date('Y-m-d H:i:s',strtotime($asData['candi_update'])).'","'.$sCreator.'","'.$sCreator.'","'.$sCreator.'","'.escapeString($asData['candi_nationality']).'","'.escapeString($asData['candi_language']).'")';
  }
  pg_free_result($oResult);
  $nNbCandidate = count($asMyQuery);
  $nNbQuery = ceil($nNbCandidate/200);

  for($nCount = 0; $nCount < $nNbQuery; $nCount++)
  {
    $asCandiToAdd = array_slice($asMyQuery, ($nCount*200), 200);
    $sQuery = 'INSERT INTO contact  (externalkey,courtesy,birthdate, firstname,lastname, date_create, date_update,followerfk,created_by,updated_by,nationalityfk,langfk) VALUES ';
    $sQuery.= implode(',', $asCandiToAdd);

    $oInsertResult = mysql_query($sQuery) or die($sQuery.mysql_error());

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">+200,Candidate</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 3)
{
  // 3. create business profiles

  $sQuery = 'SELECT * FROM contact ';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }

  $sQuery = 'SELECT * FROM company';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anCompany[$oData['externalkey']]=$oData['companypk'];
  }
  $sQuery = 'SELECT candi_company_id,candi_rno,candi_industry,candi_title FROM candidate_tbl ';

  $oResult = pg_query($sQuery);
  while($asData = pg_fetch_assoc($oResult))
  {
    $ContactPk = (int)$anContact[$asData['candi_rno']];
    $CompanyPk = (int)$anCompany[$asData['candi_company_id']];

    $asMysQuery[] = '("'.$ContactPk.'","'.$CompanyPk.'","'.$asData['candi_industry'].'","'.escapeString($asData['candi_title']).'")';
  }

  $nNbCandidate = count($asMysQuery);
  $nNbQuery = ceil($nNbCandidate/200);
  pg_free_result($oResult);
  for($nCount = 0; $nCount < $nNbQuery; $nCount++)
  {
    $asCandiToAdd = array_slice($asMysQuery, ($nCount*200), 200);
    $sQuery = 'INSERT INTO profil (contactfk,companyfk, industryfk, position) VALUES ';
    $sQuery.= implode(',', $asCandiToAdd);

    $oInsertResult = mysql_query($sQuery) or die(mysql_error());

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">+200</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 4)
{
  // 4. create contacts profiles

  $sQuery = 'SELECT * FROM contact ';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }

  $sNewQuery = 'SELECT * FROM contacts_tbl';
  $oNewResult = pg_query($sNewQuery);
  while($asData = pg_fetch_assoc($oNewResult))
  {
    $ContaNum = $asData['conta_no'] ;
    $ContactPk = (int)$anContact[$asData['conta_rno']];

   $contactValue=(int)$asData['conta_contacts'];

    if($contactValue=='1')
       {
        $sQuery = 'UPDATE contact SET phone ="'.escapeString($ContaNum).'" WHERE contactpk ='.$ContactPk;
        $oInsertResult = mysql_query($sQuery) or die(mysql_error());
      }
      if($contactValue=='5')
      {
        $sQuery = 'UPDATE contact SET email ="'.escapeString($ContaNum).'" WHERE contactpk ='.$ContactPk;
        $oInsertResult = mysql_query($sQuery) or die(mysql_error());
      }
      if($contactValue=='6')
      {
        $sQueryTest = 'UPDATE contact SET cellphone ="'.escapeString($ContaNum).'" WHERE contactpk ='.$ContactPk;
        $oInsertResult = mysql_query($sQueryTest) or die(mysql_error());
      }
  
      if($contactValue=='2')
      {
        $sQuery = 'UPDATE profil SET phone ="'.escapeString($ContaNum).'" WHERE contactfk ='.$ContactPk;
        $oInsertResult = mysql_query($sQuery) or die(mysql_error());
      }
      if($contactValue=='4')
      {
       $sQuery = 'UPDATE profil SET fax ="'.escapeString($ContaNum).'" WHERE contactfk ='.$ContactPk;
        $oInsertResult = mysql_query($sQuery) or die(mysql_error());
      }
      if($contactValue=='3')
      {
       $sQuery = 'UPDATE profil SET comment ="'.escapeString($ContaNum).'" WHERE contactfk ='.$ContactPk;
        $oInsertResult = mysql_query($sQuery) or die(mysql_error());
      }
  
    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">updated</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 5)
{
  //5. Create Industry

  $sQuery = 'SELECT * FROM candi_industry where industry <>  \'\' ' ;

  $oResult = pg_query($sQuery);
  while($asData = pg_fetch_assoc($oResult))
  {
    $asMysqlaQuery[] = '("'.$asData['indus_id'].'", "'.escapeString($asData['industry']).'")';
  }
  $nNbIndustry = count($asMysqlaQuery);
  $nNbQuery = ceil($nNbIndustry/200);

  for($nCount = 0; $nCount < $nNbQuery; $nCount++)
  {
    $asIndustryToAdd = array_slice($asMysqlaQuery, ($nCount*200), 200);
    $sQuery = 'INSERT INTO industry  (industrypk, industry_name) VALUES ';
    $sQuery.= implode(',', $asIndustryToAdd);

    $oInsertResult = mysql_query($sQuery);

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">+200</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 6)
{

  //6. Link the company with the industry

  $sQuery = 'SELECT * FROM company';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anCompany[$oData['externalkey']]=$oData['companypk'];
  }
  $sQuery = 'SELECT * FROM company_has_indus ';

  $oResult = pg_query($sQuery);
  while($asData = pg_fetch_assoc($oResult))
  {
    $CompanyPk = (int)$anCompany[$asData['company_id']];
    $asMysqlbQuery[] = '("'.$CompanyPk.'","'.$asData['indus_id'].'")';
  }

  $nNbCandidate = count($asMysqlbQuery);
  $nNbQuery = ceil($nNbCandidate/200);

  for($nCount = 0; $nCount < $nNbQuery; $nCount++)
  {
    $asCandiToAdd = array_slice($asMysqlbQuery, ($nCount*200), 200);
    $sQuery = 'INSERT INTO company_industry  (companyfk,industryfk) VALUES ';
    $sQuery.= implode(',', $asCandiToAdd);
    $oInsertResult = mysql_query($sQuery) or die(mysql_error());

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    else
      echo '<span style="color:green;">+200,Industry</span><br /><br />';

    flush(); ob_flush();
  }

}

if($nStep === 7)
{
  //7. Create Events for the company

  $sQuery = 'SELECT * FROM company';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anCompany[$oData['externalkey']]=$oData['companypk'];
  }

  $sQuery = 'SELECT * FROM company_note_tbl as cn INNER JOIN company_note_type as cnnt ON (cn.company_note_type = cnnt.company_note_note_id)';

  $oResult = pg_query($sQuery);
  $nCount = 0;
   $sCreator = 0;
  while($asData = pg_fetch_assoc($oResult))
  {
      $sQuery = 'select * from login where status=1 and lower(id) like "'.strtolower(trim($asData['company_note_owner'])).'"';
      $oDbResult = mysql_query($sQuery) or die($sQuery);
      
      while($asRecords = mysql_fetch_array($oDbResult))
      { 
          $sCreator = $asRecords['loginpk'];
       
       }
       if($sCreator==0)
           $sCreator=1;
      
      
     $sQuery = 'INSERT INTO event (type, title,content,date_create,date_display,created_by) VALUES ';
     $sQuery.= '("update", "","'.escapeString($asData['company_note_details']).'","'.date('Y-m-d H:i:s',strtotime($asData['company_note_added'])).'","'.date('Y-m-d H:i:s',strtotime($asData['company_note_added'])).'","'.$sCreator.'")';

     $oInsertResult = mysql_query($sQuery) or die($sQuery);

    $InsertId = mysql_insert_id();

    $CompanyPk = (int)$anCompany[$asData['company_note_company']];

    $sQuery = 'INSERT INTO event_link (eventfk, cp_uid,cp_action,cp_type,cp_pk) VALUES ';
    $sQuery.= '('.$InsertId.', "777-249","ppav","cp","'.$CompanyPk.'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery);
    $nCount++;

      if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';

      if(($nCount%50) == 0)
        echo '<span style="color:green;">'.$nCount.'</span><br /><br />';

    flush(); ob_flush();
  }

}

if($nStep === 8)
{
  //8. Create Events for the contact

  $sQuery = 'SELECT * FROM contact';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }

  $sQuery = 'SELECT * FROM notes_tbl ';

  $oResult = pg_query($sQuery);
  $nCount = 0;
  while($asData = pg_fetch_assoc($oResult))
  {
      
      $sQuery = 'select * from login where status=1 and lower(id) like "'.strtolower(trim($asData['note_cid'])).'"';
      $oDbResult = mysql_query($sQuery) or die($sQuery);
       $sCreator = 0;
      while($asRecords = mysql_fetch_array($oDbResult))
      { 
          $sCreator = $asRecords['loginpk'];
       
       }
        if($sCreator==0)
           $sCreator=1;
       
    $sQuery = 'INSERT INTO event (type, title,content,date_create,date_display,created_by) VALUES ';
    $sQuery.= '("update","","'.escapeString($asData['note_notes']).'","'.date('Y-m-d H:i:s',strtotime($asData['note_regist'])).'","'.date('Y-m-d H:i:s',strtotime($asData['note_regist'])).'","'.$sCreator.'")';
    
    $oInsertResult = mysql_query($sQuery) or die($sQuery);

    $InsertId = mysql_insert_id();
    $ContactPk = (int)$anContact[$asData['note_rno']];

    $sQuery = 'INSERT INTO event_link (eventfk, cp_uid,cp_action,cp_type,cp_pk) VALUES ';
    $sQuery.= '("'.$InsertId.'", "777-249","ppav","ct","'.$ContactPk.'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery);
    $nCount++;

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';


    if(($nCount%50) == 0)
        echo '<span style="color:green;">'.$nCount.'</span><br /><br />';

    flush(); ob_flush();
  }
}
/*
if($nStep === 9)
{
  //9. Create Departments for the contact

 $sQuery = 'SELECT trim(candi_dept) as candi_dept FROM candidate_tbl where candi_dept <> \'\'  group by candi_dept ';

 $oResult = pg_query($sQuery);
  $nCount = 0;
  while($asData = pg_fetch_assoc($oResult))
  { 
     $sQuery = 'INSERT INTO department (department_name) VALUES ';
     $sQuery.=  '("'.escapeString($asData['candi_dept']).'");';
          
     $oInsertResult = mysql_query($sQuery) or die(mysql_error());
     $nCount++;
  }
 
  if(!$oInsertResult)
     echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';

  if($nCount)
        echo '<span style="color:green;">'.$nCount.'</span><br /><br />';

    flush(); ob_flush();
  
}
*/
if($nStep === 10)
{
  //10. Link all the departments to the contacts

     $sQuery = 'SELECT * FROM contact';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }
    
    
  $sQuery = 'SELECT trim(candi_dept) as candi_dept,candi_rno FROM candidate_tbl where candi_dept <> \'\' ' ;

  $oResult = pg_query($sQuery);
  $nCount = 0;
  while($asData = pg_fetch_assoc($oResult))
  {
    $departMentName = $asData['candi_dept'];

   /* $sQuery = 'SELECT * FROM  department where lower(department_name) like "%'.strtolower(trim($departMentName)).'%"';
    $sResult = mysql_query($sQuery) or die($sQuery.mysql_error());
    $asResult = mysql_fetch_array($sResult);

    if(!mysql_num_rows($sResult))
      echo '<span style="color:red;">Cant find department: '.$departMentName.'</span><br /><br />';
    else
    {*/
    
      $userId = $anContact[$asData['candi_rno']];

      $sQuery = 'UPDATE profil SET department ="'.escapeString($departMentName).'" WHERE contactfk = '.$userId.'';
      $oUpdateResult = mysql_query($sQuery) or die($sQuery.mysql_error());
      $nCount++;
      
      if(!$oUpdateResult)
        echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
     
       if(($nCount%20) == 0)
        echo '<span style="color:green;">'.$nCount.' Department Updated</span><br /><br />';
    }

    flush(); ob_flush();
 // }
}

if($nStep === 11)
{
  //11. Insert the character of the contacts

  $sQuery = 'SELECT * FROM contact';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }

  $sQuery = 'SELECT * FROM character_tbl ';
  $oResult = pg_query($sQuery);
  $nCount = 0;
  $sCreator = 0;
  while($asData = pg_fetch_assoc($oResult))
  {
       $sQuery = 'select * from login where status=1 and lower(id) like "'.strtolower(trim($asData['cha_cid'])).'"';
      $oDbResult = mysql_query($sQuery) or die($sQuery);
      
      while($asRecords = mysql_fetch_array($oDbResult))
      { 
          $sCreator = $asRecords['loginpk'];
       
       }
       if($sCreator==0)
           $sCreator=1;
          
    $sQuery = 'INSERT INTO event (type, title,content,date_create,date_display,created_by) VALUES ';
    $sQuery.= '("update","","'.escapeString($asData['cha_character']).'","'.date('Y-m-d H:i:s',strtotime($asData['cha_regist'])).'","'.date('Y-m-d H:i:s',strtotime($asData['cha_regist'])).'","'.$sCreator.'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery);

    $InsertId = mysql_insert_id();
    $ContactPk = (int)$anContact[$asData['cha_rno']];

    $sQuery = 'INSERT INTO event_link (eventfk, cp_uid,cp_action,cp_type,cp_pk) VALUES ';
    $sQuery.= '("'.$InsertId.'", "777-249","ppav","ct","'.$ContactPk.'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery);
    $nCount++;

    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    
    if(($nCount%20) == 0)  
      echo '<span style="color:green;">'.$nCount.'Character Event Inserted</span><br /><br />';

    flush(); ob_flush();
  }
}

if($nStep === 12)
{
  //12. Insert the documents for company
  $sQuery = 'SELECT * FROM company';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anCompany[$oData['externalkey']]=$oData['companypk'];
  }

  $sQuery = 'SELECT dt.*,chd.company_id as company FROM company_has_doc as chd LEFT JOIN doc_tbl as dt ON dt.doc_id = chd.doc_id ';

  $oResult = pg_query($sQuery);
  $nCount = 0;
  while($asData = pg_fetch_assoc($oResult))
  {

  if(file_exists('/opt/projects/BCM/uploads/company/'.$asData['doc_filename']))
  {
  $sFileName = '/opt/projects/BCM/uploads/company/'.$asData['doc_filename'];

  $sNewPath = '/opt/projects/BCM/common/upload/addressbook/document/'.$anCompany[$asData['company']].'/';
  if(!is_dir($sNewPath))
    mkdir($sNewPath);

   $sNewName = $asData['doc_filename'];
    $pathName = $sNewPath.$sNewName;

  copy($sFileName, $pathName);

    $sQuery = 'INSERT INTO addressbook_document (title,description,loginfk,date_create,filename,path_name) VALUES ';
    $sQuery.= '("'.escapeString($asData['doc_title']).'","'.escapeString($asData['doc_desc']).'",1,"'.date('Y-m-d H:i:s',strtotime($asData['doc_added'])).'","'.escapeString($asData['doc_filename']).'","'.escapeString($pathName).'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery.mysql_error());
    $InsertId = mysql_insert_id();

    $CompanyPk = (int)$anCompany[$asData['company']];

    $sQuery = 'INSERT INTO addressbook_document_info (type,itemfk,docfk) VALUES ';
    $sQuery.= '("cp","'.$CompanyPk.'","'.$InsertId.'")';

    $oInsertResult = mysql_query($sQuery) or die($sQuery.mysql_error());

    $nCount++;
    if(!$oInsertResult)
      echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
   if(($nCount%20) == 0)  
      echo '<span style="color:green;">'.$nCount.'Documents updated for company</span><br /><br />';

    flush(); ob_flush();

  }
  else
  {
      echo 'No files found '.$asData['doc_filename'].' <br>';
  }

  }
}

if($nStep === 13)
{
  //13. Insert the documents for candidate
  $sQuery = 'SELECT * FROM contact';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }
  $sQuery = 'SELECT dt.*,chd.candi_rno as candidate FROM candi_has_doc as chd LEFT JOIN doc_tbl as dt ON dt.doc_id = chd.doc_id ';
   $oResult = pg_query($sQuery)or die($sQuery);
   $nCount= 0;
   while($asData = pg_fetch_assoc($oResult))
    {
     if(file_exists('/opt/projects/BCM/uploads/candi/'.$asData['doc_filename']))
     {
      $sFileName = '/opt/projects/BCM/uploads/candi/'.$asData['doc_filename'];
      $sNewPath = '/opt/projects/BCM/common/upload/addressbook/document/'.$anContact[$asData['candidate']].'/';
      if(!is_dir($sNewPath))
      mkdir($sNewPath);
      $sNewName = $asData['doc_filename'];
      $pathName = $sNewPath.$sNewName;
      copy($sFileName, $pathName);

      $sQuery = 'INSERT INTO addressbook_document (title,description,loginfk,date_create,filename,path_name) VALUES ';
      $sQuery.= '("'.escapeString($asData['doc_title']).'","'.escapeString($asData['doc_desc']).'",1,"'.date('Y-m-d H:i:s',strtotime($asData['doc_added'])).'","'.escapeString($asData['doc_filename']).'","'.escapeString($pathName).'")';

      $oInsertResult = mysql_query($sQuery) or die($sQuery);
      $InsertId = mysql_insert_id();
      $ContactPk = (int)$anContact[$asData['candidate']];
      $sQuery = 'INSERT INTO addressbook_document_info (type,itemfk,docfk) VALUES ';
      $sQuery.= '("ct","'.$ContactPk.'","'.$InsertId.'")';

      $oInsertResult = mysql_query($sQuery) or die($sQuery);
      $nCount++;
      
      if(!$oInsertResult)
          echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
    if(($nCount%20) == 0)  
          echo '<span style="color:green;">'.$nCount.' Documents updated for candidate</span><br /><br />';

      flush(); ob_flush();
      }
      else
      {
          echo 'No files found <br>';
      }
  }
}

if($nStep === 14)
{
    
  $sQuery = 'SELECT * FROM contact ';
  $oResult = mysql_query($sQuery);
  while($oData= mysql_fetch_array($oResult))
  {
    $anContact[$oData['externalkey']]=$oData['contactpk'];
  }
  
  //14. Update the respective grade of the candidate

    $sQuery = 'SELECT candi_rno,grade_name FROM candidate_tbl AS ctb, grade_tbl as gtb where gtb.grade_id = ctb.candi_grade';
    $oResult = pg_query($sQuery)or die($sQuery);
   $nCount= 0;
    while($asData = pg_fetch_assoc($oResult))
    {
        $sQuery = 'Update contact set grade = "'.escapeString($asData['grade_name']).'" where contactpk='.$anContact[$asData['candi_rno']];
        $oUpdateResult = mysql_query($sQuery) or die($sQuery.mysql_error());
        
        $nCount++;
        if(!$oUpdateResult)
          echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
        
        if(($nCount%20) == 0)  
          echo '<span style="color:green;">'.$nCount.'grades updated</span><br /><br />';

      flush(); ob_flush();

    }
}

if($nStep === 15)
{
     
  //15. Insert the nationality

    $sQuery = 'SELECT * from nationality_tbl ';
    $oResult = pg_query($sQuery)or die($sQuery);
   $nCount= 0;
    while($asData = pg_fetch_assoc($oResult))
    {
        $sQuery = 'INSERT INTO nationality (nationality_name) VALUES ("'.$asData['nationality_long'].'")';
        $oUpdateResult = mysql_query($sQuery) or die($sQuery.mysql_error());
              
        if(!$oUpdateResult)
          echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
        else
         echo 'Done';
        
      flush(); ob_flush();

    }
    
}
   

if($nStep === 16)
{
     
  //15. Insert the nationality

    $sQuery = 'SELECT * from profil ';
    $oResult = mysql_query($sQuery)or die($sQuery);
   $nCount= 0;
    while($asData = mysql_fetch_array($oResult))
    {
        if($asData['phone']!='')
        $sQuery = 'UPDATE contact set phone = "'.$asData['phone'].'" where contactpk = '.$asData['contactfk'];
        $oUpdateResult = mysql_query($sQuery) or die($sQuery.mysql_error());
              
        if(!$oUpdateResult)
          echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
        else
         echo 'Done';
        
      flush(); ob_flush();

    }
    
}

if($nStep === 17)
{
    $sQuery = ' SELECT * FROM country ';
     $oDbResult = mysql_query($sQuery)or die($sQuery);
     while($asData = mysql_fetch_array($oDbResult))
    {
     $sQuery = 'INSERT INTO nationality(nationality_name) VALUES("'.$asData['printable_name'].'")';
     $oResult = mysql_query($sQuery)or die($sQuery);
     if(!$oResult)
          echo '<span style="color:red;">'.$sQuery.'</span><br /><br />';
        else
         echo 'Done';
        
      flush(); ob_flush();
    }
}


if($nStep==18)
{
    $asArray = array(0 => array('name'   => 'Home',
                                'link'   => '',
                                'icon'   => 'pictures/home_48.png',
                                'target' => '_parent',
                                 'uid'   =>  '579-704',
                                 'type'  => '',
                                 'action'=> '',
                                 'pk'    => 0
                                ),
                      1 => array('name'   => 'Connections List',
                                 'link'   => '',
                                 'icon'   => 'pictures/connection_48.png',
                                 'target' => '_parent',
                                 'uid'    =>  '777-249',
                                 'type'   =>  'ct',
                                 'action' =>  'ppal',
                                 'pk'     => 0,
                                 'child'  => array( 0 => array('name'   => 'My Connections',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '777-249',
                                                                'type'   =>  'ct',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'loginpk' => 1,
                                                                'onclick' => ''
                                                                ),
                                                    1 => array('name'   => 'Search Connections',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '777-249',
                                                                'type'   =>  'ct',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'onclick' => 'resetContactSearch();'
                                                                )
                                                   )
                                ),
                     2 => array('name'   => 'Companies List',
                                 'link'   => '',
                                 'icon'   => 'pictures/company_48.png',
                                 'target' => '_parent',
                                 'uid'    =>  '777-249',
                                 'type'   =>  'cp',
                                 'action' =>  'ppal',
                                 'pk'     => 0,
                                 'child'  => array( 0 => array('name'   => 'My Companies',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '777-249',
                                                                'type'   =>  'cp',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'loginpk' => 1,
                                                                'onclick' => ''
                                                                ),
                                                    1 => array('name'   => 'Search Companies',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '777-249',
                                                                'type'   =>  'cp',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'onclick' => 'resetCompanySearch();'
                                                                )            
                                                    
                                                   )
                                ),         
                    3 => array('name'   => 'Projects',
                                 'link'   => '',
                                 'icon'   => 'pictures/project_48.png',
                                 'target' => '_parent',
                                 'uid'    =>  '456-789',
                                 'type'   =>  'prj',
                                 'action' =>  'ppal',
                                 'pk'     => 0,
                                 'child'  => array( 0 => array('name'   => 'My tasks',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '456-789',
                                                                'type'   =>  'task',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'loginpk' => 1,
                                                                'onclick' => ''
                                                                ),
                                                    1 => array('name'   => 'Projects List',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '456-789',
                                                                'type'   =>  'prj',
                                                                'action' =>  'ppal',
                                                                'pk'     => 0,
                                                                'onclick' => '' 
                                                                ),
                                                   3 => array('name'   => 'Project Users',
                                                                'link'   => '',
                                                                'target' => '_parent',
                                                                'uid'    =>  '777-249',
                                                                'type'   =>  'prjacr',
                                                                'action' =>  'ppae',
                                                                'pk'     => 0,
                                                                'onclick' => ''
                                                                )             
                                                    
                                                   )
                                ),
        
                    4 => array('name'   => 'Shared document List',
                               'link'   => '',
                               'icon'   => 'pictures/shared_space_48.png',
                               'target' => '_parent',
                               'uid'   =>  '999-111',
                               'type'  => 'shdoc',
                               'action'=> 'ppal',
                               'pk'    => 0  
                        ),
                  
                5 => array('name'   => 'Contacts',
                               'link'   => '',
                               'icon'   => 'pictures/contact_48.png',
                               'target' => '_parent',
                               'uid'   =>  '579-704',
                               'type'  => 'usr',
                               'action'=> 'ppal',
                               'pk'    => 0  
                        ),
                6 => array('name'   => 'Mail',
                               'link'   => 'https://mail.bulbouscell.com/webmail/',
                               'icon'   => 'pictures/mail_48.png',
                               'target' => '_blank',
                               'uid'   =>  '',
                               'type'  => '',
                               'action'=> '',
                               'pk'    => 0,
                               'embedLink' => 1
                        ) ,
        
         7 => array('name'   => 'Web Mail',
                               'link'   => '',
                               'icon'   => 'pictures/webmail_48.png',
                               'target' => '',
                               'uid'   =>  '009-724',
                               'type'  => 'webmail',
                               'action'=> 'ppaa',
                               'pk'    => 0,
                               'ajaxpopup' => 1,
                               'loginpk' => 1
                                
                        ),
        
            
        );
    
    echo serialize($asArray);
    
    }



?>
