Google APIs Client Library for PHP
==================================

###Description
The Google API Client Library enables you to work with Google APIs such as Buzz, Moderator, Tasks, or Latitude on your server.

###Requirements:
- [PHP 5.2.x or higher](http://www.php.net/)
- [PHP CURL extension](http://www.php.net/manual/en/intro.curl.php)
- [PHP JSON extension](http://php.net/manual/en/book.json.php)

###Basic Example
```php
<?php
  /*
   * Load Google core apiClient
   */
  require_once 'path/to/src/apiClient.php';
  
  /*
   * Load the Books Service
   */
  require_once 'path/to/src/contrib/apiBooksService.php';

  /*
   * Instantiate apiClient and apiBookService
   */
  $client = new apiClient();
  $service = new apiBooksService($client);

  /*
   * list books by *Henry David Thoreau* and filter by free-ebooks
   * Return Array of items to result variable
   */
  $results = $service->volumes->listVolumes('Henry David Thoreau', array(
      'filter' => 'free-ebooks'
  ));

  /*
   * Loop the results[items] to display each book title
  */
  foreach ($results['items'] as $book)
  {
      echo sprintf("Book: %s", $book['volumeInfo']['title']);
  }
?>
```

###Project page:
- [Official API Product Page](http://code.google.com/p/google-api-php-client)

###OAuth 2 instructions:
- [Oauth Instructions](http://code.google.com/p/google-api-php-client/wiki/OAuth2)

###Report a defect or feature request here:
- [Official API issue tracker](http://code.google.com/p/google-api-php-client/issues/entry)

###Subscribe to project updates in your feed reader:
- [Official Updates](http://code.google.com/feeds/p/google-api-php-client/updates/basic)

###Supported sample applications:
- [Official Sample Applications](http://code.google.com/p/google-api-php-client/wiki/Samples)