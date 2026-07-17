<?php

/** 
 * Function MELBIS_INC_AUTH_router
 **/
function MELBIS_INC_AUTH_router($mModule, $mVars)
{ 
    // Logout?
    if ( isset($mVars['post']['logout']) )
    {        
        MELBIS()->SessionRemoveValue('melbis_user');
    }           
        
    // Auth user and verify access for module          
    list($user_id, $result) = MELBIS_INC_AUTH_access($mModule, $mVars['post']); 
    
    // Save page vars   
    MELBIS()->GlobalAssign('PAGE', [
        'auth'      => $result,
        'user_id'   => $user_id,
        'mod'       => '/?mod='.strtolower($mModule)
        ]);
    
    // Auth form                         
    if ( isset($mVars['post']['form_auth']) ) 
    {      
        return json_encode(['result' => $result]);
    }                                           
    
    // Function switcher
    $func = $mVars['post']['func'] ?? 'default';       
    if ( function_exists($mModule.'_'.$func) )
    {
        if ( $func == 'default' )
        {
            return call_user_func($mModule.'_'.$func, $user_id, $mVars);     
        }                                                             
        else
        {
            if  ( !is_null($user_id) && $result == 'ACCEPT' )
            {
                return call_user_func($mModule.'_'.$func, $user_id, $mVars);            
            }
            else
            {
                return 'Access denied';
            }
        }            
    }  
    else 
    {
        return 'Function '.urlencode($func).' is absent';
    }   
} 


/** 
 * Function MELBIS_INC_AUTH_access    
 * Authorization user for web or application
 **/
function MELBIS_INC_AUTH_access($mModule, $mPost)
{ 
    // User module auth              
    $user = MELBIS()->SessionGetValue('melbis_user'); 
    if ( isset($user['id']) && isset($user['allow'][$mModule]) )
    {                                    
        return [$user['id'], 'ACCEPT'];
    }    
    else
    {     
        // User auth?        
        if ( isset($user['id']) )
        {
            // Test module access            
            if ( MELBIS_INC_AUTH_web_right($user['id'], $mModule) )
            {
                // Accept access, save it
                $user['allow'][$mModule] = true;
                MELBIS()->SessionSetValue('melbis_user', $user);
                                                                
                return [$user['id'], 'ACCEPT'];            
            }                   
            else
            {
                // Denied access for module     
                return [$user['id'], 'DENIED'];                                 
            }
        }
        else
        {                        
            // Not auth yet
            if ( isset($mPost['login']) )
            {                   
                // Try auth user           
                if ( isset($mPost['pass']) ) $mPost['pass_code'] = md5($mPost['pass']);
                $user['id'] = MELBIS_INC_AUTH_login($mPost['login'], $mPost['pass_code']);
                if ( is_null($user['id']) )
                {
                    // Wrong
                    return [null, 'WRONG'];
                }
                else
                {   
                    // Test module access
                    if ( MELBIS_INC_AUTH_web_right($user['id'], $mModule) )
                    {                                            
                        // Accept access                             
                        $user['allow'][$mModule] = true;
                        $result = 'ACCEPT';                                    
                    }                   
                    else
                    {
                        // Denied access for module
                        $result = 'DENIED';
                    }                                             
                    MELBIS()->SessionSetValue('melbis_user', $user);
                    return [$user['id'], $result];               
                }
            }
            else
            {
                return array(null, 'START');
            }
        }
    }                                            
}    


/** 
 * Function MELBIS_INC_AUTH_login
 * Authorization user by login and password
 **/
function MELBIS_INC_AUTH_login($mLogin, $mPass)
{ 
    $command = "SELECT id                                                                                                                                                                               
                  FROM {DBNICK}_user
                 WHERE login = :LOGIN     
                   AND pass_code = :PASS  
                   AND is_blocked = 0       
               ";
    $param = [
        'login' => $mLogin,
        'pass'  => $mPass
        ];               
    $user = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);
            
    return $user['id'] ?? null;
} 


/** 
 * Function MELBIS_INC_AUTH_command
 * Verify user access to command 
 **/   
function MELBIS_INC_AUTH_command($mUserId, $mCommand)
{
    if ( $mUserId != 1 )
    {
        $command = "SELECT or.id
                      FROM {DBNICK}_oper o
                      JOIN {DBNICK}_oper_right `or`
                        ON or.oper_id = o.id
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = or.group_id OR u.add_group_id = or.group_id )
                     WHERE o.command = :COMMAND
                       AND ( or.user_id = :USER_ID OR u.id = :USER_ID )
                   ";                                 
        $param = [
            'command'   => $mCommand,
            'user_id'   => $mUserId
            ];    
        $right = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);        

        return isset($right['id']);
    }
    else 
    {
        return true;
    }                      
}  


/** 
 * Function MELBIS_INC_AUTH_web_right 
 * Verify user rights for inside or outside module
 **/
function MELBIS_INC_AUTH_web_right($mUserId, $mModule)
{ 
    // User has super access? 
    if ( MELBIS_INC_AUTH_command($mUserId, 'PUT_OUTSIDE_RIGHT') )
    {
        return true;
    }        
    
    // User module access         
    if ( stripos($mModule, 'INSIDE') !== false ) 
    {        
        $command = "SELECT wir.id
                      FROM {DBNICK}_web_inside wi
                      JOIN {DBNICK}_web_inside_right wir
                        ON wir.inside_id = wi.id
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = wir.group_id OR u.add_group_id = wir.group_id )
                     WHERE wi.skey = :MODULE 
                       AND ( wir.user_id = :USER_ID OR u.id = :USER_ID OR wi.auth = 0 )
                   ";
    }
    else 
    {        
        $command = "SELECT wor.id
                      FROM {DBNICK}_web_outside wo
                      JOIN {DBNICK}_web_outside_right wor
                        ON wor.outside_id = wo.id
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = wor.group_id OR u.add_group_id = wor.group_id )
                     WHERE wo.skey = :MODULE 
                       AND ( wor.user_id = :USER_ID OR u.id = :USER_ID  OR wo.auth = 0 )
                   ";
    }
    $param = [
        'module'    => $mModule,
        'user_id'   => $mUserId
        ];                  
    $right = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    
    return isset($right['id']);
} 
    

/** 
 * Function MELBIS_INC_AUTH_web_key_prepare
 * Prepare user rights for web_key
 **/
function MELBIS_INC_AUTH_web_key_prepare($mUserId)
{ 
    // Clear    
    MELBIS()->SqlDelete(__LINE__, '{DBNICK}_tmp_web_key', 'user_id', $mUserId);
            
    if ( MELBIS_INC_AUTH_command($mUserId, 'PUT_OUTSIDE_RIGHT') )
    {
        // No Limits
        $command = "INSERT INTO {DBNICK}_tmp_web_key
                    SELECT id, :USER_ID, 1, 1, 1
                      FROM {DBNICK}_web_key
                   ";                                  
        $param = [ 
            'user_id'   => $mUserId 
            ];                   
        MELBIS()->SqlQuery(__LINE__, $command, $param); 
    }
    else 
    {       
        // Limit by rights                   
        $command = "INSERT INTO {DBNICK}_tmp_web_key 
                    SELECT wkr.web_key_id, :USER_ID, 
                           MAX(wkr.for_read), 
                           MAX(wkr.for_write), 
                           MAX(wkr.for_remove) 
                      FROM {DBNICK}_web_key_right wkr
                 LEFT JOIN {DBNICK}_user u
                        ON ( u.group_id = wkr.group_id OR u.add_group_id = wkr.group_id )
                     WHERE ( wkr.user_id = :USER_ID OR u.id = :USER_ID )                           
                  GROUP BY wkr.web_key_id
                   ";
        $param = [ 
            'user_id'   => $mUserId 
            ];                   
        MELBIS()->SqlQuery(__LINE__, $command, $param); 
    }

    return true;                          
}     



/** 
 * Function MELBIS_INC_AUTH_web_key_access    
 * Verify user access to web_key
 **/
function MELBIS_INC_AUTH_web_key_access($mUserId, $mKey, $mFor)
{ 
    switch ( $mFor ) 
    {
        case 'read':
            $action = 'twk.for_read = 1';
            break;    
        case 'write':
            $action = 'twk.for_write = 1';
            break;    
        case 'remove':
            $action = 'twk.for_remove = 1';
            break;
        default:
            $action = 'true';    
    }
           
    $command = "SELECT wk.id                         
                  FROM {DBNICK}_web_key wk
                  JOIN {DBNICK}_tmp_web_key twk
                    ON twk.id = wk.id
                   AND twk.user_id = :USER_ID
                   AND $action                                                                                                                  
                 WHERE wk.skey = :KEY                                                                       
               ";             
    $param = [
        'user_id'   => $mUserId,
        'key'       => $mKey
        ];                  
    $right = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    
    return isset($right['id']);
}
 

?>