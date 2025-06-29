<?php

namespace Empathy\MVC;
use Empathy\MVC\DI;

/**
 * Empathy SectionItemStandAlone class
 * @file            Empathy/MVC/SectionItemStandAlone.php
 * @description     For elib-cms modules.
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class SectionItemStandAlone extends Entity
{
    public $id;
    public $module;
    public $type;
    public $section_id;
    public $position;
    public $label;
    public $friendly_url;
    public $template;
    public $hidden;
    public $owns_inline;
    public $link;
    public $stamp;
    public $meta;
    public $user_id;

    public static $table = 'section_item';


    public function getURIData()
    {
        $uri_data = array();

        $sql = 'SELECT id, section_id, label, friendly_url FROM ' . SectionItemStandAlone::$table
            .' WHERE hidden != 1';
        $error = "Could not get URI data.";
        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $uri_data[$i] = $row;
            $i++;
        }
        return $uri_data;
    }

    public function getItem($id)
    {
        $params = [];
        $sql = 'SELECT * FROM ' . SectionItemStandAlone::$table . ' WHERE id = ?';
        $params[] = $id;
        $error = 'Could not load record.';
        $result = $this->query($sql, $error, $params);
        if (1 == $result->rowCount()) {
            $row = $result->fetch();
            foreach ($row as $index => $value) {
                $this->$index = $value;
            }
        } else {
            //echo "Whoops!";
        }
    }

    public function findSection($rows, $slug, $parent_id)
    {
        $matched = array();
        foreach ($rows as $row) {
            $comp = str_replace(' ', '', strtolower($row['label']));
            if ($comp == $slug && $parent_id == $row['section_id']) {
                $matched = $row;
                break;
            }
        }
        return $matched;
    }

    public function doResolveURI($uri)
    {
        if (!isset($uri)) {
            return false;
        }

        $matched = false;
        $rows = $this->getURIData();
        $id = 0;
        $sections = array();
        $sectionId = -1;

        foreach ($uri as $slug) {
            $section = $this->findSection($rows, $slug, $id);
            if (sizeof($section)) {
                $id = $section['id'];
                $sections[] = $section;
            } else {
                break;
            }
        }

        if (isset($uri) && sizeof($sections) === sizeof($uri)) {
            $matched = true;
            $sectionId = $sections[sizeof($sections) - 1]['id'];
        }
        return $sectionId;
    }

    public function resolveURI($uri)
    {
        if (DI::getContainer()->get('CacheEnabled')) {
            return DI::getContainer()->get('Cache')->cachedCallback(
                'section_id_' . implode('_', $uri),
                [$this, 'doResolveURI'],
                [$uri]
            );
        } else {
            return $this->doResolveURI($uri);
        }
    }
}
