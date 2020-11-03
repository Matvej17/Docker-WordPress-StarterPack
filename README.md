## Main Features

- docker-compose
- webpack4
- hot module reloading
- es6+ with babel and runtime
- eslint (recommended)
- jQuery
- scss, postcss, autoprefixer
- stylelint (recommended)

## Use

### local development with docker-compose
run `docker-compose up -d`. this will cook up a new docker container, fetch and install wordpress and map the contents of `./mgraphics` inside the container. once its done you can browse http://localhost to view your local wordpress installation.

> note that especially on the first run, it will take a bit until you can access http://localhost as docker needs to download everything, setup your mysql server and so on. so be patient.

### theme development
Step into the theme directory with `cd mgraphics` and run `npm install`.
All the source files for scss and js are found in `./mgraphics/src/` and will be compiled to `./mgraphics/dist/` on production.

#### development

to work on your theme, go to `./mgraphics/functions.php` and search for `function starter_theme_scripts()`. Inside there ensure you load the script you want to work on / test with `wp_enqueue_script('mgraphics-scripts-dev', 'http://localhost:8080/site.js');`. In development, we dont need to reference the css styles, as we inject it into our site through javascript (is faster and totally ok for development).

Run `npm run dev` to start webpack-dev-server and serve your files from memory on http://localhost:8080

since we're using HMR and webpack-dev-server, we dont need to reload our page after changes to scss or js. webpack will decide itself when to reload.

#### production

to build your theme, run `npm run build`. This compiles your scss and js and writes it to your `./mgraphics/dist/` folder.

for production, open `./mgraphics/functions.php` and search for `function starter_theme_scripts()`.
now comment out the development script reference from above, and make sure you load all your compiled files:

```php
wp_enqueue_style('mgraphics-style', get_template_directory_uri() . '/dist/site.css');
wp_enqueue_script('mgraphics-scripts', get_template_directory_uri() . '/dist/site.js');
```
