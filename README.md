# GitHub Repository Summary Widget
> AngularJS widget to show a user's public repos

Uses the GitHub GraphQL API to pull in the user's public repositories (max 10) and displays a Bootstrap widget of the top 10. Supports pinned repositories and normal repositories, ordering by pin status and then push date.

## Installation
Copy `src/github-repos.php` and `src/repo-list-directive.html` into your AngularJS/PHP project. Then configure your controller as follows

```
app.controller('controller-example', function($scope, $http) {
    $scope.repos = null;

    $http.get("github-repos.php").then(function(response) {
        $scope.repos = response.data["data"]["repositories"]; // get the repos from the API wrapper
    });
});

app.directive('repoList', function() {
    return {
        restrict: "E",
        scope: {
            repos: '='
        },
        templateUrl: 'repo-list-directive.html'  // template with content
  }; 
});
```

Within your HTML, you can then use the directive as normal:

```
<repo-list repos="repos"></repo-list>
```

The `repoList` directive takes in the list of repos, which would usually come from the API (see `$scope.repos` in the controller). You could provide a custom list if needed, which will work as long as it follows the same schema as the API's response (within `response["data"]["repositories"]`.

If you change the location of the repo, you will need to modify the AngularJS GET request to reflect the PHP file's new location.

## Release History
* 0.0.1
    * Initial version for AngularJS and PHP
    
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
