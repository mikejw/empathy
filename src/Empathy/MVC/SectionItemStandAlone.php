<?php

namespace Empathy\MVC;


/**
 * Empathy SectionItemStandAlone class
 * @file            Empathy/MVC/SectionItemStandAlone.php
 * @description     For elib-cms modules.
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class SectionItemStandAlone extends Entity
{
    public $id;
    public $module;
    public $type;
    public $parent_id;
    public $position;
    public $label;
    public $friendly_url;
    public $template;
    public $hidden;
    public $owns_inline;
    public $link;

    public static $table = "section_item";

    public function getURIData()
    {
        $uri_data = array();

        $sql = "SELECT id, label, friendly_url FROM ".SectionItemStandAlone::$table;
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
        $sql = "SELECT * FROM ".SectionItemStandAlone::$table." WHERE id = $id";
        $error = "Could not load record.";
        $result = $this->query($sql, $error);
        if (1 == $result->rowCount()) {
            $row = $result->fetch();
            foreach ($row as $index => $value) {
                $this->$index = $value;
            }
            $this->url_name = str_replace(" ", "", $this->label);
            $this->url_name = strtolower($this->url_name);
        } else {
            //echo "Whoops!";
        }
    }
}
