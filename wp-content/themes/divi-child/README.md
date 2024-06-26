# DIVI SASS SETUP

## Features!

  - Divi Builder
  - SASS Compiler
  - JS Minifier

 ## Contents
  - [Installation](#installation) 
  - [JS Libraries](#javascript-libraries) 

## Installation
---
Divi Sass Setup requires [Node.js](https://nodejs.org/) to run.
Install the dependencies and devDependencies and start the server.
Globalised the installation of gulp
```sh
$ npm install -g gulp
```

## For development environments.

**Create wp-config.php based on the wp-config-sample.php and update the setting for development**
- make sure to rename **line 42 - 52** to your project name/virtual name of your project

Install the Developer dependencies packages from package.json.
```sh
$ npm install
```
To migrate libraries that is added in the package or node_modules inside your theme.
```sh
$ gulp migrate
```

To work and compile your Sass files and JS files on the fly start:
```sh
$ gulp start
```

To work and make use of the **Browser Sync Feature**:

- make sure to update the gulpfile.js **line 65** to the current project name/virtual name of your project.

```sh
$ gulp sync
```

This command is needed upon finalising the project for production.
Removed Files:
- node_modules
- libraries
- package-lock.json
- .gitignore
- .npmignore

```sh
$ gulp restart
```

This command is needed in case you need to reinstall the GULP setup.
Removed Files:
- node_modules
- libraries
- package-lock.json
- package.json
- package.json
- gulpconfig.json
- gulpfile.js

```sh
$ gulp release
```
## Javascript Libraries

  - [Popup Usage](#popup-usage) 
  - [Match Height](#match-height) 
  - [Rellax JS](#rellax-js)
  - [Parallax JS](#parallax-js) 

### Popup Usage
---
Library link: [https://github.com/biati-digital/glightbox](https://github.com/biati-digital/glightbox)
##### Data attribute driven.

***Images***
Just add to the button/link that has id with ***data-glightbox="imagebox"***
```sh
<a id="imgid-1" data-glightbox="imagebox" href="https://dummyimage.com/900x500/000/fff.jpg&text=DreamPress+Image">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Image" alt="image" />
</a>
```

***HTML/IFRAME***
Just add to the button/link that has id with ***data-glightbox="htmlbox"***
```sh
<a id="htmlid-1" data-glightbox="htmlbox" href="#gmap_canvas">CLICK HERE!</a>
<iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=Upper%20East%20Side%20New%20York,%20NY,%20USA&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="display: none;">
</iframe>
```

***VIDEO***
Just add to the button/link that has id with ***data-glightbox="videobox"***
```sh
<a href="https://www.youtube-nocookie.com/embed/kTS9ltbsvWc" id="vid-1" data-glightbox="videobox">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Video" alt="image" />
</a>
<a href="https://vimeo.com/115041822" id="vid-2" data-glightbox="videobox">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Video" alt="image" />
</a>
```

##### Class driven.

***Images***
Just add class ***class="dp-image"***
```sh
<a class="dp-image" href="https://dummyimage.com/900x500/000/fff.jpg&text=DreamPress+Image">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Image" alt="image" />
</a>
```

***HTML/IFRAME***
Just add class ***class="dp-html"***
```sh
<a class="dp-html" href="#gmap_canvas">CLICK HERE!</a>
<iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=Upper%20East%20Side%20New%20York,%20NY,%20USA&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" style="display: none;">
</iframe>
```

***VIDEO***
Just add class ***class="dp-video"***
```sh
<a href="https://www.youtube-nocookie.com/embed/kTS9ltbsvWc" class="dp-video">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Video" alt="image" />
</a>
<a href="https://vimeo.com/115041822" id="vid-2" class="dp-video">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Video" alt="image" />
</a>
```
### Match Height
---
Library link: [https://github.com/liabry/jquery-match-height](https://github.com/liabru/jquery-match-height)

##### Class driven.
Just add class ***class="block"***
```sh
<div class="block" href="https://dummyimage.com/900x500/000/fff.jpg&text=DreamPress+Image">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Image" alt="image" />
</div>
```

##### Data driven.
Just add data attribute ***data-mh="block"***
```sh
<div data-mh="data_attribute_name" href="https://dummyimage.com/900x500/000/fff.jpg&text=DreamPress+Image">
    <img src="https://dummyimage.com/600x333/000/fff.jpg&text=DreamPress+Image" alt="image" />
</div>
```
### Rellax JS
---
Library link: [https://github.com/dixonandmoe/rellax](https://github.com/dixonandmoe/rellax)

To enable this library please follow the following steps.

- Please uncomment ***"libJS + 'rellax/rellax.min.js',"*** in Javascript Library Addons on line 41
- Please uncomment also the function callback on line 80
- then run on command line ***gulp start*** or ***gulp sync***

### Parallax JS
---
Library link: [https://github.com/wagerfield/parallax](https://github.com/wagerfield/parallax)

To enable this library please follow the following steps.

- Please uncomment ***"libJS + 'parallax-js/parallax.min.js',"*** in Javascript Library Addons on line 41
- Please uncomment also the function callback on line 80
- then run on command line ***gulp start*** or ***gulp sync***

###### By: Jude Roerick Cabigas - Web Developer
