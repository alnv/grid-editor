<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

/**
 * Table tl_columnset
 */
$GLOBALS['TL_DCA']['tl_columnset'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'onload_callback'             => array
        (
            array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'appendColumnSizesToPalette')
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
            )
        )
    ),

    // List
    'list' => array
    (
        'label' => array
        (
            'fields'                  => array ('title', 'columns'),
            'format'                  => '%s <span style="color:#ccc;">[%s ' . $GLOBALS['TL_LANG']['tl_columnset']['formatColumns'] . ']</span>'
        ),
        'sorting' => array
        (
            'mode'                    => 2,
            'flag'                    => 1,
            'fields'                  => array('title', 'columns'),
            'panelLayout'             => 'sort,search,limit',
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_columnset']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_columnset']['copy'],
                'href'                => 'act=paste&amp;mode=copy',
                'icon'                => 'copy.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_columnset']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_columnset']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => function () {
                    $callback = new Netzmacht\Bootstrap\Grid\DataContainer\ToggleIconCallback(
                        \BackendUser::getInstance(),
                        \Input::getInstance(),
                        \Database::getInstance(),
                        'tl_columnset',
                        'published'
                    );

                    return call_user_func_array($callback, func_get_args());
                },
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_columnset']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        ),
    ),

    // Palettes
    'metapalettes' => array
    (
        'default' => array
        (
            'title'                   => array('title', 'description', 'columns'),
            'columnset'               => array('sizes'),
            'expert'                  => array(':hide', 'resets', 'rowClass', 'customClasses'),
            'published'               => array('published'),
        )
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['title'],
            'exclude'                 => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'description' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['description'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class' => 'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'columns' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['columns'],
            'exclude'                 => true,
            'sorting'                 => true,
            'flag'                    => 3,
            'length'                  => 1,
            'inputType'               => 'select',
            'options_callback'        => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumns'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_columnset'],
            'eval'                    => array('submitOnChange' => true, 'chosen' => true),
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),

        'sizes' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['sizes'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'options'                 => array('xs', 'sm', 'md', 'lg'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_columnset'],
            'eval'                    => array('multiple' => true, 'submitOnChange' => true),
            'sql'                     => "mediumblob NULL"
        ),

        'resets'                    => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_columnset']['resets'],
            'exclude'       => true,
            'inputType'     => 'multiColumnWizard',
            'eval'          => array
            (
                'columnFields'       => array
                (
                    'column'  => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['column'],
                        'inputType'        => 'select',
                        'options_callback' => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumnNumbers'),
                        'eval'             => array('style' => 'width: 100px;', 'chosen' => true),
                    ),
                    'xs' => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['xs'],
                        'inputType'        => 'checkbox',
                        'eval'             => array('style' => 'width: 80px;', 'includeBlankOption' => true),
                    ),
                    'sm' => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['sm'],
                        'inputType'        => 'checkbox',
                        'eval'             => array('style' => 'width: 50px;', 'includeBlankOption' => true),
                    ),
                    'md' => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['md'],
                        'inputType'        => 'checkbox',
                        'eval'             => array('style' => 'width: 50px;', 'includeBlankOption' => true),
                    ),
                    'lg' => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['lg'],
                        'inputType'        => 'checkbox',
                        'eval'             => array('style' => 'width: 50px;', 'includeBlankOption' => true),
                    ),
                ),
            ),
            'sql'           => "blob NULL"
        ),

        'customClasses'                    => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['customClasses'],
            'exclude'       => true,
            'inputType'     => 'multiColumnWizard',
            'eval'          => array
            (
                'columnFields'       => array
                (
                    'column'  => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['column'],
                        'inputType'        => 'select',
                        'options_callback' => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumnNumbers'),
                        'eval'             => array('style' => 'width: 100px;', 'chosen' => true),
                    ),
                    'class' => array
                    (
                        'label'            => $GLOBALS['TL_LANG']['tl_columnset']['class'],
                        'inputType'        => 'text',
                        'eval'             => array('style' => 'width: 260px;'),
                    ),
                ),
            ),
            'sql'           => "blob NULL"
        ),

        'rowClass' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['rowClass'],
            'exclude'                 => true,
            'default'                 => '',
            'inputType'               => 'text',
            'reference'               => &$GLOBALS['TL_LANG']['tl_columnset'],
            'eval'                    => array(),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),

        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_columnset']['published'],
            'exclude'                 => true,
            'default'                 => '1',
            'inputType'               => 'checkbox',
            'reference'               => &$GLOBALS['TL_LANG']['tl_columnset'],
            'eval'                    => array(),
            'sql'                     => "char(1) NULL"
        )
    )
);


// defining col set fields
$colSetTemplate = array
(
    'exclude'       => true,
    'inputType'     => 'multiColumnWizard',
    'load_callback' => array
    (
        array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'createColumns')
    ),
    'eval'          => array
    (
        'includeBlankOption' => true,
        'columnFields'       => array
        (
            'width'  => array
            (
                'label'            => $GLOBALS['TL_LANG']['tl_columnset']['width'],
                'inputType'        => 'select',
                'options_callback' => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getWidths'),
                'eval'             => array('style' => 'width: 100px;', 'chosen' => true),
            ),
            'offset' => array
            (
                'label'            => $GLOBALS['TL_LANG']['tl_columnset']['offset'],
                'inputType'        => 'select',
                'options_callback' => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumns'),
                'eval'             => array('style' => 'width: 100px;', 'includeBlankOption' => true, 'chosen' => true),
            ),
            'order'  => array
            (
                'label'            => $GLOBALS['TL_LANG']['tl_columnset']['order'],
                'inputType'        => 'select',
                'options_callback' => array('Netzmacht\Bootstrap\Grid\DataContainer\ColumnSet', 'getColumnOrders'),
                'eval'             => array('style' => 'width: 160px;', 'includeBlankOption' => true, 'chosen' => true),
            ),
        ),
        'buttons'            => array('copy' => false, 'delete' => false),
    ),
    'sql'           => "blob NULL"
);

$colSetXsTemplate = $colSetTemplate;
unset($colSetXsTemplate['eval']['columnFields']['order']);

$GLOBALS['TL_DCA']['tl_columnset']['fields']['columnset_xs'] = array_merge
(
    $colSetXsTemplate, array('label' => &$GLOBALS['TL_LANG']['tl_columnset']['columnset_xs'])
);

$GLOBALS['TL_DCA']['tl_columnset']['fields']['columnset_sm'] = array_merge
(
    $colSetTemplate, array('label' => &$GLOBALS['TL_LANG']['tl_columnset']['columnset_sm'])
);

$GLOBALS['TL_DCA']['tl_columnset']['fields']['columnset_md'] = array_merge
(
    $colSetTemplate, array('label' => &$GLOBALS['TL_LANG']['tl_columnset']['columnset_md'])
);

$GLOBALS['TL_DCA']['tl_columnset']['fields']['columnset_lg'] = array_merge
(
    $colSetTemplate, array('label' => &$GLOBALS['TL_LANG']['tl_columnset']['columnset_lg'])
);
