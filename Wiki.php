<?php
/**
 * Class Wiki
 * A class for retrieving and processing content from Wikipedia based on a specified language.
 */
class Wiki {
    /**
     * @var string|null $lang The language code for Wikipedia (default is 'en').
     */
    public $lang;

    /**
     * @var DOMDocument $dom An instance of DOMDocument used for parsing HTML content.
     */
    public $dom;

    /**
     * @var bool $stop A flag used to terminate content extraction when specific sections are reached.
     */
    public $stop;

    /**
     * Wiki constructor.
     * @param string|null $lang The language code for Wikipedia (optional).
     */
    public function __construct(string $lang = null) {
        $this->lang = $lang ?: 'en';
        libxml_use_internal_errors(true);
        $this->dom = new DOMDocument();
    }

    /**
     * Fetches content from a given URL.
     * @param string $url The URL to fetch data from.
     * @return string|null The fetched content or null if an error occurs.
     */
    private function fetchContent(string $url): ?string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:133.0) Gecko/20100101 Firefox/133.0');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode != 200) {
            return json_encode([
                "Error" => "HTTP $httpCode: Unable to fetch content from $url",
                "Details" => $error
            ]);
        }

        return $response;
    }

    /**
     * Searches Wikipedia for a given text.
     * @param string $text The text to search for.
     * @return array An array containing the search results or error messages.
     */
    function search(string $text) {
        $url = "https://{$this->lang}.wikipedia.org/wiki/" . urlencode($this->wiki_text_gen($text));

        $html = $this->fetchContent($url);
        

        $this->dom->loadHTML($html);
        $bodyContent = $this->dom->getElementById("bodyContent");
        if (!$bodyContent) {
            return [
                "Error" => "Failed to locate the main content on the Wikipedia page.",
            ];
        }

        $this->removeSections($bodyContent);

        $markdown = "";
        $this->extractContent($bodyContent, $markdown);

        return [
            "data" => $markdown,
        ];
    }

    /**
     * Removes specific sections from the content.
     * @param DOMNode $node The DOM node to process.
     */
    private function removeSections($node) {
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === 'h2' && stripos($child->textContent, 'جستارهای وابسته') !== false) {
                break;
            }

            if ($child->hasChildNodes()) {
                $this->removeSections($child);
            }
        }
    }

    /**
     * Extracts and converts content into Markdown format.
     * @param DOMNode $node The DOM node to process.
     * @param string $markdown A reference to the Markdown string being built.
     */
    private function extractContent($node, &$markdown) {
        if (!$this->stop) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeName === 'h2' && stripos($child->textContent, 'جستارهای وابسته') !== false) {
                    $this->stop = true;
                    break;
                }

                if ($child->nodeName === 'span' && strpos($child->getAttribute('class'), 'math') !== false) {
                    $latex = $child->textContent;
                    $markdown .= "\n$$" . trim($latex) . "$$\n";
                }

                if (in_array($child->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
                    $markdown .= "\n" . str_repeat("#", intval(substr($child->nodeName, 1))) . " " . trim($child->textContent) . "\n";
                }

                if ($child->nodeName === 'p') {
                    $markdown .= "\n" . trim($child->textContent) . "\n";
                }

                if (in_array($child->nodeName, ['strong', 'em', 'b', 'i', 'span'])) {
                    $markdown .= trim($child->textContent) . "\n";
                }

                if ($child->hasChildNodes()) {
                    $markdown = $this->cleanContent($markdown);
                    $this->extractContent($child, $markdown);
                }
            }
        }
    }

    /**
     * Cleans the Markdown content by removing unwanted characters.
     * @param string $content The content to clean.
     * @return string The cleaned content.
     */
    private function cleanContent(string $content): string {
        return str_replace(['[]', "[" . PHP_EOL . "]"], ['', ''], $content);
    }

    /**
     * Converts a text string to a Wikipedia-compatible format.
     * @param string $text The text to convert.
     * @return string The formatted text.
     */
    function wiki_text_gen(string $text) {
        return str_replace(" ", "_", $text);
    }
}
