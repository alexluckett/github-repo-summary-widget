# GitHub Repository Summary Widget
> AngularJS widget to show a user's public repos

Uses the GitHub GraphQL API to pull in the user's public repositories (max 10) and displays a Bootstrap widget of the top 10. Supports pinned repositories and normal repositories, ordering by pin status and then push date.

The supplied PHP file will pull in all public repos associated with the user who owns the API token, supplied below.

## Installation
Copy `src/github-repos.php` and `src/repo-list-directive.html` into your AngularJS/PHP project. Then configure your AnagularJS app as follows:

#### 1) Copy the Javascript directive into your app (source in example.html)
```javascript
app.directive('githubRepoList', function() {
    // rest of code here from example.html
});
```
The code follows the above format and can be found in example.html. Please copy the full block.
Ensure to change the `templateUrl` setting in the above JS object to the correct path.

#### 2) Within your HTML, instantiate the widget:
```html
<github-repo-list api-url="path/to/github-repos.php"></github-repo-list> 
```
The `githubRepoList` directive takes an URL to the API to get the repository data. This directive supports the `github-repos.php` JSON API, but will work with any API as long as it returns data with the same schema (see the result of `github-repos.php`).

If you change the location of the API (either by using the supplied one or your own), you will need to modify the `api-url` parameter.

**Directive parameters:**

|  Parameter  |  Required |  Default value |  Description                                                                                                                                                                                      |
|-------------|-----------|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| api-url     |  true     |                | URL to retrieve the repository list from. Can be local/remote, but this URL accepts no parameters and must return the same schema as github-repos.php.                                            |
| scrollable  | false     | false          |   Adds scroll buttons to the top and bottom of the widget. Used in combination with list-length to scroll down a list of repos, one at a time, with a maximum repos on screen of list-length.     |
| list-length | false     |                | Maximum number of repos to display on screen at one time. All other repos are hidden, with the exception of when scrollable is set to true and then the user can show/hide others when scrolling. |
#### 3) Supply your Github API key to the `github-repos.php` file on the following line:
```php
$temp = $cache->get_data("github-repo-list", "https://api.github.com/graphql", "API TOKEN HERE", $json_string);
```


## Preview
See [https://alexluckett.uk/](https://alexluckett.uk/) for live link.

![widget_preview](https://github.com/alexluckett/github-repo-summary-widget/raw/master/preview.png)

## Release History
* 0.0.1
    * Initial version for AngularJS and PHP
* 0.0.2
    * Moved widget logic into the directive, which now calls the API itself
    
## Acknowledgements
* Gilbert Pellegrom's SimpleCache
    * https://github.com/gilbitron/PHP-SimpleCache
    * Used within github-repos.php to cache the API request (avoiding repeat calls for each of your website's visitors)
* Glavić's time_elapsed_string function
    * https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago/18602474#18602474
    * Used to convert timestamps into elapsed time in a human readable format

## Meta
Alex Luckett – [@alexluckett](https://twitter.com/alexluckett)

Distributed under the MIT license. See ``LICENSE`` for more information.

[https://github.com/alexluckett/github-repo-summary-widget](https://github.com/alexluckett/github-repo-summary-widget)

## Contributing
1. Fork it (<https://github.com/alexluckett/github-repo-summary-widget>)
2. Create your feature branch (`git checkout -b feature/foo-bar`)
3. Commit your changes (`git commit -am 'Add some foo-bar'`)
4. Push to the branch (`git push origin feature/foo-bar`)
5. Create a new Pull Request
