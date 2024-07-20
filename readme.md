
# AIEntries Plugin

This plugin uses Google artificial intelligence (GEMINI) and stability.AI to automate the creation of standard WordPress posts based on configurable parameters from the WordPress admin view. It can create posts from any topic you configure from the administrator view. To ensure quality content, this tool is integrated with several free-to-use APIs to fulfill its functionality.

## This plugin is aware of Google's advice on AI content: 
https://developers.google.com/search/blog/2023/02/google-search-and-ai-content?hl=es-419

In order to follow they guidelines we are doing these processes:

### News API:

we use this api to get real articles

https://newsapi.org/docs

### Google Gemini API:

We use this api to generate original content based in real one

https://ai.google.dev/gemini-api?hl=es-419

### Stability AI:

We use this api to generate post's featured imaged based on ai generated article's title .

https://stability.ai/


### DISCLAIMER : THIS IS AN IN PROGRESS PROJECT .


![politic](<Captura de pantalla de 2024-07-20 16-59-02.png>)


![travel tips](<Captura de pantalla de 2024-07-20 16-59-53.png>)


![bitcoin](<Captura de pantalla de 2024-07-20 17-47-03.png>)


![UFO](<Captura de pantalla de 2024-07-20 18-01-57.png>)

# Getting started!  💥   🚀

## Requirements  ✅

You need to have a NEWS API's API KEY to use this plugin. 

You can get one for free here : https://newsapi.org/docs

You need to have a GEMINI API KEY to use this plugin. 

You can get one for free here : https://ai.google.dev/gemini-api/docs/api-key

You need to have a stability.ai API KEY to use this plugin. 

You can get one for free here : https://platform.stability.ai/

## Run this project locally  💻  💻

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

### environment commands 

Install dependencies

```bash
  npm i
```

Start local environment (this install all dependencies including wordpress and composer /phpunit for unit tests)

```bash
  make start
```


Run all unit test in ai-entries/tests directory (see makefile to the entire command config)

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

# Docker issues  🐳 : 

Remove containers (Docker)

```bash
  docker rm -f $(docker ps -a -q)
```

Remove images (Docker)

```bash
  docker rmi -f $(docker images -q)
```

# Unit tests  💊 📄

This project is configured to accept unit tests written in the ai-entries/tests directory

To know more about how phpunit works: https://phpunit.de/manual/6.5/en/textui.html

### About wordpress/env : 

https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/

### About WP_Mock : 

https://wp-mock.gitbook.io/documentation

### npm package (installed as dependency in this project):

https://www.npmjs.com/package/@wordpress/env


## Support 📞 📬

For support, email info@glidestay.com .
