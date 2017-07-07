<?php

namespace Eightfold\DocumenterPhp\Traits;

use Eightfold\Html5Gen\Html5Gen;
use Eightfold\DocumenterPhp\Helpers\StringHelpers;
use League\CommonMark\CommonMarkConverter;

/**
 * Create a definition list from a multidimensional dictionary of objects.
 *
 * @todo This has the most logic and variation of anything in Documenter. Further, it
 *       is probably the most general purpose. However, it has no unit tests;
 *       therefore, makes it very dangerous to refactor or re-engineer. Make tests.
 */
trait DefinesSymbols
{
    static protected $noCategoryString = 'Miscellaneous';

    /**
     * Returns a dictionary of default values for the configuration dictionary.
     *
     * When calling `definitionListForSymbols` all the keys are optional. The reason
     * they are all optional is because each class using this trait defines the default
     * values for most of them.
     *
     * The dictionary returned by this method **must** have the following defined:
     *
     * - `symbolOrder`: Array of strings, which are keys in the second dimension of
     *   the symbols dictionary. ex. $symbols['Hello'][*`symbolOrder`*]
     *
     * After that, you can optionally override the defaults established below. And,
     * extend the possibilities through additional configuration keys you allow and
     * account for in the interception method: `processSymbolTypeForCategory`.
     *
     * @return array [description]
     */
    abstract static protected function definesSymbolsDefaultConfig();

    /**
     * Interception point for customizing configurations and anything else really.
     *
     * Callstack for this Trait:
     *
     * - User of class with trait calls: `definitionListForSymbols`.
     * - `definitionListForSymbols` verifies whether to process a given category.
     *   and symbol type calling: `shouldProcessSymbolDefinitionForCategory`.
     * - `definitionListForSymbols` calls: `processSymbolTypeForCategory`. At which
     *   point you may call `processSymbolsDefinitionForCategory`.
     * - The `$return` array is then imploded and returned by:
     *   `definitionListForSymbols`.
     *
     * Note:
     *
     * - If `$return` is empty, an empty string is returned by:
     *   `definitionListForSymbols`
     * - You are not required to call: `processSymbolsDefinitionForCategory`
     *
     * @param  [type] $category   [description]
     * @param  [type] $symbols    [description]
     * @param  [type] $symbolType [description]
     * @param  [type] $config     [description]
     * @param  [type] &$return    [description]
     * @return [type]             [description]
     */
    abstract protected function processSymbolTypeForCategory($category, $symbols, $symbolType, $config, &$return);

    /**
     * This is the call site for getting a definition list of the symbols.
     *
     * The easiest way to define what is meant by "symbols" in this context is:
     *
     * When you call `classesCategorized` on a project, what is returned is a
     * dictionary of symbols.
     *
     * This method takes the symbols dictionary to interpret as well as a
     * configuration as well as an optional configuration dictionary.
     *
     * There are two areas of customization for you:
     *
     * 1. The default configuration, which you must create. See
     *    `definesSymbolsDefaultString().
     * 2. The method for processing a given symbol type. See
     *    `processSymbolTypeForCategory`.
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
     *                       process. Defaults to empty array.
     * - **onlyCategories:** By default all categories will be processed.
     *                       `onlyCategories` is an array of categories to process.
     *                       Note: If the same category is in both `onlyCategories`
     *                       and `skipCategories`, the category will be processed.
     *                       Defaults to empty array.
     * - **symbolOrder:**    Array defining what order to display the symbols. All the
     *                       following must be present (default order): properties, and
     *                       methods. **No default.**
     *
     * @param  array $config   [description]
     * @param  array $symbols  [description]
     *
     * @return string               [description]
     */
    public function definitionListForSymbols($symbols, $config = [], &$return = [])
    {
        $config['LIST_TYPE'] = 'dl';
        return $this->listForSymbols($symbols, $config, $return);
    }

    public function unorderedListForSymbols($symbols, $config = [], &$return = [])
    {
        $config['LIST_TYPE'] = 'ul';
        return $this->listForSymbols($symbols, $config, $return);
    }

    public function orderedListForSymbols($symbols, $config = [], &$return = [])
    {
        $config['LIST_TYPE'] = 'ol';
        return $this->listForSymbols($symbols, $config, $return);
    }

    private function listForSymbols($symbols, $config = [], &$return = [])
    {
        $config = array_merge($this->definesSymbolsDefaultConfig(), $config);
        $list = [];
        foreach ($symbols as $category => $value) {
            $list[$category] = [];
            $process = $this->shouldProcessSymbolDefinitionForCategory($category, $config);

            if ($process) {
                $symbolOrder = $config['symbolOrder'];
                foreach ($symbolOrder as $symbolType) {
                    if (isset($symbols[$category][$symbolType])) {
                        $syms = $symbols[$category][$symbolType];
                        $this->processSymbolTypeForCategory($category, $syms, $symbolType, $config, $list[$category]);

                    }
                }
            }

            $strings = [];
            foreach ($list[$category] as $key => $elements) {
                foreach ($elements as $elementConfig) {
                    $strings[] = Html5Gen::element($elementConfig);
                }
            }
            $list[$category] = $strings;
        }
        $this->labelForList($list, $config, $return);
        return implode("\n\n", $return);
    }

    private function labelForList($list, $config, &$return)
    {
        $listType = $config['LIST_TYPE'];
        foreach ($list as $category => $symbolStrings) {
            if (count($symbolStrings) > 0) {
                $labelWrapper = (isset($config['labelWrapper']))
                    ? $config['labelWrapper']
                    : 'h2';
                $labelText = (isset($config['label']))
                    ? $config['label']
                    : ($category == 'NO_CATEGORY')
                        ? 'Miscellaneous'
                        : $category;
                $return[] = Html5Gen::$labelWrapper([
                        'content' => $labelText
                    ]);

                $return[] = Html5Gen::$listType(['content' => $symbolStrings]);
            }
        }
    }

    /**
     * Instance alias of `definitionListForSymbols`.
     *
     * Sometimes you will want to maintain state between calls to
     * `processSymbolTypeForCategory` - call this method allows for that.
     *
     * @param  [type] $symbols [description]
     * @param  array  $config  [description]
     * @return [type]          [description]
     */
    public function definitionListFor($symbols, $config = [])
    {
        $return = [];
        $this->definitionListForSymbols($symbols, $config, $return);
        return implode("\n\n", $return);
    }

    private function shouldProcessSymbolDefinitionForCategory($category, $config)
    {
        $onlyCategories = [];
        if (isset($config['onlyCategories'])) {
            $onlyCategories = $config['onlyCategories'];
        }
        $hasOnly = (count($onlyCategories) > 0);
        $inOnly = in_array($category, $onlyCategories);
        // print($category);
        // print($hasOnly);
        // print($inOnly);

        if ($hasOnly && $inOnly) {
            // print('should process');
            return true;
        }

        $skippedCategories = [];
        if (isset($config['skipCategories'])) {
            $skippedCategories = $config['skipCategories'];
        }
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

    /**
     * [processSymbolsDefinitionForCategory description]
     *
     * @param  string $category The category name being processed.
     * @param  array  $symbols  The symbols to process, where the value is the
     *                          symbol. Symbols must have a `largeDeclaration`
     *                          property or __get(table) method that returns a string,
     *                          an 'isDeprecated' property or __get(table) method that
     *                          returns aboolean, a `shortDescription` property or
     *                          __get(table) method that returns a markdown string,
     *                          and a `deprecatedDescription` property or __get(table)
     *                          method that returns a markdown string.
     * @param  [type] $config   [description]
     * @return [type]           [description]
     */
    private function processSymbolsDefinitionForCategory($category, $symbols, $config)
    {
        $listType = $config['LIST_TYPE'];
        $listItem = ($listType == 'ul' || $listType == 'ol')
            ? 'li'
            : 'dt';
        foreach ($symbols as $key => $symbol) {
            $termContent = $symbol->largeDeclaration;
            if ($symbol->isDeprecated) {
                $termContent = [
                    'element' => 'del',
                    'config' => ['content' => $termContent]
                ];
            }

            $categoryContent[] = [
                'element' => $listItem,
                'config' => ['content' => $termContent]
            ];

            if ($listType == 'dl') {
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
        }
        return $categoryContent;
    }
}
