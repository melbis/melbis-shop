<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * EntityAll   - Names the elements a file may hang on
 * EntityOne   - Weighs one entity against that list, or refuses it by name
 * EntityGone  - Refuses an element that is not there and says where to look for it
 *
 * RightElem   - Weighs the right on the element itself; every entity goes its own way
 *
 * FileAll     - Reads the files of the given elements, whole rows
 * FileOne     - Reads one file row as it lies in the table
 * FileDrop    - Deletes a file the tool will not keep, the row and the picture both
 *
 * DiskPath    - Builds the path of a row on the disk, by the formula of the product
 * DiskFolder  - Builds the folder of a file out of the hour it arrived
 * DiskPicture - Reads what a picture on the disk really is: its size and its type
 *
 * ProfileAll  - Reads the rows of the FILES_PROFILE dictionary, in their order
 * ProfileOne  - Reads one picture profile by its name
 * ProfileShow - Turns the recipe of a profile into the words of the agent
 *
 * MaskMap     - Reads the masks that have a picture: the name and the path of each
 * MaskWord    - Turns the path a recipe keeps back into the name of its mask
 *
 * Make        - Derives a picture out of one lying in the store, by the recipe of a profile
 * MakePaint   - Lays the recipe onto the picture: everything GD can paint of it
 * MakeMask    - Lays the mask over the canvas the editor's way: own size, white is glass
 * MakeSkip    - Names the parts of a recipe only the program paints
 *
 * ColorWord   - Turns a canvas colour of the base into #RRGGBB, and back
 *
 * A file is a row of files_<entity> and a picture on the disk; the right is that of the element
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_FILE;


// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The words a mask position goes by; the base keeps the number of the word
const MASK_POS = ['center', 'left-top', 'right-top', 'right-bottom', 'left-bottom'];

// No mask, the way the editor of the program writes it: the folder of the zero date
const MASK_NONE = 'files/1899/12_30/00_00/';


/**
 * Function EntityAll
 **/
function EntityAll()
{
    // Each of them is a files_<entity> table of one and the same shape
    return ['store', 'topic', 'info', 'info_value', 'brand', 'key_value'];
}


/**
 * Function EntityOne
 **/
function EntityOne($mEntity)
{
    // The word goes into a table name, so an unknown one is refused before the query is built
    $entity = trim((string)$mEntity);
    if ( in_array($entity, EntityAll()) ) return true;

    $list = implode(', ', EntityAll());

    return [
        'result'  => false,
        'message' => 'No element called ['.$entity.'] takes files - these do: '.$list
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
        'message' => 'No '.$mEntity.' ['.$mId.'] in the store - '.$said
        ];
}


/**
 * Function RightElem
 **/
function RightElem($mUserId, $mEntity, $mElemId)
{
    $elem_id = (int)$mElemId;

    // A file of a goods takes the Descriptions right of a section the goods hangs in
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

        $allow = UTIL\RightTable('topic', $mUserId, 'descr');

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
            'message' => 'The files of the goods ['.$elem_id.'] are not yours to touch: that is '.
                         'the Descriptions right on a section it hangs in'
            ];
    }

    // A file of a section takes the Descriptions right standing on that section
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

        $allow = UTIL\RightTable('topic', $mUserId, 'descr');

        $command = "SELECT id
                      FROM $allow
                     WHERE id = :ID
                   ";
        $may = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_elem);
        if ( $may > 0 ) return true;

        return [
            'result'  => false,
            'message' => 'The files of the section ['.$elem_id.'] are not yours to touch: that is '.
                         'the Descriptions right on it, given out on the section in the program'
            ];
    }

    // A file of a characteristic or of a value takes the right of the characteristic
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

        $allow = UTIL\RightTable('info', $mUserId, $place);

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
            'message' => 'The files of the '.$mEntity.' ['.$elem_id.'] are not yours to touch: '.
                         'that is its own right, given out on the characteristic in the program'
            ];
    }

    // A brand and a value of the registry keep no rights, so the operation is the whole gate
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
    // Reads them in the order a template walks them: by element, by group, by place
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
    // A refused upload was born in this very call, so the picture goes with the row
    MELBIS()->SqlDelete(__LINE__, '{DBNICK}_files_'.$mEntity, 'id', (int)$mId);

    if ( $mDisk != '' && file_exists($mDisk) ) @unlink($mDisk);
}


/**
 * Function DiskPath
 **/
function DiskPath($mRow)
{
    // The same formula the program and the engine both write a file by
    return __DIR__.'/..'.DiskFolder($mRow['upload_time']).$mRow['file_name'];
}


/**
 * Function DiskFolder
 **/
function DiskFolder($mUploadTime)
{
    // Cuts the day and the hour of arrival into the folders the store keeps files in
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
    // Asks the disk what the file really is, and answers empty when it is no picture
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
    // Turns the XML into words; a body no parser takes is answered as closed, not as empty
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

    // An update needs the raw path of the mask too; the agent never sees this face
    if ( $mRaw ) $show['mask_file'] = (string)( $xml->MASK['File'] ?? '' );

    return $show;
}


/**
 * Function MaskMap
 **/
function MaskMap()
{
    // Reads the name a person says beside the path the recipe keeps, for both ways of the turn
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
    // The zero folder and an empty path both mean no mask; an unknown path is shown as it is
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
    // The caller holds the lock and the right already, so the work here is the picture alone
    if ( !function_exists('imagecreatetruecolor') )
    {
        return [
            'result'  => false,
            'message' => 'This PHP carries no GD, so nothing can be painted here'
            ];
    }

    $disk = DiskPath($mWas);
    $what = DiskPicture($disk);
    if ( $what['type'] == '' )
    {
        return [
            'result'  => false,
            'message' => 'The file ['.$mWas['real_name'].'] is not a picture the engine reads - '.
                         'jpg, png, gif and webp are'
            ];
    }

    $paint = MakePaint($what, $disk, $mShow);
    if ( !$paint['result'] ) return $paint;

    // Lays the new picture down by the formula of the engine: this minute, table, person, id
    $table = 'files_'.$mEntity;
    $now = MELBIS()->DateTime();
    $folder = DiskFolder($now);
    $dir = __DIR__.'/..'.$folder;
    if ( !is_dir($dir) && !@mkdir($dir, 0777, true) )
    {
        imagedestroy($paint['image']);

        return [
            'result'  => false,
            'message' => 'The folder of this minute could not be made: '.$folder
            ];
    }

    $id = MELBIS()->SqlGenId($table);
    $ext = ( $mShow['type'] == 'png' ) ? 'png' : 'jpg';
    $file_name = strtolower($table.'_'.$mUserId.'_'.$id).'.'.$ext;

    // Writes the file first and the row second: a break leaves garbage, not a dead link
    $laid = ( $mShow['type'] == 'png' )
        ? imagepng($paint['image'], $dir.$file_name)
        : imagejpeg($paint['image'], $dir.$file_name, $mShow['quality']);
    imagedestroy($paint['image']);

    if ( !$laid )
    {
        return [
            'result'  => false,
            'message' => 'The picture could not be written into '.$folder
            ];
    }

    // The new file stands at the end of the group the recipe names
    $kind = $mShow['group'];
    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_".$table."
                 WHERE elem_id = :ELEM_ID
                   AND kind_key = :KIND_KEY
               ";
    $param_pos = [
        'elem_id'  => $mWas['elem_id'],
        'kind_key' => $kind
        ];
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_pos);

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
        'pos'         => $last + 1
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_'.$table, $fields);

    $message = 'Made ['.$real_name.'] - '.$paint['width'].'x'.$paint['height'].' '.$mShow['type'].
               ' by ['.$mProfile.'], in the group ['.$kind.']';
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
    // Opens the picture by its type: what GD cannot open is refused here, not later
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
            'message' => 'The picture could not be opened - the file may be broken'
            ];
    }

    $hex = substr($mShow['background'], 1);
    $red = hexdec(substr($hex, 0, 2));
    $green = hexdec(substr($hex, 2, 2));
    $blue = hexdec(substr($hex, 4, 2));

    // Mirrors and turns first, the way the editor of the program does, and fits after
    if ( $mShow['mirror'] ) imageflip($source, IMG_FLIP_HORIZONTAL);
    if ( $mShow['rotate'] != 0 )
    {
        $turned = imagerotate($source, -1 * $mShow['rotate'], ( $red << 16 ) + ( $green << 8 ) + $blue);
        imagedestroy($source);
        $source = $turned;
    }

    // Fits the picture whole into the box the margins leave, smart shrinking the canvas to it
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

    // The effects GD can paint; the contrast of the program grows the other way round
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

    // The mask goes last, over everything the recipe has painted so far
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
    // Mirrored off the editor of the program: white is glass, and mask_alpha is how solid it lies
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

    // The five places the editor lays a mask in, by the words of the registry
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

            // White is glass, the way the editor keys it, and the png alpha thins the rest
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
    // Names what this painting left out, so nothing of a recipe is skipped in silence
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
    // The base keeps a TColor, its bytes standing blue to red, and a person reads #RRGGBB
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
