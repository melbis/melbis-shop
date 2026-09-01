<?php
namespace AGENT_FILL_TOPIC;

/**
 * Function Main
 *
 * Entry point of the catalogue generator. Modes:
 *   ping   - report the environment and the current state, write nothing
 *   build  - grow branches of sections under one or several parents
 *   rename - lay the names again, by the level a node really stands on
 **/
function Main($mVars)
{
    // Dispatch by the mode of the call
    $post = $mVars['post'];
    $mode = (string) ( $post['mode'] ?? 'ping' );

    if ( $mode === 'ping' )   return Ping();
    if ( $mode === 'build' )  return Build($post);
    if ( $mode === 'rename' ) return Rename($post);

    return Answer(['error' => 'unknown mode: '.$mode]);
}

/**
 * Function Answer
 **/
function Answer($mData)
{
    // One shape of output for every mode
    return json_encode($mData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Function Ping
 **/
function Ping()
{
    // Count what the catalogue holds right now
    $command = "SELECT COUNT(*) FROM {DBNICK}_topic";
    $topic_count = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $command = "SELECT COUNT(*) FROM {DBNICK}_topic_right";
    $right_count = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    // How the tree is spread over its levels
    $command = "SELECT tlevel, COUNT(*) AS n
                  FROM {DBNICK}_topic
              GROUP BY tlevel
              ORDER BY tlevel
               ";
    $level_set = MELBIS()->SqlSelect(__LINE__, $command);

    // What the settings offer for a section
    $kind_set  = MELBIS()->SysKeyValues('TOPIC_KIND_KEY');
    $templ_set = MELBIS()->SysKeyValues('TOPIC_TEMPL_KEY');
    $order_set = MELBIS()->SysKeyValues('TOPIC_ORDER_KEY');

    // Report what the module can reach
    return Answer([
        'mode'         => 'ping',
        'topic_count'  => $topic_count,
        'right_count'  => $right_count,
        'level'        => $level_set,
        'kind_key'     => $kind_set,
        'templ_key'    => $templ_set,
        'order_key'    => $order_set,
        'php'          => PHP_VERSION,
        'time_limit'   => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit')
        ]);
}

/**
 * Function Build
 **/
function Build($mPost)
{
    // Read the plan of the branch
    $limit  = (int) ( $mPost['limit'] ?? 500 );
    $plan   = $mPost['plan'] ?? [];
    $parent = $mPost['parent_id'] ?? 1;

    // A plan is a list of counts, one per level
    if ( is_string($plan) ) $plan = array_map('intval', explode(',', $plan));
    if ( !is_array($plan) || !count($plan) ) return Answer(['error' => 'plan is empty']);

    // One call may grow several branches at once
    if ( !is_array($parent) ) $parent = explode(',', (string) $parent);
    $parent_set = array_values(array_unique(array_filter(array_map('intval', $parent))));
    if ( !count($parent_set) ) return Answer(['error' => 'parent_id is empty']);

    // Every parent must be there, and its level is the base of the branch
    $parent_line = implode(',', $parent_set);
    $command = "SELECT id, tlevel FROM {DBNICK}_topic WHERE id IN ( $parent_line )";
    $level_set = MELBIS()->SqlSelect(__LINE__, $command);
    if ( count($level_set) != count($parent_set) )
        return Answer(['error' => 'some parent is missing: '.$parent_line]);

    $base_set = array_column($level_set, 'tlevel', 'id');

    // Take the tables for the whole run, not per node
    $table_set = ['{DBNICK}_topic', '{DBNICK}_topic_right'];
    if ( !MELBIS()->SqlTableLock(__LINE__, $table_set) )
        return Answer(['error' => 'tables are busy']);

    $begin = microtime(true);
    $made = [];
    $error = '';

    // Grow every branch; a fall must still release the tables
    try
    {
        foreach ( $parent_set as $parent_id )
        {
            if ( count($made) >= $limit ) break;

            Level($parent_id, (int) $base_set[$parent_id], $plan, 0, $limit, $made);
        }
    }
    catch ( \Throwable $e )
    {
        $error = $e->getMessage();
    }

    MELBIS()->SqlTableUnlock(__LINE__, $table_set);

    $spent = round((microtime(true) - $begin) * 1000);
    $count = count($made);

    return Answer([
        'mode'        => 'build',
        'parent_id'   => $parent_set,
        'plan'        => $plan,
        'made'        => $count,
        'ms'          => $spent,
        'ms_per_node' => $count ? round($spent / $count, 2) : 0,
        'first'       => array_slice($made, 0, 3),
        'last'        => array_slice($made, -3),
        'error'       => $error
        ]);
}

/**
 * Function Rename
 *
 * Walks the tree in its own order and writes the name a node should carry by
 * the level it stands on. Goes in pages, so a long catalogue is done in parts.
 **/
function Rename($mPost)
{
    // Read the page of the walk
    $from = (int) ( $mPost['from'] ?? 0 );
    $limit = (int) ( $mPost['limit'] ?? 1000 );

    // The nodes of this page, each with its place among its own brothers
    $param_page = ['from' => $from];
    $command = "SELECT id, tindex, tlevel, absindex, num
                  FROM (
                        SELECT id, tindex, tlevel, absindex,
                               ROW_NUMBER() OVER ( PARTITION BY tindex
                                                       ORDER BY absindex ) AS num
                          FROM {DBNICK}_topic
                         WHERE id > 1
                       ) t
                 WHERE absindex > :FROM
              ORDER BY absindex
                 LIMIT ".(int) $limit;
    $node_set = MELBIS()->SqlSelect(__LINE__, $command, $param_page);

    if ( !count($node_set) ) return Answer(['mode' => 'rename', 'done' => 0, 'next' => 0]);

    // Take the table for the whole page
    if ( !MELBIS()->SqlTableLock(__LINE__, '{DBNICK}_topic') )
        return Answer(['error' => 'table is busy']);

    $begin = microtime(true);
    $done = 0;
    $next = 0;
    $error = '';

    // Name every node by its level and its place among the brothers
    try
    {
        foreach ( $node_set as $node )
        {
            $name = Name((int) $node['tlevel'], (int) $node['num']);
            $fields = [
                'id'        => (int) $node['id'],
                'name'      => $name,
                'seo_title' => $name
                ];
            MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic', $fields, 'id');

            $done++;
            $next = (int) $node['absindex'];
        }
    }
    catch ( \Throwable $e )
    {
        $error = $e->getMessage();
    }

    MELBIS()->SqlTableUnlock(__LINE__, '{DBNICK}_topic');

    return Answer([
        'mode'  => 'rename',
        'done'  => $done,
        'next'  => $next,
        'ms'    => round((microtime(true) - $begin) * 1000),
        'error' => $error
        ]);
}

/**
 * Function Level
 **/
function Level($mParentId, $mBaseLevel, $mPlan, $mDepth, $mLimit, &$mMade)
{
    // Stop at the bottom of the plan
    if ( !isset($mPlan[$mDepth]) ) return;

    $count = (int) $mPlan[$mDepth];
    $level = $mBaseLevel + $mDepth + 1;

    for ( $i = 1; $i <= $count; $i++ )
    {
        // The guard against a runaway plan
        if ( count($mMade) >= $mLimit ) return;

        $id = Node($mParentId, $level, $i);
        if ( !$id ) continue;

        $mMade[] = $id;

        Level($id, $mBaseLevel, $mPlan, $mDepth + 1, $mLimit, $mMade);
    }
}

/**
 * Function Node
 **/
function Node($mParentId, $mLevel, $mNum)
{
    // The tree utility seats the node, bare
    $id = MELBIS()->SysTreeAdd('topic', $mParentId);
    if ( !$id ) return 0;

    // Carry down the grants of the parent
    MELBIS()->SysTreeRightCopy('topic', $mParentId, $id);

    // Fill the columns the program shows
    $name = Name($mLevel, $mNum);
    $fields = [
        'id'         => $id,
        'skey'       => 'T'.$id,
        'name'       => $name,
        'kind_key'   => 'kGoods',
        'templ_key'  => Fallback('TOPIC_TEMPL_KEY'),
        'order_key'  => Fallback('TOPIC_ORDER_KEY'),
        'order_asc'  => 1,
        'no_visible' => 0,
        'in_xml'     => 1,
        'seo_psu'    => 'topic-'.$id,
        'seo_title'  => $name
        ];
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic', $fields, 'id');

    return $id;
}

/**
 * Function Fallback
 **/
function Fallback($mKeyCode)
{
    // The first value the settings offer for this code
    static $cache = [];

    if ( !isset($cache[$mKeyCode]) )
    {
        $value_set = MELBIS()->SysKeyValues($mKeyCode);
        $name_set = array_keys($value_set);
        $cache[$mKeyCode] = $name_set[0] ?? '';
    }

    return $cache[$mKeyCode];
}

/**
 * Function Name
 *
 * $mLevel is the level of the tree the node stands on, 1 for the top one.
 * $mNum is its place among its own brothers.
 **/
function Name($mLevel, $mNum)
{
    // A word list per level, so a branch reads like a real catalogue
    static $word_set = [
        1 => ['Электроника', 'Бытовая техника', 'Одежда и обувь', 'Инструмент',
              'Красота и здоровье', 'Спорт и отдых', 'Детские товары', 'Автотовары',
              'Дом и сад', 'Продукты', 'Канцелярия', 'Зоотовары', 'Мебель',
              'Строительство', 'Хобби и творчество'],
        2 => ['Смартфоны и связь', 'Аудиотехника', 'Мелкая техника', 'Верхняя одежда',
              'Ручной инструмент', 'Уход за кожей', 'Тренажёры', 'Игрушки',
              'Шины и диски', 'Освещение', 'Посуда', 'Корма', 'Мягкая мебель',
              'Отделочные материалы', 'Настольные игры', 'Аксессуары',
              'Расходные материалы', 'Комплектующие'],
        3 => ['Бюджетные', 'Средний класс', 'Премиум', 'Новинки', 'Уценённые',
              'Под заказ', 'Сезонные', 'Распродажа', 'Импортные', 'Отечественные',
              'Профессиональные', 'Для дома', 'Компактные', 'Крупногабаритные',
              'Наборы', 'Комплекты', 'Запчасти', 'Сопутствующие', 'Универсальные',
              'Специальные']
        ];

    // Deeper levels reuse the last word list
    $level = min(max($mLevel, 1), count($word_set));
    $set = $word_set[$level];
    $word = $set[($mNum - 1) % count($set)];

    // The number keeps names apart when a list runs out
    $lap = intdiv($mNum - 1, count($set));

    return $lap ? $word.' '.($lap + 1) : $word;
}
