<?php

declare(strict_types=1);

namespace Empathy\MVC;

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
    public int $id;
    public string $module;
    public string $type;
    public int $section_id;
    public int $position;
    public string $label;
    public string $friendly_url;
    public string $template;
    public bool $hidden;
    public bool $owns_inline;
    public string $link;
    public string $stamp;
    public string $meta;
    public int $user_id;

    public static string $table = 'section_item';


    public function getURIData(): array
    {
        $uri_data = [];

        $sql = 'SELECT id, section_id, label, friendly_url FROM ' . SectionItemStandAlone::$table
            .' WHERE hidden != 1';
        $error = 'Could not get URI data.';
        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $uri_data[$i] = $row;
            $i++;
        }
        return $uri_data;
    }

    public function getItem($id): void
    {
        $params = [];
        $sql = 'SELECT * FROM ' . SectionItemStandAlone::$table . ' WHERE id = ?';
        $params[] = $id;
        $error = 'Could not load record.';
        $result = $this->query($sql, $error, $params);
        if (1 === $result->rowCount()) {
            $row = $result->fetch();
            foreach ($row as $index => $value) {
                $this->$index = $value;
            }
        } else {
            //echo "Whoops!";
        }
    }

    public function findSection($rows, $slug, $parent_id): array
    {
        $matched = [];
        foreach ($rows as $row) {
            $comp = str_replace(' ', '', strtolower($row['label']));
            if ($comp === $slug && $parent_id === $row['section_id']) {
                $matched = $row;
                break;
            }
        }
        return $matched;
    }

    public function doResolveURI(?array $uri): int
    {
        if ($uri === null) {
            return 0;
        }

        $rows = $this->getURIData();
        $id = 0;
        $sections = [];
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

        if (sizeof($sections) === sizeof($uri)) {
            $sectionId = $sections[sizeof($sections) - 1]['id'];
        }
        return $sectionId;
    }

    public function resolveURI($uri): int
    {
        $cache = null;
        $cacheEnabled = false;
        try {
            $cache = DI::getContainer()->get('Cache');
            $cacheEnabled = DI::getContainer()->get('cacheEnabled');
        } catch (\Exception $e) {
            //
        }
        if ($cache && $cacheEnabled) {
            return $cache->cachedCallback(
                'section_id_' . implode('_', $uri),
                [$this, 'doResolveURI'],
                [$uri]
            );
        } else {
            return $this->doResolveURI($uri);
        }
    }
}
