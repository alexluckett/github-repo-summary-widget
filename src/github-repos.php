<?php

/*
 * SimpleCache v1.4.1
 *
 * By Gilbert Pellegrom
 * http://dev7studios.com
 *
 * Modified by Alex Luckett to work with GitHub API and retain files for longer
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
class SimpleCache {

	// Path to cache folder (with trailing /)
	public $cache_path = 'cache/';
	// Length of time to cache a file (in seconds)
	public $cache_time = 36000; // alexluckett modification: 10 hours
	// Cache file extension
	public $cache_extension = '.cache';
    public $ignore_ssl_host_verification = false;
    
    public function __construct($ssl_verification = false) {
        if(!is_dir($this->cache_path)) { // alexluckett modification: create cache path if it doesn't exist
          mkdir($this->cache_path);
        }
        
        $this->ignore_ssl_host_verification = $ssl_verification;
    }

	// This is just a functionality wrapper function
	public function get_data($label, $url, $token, $query)
	{
        if($data = $this->get_cache($label)){
			return $data;
		} else {
			$data = $this->do_curl($url, $token, $query);
			$this->set_cache($label, $data);
			return $data;
		}
	}

	public function set_cache($label, $data)
	{
		file_put_contents($this->cache_path . $this->safe_filename($label) . $this->cache_extension, $data);
	}

	public function get_cache($label)
	{
		if($this->is_cached($label)){
			$filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;
			return file_get_contents($filename);
		}

		return false;
	}

	public function is_cached($label)
	{
		$filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;

		if(file_exists($filename) && (filemtime($filename) + $this->cache_time >= time())) return true;

		return false;
	}

	//Helper function for retrieving data from url
	public function do_curl($url, $token, $query)
	{
		if(function_exists("curl_init")){
            $ch = curl_init();
            
            if ($this->ignore_ssl_host_verification) { // alexluckett modification: used for local testing
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            
            // Alex Luckett modification: add API token to request
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer ".$token
            ));
            
			curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // alexluckett modification: GitHub API uses POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);  // alexluckett modification: Send JSON query below to API as GraphQL instead of using their REST API GET params
            curl_setopt($ch, CURLOPT_USERAGENT, "alexluckett-github-repo-widget");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
			$content = curl_exec($ch);
            
            if(!$content) {
                $error_text = curl_error($ch);
                
                $content = '{ "error": "' . "$error_text" . '", "data": {} }';
                
                throw new Exception($content);
            }
            
			curl_close($ch);
            
			return $content;
		} else {
			return file_get_contents($url);
		}
	}

	//Helper function to validate filenames
	private function safe_filename($filename)
	{
		return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
	}
}


$json_string = <<<JSONSTRING
{
    "query": "{
  viewer {
    pinnedRepositories(first: 10) {
      nodes {
        name,
        nameWithOwner,
        description,
        pushedAt,
        url,
        primaryLanguage{
          name,
          color
        },
        repositoryTopics(first: 5) {
          nodes {
            topic {
              name
            }
          }
        }
      }
    },
    repositories(first: 10, privacy: PUBLIC, orderBy: {field: PUSHED_AT, direction: DESC}) {
      nodes {
        name
        nameWithOwner
        description,
        pushedAt,
        url,
        primaryLanguage {
          name
          color
        }
        repositoryTopics(first: 5) {
          nodes {
            topic {
              name
            }
          }
        }
      }
    }
  }
}"
}
JSONSTRING;

// Author: Glavić @ StackOverflow
// https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago/18602474#18602474
// converts a time into elapsed time, human readable
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// orders the github repos, pinned repos first and then all others are sorted by their latest push.
function order_repos(&$pinned_repos, $other_repos) {
    $indexed = array();
    
    foreach($pinned_repos as $position=>&$repo) {
        $repo["starred"] = true;
        $indexed[$repo["name"]] = $repo;
    }
    
    foreach($other_repos as $repo) {
        if(!isset($indexed[$repo["name"]])) {
            $indexed[$repo["name"]] = $repo;
        }
    }
    
    return array_values($indexed);
}

// adds a human readable elapsed time to each repo
function add_last_modified_human_readable(&$repos) {
    foreach($repos as &$repo) {
        $timestamp = $repo["pushedAt"];
        
        $repo["last_modified_human"] = time_elapsed_string($timestamp);
    }
}


$json_string = str_replace(array("\r\n", "\n", "\r"), ' ', $json_string); // clean up the JSON so GitHub will parse it

try {
    $cache = new SimpleCache(true);
    $temp = $cache->get_data("github-repo-list", "https://api.github.com/graphql", "API KEY HERE", $json_string);
    
    $json_data = json_decode($temp, true);
    
    $temp_as_json = $json_data["data"]["viewer"];

    $other_repos = $temp_as_json["repositories"]["nodes"];
    $pinned_repos = $temp_as_json["pinnedRepositories"]["nodes"];
    $merged_repos = order_repos($pinned_repos, $other_repos); // merge both pinned and normal repos

    add_last_modified_human_readable($merged_repos);

    $response = array();
    $response["data"]["repositories"] = $merged_repos;

    echo (json_encode($response));  // send data back to API client in JSON form
} catch(Exception $e) {
    echo ($e->getMessage());
}