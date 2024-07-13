
# AIEntries Plugin

This plugin uses Google's artificial intelligence (GEMINI) to automate the creation of WordPress Posts based on configurable parameters from the WordPress administrator view.


## Requirements 

You need to have a GEMINI API KEY to use this plugin. 

You can get one for free here : https://ai.google.dev/gemini-api/docs/api-key?hl=es-419

## Run this project locally

You need to have installed first: 

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



## Features

* Input to ask you what topic to create the post on
* Input to ask how many Posts to create
* Input to store the api key
* Input to store the category that will be added to the posts
* The plugin runs as a daily task
## Support

For support, email info@glidestay.com .
