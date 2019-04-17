# TBcom-Lib
Core library for my website, in the form of Composer package.

## How to use this

Create an [example PHP file](https://raw.githubusercontent.com/Babkock/TBcom-Lib/examples/index.php) to use this library.

```
namespace TBcom;
require_once("vendor/Babkock/TBcom-Lib/TBcom.php");
require_once("vendor/Babkock/TBcom-Lib/Log.php");

Methods::Cache(__FILE__);

// write a line like this to use Zend OpCache

$mypage = new Build\Page("header", "index", "footer");

// Initialize the page title and description like this

$mypage->init("Hello World", "This is my page description.", 2);

// The last argument can be anything, edit the pagecodes in Codes.php to make ones that work for you

$mypage->middle->replace("PLACEHOLDER_TEXT", "Hello world!");

$mypage->output();

```

The line that creates the page object, **`new Build\Page()`** can take up to three arguments, and at least two. The first argument is the page's header template, it's picked from a file in "/resources/views/".

The second argument is the "middle" of the page. The third argument has a default value of "footer", which translates to "/resources/views/footer.html". If you want to use a different file for the footer, you can supply a third argument.

Have a look at the [example HTML templates](https://raw.githubusercontent.com/Babkock/TBcom-Lib/master/examples/resources/views/index.html) in /examples/resources/views.
