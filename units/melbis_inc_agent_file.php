<?php
/***************************************************************************************************
 * @version 6.5.1.470 @ 2026-09-25
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Home        - The files table of it
 * EntityOne   - Weighs one entity
 * EntityGone  - Refuses an element gone
 *
 * RightElem   - Weighs the element right
 *
 * FileAll     - Reads files of the elements
 * FileOne     - Reads one file row
 * FileDrop    - Deletes row and picture
 * Held        - The workspace held for it
 * Touched     - Marks the element changed
 *
 * DiskPath    - The path of a row
 * DiskFolder  - The folder of a file
 * DiskPicture - What a picture really is
 *
 * ProfileAll  - Reads the picture profiles
 * ProfileOne  - Reads one picture profile
 * ProfileShow - The recipe in agent words
 * ProfileXml  - The recipe into its body
 * BoxSize     - Megapixels in a shape
 *
 * MaskMap     - The masks with a picture
 * MaskWord    - The mask behind a path
 *
 * Make        - Derives a picture
 * MakePaint   - The recipe onto the picture
 * MakeMask    - The mask over the canvas
 * MakeInk     - The mask picture as ink
 * MakeLay     - One copy of the ink
 * MakeSkip    - What only the program paints
 *
 * ColorWord   - A canvas colour into #RRGGBB
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_FILE;


// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;

// The words of a type, by the number of the editor
const TYPE_WORD = ['jpeg', 'png', 'webp'];

// The largest picture the program paints
const MAX_PIXELS = 50000000;

// The side of the copy the frame is looked for on
const FRAME_SIDE = 1600;

// The words of a position
const MASK_POS = ['center', 'left-top', 'right-top', 'right-bottom', 'left-bottom', 'tile'];

// No mask, as the editor
const MASK_NONE = 'files/1899/12_30/00_00/';

// The working twins of the workspace
const FRAME = [
    'u_store'      => 'u_files_store',
    'u_info_value' => 'u_files_info_value'
    ];


/**
 * Function Home
 **/
function Home($mEntity)
{
    // The workspace keeps its twins
    return FRAME[$mEntity] ?? 'files_'.$mEntity;
}


/**
 * Function EntityOne
 **/
function EntityOne($mEntity)
{
    // An unknown word refused early
    $entity = trim((string)$mEntity);
    $common = MELBIS()->SysFileEntities();
    $frame = array_keys(FRAME);
    $all = array_merge($common, $frame);
    if ( in_array($entity, $all) ) return true;

    $list = implode(', ', $all);

    return [
        'result'  => false,
        'message' => 'No files on ['.$mEntity.']; these take: '.$list
        ];
}


/**
 * Function EntityGone
 **/
function EntityGone($mEntity, $mId)
{
    $where = [
        'store'       => 'the Browser tool answers the goods',
        'topic'       => 'the Catalog tool answers them',
        'info'        => 'the Attributes tool answers them',
        'info_value'  => 'the Attributes tool answers them',
        'brand'       => 'the Brands tool answers them',
        'key_value'   => 'the Basic settings tool answers them',
        'advert_text' => 'the Promo blocks tool answers them',
        'u_store'      => 'the tool of the personal workspace answers them',
        'u_info_value' => 'the tool of the personal workspace answers them'
        ];
    $said = $where[$mEntity] ?? 'its own tool answers them';

    return [
        'result'  => false,
        'message' => 'No '.$mEntity.' ['.$mId.'] in the store - '.$said
        ];
}


/**
 * Function RightElem
 **/
function RightElem($mUserId, $mEntity, $mElemId)
{
    $elem_id = (int)$mElemId;

    // The person's own workspace
    if ( isset(FRAME[$mEntity]) )
    {
        $command = "SELECT *
                      FROM {DBNICK}_$mEntity
                     WHERE user_id = :USER_ID
                       AND id = :ID
                   ";
        $param_own = [
            'user_id' => $mUserId,
            'id'      => $elem_id
            ];
        $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_own);
        if ( !isset($row['id']) ) return EntityGone($mEntity, $elem_id);
        if ( $mEntity == 'u_store' ) return true;

        // A value asks its characteristic
        $allow = SYS\RightTable('info', $mUserId, 'value');

        $command = "SELECT id
                      FROM $allow
                     WHERE id = :ID
                   ";
        $param_info = [
            'id' => $row['info_id']
            ];
        $may = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_info);
        if ( $may > 0 ) return true;

        return [
            'result'  => false,
            'message' => 'The files of ['.$elem_id.'] are not yours'
            ];
    }

    // The Description right of section
    if ( $mEntity == 'store' )
    {
        $command = "SELECT id
                      FROM {DBNICK}_store
                     WHERE id = :ID
                   ";
        $param_elem = [
            'id' => $elem_id
            ];
        $found = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
        if ( $found == 0 ) return EntityGone($mEntity, $elem_id);

        $allow = SYS\RightTable('topic', $mUserId, 'descr');

        $command = "SELECT ts.store_id
                      FROM {DBNICK}_topic_store ts
                      JOIN $allow at
                        ON at.id = ts.topic_id
                     WHERE ts.store_id = :ID
                   ";
        $may = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
        if ( $may > 0 ) return true;

        return [
            'result'  => false,
            'message' => 'The files of ['.$elem_id.'] are not yours'
            ];
    }

    // The Description right on it
    if ( $mEntity == 'topic' )
    {
        $command = "SELECT id
                      FROM {DBNICK}_topic
                     WHERE id = :ID
                   ";
        $param_elem = [
            'id' => $elem_id
            ];
        $found = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
        if ( $found == 0 ) return EntityGone($mEntity, $elem_id);

        $allow = SYS\RightTable('topic', $mUserId, 'descr');

        $command = "SELECT id
                      FROM $allow
                     WHERE id = :ID
                   ";
        $may = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
        if ( $may > 0 ) return true;

        return [
            'result'  => false,
            'message' => 'The files of ['.$elem_id.'] are not yours'
            ];
    }

    // The right of the characteristic
    if ( $mEntity == 'info' || $mEntity == 'info_value' )
    {
        $info_id = $elem_id;
        $place = 'info';

        if ( $mEntity == 'info_value' )
        {
            $command = "SELECT info_id
                          FROM {DBNICK}_info_value
                         WHERE id = :ID
                       ";
            $param_value = [
                'id' => $elem_id
                ];
            $info_id = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_value);
            if ( $info_id == 0 ) return EntityGone($mEntity, $elem_id);

            $place = 'value';
        }

        $allow = SYS\RightTable('info', $mUserId, $place);

        $command = "SELECT id
                      FROM $allow
                     WHERE id = :ID
                   ";
        $param_info = [
            'id' => $info_id
            ];
        $may = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_info);
        if ( $may > 0 ) return true;

        if ( $mEntity == 'info' )
        {
            $command = "SELECT id
                          FROM {DBNICK}_info
                         WHERE id = :ID
                       ";
            $found = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_info);
            if ( $found == 0 ) return EntityGone($mEntity, $elem_id);
        }

        return [
            'result'  => false,
            'message' => 'The files of ['.$elem_id.'] are not yours'
            ];
    }

    // The operation is the gate
    $command = "SELECT id
                  FROM {DBNICK}_$mEntity
                 WHERE id = :ID
               ";
    $param_elem = [
        'id' => $elem_id
        ];
    $found = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
    if ( $found == 0 ) return EntityGone($mEntity, $elem_id);

    return true;
}


/**
 * Function FileAll
 **/
function FileAll($mEntity, $mIds, $mUserId = 0)
{
    // The order the program keeps
    $list = implode(',', $mIds);
    if ( $list == '' ) return [];

    $table = Home($mEntity);
    $mine = '';
    if ( isset(FRAME[$mEntity]) ) $mine = 'AND user_id = '.(int)$mUserId;

    $command = "SELECT *
                  FROM {DBNICK}_$table
                 WHERE elem_id IN ( $list )
                       $mine
              ORDER BY elem_id, pos
               ";

    return MELBIS()->SqlSelect(__LINE__, $command);
}


/**
 * Function FileOne
 **/
function FileOne($mEntity, $mId, $mUserId = 0)
{
    $table = Home($mEntity);
    $mine = '';
    if ( isset(FRAME[$mEntity]) ) $mine = 'AND user_id = '.(int)$mUserId;

    $command = "SELECT *
                  FROM {DBNICK}_$table
                 WHERE id = :ID
                       $mine
               ";
    $param_file = [
        'id' => (int)$mId
        ];

    return MELBIS()->SqlSelectFlat(__LINE__, $command, $param_file);
}


/**
 * Function FileDrop
 **/
function FileDrop($mEntity, $mId, $mDisk, $mUserId = 0)
{
    $table = Home($mEntity);
    $mine = '';
    if ( isset(FRAME[$mEntity]) ) $mine = 'AND user_id = '.(int)$mUserId;

    // Born here, gone with it
    $command = "DELETE
                  FROM {DBNICK}_$table
                 WHERE id = :ID
                       $mine
               ";
    $param_file = [
        'id' => (int)$mId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param_file);
    MELBIS()->SqlTableChange(__LINE__, '{DBNICK}_'.$table, true);

    if ( $mDisk != '' && file_exists($mDisk) ) @unlink($mDisk);
}


/**
 * Function Held
 **/
function Held($mUserId, $mEntity)
{
    if ( !isset(FRAME[$mEntity]) ) return true;

    // The workspace's tool holds it
    $table = Home($mEntity);
    $tables = ['{DBNICK}_'.$table];
    $held = MELBIS()->SqlTableHeld(__LINE__, $tables, $mUserId);
    if ( $held ) return true;

    return [
        'result'  => false,
        'message' => 'The working table ['.$table.'] is not held by you - the tool of your personal workspace takes it first'
        ];
}


/**
 * Function Touched
 **/
function Touched($mUserId, $mEntity, $mElemIds)
{
    if ( !isset(FRAME[$mEntity]) ) return;
    if ( count($mElemIds) == 0 ) return;

    // Marked as the window marks
    $ids = array_map('intval', $mElemIds);
    $list = implode(',', $ids);
    $command = "UPDATE {DBNICK}_$mEntity
                   SET was_update = 1
                 WHERE user_id = :USER_ID
                   AND id IN ( $list )
               ";
    $param_user = [
        'user_id' => $mUserId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);
}


/**
 * Function DiskPath
 **/
function DiskPath($mRow)
{
    // The formula both sides use
    return __DIR__.'/..'.DiskFolder($mRow['upload_time']).$mRow['file_name'];
}


/**
 * Function DiskFolder
 **/
function DiskFolder($mUploadTime)
{
    // The day and hour, foldered
    list( $date, $time ) = explode(' ', $mUploadTime);
    list( $y, $m, $d ) = explode('-', $date);
    list( $h, $n, $s ) = explode(':', $time);

    return '/files/'.$y.'/'.$m.'_'.$d.'/'.$h.'_'.$n.'/';
}


/**
 * Function DiskPicture
 **/
function DiskPicture($mDisk)
{
    // What the file really is
    $what = [
        'type'   => '',
        'width'  => 0,
        'height' => 0
        ];

    if ( !file_exists($mDisk) ) return $what;

    $size = @getimagesize($mDisk);
    if ( $size === false ) return $what;

    $names = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp'
        ];

    $what['type'] = $names[$size[2]] ?? '';
    $what['width'] = (int)$size[0];
    $what['height'] = (int)$size[1];

    return $what;
}


/**
 * Function ProfileAll
 **/
function ProfileAll()
{
    $command = "SELECT id, key_name, value_txt, sys_key, pos
                  FROM {DBNICK}_key_value
                 WHERE key_code = 'FILES_PROFILE'
              ORDER BY pos
               ";

    return MELBIS()->SqlSelect(__LINE__, $command);
}


/**
 * Function ProfileOne
 **/
function ProfileOne($mName)
{
    $command = "SELECT id, key_name, value_txt, sys_key, pos
                  FROM {DBNICK}_key_value
                 WHERE key_code = 'FILES_PROFILE'
                   AND BINARY key_name = :KEY_NAME
               ";
    $param_one = [
        'key_name' => $mName
        ];

    return MELBIS()->SqlSelectFlat(__LINE__, $command, $param_one);
}


/**
 * Function ProfileShow
 **/
function ProfileShow($mRow, $mRaw = false)
{
    // The XML into words
    $show = [
        'name'   => $mRow['key_name'],
        'system' => ( $mRow['sys_key'] > 0 )
        ];

    $xml = @simplexml_load_string((string)$mRow['value_txt']);
    if ( $xml === false || !isset($xml->JPEG) )
    {
        $show['broken'] = 'the recipe is not readable - the program\'s editor owns this row';
        return $show;
    }

    $pos = (int)( $xml->MASK['Pos'] ?? 0 );
    if ( !isset(MASK_POS[$pos]) ) $pos = 0;

    $type = (int)( $xml->JPEG['FileType'] ?? 0 );
    if ( !isset(TYPE_WORD[$type]) ) $type = 0;

    // A size wins, the megapixels beside it are not read
    $sized = ( isset($xml->JPEG['Width']) && isset($xml->JPEG['Hight']) );

    $show['frame_auto']   = ( (int)( $xml->FRAME['Kind'] ?? 0 ) == 2 );
    $show['frame_range']  = (int)( $xml->FRAME['Range'] ?? 255 );
    $show['frame_border'] = (int)( $xml->FRAME['Border'] ?? 0 );
    $show['type']         = TYPE_WORD[$type];
    $show['quality']      = (int)$xml->JPEG['Compress'];
    $show['width']        = ( $sized ) ? (int)$xml->JPEG['Width'] : null;
    $show['height']       = ( $sized ) ? (int)$xml->JPEG['Hight'] : null;
    $show['resolution']   = ( !$sized && isset($xml->JPEG['Resolution']) ) ? (float)$xml->JPEG['Resolution'] : null;
    $show['smart']        = ( (string)$xml->JPEG['Smart'] == 'True' );
    $show['size_base']    = ( (string)( $xml->JPEG['Base'] ?? '' ) == 'True' );
    $show['size_optim']   = ( (string)( $xml->JPEG['Optim'] ?? '' ) == 'True' );
    $show['group']        = (string)( $xml->FILE['KindKey'] ?? 'kDefault' );
    $show['group_base']   = ( (string)( $xml->FILE['Base'] ?? '' ) == 'True' );
    $show['range']        = (int)( $xml->CANVAS['Range'] ?? 255 );
    $show['range_border'] = (int)( $xml->CANVAS['RangeBorder'] ?? 10 );
    $show['border']       = (int)( $xml->CANVAS['Border'] ?? 0 );
    $show['background']   = ColorWord((int)( $xml->CANVAS['Color'] ?? 16777215 ));
    $show['canvas_alpha'] = ( (string)( $xml->CANVAS['Alpha'] ?? '' ) == 'True' );
    $show['rotate']       = (int)( $xml->ROTATE['Rotate'] ?? 0 );
    $show['mirror']       = ( (string)( $xml->ROTATE['Mirror'] ?? '' ) == 'True' );
    $show['mask']         = MaskWord((string)( $xml->MASK['File'] ?? '' ));
    $show['mask_pos']     = MASK_POS[$pos];
    $show['mask_alpha']   = (int)( $xml->MASK['Alpha'] ?? 0 );
    $show['mask_size_w']  = (int)( $xml->MASK['SizeW'] ?? 0 );
    $show['mask_size_h']  = (int)( $xml->MASK['SizeH'] ?? 0 );
    $show['mask_indent']  = (int)( $xml->MASK['Indent'] ?? 0 );
    $show['mask_rotate']  = (int)( $xml->MASK['Rotate'] ?? 0 );
    $show['red']          = (int)( $xml->EFFECTS['Red'] ?? 0 );
    $show['green']        = (int)( $xml->EFFECTS['Green'] ?? 0 );
    $show['blue']         = (int)( $xml->EFFECTS['Blue'] ?? 0 );
    $show['intensive']    = (int)( $xml->EFFECTS['Intensive'] ?? 0 );
    $show['contrast']     = (int)( $xml->EFFECTS['Contrast'] ?? 0 );
    $show['sharpen']      = (int)( $xml->EFFECTS['Sharpen'] ?? 0 );

    // The raw path, for update
    if ( $mRaw ) $show['mask_file'] = (string)( $xml->MASK['File'] ?? '' );

    return $show;
}


/**
 * Function ProfileXml
 **/
function ProfileXml($mSet)
{
    // The XML the editor writes
    $stamp = 'Melbis Shop v'.MELBIS_SCRIPT_VERSION.'.'.MELBIS_SCRIPT_BUILD;
    $word = function($mText) { return htmlspecialchars((string)$mText, ENT_QUOTES); };
    $flag = function($mValue) { return ( $mValue ) ? 'True' : 'False'; };

    // The size, or the megapixels of a picture of its own shape
    $size = ' Resolution="'.number_format((float)( $mSet['resolution'] ?? 0 ), 2, '.', '').'"';
    if ( isset($mSet['width']) && isset($mSet['height']) )
    {
        $size = ' Width="'.$mSet['width'].'" Hight="'.$mSet['height'].'"';
    }

    return '<MELBISSHOP ShopVersion="'.$word($stamp).'">'.
           '<FRAME Kind="'.( ( $mSet['frame_auto'] ) ? 2 : 0 ).'"'.
                ' Range="'.$mSet['frame_range'].'"'.
                ' Border="'.$mSet['frame_border'].'"/>'.
           '<JPEG FileType="'.array_search($mSet['type'], TYPE_WORD).'"'.
                ' Compress="'.$mSet['quality'].'"'.
                $size.
                ' Smart="'.$flag($mSet['smart']).'"'.
                ' Base="'.$flag($mSet['size_base']).'"'.
                ' Optim="'.$flag($mSet['size_optim']).'"/>'.
           '<FILE KindKey="'.$word($mSet['group']).'"'.
                ' Base="'.$flag($mSet['group_base']).'"/>'.
           '<MASK File="'.$word(( $mSet['mask_file'] == '' ) ? MASK_NONE : $mSet['mask_file']).'"'.
                ' Pos="'.array_search($mSet['mask_pos'], MASK_POS).'"'.
                ' Alpha="'.$mSet['mask_alpha'].'"'.
                ' SizeW="'.$mSet['mask_size_w'].'"'.
                ' SizeH="'.$mSet['mask_size_h'].'"'.
                ' Indent="'.$mSet['mask_indent'].'"'.
                ' Rotate="'.$mSet['mask_rotate'].'"/>'.
           '<CANVAS Range="'.$mSet['range'].'"'.
                ' RangeBorder="'.$mSet['range_border'].'"'.
                ' Border="'.$mSet['border'].'"'.
                ' Color="'.ColorWord($mSet['background'], true).'"'.
                ' Alpha="'.$flag($mSet['canvas_alpha']).'"/>'.
           '<ROTATE Rotate="'.$mSet['rotate'].'"'.
                ' Mirror="'.$flag($mSet['mirror']).'"/>'.
           '<EFFECTS Red="'.$mSet['red'].'"'.
                ' Green="'.$mSet['green'].'"'.
                ' Blue="'.$mSet['blue'].'"'.
                ' Intensive="'.$mSet['intensive'].'"'.
                ' Contrast="'.$mSet['contrast'].'"'.
                ' Sharpen="'.$mSet['sharpen'].'"/>'.
           '</MELBISSHOP>';
}


/**
 * Function BoxSize
 **/
function BoxSize($mResolution, $mRatio)
{
    // Megapixels spread over a shape, the width leads
    $pixels = $mResolution * 1000000;
    $width = (int)round(sqrt($pixels * $mRatio));
    $height = (int)round($width / $mRatio);

    return [$width, $height];
}


/**
 * Function MaskMap
 **/
function MaskMap()
{
    // The name beside the path
    $command = "SELECT kv.key_name, f.file_name, f.upload_time
                  FROM {DBNICK}_key_value kv
                  JOIN {DBNICK}_files_key_value f
                    ON f.elem_id = kv.id
                 WHERE kv.key_code = 'FILES_MASK'
              ORDER BY kv.pos, f.pos
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $masks = [];
    foreach ( $rows as $row )
    {
        if ( isset($masks[$row['key_name']]) ) continue;

        list( $date, $time ) = explode(' ', $row['upload_time'].' ');
        $day = explode('-', $date);
        $clock = explode(':', $time.'::');
        $path = 'files/'.$day[0].'/'.$day[1].'_'.$day[2].'/'.$clock[0].'_'.$clock[1].'/'.$row['file_name'];

        $masks[$row['key_name']] = [
            'name' => $row['key_name'],
            'path' => $path
            ];
    }

    return array_values($masks);
}


/**
 * Function MaskWord
 **/
function MaskWord($mPath)
{
    // Zero folder means no mask
    if ( $mPath == '' || $mPath == MASK_NONE || substr($mPath, -1) == '/' ) return '';

    foreach ( MaskMap() as $mask )
    {
        if ( $mask['path'] == $mPath ) return $mask['name'];
    }

    return $mPath;
}


/**
 * Function Make
 **/
function Make($mUserId, $mEntity, $mWas, $mProfile, $mShow, $mRealName = '')
{
    // The picture alone
    if ( !function_exists('imagecreatetruecolor') )
    {
        return [
            'result'  => false,
            'message' => 'This PHP carries no GD'
            ];
    }

    $disk = DiskPath($mWas);
    $what = DiskPicture($disk);
    if ( $what['type'] == '' )
    {
        return [
            'result'  => false,
            'message' => 'The file ['.$mWas['real_name'].'] is no picture'
            ];
    }

    $paint = MakePaint($what, $disk, $mShow);
    if ( !$paint['result'] ) return $paint;

    // Laid by the engine formula
    $table = Home($mEntity);
    $now = MELBIS()->DateTime();
    $folder = DiskFolder($now);
    $dir = __DIR__.'/..'.$folder;
    if ( !is_dir($dir) && !@mkdir($dir, 0777, true) )
    {
        imagedestroy($paint['image']);

        return [
            'result'  => false,
            'message' => 'The folder could not be made'
            ];
    }

    $id = MELBIS()->SqlGenId($table, $mUserId);
    $ext = ( $mShow['type'] == 'jpeg' ) ? 'jpg' : $mShow['type'];
    $file_name = strtolower($table.'_'.$mUserId.'_'.$id).'.'.$ext;

    // File first, row second
    $laid = false;
    if ( $mShow['type'] == 'jpeg' ) $laid = imagejpeg($paint['image'], $dir.$file_name, $mShow['quality']);
    if ( $mShow['type'] == 'png' ) $laid = imagepng($paint['image'], $dir.$file_name);
    if ( $mShow['type'] == 'webp' ) $laid = imagewebp($paint['image'], $dir.$file_name, $mShow['quality']);
    imagedestroy($paint['image']);

    if ( !$laid )
    {
        return [
            'result'  => false,
            'message' => 'The picture could not be written'
            ];
    }

    // The new file stands last, and may keep the group it already has
    $kind = ( $mShow['group_base'] ) ? $mWas['kind_key'] : $mShow['group'];
    $real_name = trim((string)$mRealName);
    if ( $real_name == '' )
    {
        // Only the file's own extension goes
        $stem = $mWas['real_name'];
        $own = pathinfo($mWas['file_name'], PATHINFO_EXTENSION);
        $tail = '.'.$own;
        $size = strlen($tail);
        $end = substr($stem, -$size);
        if ( $own != '' && strcasecmp($end, $tail) == 0 ) $stem = substr($stem, 0, -$size);
        $real_name = $stem.' ('.$mProfile.').'.$ext;
    }

    $fields = [
        'id'          => $id,
        'elem_id'     => $mWas['elem_id'],
        'kind_key'    => $kind,
        'file_name'   => $file_name,
        'file_size'   => filesize($dir.$file_name),
        'upload_time' => $now,
        'upload_ok'   => 1,
        'real_name'   => $real_name,
        'format_xml'  => '',
        'pos'         => $id
        ];

    // A working row is one person's
    if ( isset(FRAME[$mEntity]) ) $fields['user_id'] = $mUserId;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_'.$table, $fields);

    $message = 'Made ['.$real_name.'] - '.$paint['width'].'x'.$paint['height'].' by ['.$mProfile.']';
    $skipped = MakeSkip($mShow);
    if ( $skipped != '' ) $message .= '. Left to the program, it alone paints: '.$skipped;

    return [
        'result'  => true,
        'id'      => $id,
        'name'    => $real_name,
        'message' => $message
        ];
}


/**
 * Function MakePaint
 **/
function MakePaint($mWhat, $mDisk, $mShow)
{
    // Opened by its type
    $doors = [
        'jpg'  => 'imagecreatefromjpeg',
        'png'  => 'imagecreatefrompng',
        'gif'  => 'imagecreatefromgif',
        'webp' => 'imagecreatefromwebp'
        ];
    $open = $doors[$mWhat['type']];
    $source = @$open($mDisk);
    if ( $source === false )
    {
        return [
            'result'  => false,
            'message' => 'The picture could not be opened'
            ];
    }

    $hex = substr($mShow['background'], 1);
    $red = hexdec(substr($hex, 0, 2));
    $green = hexdec(substr($hex, 2, 2));
    $blue = hexdec(substr($hex, 4, 2));

    // The frame the profile finds, before the picture is turned
    if ( $mShow['frame_auto'] ) $source = MakeFrame($source, $red, $green, $blue, $mShow);

    // Mirror and turn, then fit
    if ( $mShow['mirror'] ) imageflip($source, IMG_FLIP_HORIZONTAL);
    if ( $mShow['rotate'] != 0 )
    {
        $turned = imagerotate($source, -1 * $mShow['rotate'], ( $red << 16 ) + ( $green << 8 ) + $blue);
        imagedestroy($source);
        $source = $turned;
    }

    // The area itself, the size of the profile, or its megapixels in the shape of the area
    $source_w = imagesx($source);
    $source_h = imagesy($source);
    if ( $mShow['size_base'] )
    {
        $box_w = $source_w;
        $box_h = $source_h;
    }
    elseif ( $mShow['width'] !== null )
    {
        $box_w = $mShow['width'];
        $box_h = $mShow['height'];
    }
    else
    {
        list( $box_w, $box_h ) = BoxSize($mShow['resolution'], $source_w / $source_h);
    }
    $box_w = max(2, $box_w);
    $box_h = max(2, $box_h);

    // Beyond the limit nothing is painted
    if ( $box_w * $box_h > MAX_PIXELS )
    {
        imagedestroy($source);

        return [
            'result'  => false,
            'message' => 'The picture is too large: '.$box_w.'x'.$box_h.' is over '.( MAX_PIXELS / 1000000 ).' megapixels'
            ];
    }

    // Nothing smaller than asked is blown up
    $small = ( $source_w < $box_w && $source_h < $box_h );
    if ( $mShow['size_optim'] && $small )
    {
        $box_w = $source_w;
        $box_h = $source_h;
    }

    // The canvas takes the shape of the area
    if ( $mShow['smart'] )
    {
        $by_w = $box_w / $source_w;
        $by_h = $box_h / $source_h;
        if ( $by_w < $by_h ) $box_h = (int)round($source_h * $by_w);
        if ( $by_w >= $by_h ) $box_w = (int)round($source_w * $by_h);
    }

    // Fitted into the canvas, then two margins off the long side
    $scale = max($source_w / $box_w, $source_h / $box_h);
    $fit_w = (int)round($source_w / $scale);
    $fit_h = (int)round($source_h / $scale);
    $border = $mShow['border'];
    if ( $fit_w >= $fit_h )
    {
        $shape = $fit_h / $fit_w;
        $fit_w = $fit_w - 2 * $border;
        $fit_h = (int)round($fit_w * $shape);
    }
    else
    {
        $shape = $fit_w / $fit_h;
        $fit_h = $fit_h - 2 * $border;
        $fit_w = (int)round($fit_h * $shape);
    }
    $fit_w = max(1, $fit_w);
    $fit_h = max(1, $fit_h);
    $canvas_w = $box_w;
    $canvas_h = $box_h;

    // Alpha is for png and webp alone
    $alpha = ( $mShow['canvas_alpha'] && $mShow['type'] != 'jpeg' );

    $canvas = imagecreatetruecolor($canvas_w, $canvas_h);
    if ( $alpha )
    {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $back = imagecolorallocatealpha($canvas, $red, $green, $blue, 127);
        imagefilledrectangle($canvas, 0, 0, $canvas_w - 1, $canvas_h - 1, $back);
    }
    else
    {
        $back = imagecolorallocate($canvas, $red, $green, $blue);
        imagefill($canvas, 0, 0, $back);
    }

    $x = (int)round(( $canvas_w - $fit_w ) / 2);
    $y = (int)round(( $canvas_h - $fit_h ) / 2);
    imagecopyresampled($canvas, $source, $x, $y, 0, 0, $fit_w, $fit_h, $source_w, $source_h);
    imagedestroy($source);

    // The effects GD can paint
    if ( $mShow['red'] != 0 || $mShow['green'] != 0 || $mShow['blue'] != 0 )
    {
        imagefilter($canvas, IMG_FILTER_COLORIZE, $mShow['red'], $mShow['green'], $mShow['blue']);
    }
    if ( $mShow['intensive'] != 0 ) imagefilter($canvas, IMG_FILTER_BRIGHTNESS, $mShow['intensive']);
    if ( $mShow['contrast'] != 0 ) imagefilter($canvas, IMG_FILTER_CONTRAST, -1 * $mShow['contrast']);
    if ( $mShow['sharpen'] > 0 )
    {
        $amount = min(1, $mShow['sharpen'] / 250);
        $matrix = [
            [0, -1 * $amount, 0],
            [-1 * $amount, 1 + 4 * $amount, -1 * $amount],
            [0, -1 * $amount, 0]
            ];
        imageconvolution($canvas, $matrix, 1, 0);
    }

    // The mask goes last
    if ( $mShow['mask_file'] != '' && $mShow['mask_alpha'] > 0 )
    {
        MakeMask($canvas, $canvas_w, $canvas_h, $mShow);
    }

    return [
        'result' => true,
        'image'  => $canvas,
        'width'  => $canvas_w,
        'height' => $canvas_h
        ];
}


/**
 * Function MakeFrame
 **/
function MakeFrame($mImage, $mRed, $mGreen, $mBlue, $mShow)
{
    $wide = imagesx($mImage);
    $high = imagesy($mImage);

    // The edges are read off a smaller copy
    $scale = max($wide, $high) / FRAME_SIDE;
    if ( $scale < 1 ) $scale = 1;
    $scan_w = (int)round($wide / $scale);
    $scan_h = (int)round($high / $scale);

    // A transparent place takes the background
    $scan = imagecreatetruecolor($scan_w, $scan_h);
    $back = imagecolorallocate($scan, $mRed, $mGreen, $mBlue);
    imagefilledrectangle($scan, 0, 0, $scan_w - 1, $scan_h - 1, $back);
    imagecopyresampled($scan, $mImage, 0, 0, 0, 0, $scan_w, $scan_h, $wide, $high);

    // A light tone and the background itself are no picture
    $level = $mShow['frame_range'];
    $left = $scan_w;
    $top = $scan_h;
    $right = -1;
    $bottom = -1;
    for ( $y = 0; $y < $scan_h; $y++ )
    {
        for ( $x = 0; $x < $scan_w; $x++ )
        {
            $color = imagecolorat($scan, $x, $y);
            $red = ( $color >> 16 ) & 255;
            $green = ( $color >> 8 ) & 255;
            $blue = $color & 255;

            if ( min($red, $green, $blue) > $level ) continue;
            if ( $red == $mRed && $green == $mGreen && $blue == $mBlue ) continue;

            if ( $x < $left ) $left = $x;
            if ( $x > $right ) $right = $x;
            if ( $y < $top ) $top = $y;
            if ( $y > $bottom ) $bottom = $y;
        }
    }
    imagedestroy($scan);

    // All background keeps the whole picture
    if ( $right < $left )
    {
        $left = 0;
        $top = 0;
        $right = $scan_w - 1;
        $bottom = $scan_h - 1;
    }

    // The indent stays inside the picture
    $pad = (int)round(min($scan_w, $scan_h) * $mShow['frame_border'] / 100);
    $left = max(0, $left - $pad);
    $top = max(0, $top - $pad);
    $right = min($scan_w - 1, $right + $pad);
    $bottom = min($scan_h - 1, $bottom + $pad);

    $at_x = (int)round($left * $scale);
    $at_y = (int)round($top * $scale);
    $cut_w = min($wide - $at_x, (int)round(( $right + 1 ) * $scale) - $at_x);
    $cut_h = min($high - $at_y, (int)round(( $bottom + 1 ) * $scale) - $at_y);
    if ( $cut_w == $wide && $cut_h == $high ) return $mImage;

    // The cut carries what the source had, alpha included
    $cut = imagecreatetruecolor($cut_w, $cut_h);
    imagealphablending($cut, false);
    imagesavealpha($cut, true);
    imagecopy($cut, $mImage, 0, 0, $at_x, $at_y, $cut_w, $cut_h);
    imagedestroy($mImage);

    return $cut;
}


/**
 * Function MakeMask
 **/
function MakeMask($mCanvas, $mCanvasW, $mCanvasH, $mShow)
{
    // The mask as ink with its own alpha
    $disk = __DIR__.'/../'.$mShow['mask_file'];
    $ink = MakeInk($disk);
    if ( $ink === false ) return;

    // Turned first, as the canvas turns
    if ( $mShow['mask_rotate'] != 0 )
    {
        $angle = -1 * $mShow['mask_rotate'];
        $clear = imagecolorallocatealpha($ink, 0, 0, 0, 127);
        $turned = imagerotate($ink, $angle, $clear);
        imagedestroy($ink);
        $ink = $turned;
        imagealphablending($ink, false);
        imagesavealpha($ink, true);
    }

    // Fitted into its frame of the picture, a zero side sets no limit
    $ink_w = imagesx($ink);
    $ink_h = imagesy($ink);
    $scale = 0;
    if ( $mShow['mask_size_w'] > 0 ) $scale = $mCanvasW * $mShow['mask_size_w'] / 100 / $ink_w;
    if ( $mShow['mask_size_h'] > 0 )
    {
        $limit = $mCanvasH * $mShow['mask_size_h'] / 100 / $ink_h;
        if ( $scale == 0 || $limit < $scale ) $scale = $limit;
    }
    if ( $scale > 0 )
    {
        $fit_w = max(1, (int)round($ink_w * $scale));
        $fit_h = max(1, (int)round($ink_h * $scale));
        $fit = imagecreatetruecolor($fit_w, $fit_h);
        imagealphablending($fit, false);
        imagesavealpha($fit, true);
        imagecopyresampled($fit, $ink, 0, 0, 0, 0, $fit_w, $fit_h, $ink_w, $ink_h);
        imagedestroy($ink);
        $ink = $fit;
        $ink_w = $fit_w;
        $ink_h = $fit_h;
    }

    // The indent follows the shorter side
    $pad = (int)round(min($mCanvasW, $mCanvasH) * $mShow['mask_indent'] / 100);
    $solid = $mShow['mask_alpha'] / 255;

    if ( $mShow['mask_pos'] == 'tile' )
    {
        // An odd grid keeps one copy in the very centre
        $cell_w = $ink_w + 2 * $pad;
        $cell_h = $ink_h + 2 * $pad;
        $cols = intdiv($mCanvasW, $cell_w) + 1;
        if ( $cols % 2 == 0 ) $cols++;
        $rows = intdiv($mCanvasH, $cell_h) + 1;
        if ( $rows % 2 == 0 ) $rows++;
        $from_x = intdiv($mCanvasW - $cols * $cell_w, 2) + $pad;
        $from_y = intdiv($mCanvasH - $rows * $cell_h, 2) + $pad;

        for ( $row = 0; $row < $rows; $row++ )
        {
            for ( $col = 0; $col < $cols; $col++ )
            {
                $at_x = $from_x + $col * $cell_w;
                $at_y = $from_y + $row * $cell_h;
                MakeLay($mCanvas, $mCanvasW, $mCanvasH, $ink, $at_x, $at_y, $solid);
            }
        }
    }
    else
    {
        // The centre takes no indent, the corners do
        $spots = [
            'center'       => [(int)round(( $mCanvasW - $ink_w ) / 2), (int)round(( $mCanvasH - $ink_h ) / 2)],
            'left-top'     => [$pad, $pad],
            'right-top'    => [$mCanvasW - $ink_w - $pad, $pad],
            'right-bottom' => [$mCanvasW - $ink_w - $pad, $mCanvasH - $ink_h - $pad],
            'left-bottom'  => [$pad, $mCanvasH - $ink_h - $pad]
            ];
        list( $at_x, $at_y ) = $spots[$mShow['mask_pos']] ?? $spots['center'];
        MakeLay($mCanvas, $mCanvasW, $mCanvasH, $ink, $at_x, $at_y, $solid);
    }

    imagedestroy($ink);
}


/**
 * Function MakeInk
 **/
function MakeInk($mDisk)
{
    // Opened by its type
    $what = DiskPicture($mDisk);
    if ( $what['type'] == '' ) return false;

    $doors = [
        'jpg'  => 'imagecreatefromjpeg',
        'png'  => 'imagecreatefrompng',
        'gif'  => 'imagecreatefromgif',
        'webp' => 'imagecreatefromwebp'
        ];
    $open = $doors[$what['type']];
    $ink = @$open($mDisk);
    if ( $ink === false ) return false;

    // A palette hands its transparent colour over as alpha
    if ( !imageistruecolor($ink) ) imagepalettetotruecolor($ink);
    imagealphablending($ink, false);
    imagesavealpha($ink, true);

    // A mask with a transparency of its own lays by it
    $ink_w = imagesx($ink);
    $ink_h = imagesy($ink);
    for ( $y = 0; $y < $ink_h; $y++ )
    {
        for ( $x = 0; $x < $ink_w; $x++ )
        {
            $dot = imagecolorat($ink, $x, $y);
            if ( ( $dot >> 24 ) & 0x7F ) return $ink;
        }
    }

    // One without any takes pure white as its holes
    $hole = imagecolorallocatealpha($ink, 255, 255, 255, 127);
    for ( $y = 0; $y < $ink_h; $y++ )
    {
        for ( $x = 0; $x < $ink_w; $x++ )
        {
            $dot = imagecolorat($ink, $x, $y);
            if ( ( $dot & 0xFFFFFF ) == 0xFFFFFF ) imagesetpixel($ink, $x, $y, $hole);
        }
    }

    return $ink;
}


/**
 * Function MakeLay
 **/
function MakeLay($mCanvas, $mCanvasW, $mCanvasH, $mInk, $mAtX, $mAtY, $mSolid)
{
    // One copy over the picture, the alpha of the canvas stands
    $ink_w = imagesx($mInk);
    $ink_h = imagesy($mInk);
    for ( $y = 0; $y < $ink_h; $y++ )
    {
        $to_y = $mAtY + $y;
        if ( $to_y < 0 || $to_y >= $mCanvasH ) continue;

        for ( $x = 0; $x < $ink_w; $x++ )
        {
            $to_x = $mAtX + $x;
            if ( $to_x < 0 || $to_x >= $mCanvasW ) continue;

            $dot = imagecolorat($mInk, $x, $y);
            $thin = ( 127 - ( ( $dot >> 24 ) & 0x7F ) ) / 127;
            $mix = $mSolid * $thin;
            if ( $mix <= 0 ) continue;

            $red = ( $dot >> 16 ) & 0xFF;
            $green = ( $dot >> 8 ) & 0xFF;
            $blue = $dot & 0xFF;
            $was = imagecolorat($mCanvas, $to_x, $to_y);
            $mix_r = (int)round($red * $mix + ( ( $was >> 16 ) & 0xFF ) * ( 1 - $mix ));
            $mix_g = (int)round($green * $mix + ( ( $was >> 8 ) & 0xFF ) * ( 1 - $mix ));
            $mix_b = (int)round($blue * $mix + ( $was & 0xFF ) * ( 1 - $mix ));
            $keep = $was & 0x7F000000;
            imagesetpixel($mCanvas, $to_x, $to_y, $keep + ( $mix_r << 16 ) + ( $mix_g << 8 ) + $mix_b);
        }
    }
}


/**
 * Function MakeSkip
 **/
function MakeSkip($mShow)
{
    // What this painting left out
    $skipped = [];
    if ( $mShow['mask'] != '' && $mShow['mask_alpha'] == 0 )
    {
        $skipped[] = 'the mask ['.$mShow['mask'].'] (mask_alpha is 0, so it lies invisible)';
    }
    if ( $mShow['range'] < 255 )
    {
        $wash = 'the grey wash (range '.$mShow['range'].', range_border '.$mShow['range_border'].')';
        if ( $mShow['canvas_alpha'] ) $wash .= ', and the cut into transparency with it';

        $skipped[] = $wash;
    }

    return implode(', ', $skipped);
}


/**
 * Function ColorWord
 **/
function ColorWord($mColor, $mBack = false)
{
    // TColor runs blue to red
    if ( $mBack )
    {
        $hex = substr((string)$mColor, 1);
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));

        return $red + ( $green << 8 ) + ( $blue << 16 );
    }

    $color = (int)$mColor;

    return sprintf('#%02X%02X%02X', $color & 0xFF, ( $color >> 8 ) & 0xFF, ( $color >> 16 ) & 0xFF);
}
