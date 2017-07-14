<?php

namespace Eightfold\DocumenterPhp\Traits;

trait HasClassDefinitionsList
{
    /**
     * [definesSymbolsDefaultConfig description]
     * @return [type] [description]
     *
     */
    protected function definesSymbolsDefaultConfig()
    {
        return [
            'symbolOrder' => [
                'properties',
                'methods'
            ],
            'accessOrder' => [
                'public',
                'protected',
                'private',
                'static_public',
                'static_protected',
                'static_private'
            ]
        ];
    }

    /**
     * [processSymbolTypeForCategory description]
     * @param  [type] $category   [description]
     * @param  [type] $symbols    [description]
     * @param  [type] $symbolType [description]
     * @param  [type] $config     [description]
     * @param  [type] &$return    [description]
     * @return [type]             [description]
     */
    protected function processSymbolTypeForCategory($category, $symbols, $symbolType, $config, &$return)
    {
        if (count($config['accessOrder']) > 0) {
            foreach ($config['accessOrder'] as $access) {
                if (in_array($access, $this->version->project->documenter->maxVisibility) && isset($symbols[$access])) {
                    $symbolsToProcess = $symbols[$access];
                    $return[] = $this->processSymbolsDefinitionForCategory($category, $symbolsToProcess, $config);
                }
            }
        }
    }
}
