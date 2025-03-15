<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff     
 **************************************************************************************************
 * 
 * MELBIS_INC_TRANSLATE_service          - Return file path from time
 * MELBIS_INC_TRANSLATE_service             - Convert HTML to plain text      
 *     
 **************************************************************************************************/

/** 
 * Function MELBIS_INC_TRANSLATE_service
 **/
function MELBIS_INC_TRANSLATE_service($mUserId, $mVars)
{ 
    global $gParser;
                       
    $command = "SELECT * FROM {DBNICK}_lang WHERE is_origin = 1";               
    $from = $gParser->SqlSelectToArray(__LINE__, $command);    

    $command = "SELECT * FROM {DBNICK}_lang WHERE id = '$mVars[lang_id]'";               
    $to = $gParser->SqlSelectToArray(__LINE__, $command);   

    $html = ( $mVars['is_html'] > 0 );
    
    $result = MELBIS_INC_TRANSLATE_deepl($mVars['origin'], $mVars['context'], $from['skey'], $to['skey'], $html);

    return $result;
} 


/** 
 * Function MELBIS_INC_TRANSLATE_google
 **/
function MELBIS_INC_TRANSLATE_google($mText, $mContext, $mLangFrom, $mLangTo, $mHtml = false)
{    
    $data = array('q'           => '!'.$mText,
                  'target'      => $mLangTo,
                  'format'      => ( $mHtml ) ? 'html' : 'text',
                  'source'      => $mLangFrom,
                  'model'       => 'nmt',
                  'key'         => 'AIzaSyBaR5NstqckQ2N-p7yb-1GY5gjkEpnmmTg'
                  );
    $ch = curl_init();    
    curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2');
    //curl_setopt($ch, CURLOPT_REFERER, 'https://astroscope.com.ua');
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);                
    $response = curl_exec($ch);
    if ( $response === false ) 
    {
        $error = curl_error($ch);
        curl_close($ch);
        die('PHP_ERROR: '.$error);
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);    
    if ( $http_code >= 200 && $http_code < 300 ) 
    {
        $data = json_decode($response, true);
        $result = ltrim($data['data']['translations'][0]['translatedText'], '!');  

        return $result;
    } 
    else 
    {        
        $error = json_decode($response, true);
        die("PHP_ERROR: ".$http_code." - ".($error['message'] ?? 'Unkhown error'));
    }  
}     


/** 
 * Function MELBIS_INC_TRANSLATE_deepl
 **/
function MELBIS_INC_TRANSLATE_deepl($mText, $mContext, $mLangFrom, $mLangTo, $mHtml = false)
{     
    $data = [
        'text'          => [ $mText ],
        'context'       => $mContext,
        'tag_handling'  => ( $mHtml ) ? 'html' : '',
        'source_lang'   => $mLangFrom,
        'target_lang'   => $mLangTo
    ];
    
    // cURL
    //$ch = curl_init('https://api.deepl.com/v2/translate'); 
    $ch = curl_init('https://api-free.deepl.com/v2/translate');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: DeepL-Auth-Key 57098793-9d18-4c85-a3f8-c984e2699c38:fx',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ( $response === false ) 
    {
        $error = curl_error($ch);
        curl_close($ch);
        die('PHP_ERROR: '.$error);
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);    
    if ( $http_code >= 200 && $http_code < 300 ) 
    {
        $data = json_decode($response, true);
        $result = $data['translations'][0]['text'];    

        return $result;
    } 
    else 
    {        
        $error = json_decode($response, true);
        die("PHP_ERROR: ".$http_code." - ".($error['message'] ?? 'Unkhown error'));
    }                             
} 



?>