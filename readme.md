
# AIEntries Plugin

This plugin uses Google artificial intelligence (GEMINI) and stability.AI to automate the creation of standard WordPress posts based on configurable parameters from the WordPress admin view.

![image](https://github.com/user-attachments/assets/f01bf1a9-a2b9-4bfd-9995-2756219be659)

![image](https://github.com/user-attachments/assets/81b1e192-83a7-40f1-b856-b4f33f007f1c)


# Getting started!  💥   🚀

## Requirements  ✅

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
