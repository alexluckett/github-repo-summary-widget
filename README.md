# GitHub Repository Summary Widget
> AngularJS widget to show a user's public repos

Uses the GitHub GraphQL API to pull in the user's public repositories (max 10) and displays a Bootstrap widget of the top 10. Supports pinned repositories and normal repositories, ordering by pin status and then push date.

## Installation
Copy `src/github-repos.php` and `src/repo-list-directive.html` into your AngularJS/PHP project. Then configure your app as follows:

```javascript
app.directive('repoList', function() {
    return {
        restrict: "E",
        scope: {
            apiUrl: '@'
        },
        templateUrl: 'path/to/repo-list-directive.html',
        controller:function($scope, $http){
            console.log("Directive controller called");

            let api_url = $scope.apiUrl;

            $scope.repos = null;

            console.log("Calling API using URL: " + api_url);
            $http.get(api_url).then(function(response) {
                $scope.repos = response.data["data"]["repositories"];
            }), function(result) {
                console.log("Error calling API using URL: " + api_url + ". Result: ");
                console.log(result)
            };
        }
  };
});
```

Within your HTML, you can then use the directive as normal:

```
<repo-list api-url="path/to/github-repos.php"></repo-list> 
```

The `repoList` directive takes an URL to the API to get the repository data. This directive supports the `github-repos.php` JSON API, but will work with any API as long as it returns data with the same schema (see the result of `github-repos.php`).

If you change the location of the API (either by using the supplied one or your own), you will need to modify the `api-url` parameter. Also ensure to change the `templateUrl` setting in the above JS bject to the correct path.

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
