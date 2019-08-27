#Twilight Sparkle

##whois?

Twilight Sparkle is a generator for new 3sign projects.

##Installation

1. install robo globally ```composer global require consolidation/robo```

2. Clone te repo to your computer

````$ git clone git@codebasehq.com:3sign/3sign/twilight_sparkle.git```

2. Go into the Twilight Sparkle folder
3. install Twilight Sparkle

````$ robo install````

## Creating new projects

Run ```$ robo generate:drupal8``` From the Twilight Sparkle directory.

## Creating an alias

Add the following alias to your .zshrc, ... file

```alias newproject="robo generate:drupal8 --load-from path/to/twilight_sparkle"```