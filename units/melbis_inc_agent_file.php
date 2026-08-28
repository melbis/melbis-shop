<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * EntityAll   - The elements files hang on
 * EntityOne   - Weighs one entity
 * EntityGone  - Refuses an element gone
 *
 * RightElem   - Weighs the element right
 *
 * FileAll     - Reads files of the elements
 * FileOne     - Reads one file row
 * FileDrop    - Deletes row and picture
 *
 * DiskPath    - The path of a row
 * DiskFolder  - The folder of a file
 * DiskPicture - What a picture really is
 *
 * ProfileAll  - Reads the picture profiles
 * ProfileOne  - Reads one picture profile
 * ProfileShow - The recipe in agent words
 *
 * MaskMap     - The masks with a picture
 * MaskWord    - The mask behind a path
 *
 * Make        - Derives a picture
 * MakePaint   - The recipe onto the picture
 * MakeMask    - The mask over the canvas
 * MakeSkip    - What only the program paints
 *
 * ColorWord   - A canvas colour into #RRGGBB
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_FILE;


// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;

// The words of a position
const MASK_POS = ['center', 'left-top', 'right-top', 'right-bottom', 'left-bottom'];

// No mask, as the editor
const MASK_NONE = 'files/1899/12_30/00_00/';


/**
 * Function EntityAll
 **/
function EntityAll()
{
    // One shape for every entity
    return ['store', 'topic', 'info', 'info_value', 'brand', 'key_value'];
}


/**
 * Function EntityOne
 **/
function EntityOne($mEntity)
{
    // An unknown word refused early
    $entity = trim((string)$mEntity);
    if ( in_array($entity, EntityAll()) ) return true;

    $list = implode(', ', EntityAll());

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
        'store'      => 'the Search tool answers the goods',
        'topic'      => 'the Catalog tool answers them',
        'info'       => 'the Attributes tool answers them',
        'info_value' => 'the Attributes tool answers them',
        'brand'      => 'the Brands tool answers them',
        'key_value'  => 'the Registry tool answers them'
        ];
    $said = $where[$mEntity] ?? 'its own tool answers them';

    return [
        'result'  => false,
        'message' => 'No '.$mEntity.' ['.$elem_id.'] in the store'
        ];
}


/**
 * Function RightElem
 **/
function RightElem($mUserId, $mEntity, $mElemId)
{
    $elem_id = (int)$mElemId;

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
function FileAll($mEntity, $mIds)
{
    // The order a template walks
    $list = implode(',', $mIds);
    if ( $list == '' ) return [];

    $command = "SELECT *
                  FROM {DBNICK}_files_$mEntity
                 WHERE elem_id IN ( $list )
              ORDER BY elem_id, kind_key, pos
               ";

    return MELBIS()->SqlSelect(__LINE__, $command);
}


/**
 * Function FileOne
 **/
function FileOne($mEntity, $mId)
{
    $command = "SELECT *
                  FROM {DBNICK}_files_$mEntity
                 WHERE id = :ID
               ";
    $param_file = [
        'id' => (int)$mId
        ];

    return MELBIS()->SqlSelectFlat(__LINE__, $command, $param_file);
}


/**
 * Function FileDrop
 **/
function FileDrop($mEntity, $mId, $mDisk)
{
    // Born here, gone with it
    MELBIS()->SqlDelete(__LINE__, '{DBNICK}_files_'.$mEntity, 'id', (int)$mId);

    if ( $mDisk != '' && file_exists($mDisk) ) @unlink($mDisk);
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

    $show['type']       = ( (int)$xml->JPEG['FileType'] == 1 ) ? 'png' : 'jpeg';
    $show['quality']    = (int)$xml->JPEG['Compress'];
    $show['width']      = (int)$xml->JPEG['Width'];
    $show['height']     = (int)$xml->JPEG['Hight'];
    $show['smart']      = ( (string)$xml->JPEG['Smart'] == 'True' );
    $show['group']      = (string)( $xml->FILE['KindKey'] ?? 'kDefault' );
    $show['range']      = (int)( $xml->CANVAS['Range'] ?? 255 );
    $show['border']     = (int)( $xml->CANVAS['Border'] ?? 0 );
    $show['background'] = ColorWord((int)( $xml->CANVAS['Color'] ?? 16777215 ));
    $show['rotate']     = (int)( $xml->ROTATE['Rotate'] ?? 0 );
    $show['mirror']     = ( (string)( $xml->ROTATE['Mirror'] ?? '' ) == 'True' );
    $show['mask']       = MaskWord((string)( $xml->MASK['File'] ?? '' ));
    $show['mask_pos']   = MASK_POS[$pos];
    $show['mask_alpha'] = (int)( $xml->MASK['Alpha'] ?? 0 );
    $show['red']        = (int)( $xml->EFFECTS['Red'] ?? 0 );
    $show['green']      = (int)( $xml->EFFECTS['Green'] ?? 0 );
    $show['blue']       = (int)( $xml->EFFECTS['Blue'] ?? 0 );
    $show['intensive']  = (int)( $xml->EFFECTS['Intensive'] ?? 0 );
    $show['contrast']   = (int)( $xml->EFFECTS['Contrast'] ?? 0 );
    $show['sharpen']    = (int)( $xml->EFFECTS['Sharpen'] ?? 0 );

    // The raw path, for update
    if ( $mRaw ) $show['mask_file'] = (string)( $xml->MASK['File'] ?? '' );

    return $show;
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
    $table = 'files_'.$mEntity;
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

    $id = MELBIS()->SqlGenId($table);
    $ext = ( $mShow['type'] == 'png' ) ? 'png' : 'jpg';
    $file_name = strtolower($table.'_'.$mUserId.'_'.$id).'.'.$ext;

    // File first, row second
    $laid = ( $mShow['type'] == 'png' )
        ? imagepng($paint['image'], $dir.$file_name)
        : imagejpeg($paint['image'], $dir.$file_name, $mShow['quality']);
    imagedestroy($paint['image']);

    if ( !$laid )
    {
        return [
            'result'  => false,
            'message' => 'The picture could not be written'
            ];
    }

    // The new file stands last
    $kind = $mShow['group'];
    $real_name = trim((string)$mRealName);
    if ( $real_name == '' )
    {
        $stem = pathinfo($mWas['real_name'], PATHINFO_FILENAME);
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

    // Mirror and turn, then fit
    if ( $mShow['mirror'] ) imageflip($source, IMG_FLIP_HORIZONTAL);
    if ( $mShow['rotate'] != 0 )
    {
        $turned = imagerotate($source, -1 * $mShow['rotate'], ( $red << 16 ) + ( $green << 8 ) + $blue);
        imagedestroy($source);
        $source = $turned;
    }

    // Fits whole into the box
    $source_w = imagesx($source);
    $source_h = imagesy($source);
    $border = $mShow['border'];
    $inner_w = max(1, $mShow['width'] - 2 * $border);
    $inner_h = max(1, $mShow['height'] - 2 * $border);
    $scale = min($inner_w / $source_w, $inner_h / $source_h);
    $fit_w = max(1, (int)round($source_w * $scale));
    $fit_h = max(1, (int)round($source_h * $scale));
    $canvas_w = ( $mShow['smart'] ) ? $fit_w + 2 * $border : $mShow['width'];
    $canvas_h = ( $mShow['smart'] ) ? $fit_h + 2 * $border : $mShow['height'];

    $canvas = imagecreatetruecolor($canvas_w, $canvas_h);
    $back = imagecolorallocate($canvas, $red, $green, $blue);
    imagefill($canvas, 0, 0, $back);

    $x = (int)(( $canvas_w - $fit_w ) / 2);
    $y = (int)(( $canvas_h - $fit_h ) / 2);
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
 * Function MakeMask
 **/
function MakeMask($mCanvas, $mCanvasW, $mCanvasH, $mShow)
{
    // White glass, alpha solid
    $disk = __DIR__.'/../'.$mShow['mask_file'];
    $what = DiskPicture($disk);
    if ( $what['type'] == '' ) return;

    $doors = [
        'jpg'  => 'imagecreatefromjpeg',
        'png'  => 'imagecreatefrompng',
        'gif'  => 'imagecreatefromgif',
        'webp' => 'imagecreatefromwebp'
        ];
    $open = $doors[$what['type']];
    $mask = @$open($disk);
    if ( $mask === false ) return;

    $mask_w = imagesx($mask);
    $mask_h = imagesy($mask);

    // Five places for a mask
    $spots = [
        'center'       => [(int)(( $mCanvasW - $mask_w ) / 2), (int)(( $mCanvasH - $mask_h ) / 2)],
        'left-top'     => [0, 0],
        'right-top'    => [$mCanvasW - $mask_w, 0],
        'right-bottom' => [$mCanvasW - $mask_w, $mCanvasH - $mask_h],
        'left-bottom'  => [0, $mCanvasH - $mask_h]
        ];
    list( $at_x, $at_y ) = $spots[$mShow['mask_pos']] ?? $spots['center'];

    $solid = $mShow['mask_alpha'] / 255;
    for ( $y = 0; $y < $mask_h; $y++ )
    {
        $to_y = $at_y + $y;
        if ( $to_y < 0 || $to_y >= $mCanvasH ) continue;

        for ( $x = 0; $x < $mask_w; $x++ )
        {
            $to_x = $at_x + $x;
            if ( $to_x < 0 || $to_x >= $mCanvasW ) continue;

            $dot = imagecolorat($mask, $x, $y);
            $red = ( $dot >> 16 ) & 0xFF;
            $green = ( $dot >> 8 ) & 0xFF;
            $blue = $dot & 0xFF;

            // White is glass, alpha thins
            if ( $red == 255 && $green == 255 && $blue == 255 ) continue;

            $thin = ( 127 - ( ( $dot >> 24 ) & 0x7F ) ) / 127;
            $mix = $solid * $thin;
            if ( $mix <= 0 ) continue;

            $was = imagecolorat($mCanvas, $to_x, $to_y);
            $mix_r = (int)round($red * $mix + ( ( $was >> 16 ) & 0xFF ) * ( 1 - $mix ));
            $mix_g = (int)round($green * $mix + ( ( $was >> 8 ) & 0xFF ) * ( 1 - $mix ));
            $mix_b = (int)round($blue * $mix + ( $was & 0xFF ) * ( 1 - $mix ));
            imagesetpixel($mCanvas, $to_x, $to_y, ( $mix_r << 16 ) + ( $mix_g << 8 ) + $mix_b);
        }
    }

    imagedestroy($mask);
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
    if ( $mShow['range'] < 255 ) $skipped[] = 'the grey wash (range '.$mShow['range'].')';

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
