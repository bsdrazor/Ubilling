<?php

 function em_EmployeeShowForm() {
     $show_q="SELECT * from `employee`";
     $allemployee=simple_queryall($show_q);
     
     $cells=  wf_TableCell(__('ID'));
     $cells.= wf_TableCell(__('Real Name'));
     $cells.= wf_TableCell(__('Active'));
     $cells.= wf_TableCell(__('Appointment'));
     $cells.= wf_TableCell(__('Mobile'));
     $cells.= wf_TableCell(__('Administrator'));
     $cells.= wf_TableCell(__('Actions'));
     $rows  = wf_TableRow($cells, 'row1');
     
     if (!empty ($allemployee)) {
         foreach ($allemployee as $ion=>$eachemployee) {
             $cells=  wf_TableCell($eachemployee['id']);
             $cells.= wf_TableCell($eachemployee['name']);
             $cells.= wf_TableCell(web_bool_led($eachemployee['active']));
             $cells.= wf_TableCell($eachemployee['appointment']);
             $cells.= wf_TableCell($eachemployee['mobile']);
             $cells.= wf_TableCell($eachemployee['admlogin']);
             $actions=  wf_JSAlert('?module=employee&delete='.$eachemployee['id'], web_delete_icon(), 'Removing this may lead to irreparable results');
             $actions.= wf_JSAlert('?module=employee&edit='.$eachemployee['id'], web_edit_icon(), 'Are you serious');
             $cells.= wf_TableCell($actions);
             $rows.=  wf_TableRow($cells, 'row3');
         
         }
       }
      
     //new employee create form inputs  
     $inputs=  wf_HiddenInput('addemployee', 'true');
     $inputs.= wf_TableCell('');
     $inputs.= wf_TableCell(wf_TextInput('employeename', '', '', false, 30));
     $inputs.= wf_TableCell('');
     $inputs.= wf_TableCell(wf_TextInput('employeejob', '', '', false, 20));
     $inputs.= wf_TableCell(wf_TextInput('employeemobile', '', '', false, 15));
     $inputs.= wf_TableCell(wf_TextInput('employeeadmlogin', '', '', false, 10));
     $inputs.= wf_TableCell(wf_Submit(__('Create')));
     $inputs=  wf_TableRow($inputs, 'row2');
     $addForm=  wf_Form("", 'POST', $inputs, '');
     $rows.=$addForm;
      
      $result=  wf_TableBody($rows, '100%', '0', 'sortable');
      
      show_window(__('Employee'),$result);
   }

function em_JobTypeForm() {
     $show_q="SELECT * from `jobtypes`";
     $alljobs=simple_queryall($show_q);
     
     $cells=  wf_TableCell(__('ID'));
     $cells.= wf_TableCell(__('Job type'));
     $cells.= wf_TableCell(__('Actions'));
     $rows=  wf_TableRow($cells, 'row1');
     
     if (!empty ($alljobs)) {
         foreach ($alljobs as $ion=>$eachjob) {
      
            $cells=  wf_TableCell($eachjob['id']);
            $cells.= wf_TableCell($eachjob['jobname']);
              $actionlinks= wf_JSAlert('?module=employee&deletejob='.$eachjob['id'], web_delete_icon(), 'Removing this may lead to irreparable results') .' ';
              $actionlinks.=wf_JSAlert('?module=employee&editjob='.$eachjob['id'], web_edit_icon(), 'Are you serious');
            $cells.= wf_TableCell($actionlinks);
            $rows.=  wf_TableRow($cells, 'row3');
         }
       }
       
      $inputs=  wf_HiddenInput('addjobtype','true');
      $inputs.= wf_TableCell('');
      $inputs.= wf_TableCell(wf_TextInput('newjobtype', '', '', false, '30'));
      $inputs.= wf_TableCell(wf_img('skins/icon_add.gif').' '.  wf_Submit(__('Create')));
      $inputs= wf_TableRow($inputs, 'row2');
      $createForm=  wf_Form("", 'POST', $inputs, '');
      $rows.= $createForm;

      $result=  wf_TableBody($rows, '100%', '0', 'sortable');
      
      show_window(__('Job types'),$result);
   }

function em_EmployeeAdd($name,$job,$mobile='',$admlogin='') {
        $name=mysql_real_escape_string(trim($name));
        $job=mysql_real_escape_string(trim($job));
        $mobile=  mysql_real_escape_string($mobile);
        $admlogin=  mysql_real_escape_string($admlogin);
        
        $query="
            INSERT INTO `employee` (
                `id` ,
                `name` ,
                `appointment`,
                `mobile`,
                `admlogin`,
                `active`
                )
                VALUES (
                NULL , '".$name."', '".$job."','".$mobile."', '".$admlogin."' , '1'
                );
                ";

     nr_query($query);
     log_register('EMPLOYEE ADD `'.$name.'` JOB `'.$job.'`');
    }
    
function em_EmployeeDelete($id) {
     $id=vf($id,3);
     $query="DELETE from `employee` WHERE `id`=".$id;
     nr_query($query);
     log_register('EMPLOYEE DEL ['.$id.']');
    }

 function stg_add_jobtype($jobtype) {
        $jobtype=mysql_real_escape_string(trim($jobtype));
        $query="
            INSERT INTO `jobtypes` (
                `id` ,
                `jobname`
                )
                VALUES (
                NULL , '".$jobtype."'
                );
                ";
     nr_query($query);
     stg_putlogevent('JOBTYPEADD '.$jobtype);
    }

 function stg_delete_jobtype($id) {
     $query="DELETE from `jobtypes` WHERE `id`=".$id;
     nr_query($query);
     stg_putlogevent('JOBTYPEDEL ['.$id.']');
    }

function stg_get_employee_name($id) {
$query='SELECT `name` from `employee` WHERE `id`="'.$id.'"';
$employee=simple_query($query);
return($employee['name']);
}

function stg_get_employee_data($id) {
$query='SELECT *  from `employee` WHERE `id`="'.$id.'"';
$employee=simple_query($query);
return($employee);
}

function stg_get_jobtype_name($id) {
$query='SELECT `jobname` from `jobtypes` WHERE `id`="'.$id.'"';
$jobtype=simple_query($query);
return($jobtype['jobname']);
}


function stg_worker_selector() {
    $query="SELECT * from `employee` WHERE `active`='1'";
    $allemployee=simple_queryall($query);
    $result='<select name="worker">';
    if (!empty ($allemployee)) {
        foreach ($allemployee as $io=>$eachwrker) {
        $result.='<option value="'.$eachwrker['id'].'">'.$eachwrker['name'].'</option>';
        }
    }
    $result.='</select>';
    return($result);
}

function stg_jobtype_selector() {
    $query="SELECT * from `jobtypes` ORDER by `id` ASC";
    $alljobtypes=simple_queryall($query);
    $result='<select name="jobtype">';
    if (!empty ($alljobtypes)) {
        foreach ($alljobtypes as $io=>$eachjobtype) {
        $result.='<option value="'.$eachjobtype['id'].'">'.$eachjobtype['jobname'].'</option>';
        }
    }
    $result.='</select>';
    return($result);
}

function stg_show_jobs($username) {
    $query_jobs='SELECT * FROM `jobs` WHERE `login`="'.$username.'" ORDER BY `id` ASC';
    $alljobs=simple_queryall($query_jobs);
    $allemployee=  ts_GetAllEmployee();
    $alljobtypes= ts_GetAllJobtypes();
    $activeemployee=  ts_GetActiveEmployee();
    
    $cells= wf_TableCell(__('ID'));
    $cells.=wf_tableCell(__('Date'));
    $cells.=wf_TableCell(__('Worker'));
    $cells.=wf_TableCell(__('Job type'));
    $cells.=wf_TableCell(__('Notes'));
    $cells.=wf_TableCell('');
    $rows=  wf_TableRow($cells, 'row1');
    
    if (!empty ($alljobs)) {
        foreach ($alljobs as $ion=>$eachjob) {
            //backlink to taskman if some TASKID inside
            if (ispos($eachjob['note'], 'TASKID:[')) {
                $taskid=vf($eachjob['note'],3);
                $jobnote=  wf_Link("?module=taskman&&edittask=".$taskid, __('Task is done').' #'.$taskid, false, '');
                
            } else {
                $jobnote=$eachjob['note'];
            }
            
            $cells= wf_TableCell($eachjob['id']);
            $cells.=wf_tableCell($eachjob['date']);
            $cells.=wf_TableCell(@$allemployee[$eachjob['workerid']]);
            $cells.=wf_TableCell(@$alljobtypes[$eachjob['jobid']]);
            $cells.=wf_TableCell($jobnote);
            $cells.=wf_TableCell(wf_JSAlert('?module=jobs&username='.$username.'&deletejob='.$eachjob['id'].'', web_delete_icon(), 'Are you serious'));
            $rows.=  wf_TableRow($cells, 'row3');
            
        }
     }
    
    //onstruct job create form
    $curdatetime=curdatetime();
    $inputs= wf_HiddenInput('addjob', 'true') ;
    $inputs.=wf_HiddenInput('jobdate', $curdatetime) ;
    $inputs.=wf_TableCell('');
    $inputs.=wf_tableCell($curdatetime);
    $inputs.=wf_TableCell(stg_worker_selector());
    $inputs.=wf_TableCell(stg_jobtype_selector());
    $inputs.=wf_TableCell(wf_TextInput('notes', '', '', false, '20'));
    $inputs.=wf_TableCell(wf_Submit('Create'));
    $inputs=wf_TableRow($inputs, 'row2');
  
    $addform=  wf_Form("", 'POST', $inputs, '');
           
        if ((!empty($activeemployee)) AND (!empty($alljobtypes))) {
            $rows.=$addform;
        } else {
            show_window(__('Error'),__('No job types and employee available'));
        }
        
        $result=  wf_TableBody($rows, '100%', '0', '');
        
    show_window(__('Jobs'), $result);
}

function stg_delete_job($jobid) {
    $jobid=vf($jobid);
    $query="DELETE from `jobs` WHERE `id`='".$jobid."'";
    nr_query($query);
    log_register("DELETE JOB [".$jobid."]");
}


function stg_add_new_job($login,$date,$worker_id,$jobtype_id,$job_notes) {
$job_notes=mysql_real_escape_string(trim($job_notes));
$datetime=curdatetime();
$query="INSERT INTO `jobs` (
       `id` ,
        `date` ,
        `jobid` ,
        `workerid` ,
        `login` ,
        `note` 
        )
        VALUES (
           NULL , '".$datetime."', '".$jobtype_id."', '".$worker_id."', '".$login."', '".$job_notes."'
            );
    ";
nr_query($query);
log_register("ADD JOB W:[".$worker_id."] J:[".$jobtype_id."] (".$login.")");
}

//
// New Task management API - old is shitty and exists only for backward compatibility
//

function ts_DetectUserByAddress($address) {
    $address= strtolower_utf8($address);
    $usersAddress= zb_AddressGetFulladdresslist();
    $alladdress=array();
    if (!empty($usersAddress)) {
        foreach ($usersAddress as $login=>$eachaddress) {
            $alladdress[$login]=  strtolower_utf8($eachaddress);
        }
    }
    $alladdress=  array_flip($alladdress);
    
    if (isset($alladdress[$address])) {
        return ($alladdress[$address]);
    } else {
        return(false);
    }
}

   function ts_GetAllEmployee() {
        $query="SELECT * from `employee`";
        $allemployee=  simple_queryall($query);
        $result=array();
        if (!empty($allemployee)) {
            foreach ($allemployee as $io=>$each) {
                $result[$each['id']]=$each['name'];
            }
        }
        return ($result);
    }
    
    function ts_GetAllJobtypes() {
        $query="SELECT * from `jobtypes`";
        $alljt=  simple_queryall($query);
        $result=array();
        if (!empty($alljt)) {
            foreach ($alljt as $io=>$each) {
                $result[$each['id']]=$each['jobname'];
            }
        }
        return ($result);
    }
    
       function ts_GetActiveEmployee () {
        $query="SELECT * from `employee` WHERE `active`='1'";
        $allemployee=  simple_queryall($query);
        $result=array();
        if (!empty($allemployee)) {
            foreach ($allemployee as $io=>$each) {
                $result[$each['id']]=$each['name'];
            }
        }
        return ($result);
    }
    
       function ts_JGetJobsReport() {
       $allemployee=  ts_GetAllEmployee();
       $alljobtypes= ts_GetAllJobtypes();
       $cyear=  curyear();
       
       $query="SELECT * from `jobs` WHERE `date` LIKE '".$cyear."-%' ORDER BY `id` DESC";
       $alljobs=  simple_queryall($query);
       
       $i=1;
       $jobcount=sizeof($alljobs);
       $result='';
       
       if (!empty($alljobs)) {
           foreach ($alljobs as $io=>$eachjob) {
               if ($i!=$jobcount) {
                    $thelast=',';
                } else {
                    $thelast='';
                }
               
               $startdate=strtotime($eachjob['date']);
               $startdate=date("Y, n-1, j",$startdate);
               
               $result.="
                      {
                        title: '".$allemployee[$eachjob['workerid']]." - ".@$alljobtypes[$eachjob['jobid']]."',
                        start: new Date(".$startdate."),
                        end: new Date(".$startdate."),
                        url: '?module=userprofile&username=".$eachjob['login']."'
		      }
                    ".$thelast;
               $i++;
           }
       }
       return ($result);
   } 
   
   
    function ts_JGetUndoneTasks() {
        $allemployee=  ts_GetAllEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        $curyear=curyear();
        $curmonth=date("m");
        
        //per employee filtering
        $displaytype =  (isset($_POST['displaytype'])) ? $_POST['displaytype'] : 'all';
        if ($displaytype=='onlyme') {
            $whoami=whoami();
            $curempid=  ts_GetEmployeeByLogin($whoami);
            $appendQuery=" AND `employee`='".$curempid."'";
        } else {
            $appendQuery='';
        }
        
        if (($curmonth!=1) AND ($curmonth!=12))  {
            $query="SELECT * from `taskman` WHERE `status`='0' AND `startdate` LIKE '".$curyear."-%' ".$appendQuery." ORDER BY `date` ASC";
        } else {
            $query="SELECT * from `taskman` WHERE `status`='0' ".$appendQuery." ORDER BY `date` ASC";
        }
        
        $allundone=  simple_queryall($query);
        $result='';
        $i=1;
        $taskcount=sizeof($allundone);
        
        if (!empty($allundone)) {
            foreach ($allundone as $io=>$eachtask) {
                if ($i!=$taskcount) {
                    $thelast=',';
                } else {
                    $thelast='';
                }
                
                $startdate=strtotime($eachtask['startdate']);
                $startdate=date("Y, n-1, j",$startdate);
                if ($eachtask['enddate']!='') {
                    $enddate=strtotime($eachtask['enddate']);
                    $enddate=date("Y, n-1, j",$enddate);
                } else {
                    $enddate=$startdate;
                }
          
                $result.="
                      {
                        title: '".$eachtask['address']." - ".@$alljobtypes[$eachtask['jobtype']]."',
                        start: new Date(".$startdate."),
                        end: new Date(".$enddate."),
                        className : 'undone',
                        url: '?module=taskman&edittask=".$eachtask['id']."'
                        
		      } 
                    ".$thelast;
            }
        }
     
        return ($result);
    }
    
    function ts_JGetDoneTasks() {
        $allemployee=  ts_GetAllEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        
        $curyear=curyear();
        $curmonth=date("m");
        
        //per employee filtering
        $displaytype =  (isset($_POST['displaytype'])) ? $_POST['displaytype'] : 'all';
        if ($displaytype=='onlyme') {
            $whoami=whoami();
            $curempid=  ts_GetEmployeeByLogin($whoami);
            $appendQuery=" AND `employee`='".$curempid."'";
        } else {
            $appendQuery='';
        }
        
        if (($curmonth!=1) AND ($curmonth!=12))  {
            $query="SELECT * from `taskman` WHERE `status`='1' AND `startdate` LIKE '".$curyear."-%' ".$appendQuery." ORDER BY `date` ASC";
        } else {
            $query="SELECT * from `taskman` WHERE `status`='1' ".$appendQuery." ORDER BY `date` ASC";
        }
        
        $allundone=  simple_queryall($query);
        $result='';
        $i=1;
        $taskcount=sizeof($allundone);
        
        if (!empty($allundone)) {
            foreach ($allundone as $io=>$eachtask) {
                if ($i!=$taskcount) {
                    $thelast=',';
                } else {
                    $thelast='';
                }
                
                $startdate=strtotime($eachtask['startdate']);
                $startdate=date("Y, n-1, j",$startdate);
                if ($eachtask['enddate']!='') {
                    $enddate=strtotime($eachtask['enddate']);
                    $enddate=date("Y, n-1, j",$enddate);
                } else {
                    $enddate=$startdate;
                }
          
                $result.="
                      {
                        title: '".$eachtask['address']." - ".@$allemployee[$eachtask['employeedone']]."',
                        start: new Date(".$startdate."),
                        end: new Date(".$enddate."),
                        url: '?module=taskman&edittask=".$eachtask['id']."'
		      }
                    ".$thelast;
            }
        }
     
        return ($result);
    }
    
    function ts_JGetAllTasks() {
        $allemployee=  ts_GetAllEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        
        $curyear=curyear();
        $curmonth=date("m");
        
         //per employee filtering
        $displaytype =  (isset($_POST['displaytype'])) ? $_POST['displaytype'] : 'all';
        if ($displaytype=='onlyme') {
            $whoami=whoami();
            $curempid=  ts_GetEmployeeByLogin($whoami);
            $appendQuery=" AND `employee`='".$curempid."'";
        } else {
            $appendQuery='';
        }
        
        if (($curmonth!=1) AND ($curmonth!=12))  {
            $query="SELECT * from `taskman` WHERE `startdate` LIKE '".$curyear."-%' ".$appendQuery." ORDER BY `date` ASC";
        } else {
            $query="SELECT * from `taskman` ".$appendQuery." ORDER BY `date` ASC";
        }
     
        $allundone=  simple_queryall($query);
        $result='';
        $i=1;
        $taskcount=sizeof($allundone);
        
        if (!empty($allundone)) {
            foreach ($allundone as $io=>$eachtask) {
                if ($i!=$taskcount) {
                    $thelast=',';
                } else {
                    $thelast='';
                }
                
                $startdate=strtotime($eachtask['startdate']);
                $startdate=date("Y, n-1, j",$startdate);
                if ($eachtask['enddate']!='') {
                    $enddate=strtotime($eachtask['enddate']);
                    $enddate=date("Y, n-1, j",$enddate);
                } else {
                    $enddate=$startdate;
                }
                
                if ($eachtask['status']==0) {
                    $coloring="className : 'undone',";
                } else {
                    $coloring='';
                }
          
                $result.="
                      {
                        title: '".$eachtask['address']." - ".@$alljobtypes[$eachtask['jobtype']]."',
                        start: new Date(".$startdate."),
                        end: new Date(".$enddate."),
                        ".$coloring."
                        url: '?module=taskman&edittask=".$eachtask['id']."'
		      }
                    ".$thelast;
            }
        }
     
        return ($result);
    }
    
    function ts_TaskTypicalNotesSelector($settings=true) {
    
        $rawNotes=  zb_StorageGet('PROBLEMS');
        if ($settings) {
            $settingsControl=  wf_Link("?module=taskman&probsettings=true", wf_img('skins/settings.png',__('Settings')), false, '');
        } else {
            $settingsControl='';
        }
        if (!empty($rawNotes)) {
            $rawNotes=  base64_decode($rawNotes);
            $rawNotes=  unserialize($rawNotes);
        } else {
          $emptyArray=array();
          $newNotes= serialize($emptyArray);
          $newNotes= base64_encode($newNotes);
          zb_StorageSet('PROBLEMS', $newNotes);
          $rawNotes=$emptyArray;
        }
        
        $typycalNotes=array(''=>'-');
        
        if (!empty($rawNotes)) {
            foreach ($rawNotes as $eachnote) {
                if (mb_strlen($eachnote,'utf-8')>20) { 
                    $shortNote=mb_substr($eachnote, 0, 20,'utf-8').'...';
                } else {
                    $shortNote=$eachnote;
                }
                $typycalNotes[$eachnote]=$shortNote;
            }
        }
        
        $selector=  wf_Selector('typicalnote', $typycalNotes, __('Problem').' '.$settingsControl, '', true);
        return ($selector);
    }
    
    function ts_TaskCreateForm() {
        $altercfg=  rcms_parse_ini_file(CONFIG_PATH."alter.ini");
        $alljobtypes= ts_GetAllJobtypes();
        $allemployee= ts_GetActiveEmployee();
        //construct sms sending inputs
        if ($altercfg['WATCHDOG_ENABLED']) {
            $smsInputs=  wf_CheckInput('newtasksendsms', __('Send SMS'), false, false);
        } else {
            $smsInputs='';
        }
        
        $inputs='<!--ugly hack to prevent datepicker autoopen --> <input type="text" name="shittyhack" style="width: 0; height: 0; top: -100px; position: absolute;"/>';
        $inputs.=  wf_HiddenInput('createtask', 'true');
        $inputs.=wf_DatePicker('newstartdate').' <label>'.__('Target date').'<sup>*</sup></label><br><br>';
        $inputs.=wf_TextInput('newtaskaddress', __('Address').'<sup>*</sup>', '', true, '30');
        $inputs.=wf_tag('br');
        $inputs.=wf_TextInput('newtaskphone', __('Phone').'<sup>*</sup>', '', true, '30');
        $inputs.=wf_tag('br');
        $inputs.=wf_Selector('newtaskjobtype', $alljobtypes, __('Job type'), '', true);
        $inputs.=wf_tag('br');
        $inputs.=wf_Selector('newtaskemployee', $allemployee, __('Who should do'), '', true);
        $inputs.=wf_tag('br');
        $inputs.=ts_TaskTypicalNotesSelector();
        $inputs.=wf_tag('label').__('Job note').wf_tag('label',true).  wf_tag('br');
        $inputs.=wf_TextArea('newjobnote', '', '', true, '35x5');
        $inputs.=$smsInputs;
        $inputs.=wf_Submit(__('Create new task'));
        $result=  wf_Form("", 'POST', $inputs, 'glamour');
        $result.=__('All fields marked with an asterisk are mandatory');
        return ($result);
    }
    
    function ts_TaskCreateFormProfile($address,$mobile,$phone) {
        $altercfg=  rcms_parse_ini_file(CONFIG_PATH."alter.ini");
        $alljobtypes= ts_GetAllJobtypes();
        $allemployee= ts_GetActiveEmployee();
        
          //construct sms sending inputs
        if ($altercfg['WATCHDOG_ENABLED']) {
            $smsInputs=  wf_CheckInput('newtasksendsms', __('Send SMS'), false, false);
        } else {
            $smsInputs='';
        }
        
        $inputs='<!--ugly hack to prevent datepicker autoopen --> <input type="text" name="shittyhack" style="width: 0; height: 0; top: -100px; position: absolute;"/>';
        $inputs.=wf_HiddenInput('createtask', 'true');
        $inputs.=wf_DatePicker('newstartdate').' <label>'.__('Target date').'<sup>*</sup></label><br><br>';
        $inputs.=wf_TextInput('newtaskaddress', __('Address').'<sup>*</sup>', $address, true, '30');
        $inputs.=wf_tag('br');
        $inputs.=wf_TextInput('newtaskphone', __('Phone').'<sup>*</sup>', $mobile.' '.$phone, true, '30');
        $inputs.=wf_tag('br');
        $inputs.=wf_Selector('newtaskjobtype', $alljobtypes, __('Job type'), '', true);
        $inputs.=wf_tag('br');
        $inputs.=wf_Selector('newtaskemployee', $allemployee, __('Who should do'), '', true);
        $inputs.=wf_tag('br');
        $inputs.=wf_tag('label').__('Job note').wf_tag('label',true).  wf_tag('br');
        $inputs.=ts_TaskTypicalNotesSelector();
        $inputs.=wf_TextArea('newjobnote', '', '', true, '35x5');
        $inputs.=$smsInputs;
        $inputs.=wf_Submit(__('Create new task'));
        $result=  wf_Form("?module=taskman&gotolastid=true", 'POST', $inputs, 'glamour');
        $result.=__('All fields marked with an asterisk are mandatory');
        return ($result);
    }
    
    function ts_TaskCreateFormSigreq($address,$phone) {
        $alljobtypes= ts_GetAllJobtypes();
        $allemployee= ts_GetActiveEmployee();
        $inputs='<!--ugly hack to prevent datepicker autoopen --> <input type="text" name="shittyhack" style="width: 0; height: 0; top: -100px; position: absolute;"/>';
        $inputs.=wf_HiddenInput('createtask', 'true');
        $inputs.=wf_DatePicker('newstartdate').' <label>'.__('Target date').'<sup>*</sup></label><br><br>';
        $inputs.=wf_TextInput('newtaskaddress', __('Address').'<sup>*</sup>', $address, true, '30');
        $inputs.='<br>';
        $inputs.=wf_TextInput('newtaskphone', __('Phone').'<sup>*</sup>', $phone, true, '30');
        $inputs.='<br>';
        $inputs.=wf_Selector('newtaskjobtype', $alljobtypes, __('Job type'), '', true);
        $inputs.='<br>';
        $inputs.=wf_Selector('newtaskemployee', $allemployee, __('Who should do'), '', true);
        $inputs.='<br>';
        $inputs.='<label>'.__('Job note').'</label><br>';
        $inputs.=ts_TaskTypicalNotesSelector();
        $inputs.=wf_TextArea('newjobnote', '', '', true, '35x5');
        $inputs.=wf_Submit(__('Create new task'));
        $result=  wf_Form("?module=taskman&gotolastid=true", 'POST', $inputs, 'glamour');
        $result.=__('All fields marked with an asterisk are mandatory');
        return ($result);
    }
    
    
    function ts_ShowPanel() {
        $createform=  ts_TaskCreateForm();
        $result=  wf_modal(__('Create task'), __('Create task'), $createform, 'ubButton', '420', '500');
        $result.=wf_Link('?module=taskman&show=undone', __('Undone tasks'), false, 'ubButton');
        $result.=wf_Link('?module=taskman&show=done', __('Done tasks'), false, 'ubButton');
        $result.=wf_Link('?module=taskman&show=all', __('List all tasks'), false, 'ubButton');
        $result.=wf_Link('?module=taskman&lateshow=true', __('Show late'), false, 'ubButton');
        $result.=wf_Link('?module=taskman&print=true', __('Tasks printing'), false, 'ubButton');
        
        //show type selector
        $whoami=whoami();
        $employeeid=  ts_GetEmployeeByLogin($whoami);
        if ($employeeid) { 
            $result.=wf_delimiter();
            $curselected = (isset($_POST['displaytype'])) ? $_POST['displaytype'] : '' ;
            $displayTypes=array('all'=>__('Show tasks for all users'),'onlyme'=>__('Show only mine tasks'));
            $inputs=  wf_Selector('displaytype', $displayTypes, '', $curselected, false);
            $inputs.= wf_Submit('Show');
            $showTypeForm=  wf_Form('', 'POST', $inputs, 'glamour');
            $result.=$showTypeForm;
        } 
        
        return ($result);
    }
    
    function ts_SendSMS($employeeid,$message) {
        $query="SELECT `mobile`,`name` from `employee` WHERE `id`='".$employeeid."'";
        $empData=  simple_query($query);
        $mobile=$empData['mobile'];
        $employeeName=$empData['name'];
        $result=array();
        if (!empty($mobile)) {
          if (ispos($mobile, '+')) {
        $message=  str_replace('\r\n', ' ', $message);
        $message= zb_TranslitString($message);
        $message=  trim($message);
        
        $number=trim($mobile);
        $filename='content/tsms/ts_'.zb_rand_string(8);
        $storedata='NUMBER="'.$number.'"'."\n";
        $storedata.='MESSAGE="'.$message.'"'."\n";
        $result['number']=$number;
        $result['message']=$message;
        file_put_contents($filename, $storedata);
        log_register("TASKMAN SEND SMS `".$number."` FOR `".$employeeName."`");
        } else {
                throw new Exception('BAD_MOBILE_FORMAT');
            }
        }
        return ($result);
    }
    
     function ts_CreateTask($startdate,$address,$phone,$jobtypeid,$employeeid,$jobnote) {
        $altercfg=  rcms_parse_ini_file(CONFIG_PATH."alter.ini");
        $curdate=curdatetime();
        $admin=  whoami();
        $address=  str_replace('\'', '`', $address);
        $address=  mysql_real_escape_string($address);
        $phone=  mysql_real_escape_string($phone);
        $jobtypeid=vf($jobtypeid,3);
        $employeeid=vf($employeeid,3);
        $jobnote=  mysql_real_escape_string($jobnote);
        $smsData='NULL';
        //store sms for backround processing via watchdog
        if ($altercfg['WATCHDOG_ENABLED']) {
            if (isset($_POST['newtasksendsms'])) {
                $newSmsText=$address.' '.$phone.' '.$jobnote;
                $smsDataRaw=ts_SendSMS($employeeid, $newSmsText);
                if (!empty($smsDataRaw)) {
                $smsData=  serialize($smsDataRaw);
                $smsData= "'".base64_encode($smsData)."'";
                }
            }
        }
        
        $query="INSERT INTO `taskman` (
                            `id` ,
                            `date` ,
                            `address` ,
                            `jobtype` ,
                            `jobnote` ,
                            `phone` ,
                            `employee` ,
                            `employeedone` ,
                            `donenote` ,
                            `startdate` ,
                            `enddate` ,
                            `admin` ,
                            `status`,
                            `smsdata`
                                       )
                                VALUES (
                                    NULL ,
                                    '".$curdate."',
                                    '".$address."',
                                    '".$jobtypeid."',
                                    '".$jobnote."',
                                    '".$phone."',
                                    '".$employeeid."',
                                    'NULL',
                                    NULL ,
                                    '".$startdate."',
                                    NULL ,
                                    '".$admin."',
                                    '0',
                                    ".$smsData."
                    );";
        nr_query($query);
        log_register("TASKMAN CREATE `".$address."`");
    }
    
 function ts_GetTaskData($taskid) {
        $taskid=vf($taskid,3);
        $query="SELECT * from `taskman` WHERE `id`='".$taskid."'";
        $result=  simple_query($query);
        return ($result);
    }   
    
    
    function ts_TaskModifyForm($taskid) {
        $taskid=vf($taskid,3);
        $taskdata=  ts_GetTaskData($taskid);
        $result='';
        $allemployee= ts_GetAllEmployee();
        $activeemployee=  ts_GetActiveEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        if (!empty($taskdata)) {
        $inputs=wf_HiddenInput('modifytask', $taskid);
        $inputs.=wf_TextInput('modifystartdate', __('Target date').'<sup>*</sup>', $taskdata['startdate'], false);
        $inputs.='<br>';
        $inputs.=wf_TextInput('modifytaskaddress', __('Address').'<sup>*</sup>', $taskdata['address'], true, '30');
        $inputs.='<br>';
        $inputs.=wf_TextInput('modifytaskphone', __('Phone').'<sup>*</sup>', $taskdata['phone'], true, '30');
        $inputs.='<br>';
        $inputs.=wf_Selector('modifytaskjobtype', $alljobtypes, __('Job type'), $taskdata['jobtype'], true);
        $inputs.='<br>';
        $inputs.=wf_Selector('modifytaskemployee', $activeemployee, __('Who should do'), $taskdata['employee'], true);
        $inputs.='<br>';
        $inputs.='<label>'.__('Job note').'</label><br>';
        $inputs.=wf_TextArea('modifytaskjobnote', '', $taskdata['jobnote'], true, '35x5');
        $inputs.=wf_Submit(__('Save'));
        $result=  wf_Form("", 'POST', $inputs, 'glamour');
        $result.=__('All fields marked with an asterisk are mandatory');
            
        }
        
        
        return ($result);
    }
    
    
        function ts_ModifyTask($taskid,$startdate,$address,$phone,$jobtypeid,$employeeid,$jobnote) {
        $taskid=vf($taskid,3);
        $startdate=  mysql_real_escape_string($startdate);
        $address=  str_replace('\'', '`', $address);
        $address=  mysql_real_escape_string($address);
        $phone=  mysql_real_escape_string($phone);
        $jobtypeid=vf($jobtypeid,3);
        $employeeid=vf($employeeid,3);
        
        simple_update_field('taskman', 'startdate', $startdate, "WHERE `id`='".$taskid."'");
        simple_update_field('taskman', 'address', $address, "WHERE `id`='".$taskid."'");
        simple_update_field('taskman', 'phone', $phone, "WHERE `id`='".$taskid."'");
        simple_update_field('taskman', 'jobtype', $jobtypeid, "WHERE `id`='".$taskid."'");
        simple_update_field('taskman', 'employee', $employeeid, "WHERE `id`='".$taskid."'");
        simple_update_field('taskman', 'jobnote', $jobnote, "WHERE `id`='".$taskid."'");
        log_register("TASKMAN MODIFY `".$address.'`');
        
    }
    
      function ts_TaskChangeForm($taskid) {
        $taskid=vf($taskid,3);
        $taskdata=  ts_GetTaskData($taskid);
        $result='';
        $allemployee= ts_GetAllEmployee();
        $activeemployee=  ts_GetActiveEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        $smsData='';
        
        if (!empty($taskdata)) {
            //not done task
            $login_detected=ts_DetectUserByAddress($taskdata['address']);
            if ($login_detected) {
                $addresslink=wf_Link("?module=userprofile&username=".$login_detected, web_profile_icon().' '.$taskdata['address'], false);
            } else {
                $addresslink=$taskdata['address'];
            }
            
            //job generation form
            if ($login_detected) {
                $jobgencheckbox=  wf_CheckInput('generatejob', __('Generate job performed for this task'), true, true);
                $jobgencheckbox.= wf_HiddenInput('generatelogin', $login_detected);
                $jobgencheckbox.= wf_HiddenInput('generatejobid', $taskdata['jobtype']);
                $jobgencheckbox.= wf_delimiter();
                
            } else {
                $jobgencheckbox='';
            }
            
            //modify form handlers
            $modform=  wf_modal(web_edit_icon(), __('Edit'), ts_TaskModifyForm($taskid), '', '420', '500');
            //modform end
            
            //extracting sms data
            if (!empty($taskdata['smsdata'])) {
                $rawSmsData=  $taskdata['smsdata'];
                $rawSmsData=  base64_decode($rawSmsData);
                $rawSmsData=  unserialize($rawSmsData);
               
                
                $smsDataCells=  wf_TableCell(__('Mobile'), '', 'row2');
                $smsDataCells.= wf_TableCell($rawSmsData['number']);
                $smsDataRows= wf_TableRow($smsDataCells, 'row3');
                $smsDataCells=  wf_TableCell(__('Message'), '', 'row2');
                $smsDataCells.= wf_TableCell($rawSmsData['message']);
                $smsDataRows.= wf_TableRow($smsDataCells, 'row3');
                $smsDataTable=  wf_TableBody($smsDataRows, '100%', '0', 'glamour');
                
                $smsData=  wf_modal(wf_img('skins/icon_sms_micro.gif', __('SMS sent to employees')), __('SMS sent to employees'), $smsDataTable, '', '400', '200');
            }
            
            $tablecells=  wf_TableCell(__('Task creation date').' / '.__('Administrator'),'30%');
            $tablecells.=  wf_TableCell($taskdata['date'].' / '.$taskdata['admin']);
            $tablerows=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Target date'));
            $tablecells.=  wf_TableCell('<strong>'.$taskdata['startdate'].'</strong>');
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Task address'));
            $tablecells.=  wf_TableCell($addresslink);
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Phone'));
            $tablecells.=  wf_TableCell($taskdata['phone']);
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Job type'));
            $tablecells.=  wf_TableCell(@$alljobtypes[$taskdata['jobtype']]);
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Who should do'));
            $tablecells.=  wf_TableCell(@$allemployee[$taskdata['employee']].' '.$smsData);
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $tablecells=  wf_TableCell(__('Job note'));
            $tablecells.=  wf_TableCell(nl2br($taskdata['jobnote']));
            $tablerows.=  wf_TableRow($tablecells,'row3');
            
            $result.=wf_TableBody($tablerows, '100%', '0', 'glamour');
            // show task preview
            show_window(__('View task').' '.$modform,$result);
            
            //if task undone
            if ($taskdata['status']==0) {
            
            $inputs=  wf_HiddenInput('changetask', $taskid);
            $inputs.=wf_DatePicker('editenddate').' <label>'.__('Finish date').'<sup>*</sup></label> <br>';
            $inputs.='<br>';
            $inputs.=wf_Selector('editemployeedone', $activeemployee, __('Worker done'), $taskdata['employee'], true);
            $inputs.=wf_tag('br');
            $inputs.='<label>'.__('Finish note').'</label> <br>';
            $inputs.=wf_TextArea('editdonenote', '', '', true, '35x3');
            $inputs.=wf_tag('br');
            $inputs.= $jobgencheckbox;
            $inputs.=wf_Submit(__('This task is done'));
            
            
            $form=  wf_Form("", 'POST', $inputs, 'glamour');
                
            //show editing form
            show_window(__('If task is done'),$form);
            
            } else {
                $donecells=  wf_TableCell(__('Finish date'),'30%');
                $donecells.=wf_TableCell($taskdata['enddate']);
                $donerows=  wf_TableRow($donecells,'row3');
                
                $donecells=  wf_TableCell(__('Worker done'));
                $donecells.=wf_TableCell($allemployee[$taskdata['employeedone']]);
                $donerows.=wf_TableRow($donecells,'row3');
                
                $donecells=  wf_TableCell(__('Finish note'));
                $donecells.=wf_TableCell($taskdata['donenote']);
                $donerows.=wf_TableRow($donecells,'row3');
                
               $doneresult= wf_TableBody($donerows,'100%','0','glamour');
               $doneresult.=wf_JSAlert('?module=taskman&deletetask='.$taskid, web_delete_icon(__('Remove this task - it is an mistake')),__('Removing this may lead to irreparable results'));
               $doneresult.='&nbsp;';
               $doneresult.=wf_JSAlert('?module=taskman&setundone='.$taskid,  wf_img('skins/icon_key.gif',__('No work was done')),__('Are you serious'));
               
               show_window(__('Task is done'),$doneresult);
            }
        }
        
    }
    
    function ts_DeleteTask($taskid) {
      $taskid=vf($taskid,3);
      $query="DELETE from `taskman` WHERE `id`='".$taskid."'";
      nr_query($query);
      log_register("TASKMAN DELETE ".$taskid);
      
  }
  
  function ts_TaskProblemsEditForm() {
        $rawNotes=  zb_StorageGet('PROBLEMS');
        
        //extract old or create new typical problems array
        if (!empty($rawNotes)) {
            $rawNotes=  base64_decode($rawNotes);
            $rawNotes=  unserialize($rawNotes);
        } else {
          $emptyArray=array();
          $newNotes= serialize($emptyArray);
          $newNotes= base64_encode($newNotes);
          zb_StorageSet('PROBLEMS', $newNotes);
          $rawNotes=$emptyArray;
        }
        
        //adding and deletion subroutines
        if (wf_CheckPost(array('createtypicalnote'))) {
            $toPush=strip_tags($_POST['createtypicalnote']);
            array_push($rawNotes, $toPush);
            $newNotes= serialize($rawNotes);
            $newNotes= base64_encode($newNotes);
            zb_StorageSet('PROBLEMS', $newNotes);
            log_register('TASKMAN ADD TYPICALPROBLEM');
            rcms_redirect("?module=taskman&probsettings=true");
        }
        
        if (wf_CheckPost(array('deletetypicalnote','typicalnote'))) {
            $toUnset=$_POST['typicalnote'];
            if (($delkey = array_search($toUnset, $rawNotes)) !== false) {
                unset($rawNotes[$delkey]);
            }
  
            $newNotes= serialize($rawNotes);
            $newNotes= base64_encode($newNotes);
            zb_StorageSet('PROBLEMS', $newNotes);
            log_register('TASKMAN DELETE TYPICALPROBLEM');
            rcms_redirect("?module=taskman&probsettings=true");
            
        }
        
    
        $rows='';
        $result=  wf_Link("?module=taskman", __('Back'), true, 'ubButton');
        
        if (!empty($rawNotes)) {
            foreach ($rawNotes as $eachNote) {
                $cells=  wf_TableCell($eachNote);
                $rows.= wf_TableRow($cells, 'row3');
            }
        }
        
        $result.=  wf_TableBody($rows, '100%', '0', '');
        $result.=  wf_delimiter();
        
        $addinputs=  wf_TextInput('createtypicalnote', __('Create'), '', true, '20');
        $addinputs.= wf_Submit(__('Save'));
        $addform=  wf_Form("", "POST", $addinputs, 'glamour');
        $result.= $addform;
        
        $delinputs=  ts_TaskTypicalNotesSelector(false);
        $delinputs.= wf_HiddenInput('deletetypicalnote','true');
        $delinputs.= wf_Submit(__('Delete'));
        $delform= wf_Form("", "POST", $delinputs, 'glamour');
        $result.= $delform;
        
        return ($result);
    
  }
  
  function ts_PrintDialogue() {
      $inputs=  wf_DatePickerPreset('printdatefrom', curdate()).' '.__('From');
      $inputs.= wf_DatePickerPreset('printdateto', curdate()).' '.__('To');
      $inputs.= wf_Submit(__('Print'));
      $result=  wf_Form("", 'POST', $inputs, 'glamour');
      return ($result);
  }
  
  function ts_PrintTasks($datefrom,$dateto) {
      $datefrom=  mysql_real_escape_string($datefrom);
      $dateto= mysql_real_escape_string($dateto);
      $allemployee=  ts_GetAllEmployee();
      $alljobtypes=  ts_GetAllJobtypes();
      $result=  wf_tag('style');
      $result.= '
        table.gridtable {
	font-family: verdana,arial,sans-serif;
	
	font-size:9pt; 
	color:#333333;
	border-width: 1px;
	border-color: #666666;
	border-collapse: collapse;
        }
        table.gridtable th {
	border-width: 1px;
	padding: 3px;
	border-style: solid;
	border-color: #666666;
	background-color: #dedede;
        }
        table.gridtable td {
	border-width: 1px;
	padding: 3px;
	border-style: solid;
	border-color: #666666;
	background-color: #ffffff; 
        }
        ';
      $result.= wf_tag('style', true);
      
      $query="select * from `taskman` where `startdate` BETWEEN '".$datefrom." 00:00:00' AND '".$dateto." 23:59:59' AND `status`='0'";
      $alltasks=  simple_queryall($query);
      if (!empty($alltasks)) {
          foreach ($alltasks as $io=>$each) {
              $rows='';
              $cells=   wf_TableCell(__('ID'));
              $cells.=  wf_TableCell($each['id']);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Target date'));
              $cells.=  wf_TableCell($each['startdate']);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Task address'));
              $cells.=  wf_TableCell($each['address']);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Phone'));
              $cells.=  wf_TableCell($each['phone']);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Job type'));
              $cells.=  wf_TableCell(@$alljobtypes[$each['jobtype']]);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Who should do'));
              $cells.=  wf_TableCell(@$allemployee[$each['employee']]);
              $rows.=   wf_TableRow($cells);
              
              $cells=   wf_TableCell(__('Job note'));
              $cells.=  wf_TableCell($each['jobnote']);
              $rows.=   wf_TableRow($cells);
              $tasktable= wf_TableBody($rows, '100%', '0','gridtable');
              $result.= wf_tag('div', false, '', 'style="width: 300px; height: 250px; float: left; border: dashed; border-width:1px; margin:5px; page-break-inside: avoid;"');
              $result.= $tasktable;
              $result.= wf_tag('div', true);
          }
          $result.='<script language="javascript"> 
                        window.print();
                    </script>';
          die($result);
      }
      
      
  }
  
  function ts_ShowLate() {
        $allemployee=  ts_GetAllEmployee();
        $alljobtypes= ts_GetAllJobtypes();
        $curyear= curyear();
        $curmonth= date("m");
        $curdate=  curdate();
        if (($curmonth!=1) AND ($curmonth!=12))  {
            $query="SELECT * from `taskman` WHERE `status`='0' AND `startdate` LIKE '".$curyear."-%' AND `startdate`< '".$curdate."' ORDER BY `startdate` ASC";
        } else {
            $query="SELECT * from `taskman` WHERE `status`='0' AND `startdate`< '".$curdate."' ORDER BY `startdate` ASC";
        }
        
        $cells=  wf_TableCell(__('Target date'));
        $cells.= wf_TableCell(__('Task address'));
        $cells.= wf_TableCell(__('Phone'));
        $cells.= wf_TableCell(__('Job type'));
        $cells.= wf_TableCell(__('Who should do'));
        $cells.= wf_TableCell(__('Actions'));
        $rows= wf_TableRow($cells, 'row1');
        
        
        $all =  simple_queryall($query);
        if (!empty($all)) {
            foreach ($all as $io=>$each) {
                $cells=  wf_TableCell($each['startdate']);
                $cells.= wf_TableCell($each['address']);
                $cells.= wf_TableCell($each['phone']);
                $cells.= wf_TableCell(@$alljobtypes[$each['jobtype']]);
                $cells.= wf_TableCell(@$allemployee[$each['employee']]);
                $actions=  wf_Link('?module=taskman&edittask='.$each['id'], web_edit_icon(), false, '');
                $cells.= wf_TableCell($actions);
                $rows.= wf_TableRow($cells, 'row3');
            }
        }
        
        $result=  wf_TableBody($rows, '100%', '0', 'sortable');
        return ($result);
  }
  
  /*
   * Gets employee by administrator login
   * 
   * @param $login logged in administrators login
   * 
   * @return mixed 
   */
  function ts_GetEmployeeByLogin($login) {
      $login=  mysql_real_escape_string($login);
      $query="SELECT `id` from `employee` WHERE `admlogin`='".$login."'";
      $raw=  simple_query($query);
      if (!empty($raw)) {
          $result=$raw['id'];
      } else {
          $result=false;
      }
      return ($result);
  }

?>
