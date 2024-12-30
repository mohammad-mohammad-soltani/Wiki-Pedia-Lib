# Wiki Class Documentation 📖
### Mohammad Mohammad soltani

## Overview ✨
The `Wiki` class provides a way to fetch and process content from Wikipedia based on a specified language. This class includes methods for retrieving content, converting it to Markdown, and managing specific sections of Wikipedia articles.

---
## Features 🚀
- **Customizable Language:** Retrieve articles in your preferred language by specifying the language code.
- **Content Scraping:** Fetch Wikipedia articles and process their content using DOM parsing.
- **Markdown Conversion:** Convert article content into Markdown format for easy integration.
- **Section Removal:** Remove unwanted sections (e.g., “جستارهای وابسته”) from articles.
- **Error Handling:** Provides detailed error reports for better debugging.

---

## Class Properties 🏗️

### `public string|null $lang`
Specifies the language code for Wikipedia (default: `'en'`).

### `public DOMDocument $dom`
An instance of `DOMDocument` used for parsing HTML content.

### `public bool $stop`
A flag to control the termination of content extraction when specific sections are encountered.

---

## Methods 📜

### `__construct(string $lang = null)`
Initializes the `Wiki` class with a specified language. Defaults to English (`'en'`).

### `search(string $text): array`
Searches Wikipedia for the given text and returns the content in Markdown format.
- **Parameters:**
  - `string $text`: The text to search for.
- **Returns:**
  - `array`: Contains the search results or error messages.

### `private fetchContent(string $url): ?string`
Fetches content from a given URL.
- **Parameters:**
  - `string $url`: The URL to fetch data from.
- **Returns:**
  - `string|null`: The fetched content or `null` if an error occurs.

### `private removeSections(DOMNode $node)`
Removes specific sections (e.g., "جستارهای وابسته") from the content.
- **Parameters:**
  - `DOMNode $node`: The DOM node to process.

### `private extractContent(DOMNode $node, string &$markdown)`
Extracts content from a given DOM node and converts it into Markdown format.
- **Parameters:**
  - `DOMNode $node`: The DOM node to process.
  - `string &$markdown`: A reference to the Markdown string being built.

### `private cleanContent(string $content): string`
Cleans the Markdown content by removing unwanted characters.
- **Parameters:**
  - `string $content`: The content to clean.
- **Returns:**
  - `string`: The cleaned content.

### `wiki_text_gen(string $text): string`
Converts a text string to a Wikipedia-compatible format by replacing spaces with underscores.
- **Parameters:**
  - `string $text`: The text to convert.
- **Returns:**
  - `string`: The formatted text.

---

## Example Usage 💻
```php
require 'Wiki.php';

// Initialize Wiki class with default language (English)
$wiki = new Wiki();

// Search for an article
$result = $wiki->search("Artificial Intelligence");

if (isset($result['Error'])) {
    echo "Error: " . $result['Error'] . "\n";
    echo "Details: " . $result['Details'] . "\n";
} else {
    echo "Markdown Content:\n" . $result['data'] . "\n";
}
```

---

## Notes 📝
- Ensure that the `curl` and `libxml` extensions are enabled in your PHP configuration.
- Handle errors gracefully by checking the returned array for an `Error` key.

---

## Future Enhancements 🌟
- Add support for multilingual content parsing.
- Integrate additional formatting options for Markdown output.
- Optimize error reporting for edge cases.

---

Enjoy using the `Wiki` class for your Wikipedia-related projects! 🌐

