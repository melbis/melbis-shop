<?php
/***************************************************************************************************
 * @version 6.5.1.420 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Router   - Signs in, out, or answers
 * Access   - Whether this person enters
 * WebRight - The right of a module
 *
 **************************************************************************************************/

namespace MELBIS_INC_AUTH;

/** 
 * Function Router
 **/
function Router($mModule, $mVars)
{ 
    // Logout?
    if ( isset($mVars['post']['logout']) )
    {        
        MELBIS()->SessionRemoveValue('melbis_user');
    }           
        
    // Auth and verify the access
    list($user_id, $result) = Access($mModule, $mVars['post']); 
    
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
    
    // Function switcher, or page
    $func = $mVars['post']['func'] ?? 'Page';       
    if ( !function_exists($mModule.'\\'.$func) && !function_exists($mModule.'_'.$func) )
    {
        return 'Function '.urlencode($func).' is absent';
    }
    
    if ( $func != 'Page' && ( is_null($user_id) || $result != 'ACCEPT' ) )
    {
        return 'Access denied';
    }
    
    // A name given as data
    return MELBIS()->UnitFunc($func, $user_id, $mVars);
} 


/** 
 * Function Access    
 **/
function Access($mModule, $mPost)
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
            if ( WebRight($user['id'], $mModule) )
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
                // Try auth, zero means no
                if ( isset($mPost['pass']) ) $mPost['pass_code'] = md5($mPost['pass']);
                $user_id = MELBIS()->SysUserLoginCheck($mPost['login'], $mPost['pass_code'] ?? '');
                if ( $user_id < 1 )
                {
                    // Wrong
                    return [null, 'WRONG'];
                }
                else
                {   
                    $user['id'] = $user_id;
                    
                    // Test module access
                    if ( WebRight($user['id'], $mModule) )
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
 * Function WebRight 
 **/
function WebRight($mUserId, $mModule)
{ 
    if ( stripos($mModule, 'INSIDE') !== false ) 
    {        
        return MELBIS()->SysWebInRight($mUserId, $mModule);
    }
    else
    {    
        return MELBIS()->SysWebOutRight($mUserId, $mModule);
    }
} 
 

?>