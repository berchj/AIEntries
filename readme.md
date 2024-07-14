
# AIEntries Plugin

This plugin uses Google artificial intelligence (GEMINI) and stability.AI to automate the creation of standard WordPress posts based on configurable parameters from the WordPress admin view.


## Requirements 

You need to have a GEMINI API KEY to use this plugin. 

You can get one for free here : https://ai.google.dev/gemini-api/docs/api-key

You need to have a stability.ai API KEY to use this plugin. 

You can get one for free here : https://platform.stability.ai/

## Run this project locally

### pre-installs

You need to have installed first (we recomend run thi project in a linux debian based distribution): 

* nodejs
* npm
* Docker


Clone the project

```bash
  git clone https://github.com/berchj/AIEntries.git
```

Go to the project directory

```bash
  cd AIEntries
```

Install dependencies

```bash
  npm i
```

### environment commands 

Start local environment

```bash
  make test
```

Clean environments

```bash
  make clear
```

Debug environment

```bash
  make debug
```

Destroy environment

```bash
  make destroy
```

Make .zip to upload to wordpress

```bash
  make zip
```

### About wordpress/env : 

https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/


### npm package (installed as dependency in this project):

https://www.npmjs.com/package/@wordpress/env

## Form Features

* Input to ask you what topic to create the posts 
* Input to ask how many Posts to create
* Input to store google's gemini api key
* Input to store stability ai api key
* Input to store the category that will be added to the posts (creates if no exists)
* Generation of featured image based on post's title and attach to post. 
* The plugin runs as a daily cron wordpress task 
## Support

For support, email info@glidestay.com .
