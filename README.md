# Twitter Bookmarks to Markdown

This is based on the workdone by https://gist.github.com/divyajyotiuk/9fb29c046e1dfcc8d5683684d7068efe, coverted into PHP and optimised for quick use.

## Getting your bookmarks

* Open the Dev Tools in your browser and navigate to your 'Network' tab, ensuring that 'Preserve Log' is turned off.
* Visit your [Twitter Bookmarks page](https://twitter.com/i/bookmarks), then hit refresh (to ensure you get the first page of results).
* Run this code in your browser's console: https://gist.github.com/duncangh/c49d25d9c352fc0bdfaec5d281e6fd29. This will continually scroll your browser until it reaches the end of your bookmarks page. If at any time you get an error, just click 'try again' and let it scroll automatically.
* Once you've reached the bottom of your bookmarks, press the 'Export HAR' button on the Network tab. Save this as 'twitter.com.har'.

## Converting HAR to JSON

The HAR file is just a JSON file with all your requests from the current page. We're going to parse this to get bookmarks!

* Add 'twitter.com.har' to the current directory.
* Run php har.php from your terminal.
* You'll see a new folder created, 'Bookmarks' containing each page from your export.

## Converting JSON to Markdown

Finally, the magic happens. Just run 'get.php' from your terminal.

This will read each file and add it to your new 'bookmarks.md' file.