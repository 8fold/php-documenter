<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

/**
 * Create a definition list from a multidimensional dictionary of objects.
 *
 * @todo This has the logic and varation of anything in Documenter, yet has no unit
 *       tests; therefore, makes it very dangerous to refactor or re-engineer. Make
 *       some.
 */
trait DefinesSymbols
{
    static protected $noCategoryString = 'Miscellaneous';

    abstract static protected function processSymbolTypesForCategory($category, $symbols, $symbolType, $config, &$return);

    /**
     * A definition list of the symbols for the class.
     *
     * `$symbols` is a dictionary of objects retrieved by something that defines
     * symbols. For example, a Project has Class_ and other symbols.
     * The symbol dictionary must be multidimensional, with the first dimension being
     * a "category" string (ex. $symbols['Hello']).
     *
     * The second dimension is a dictionary of "symbol types"
     * (ex. $symbols['Hello']['some_type']); you can specify the order to display
     * symbol types using the "symbolOrder" key in `$config`.
     *
     * You must define `processSymbolTypesForCategory()` to handle anything beyond
     * these first two dimensions.
     *
     * `$config` is an optional dictionary with the following optional keys.
     *
     * - **label:**          Placed before the definition list in the HTML string.
     *                       Defaults to the category name.
     * - **labelWrapper:**   HTML element to wrap the label in. Defaults to h2.
     * - **skipCategories:** By default all categories will be processed.
     *                       `skipCategories` is an array of categories to *not*
     *                       process.
     * - **onlyCategories:** By default all categories will be processed.
     *                       `onlyCategories` is an array of categories to process.
     *                       Note: If the same category is in both `onlyCategories`
     *                       and `skipCategories`, the category will be processed.
     * - **symbolOrder:**   Array defining what order to display the symbols. All the
     *                       following must be present (default order): properties, and
     *                       methods.
     * - **accessOrder:**    Array defining what order to display access orders. All
     *                       the following must present (default order): public,
     *                       protected, private, static_public, static_protected, and
     *                       static_private.
     *
     * @param  array $config   [description]
     * @param  array $symbols  [description]
     *
     * @return string               [description]
     */
    static public function definitionListForSymbols($symbols, $config = [])
    {
        $default = [
            'symbolOrder' => [
                'abstract',
                'concrete'
            ],
            'accessOrder' => [
                'public',
                'protected',
                'private',
                'static_public',
                'static_protected',
                'static_private'
            ],
            'onlyCategories' => [],
            'skipCategories' => [],
            'labelWrapper' => 'h2'
        ];
        $config = array_merge($default, $config);

        $return = [];
        foreach ($symbols as $category => $accessLevels) {
            $process = static::shouldProcessSymbolDefinitionForCategory($category, $config);

            if ($process) {
                $symbolOrder = $config['symbolOrder'];
                // Both are three levels deep
                // generic = [category][key1][key2]
                // Class_ = [category][concrete/abstract][classname]
                // Symbol = [category][symboltype][access]
                //
                foreach ($symbolOrder as $symbolType) {
                    static::processSymbolTypesForCategory($category, $symbols, $symbolType, $config, $return);
                }
            }
        }
        return implode("\n\n", $return);
    }

    static private function shouldProcessSymbolDefinitionForCategory($category, $config)
    {
        $onlyCategories = $config['onlyCategories'];
        $hasOnly = (count($onlyCategories) > 0);
        $inOnly = in_array($category, $onlyCategories);
        // print($category);
        // print($hasOnly);
        // print($inOnly);

        if ($hasOnly && $inOnly) {
            // print('should process');
            return true;
        }

        $skippedCategories = $config['skipCategories'];
        $hasSkipped = (count($skippedCategories) > 0);
        $inSkipped = in_array($category, $skippedCategories);
        // print($hasSkipped);
        // print($inSkipped);

        if (($hasOnly && !$inSkipped) || ($hasSkipped && $inSkipped)) {
            // print('skipping');
            return false;
        }
        // print('default process');
        return true;
    }

    static private function processSymbolsDefinitionForCategory($category, $symbols, $config)
    {
        foreach ($symbols as $slug => $symbol) {
            $termContent = $symbol->largeDeclaration;
            if ($symbol->isDeprecated) {
                $termContent = [
                    'element' => 'del',
                    'config' => ['content' => $termContent]
                ];
            }

            $categoryContent[] = [
                'element' => 'dt',
                'config' => ['content' => $termContent]
            ];

            $description = $symbol->shortDescription;
            if ($symbol->isDeprecated) {
                $description = $symbol->deprecatedDescription;
            }
            $converter = new CommonMarkConverter();
            $description = $converter->convertToHtml($description);

            if (strlen($description) > 0) {
                $categoryContent[] = [
                    'element' => 'dd',
                    'config' => ['content' => $description]
                ];

            }
        }
        $list = Html5Gen::dl(['content' => $categoryContent]);

        $labelWrapper = $config['labelWrapper'];
        $labelText = (isset($config['label']))
            ? $config['label']
            : ($category == 'NO_CATEGORY')
                ? 'Miscellaneous'
                : $category;
        $label = Html5Gen::$labelWrapper([
                'content' => $labelText
            ]);

        return $label ."\n\n". $list;
    }
}
