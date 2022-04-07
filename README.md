# Twilight Sparkle

## What?

Twilight Sparkle is a generator for new 3sign projects.

## Installation

### Prerequisites

- Composer should be installed globally
- [globalcomposer]/vendor/bin should be added to your PATH (on OSX /Users/[username]/.composer/vendor/bin )
- Ddev has to be installed

### Installation instructions

1. Install twilight sparkle 

```$ composer global require 3sign/twilightsparkle```


## Creating new projects

Run ```$ twilightsparkle generate:drupal9``` From the Twilight Sparkle directory.

go to your project directory & continue setting up your project with spike.

``` 
$ cd /path/to/project
$ composer spike
```

## Run local (testing)

1. Perform your code changes
2. ```$ sh build.sh```
3. ```$ chmod 755 bin/twilightsparkle```
4. ```$ bin/twilightsparkle```
