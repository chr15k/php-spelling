<?php

namespace Chr15k\Spelling;

use Chr15k\String\Str;

class Spelling
{
    /**
     * pspell link.
     *
     * @var int $link
     */
    protected $link;

    /**
     * Cache the suggestion list.
     *
     * @var array
     */
    protected static $suggestionCache = [];

    /**
     * Cache the auto suggestion.
     *
     * @var array
     */
    protected static $autoSuggestionCache = [];

    /**
     * Cache the suggestion list.
     *
     * @var array
     */
    protected static $checkCache = [];

    /**
     * Accepted punctuation list.
     *
     * @var array
     */
    protected $punct;

    /**
     * Spelling constructor.
     *
     * @return void
     */
    public function __construct($lang = 'en')
    {
        $this->link = pspell_new(
            $lang, '', '', '', (PSPELL_FAST)
        );

        if (! $this->link) {
            throw new \Exception('Dictionary link error occurred');
        }

        $this->punct = [',', '.', ':', ';', '?', '!', '%', '*', '(', ')'];
    }

    /**
     * Check the spelling of a single word.
     *
     * @param  string $value
     * @return bool
     */
    public function check(string $value = '')
    {
        if (isset(static::$checkCache[$value])) {
            return static::$checkCache[$value];
        }

        return static::$checkCache[$value] = pspell_check($this->link, $value);
    }

    /**
     * Return an array of suggestions if the
     *
     * @param  string $value
     * @return array|null
     */
    public function suggestions(string $value = '')
    {
        $value = $this->stripPunctuationFromValue($value);

        if (! static::check($value)) {
            if (isset(static::$suggestionCache[$value])) {
                return static::$suggestionCache[$value];
            }

            return static::$suggestionCache[$value] = pspell_suggest($this->link, $value);
        }
    }

    /**
     * Auto suggest a word.
     *
     * @param  string $value
     * @return string
     */
    public function autoSuggestion(string $value = '')
    {
        if (isset(static::$autoSuggestionCache[$value])) {
            return static::$autoSuggestionCache[$value];
        }

        $suggestions = $this->suggestions($value);

        if (empty($suggestions)) {
            return static::$autoSuggestionCache[$value] = $value;
        }

        // Use levenshtein algorithm to determine the closest matching word from the list.
        $closest = $this->determineClosestMatch($suggestions, $value);

        // Convert to plural if the original value ends with an s or an apostrophe
        $closest = (Str::endsWith($this->stripPunctuationFromValue($value), ['s', "'"]))
            ? Str::plural($closest)
            : Str::singular($closest);

        // append punctuation back to new value
        if (Str::endsWith($value, $this->punct)) {
            $closest .= substr($value, -1);
        }

        // prepend punctuation back to new value
        if (Str::startsWith($value, $this->punct)) {
            $closest = substr($value, 0, 1) . $closest;
        }

        return static::$autoSuggestionCache[$value] = $closest;
    }

    public function autoCorrection($words = '')
    {
        $corrected = [];
        $words = explode(' ', $words);

        foreach ($words as $word) {
            $corrected[] = $this->autoSuggestion($word);
        }

        return implode(' ', $corrected);
    }

    private function stripPunctuationFromValue($value = '')
    {
        return str_replace($this->punct, '', trim($value));
    }

    private function determineClosestMatch($suggestions = [], $value = '')
    {
        // If there is only 1 match, just return the value.
        if (count($suggestions) === 1) {
            return reset($suggestions);
        }

        // no shortest distance found, yet
        $shortest = -1;

        foreach ($suggestions as $suggestion) {

            // ignore suggestions with dashes, spaces, or apostrophes
            if (Str::contains($suggestion, ['-', ' ', '\''])) {
                continue;
            }

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($value, $suggestion);

            // check for an exact match
            if ($lev == 0) {

                // closest word is this one (exact match)
                $closest = $suggestion;
                $shortest = 0;

                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest  = $suggestion;
                $shortest = $lev;
            }
        }

        return $closest;
    }
}
