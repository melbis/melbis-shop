<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************
 *
 * MELBIS_INC_AUTH                      - Smart user authorization start
 * MELBIS_INC_AUTH_user                 - Authorization user by login and password
 * MELBIS_INC_AUTH_user_command         - Verify user access to system command
 * MELBIS_INC_AUTH_web                  - Authorization user for web or application 
 * MELBIS_INC_AUTH_web_access           - Verify user access for inside or outside module
 * MELBIS_INC_AUTH_web_key_prepare      - Prepare user rights for web_key
 * MELBIS_INC_AUTH_web_key_access       - Verify user access to web_key
 * MELBIS_INC_AUTH_order_right_prepare  - Prepare user access to order     
 * 
 **************************************************************************************************/
              
 
/** 
 * Function MELBIS_INC_AUTH
 * Smart user authorization start
 **/
function MELBIS_INC_AUTH($mModule, $mVars)
{ 
    global $gParser, $gSession;    
        
    // Logout?
    if ( isset($mVars['post']['logout']) )
    {        
        $gSession->RemoveValue('melbis_auth_user_id');
    }           
        
    // Init vars             
    $gParser->gVars['melbis']['mod'] = strtolower($mModule);    
    
    // Auth module          
    list($user_id, $result) = MELBIS_INC_AUTH_web($mModule, $mVars['post']);    
                             
    // Action switcher
    $func = $mVars['post']['func'] ?? 'default';   
    if ( function_exists($mModule.'_'.$func) )
    {
        if ( $func == 'default' )
        {
            return call_user_func($mModule.'_'.$func, $user_id, $result, $mVars);     
        }                                                             
        else
        {
            if  ( $user_id > 0 && $result == 'accept' )
            {
                return call_user_func($mModule.'_'.$func, $user_id, $mVars);            
            }
            else
            {
                return 'User access denied!';
            }
        }            
    }  
    else 
    {
        return 'Function '.$func.' is absent!';
    }                                   
}    

/** 
 * Function MELBIS_INC_AUTH_user
 * Authorization user by login and password
 **/
function MELBIS_INC_AUTH_user($mLogin, $mPassCode)
{ 
    global $gParser;       
                                 
    $command = "SELECT *                                                                                                                                                                               
                  FROM {DBNICK}_user
                 WHERE login = '".addslashes($mLogin)."'     
                   AND pass_code = '".addslashes($mPassCode)."'  
                   AND is_blocked = 0       
               ";   
    $user = $gParser->SqlSelectToArray(__LINE__, $command);
            
    return $user['id'] ?? 0;
} 


/** 
 * Function MELBIS_INC_AUTH_user_command
 * Verify user access to system command 
 **/   
function MELBIS_INC_AUTH_user_command($mUserId, $mCommand)
{
    global $gParser;     

    if ( $mUserId != 1 )
    {
        $command = "SELECT oper_right.*
                      FROM {DBNICK}_oper o
                      JOIN {DBNICK}_oper_right oper_right
                        ON oper_right.oper_id = o.id
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = oper_right.group_id OR u.add_group_id = oper_right.group_id )
                     WHERE BINARY o.command = '$mCommand'
                       AND ( oper_right.user_id = '$mUserId' OR u.id = '$mUserId' )
                   ";
        $query = $gParser->SqlQuery(__LINE__, $command);

        return ( $gParser->SqlNumRows($query) > 0 );
    }
    else 
    {
        return true;
    }                      
}  
 
 
/** 
 * Function MELBIS_INC_AUTH_web    
 * Authorization user for web or application
 **/
function MELBIS_INC_AUTH_web($mModule, $mPost)
{ 
    global $gParser, $gSession;                
                        
    // Prepare pass code
    if ( isset($mPost['pass']) ) $mPost['pass_code'] = md5($mPost['pass']);                                                
            
    // User module auth
    $user_id = $gSession->GetValue('melbis_auth_'.$mModule.'_user_id');    
    if ( $gSession->GetValue('melbis_auth_user_id') == $user_id && $user_id > 0 )
    {                                    
        return array($user_id, 'accept');
    }    
    else
    {     
        // User auth?
        $user_id = $gSession->GetValue('melbis_auth_user_id');
        if ( $user_id > 0 )
        {
            // Test module access            
            if ( MELBIS_INC_AUTH_web_access($user_id, $mModule) > 0 )
            {
                // Accept access, save it
                $gSession->SetValue('melbis_auth_'.$mModule.'_user_id', $user_id);
                return array($user_id, 'accept');            
            }                   
            else
            {
                // Denied access for module     
                return array($user_id, 'denied');                                 
            }
        }
        else
        {                        
            // Not auth yet
            if ( isset($mPost['login']) )
            {                   
                // Try auth user
                $user_id = MELBIS_INC_AUTH_user($mPost['login'], $mPost['pass_code']);
                if ( $user_id == 0 )
                {
                    // Wrong
                    return array(0, 'wrong');
                }
                else
                {   
                    // Accept user and save it
                    $gSession->SetValue('melbis_auth_user_id', $user_id);
                    // Test module access
                    if ( MELBIS_INC_AUTH_web_access($user_id, $mModule) > 0 )
                    {
                        // Accept access, save it
                        $gSession->SetValue('melbis_auth_'.$mModule.'_user_id', $user_id);
                        return array($user_id, 'accept');            
                    }                   
                    else
                    {
                        // Denied access for module
                        return array($user_id, 'denied');                                 
                    }                    
                }
            }
            else
            {
                return array(0, 'start');
            }
        }
    }                                            
}    



/** 
 * Function MELBIS_INC_AUTH_web_access 
 * Verify user access for inside or outside module
 **/
function MELBIS_INC_AUTH_web_access($mUserId, $mModule)
{ 
    global $gParser;    
                      
    // User has admin access? 
    $user_admin = MELBIS_INC_AUTH_user_command($mUserId, 'PUT_OUTSIDE_RIGHT');        
    
    // User module access         
    if ( !$user_admin )
    {                                 
        $table = ( strpos($mModule, 'OUTSIDE') !== false ) ? 'outside' : 'inside';
        switch ($table) 
        {
            case 'inside':
                $command = "SELECT inside_right.*
                              FROM {DBNICK}_web_inside inside
                              JOIN {DBNICK}_web_inside_right inside_right
                                ON inside_right.inside_id = inside.id
                         LEFT JOIN {DBNICK}_user u
                                ON ( u.group_id = inside_right.group_id OR u.add_group_id = inside_right.group_id )
                             WHERE inside.skey = '$mModule' 
                               AND ( inside_right.user_id = '$mUserId' OR u.id = '$mUserId' OR inside.auth = 0 )
                           ";
                $query = $gParser->SqlQuery(__LINE__, $command);                                               
                $result = ( $gParser->SqlNumRows($query) > 0 );                                                 
                break;  
                
            case 'outside':
                $command = "SELECT outside_right.*
                              FROM {DBNICK}_web_outside outside
                              JOIN {DBNICK}_web_outside_right outside_right
                                ON outside_right.outside_id = outside.id
                         LEFT JOIN {DBNICK}_user u
                                ON ( u.group_id = outside_right.group_id OR u.add_group_id = outside_right.group_id )
                             WHERE outside.skey = '$mModule' 
                               AND ( outside_right.user_id = '$mUserId' OR u.id = '$mUserId' OR outside.auth = 0 )
                           ";
                $query = $gParser->SqlQuery(__LINE__, $command);                                               
                $result = ( $gParser->SqlNumRows($query) > 0 );  
                break;     
                
            default:
                $result = false;  
        }                  
    }
    else
    {
        $result = true;
    }
    
    // Result    
    if ( $result )
    {
        return $mUserId;
    }                   
    else
    {
        return 0;
    }    
} 
     


/** 
 * Function MELBIS_INC_AUTH_web_key_prepare
 * Prepare user rights for web_key
 **/
function MELBIS_INC_AUTH_web_key_prepare($mUserId)
{ 
    global $gParser;       
                                            
    // Clear
    $command = "DELETE FROM {DBNICK}_tmp_web_key
                 WHERE user_id = '$mUserId'
               "; 
    $gParser->SqlQuery(__LINE__, $command);    
            
    if ( MELBIS_INC_AUTH_user_command($mUserId, 'PUT_OUTSIDE_RIGHT') )
    {
        // No Limits
        $command = "INSERT INTO {DBNICK}_tmp_web_key
                    SELECT id, $mUserId, 1, 1, 1
                      FROM {DBNICK}_web_key
                   ";                   
        $gParser->SqlQuery(__LINE__, $command);         
    }
    else 
    {       
        // Limit by rights                   
        $command = "INSERT INTO {DBNICK}_tmp_web_key 
                    SELECT web_key_right.web_key_id, $mUserId, 
                           MAX(web_key_right.for_read), 
                           MAX(web_key_right.for_write), 
                           MAX(web_key_right.for_remove) 
                      FROM {DBNICK}_web_key_right web_key_right
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = web_key_right.group_id OR u.add_group_id = web_key_right.group_id )
                     WHERE ( web_key_right.user_id = '$mUserId' OR u.id = '$mUserId' )                           
                  GROUP BY web_key_right.web_key_id
                   ";
        $gParser->SqlQuery(__LINE__, $command);
    }
                

    return true;                          
}     



/** 
 * Function MELBIS_INC_AUTH_web_key_access    
 * Verify user access to web_key
 **/
function MELBIS_INC_AUTH_web_key_access($mUserId, $mKey, $mFor)
{ 
    global $gParser;       
           
    $command = "SELECT wk.id                         
                  FROM {DBNICK}_web_key wk
                  JOIN {DBNICK}_tmp_web_key twk
                    ON twk.id = wk.id
                   AND twk.user_id = '$mUserId'
                   AND twk.{$mFor} = 1                                                                                                                  
                 WHERE wk.skey = '$mKey'                                                                       
               ";
    $query = $gParser->SqlQuery(__LINE__, $command);
    
    return ( $gParser->SqlNumRows($query) > 0 );
}




/** 
 * Function MELBIS_INC_AUTH_order_right_prepare    
 * Prepare user access to order
 **/
function MELBIS_INC_AUTH_order_right_prepare($mUserId)
{ 
    global $gParser;       
                                            
    // Clear
    $command = "DELETE FROM {DBNICK}_tmp_order_right
                 WHERE user_id = '$mUserId'
               "; 
    $gParser->SqlQuery(__LINE__, $command);    
            
    if ( MELBIS_INC_AUTH_user_command($mUserId, 'PUT_ORDER_OPTION') )
    {
        // No Limits
        $command = "INSERT INTO {DBNICK}_tmp_order_right
                    SELECT id, $mUserId
                      FROM {DBNICK}_order_option_value
                   ";                   
        $gParser->SqlQuery(__LINE__, $command);         
    }
    else 
    {       
        // Limit by rights                   
        $command = "INSERT INTO {DBNICK}_tmp_order_right
                    SELECT o_r.value_id, $mUserId 
                      FROM {DBNICK}_order_right o_r
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = o_r.group_id OR u.add_group_id = o_r.group_id )
                     WHERE ( o_r.user_id = '$mUserId' OR u.id = '$mUserId' )                           
                  GROUP BY o_r.value_id
                   ";
        $gParser->SqlQuery(__LINE__, $command);
    }
                
    return true;                          
}   



?>