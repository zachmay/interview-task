# interview-task
Starting point for interview coding exercise

## Setup

1. Clone this repo into your working directory.
2. Install [Composer](https://getcomposer.org/doc/00-intro.md) into you working directory.
3. Find the `guzzlehttp/guzzle` and `symphony/dom-crawler` packages on [Packagist.org](https://packagist.org/) and use Composer to add them to your project. Make sure that the two libraries are installed in the `vendor/` directory and an `autoload.php` has been generated in `vendor/`. 

## Task

We're going to do some webcrawling. You will write a small PHP tool that will take a web page URL, parse the HTML to find link tags (`<a>`), and follow those links recursively up to a specified depth.

1. Create an HTML form that allows the user to enter:
    - A URL, the web page to load
    - A number, the maximum depth to follow links (e.g., depth 1 would only display the links on the entered page, depth 2 would display the links on the pages linked to from the first page).
2. On submit:
    - Use [Guzzle](http://docs.guzzlephp.org/en/latest/) to fetch the HTML of the specified URL. (The whole script shouldn't fail if fetching the contents of the URL fails, but save the failure details to report later.)
    - Pass the response body of the HTTP request into [`DomCrawler`](http://symfony.com/doc/current/components/dom_crawler.html) to parse the HTML.
    - For each `<a>` tag in the resulting DOM tree, note the URL the link points to and the text of the link. Then, recursively call this procedure with that URL if we haven't yet reached the specified depth. You can ignore `<a>` tags without an `href` attribute or whose `href` values don't start with `"http"`.
    - You may want to organize this in an associative array like this:

```
[
    [
        "linkText" => "Level 1, Link 1",
        "url" => "http://www.webpage.com/1/1"
        "descendants" => [
            [
                "linkText" => "Level 2, Link 1"
                "url" => "http://www.webpage.com/2/1"
                "descendants" => [ /* No Descendants */ ]
            ],
            [
                "linkText" => "Level 2, Link 2"
                "url" => "http://www.webpage.com/2/2"
                "error" => "404 Not Found"
            ]
        ]
    ],
    [
        "linkText" => "Level 1, Link 2",
        "url" => "http://www.webpage.com/1/2"
        "descendants" => [
            [
                "linkText" => "Level 2, Link 3"
                "url" => "http://www.webpage.com/2/3"
                "descendants" => []
            ],
            [
                "linkText" => "Level 2, Link 4"
                "url" => "http://www.webpage.com/2/4"
                "descendants" => [
                    [
                        "linkText" => "Level 3, Link 1"
                        "url" => "http://www.webpage.com/3/1"
                        "descendants" => []
                    ]
                ]
            ]
        ]
    ]
]
```

Once you have a data structure like this, iterate through it (probably recursively), and produce nested unordered lists to display the info you gathered. Something like this:

- http://www.webpage.com
    - Level 1, Link 1 - http://www.webpage.com/1/1
        - Level 2, Link 1 - http://www.webpage.com/2/1
            - No descendants
        - Level 2, Link 2 - http://www.webpage.com/2/2
            - Error: 404 Not Found
    - Level 1, Link 2 - http://www.webpage.com/1/2
        - Level 2, Link 3 - http://www.webpage.com/2/3
            - No descendants
        - Level 2, Link 4 - http://www.webpage.com/2/4
            - Level 3, Link 1 - http://www.webpage.com/3/1
                - No descendants

## Questions
- What special care would you need to take in a program like this?
- Why might you want to be careful about exposing a page like this to the general public?
- What is your approach to testing a piece of code like this? How did the design of the code help or hurt testability?
