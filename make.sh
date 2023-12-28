#!/bin/bash

composer install -a

npm install


cp node_modules/bootstrap/dist/css/bootstrap.min.css ./webroot/vendor/bootstrap/
cp node_modules/bootstrap/dist/css/bootstrap.min.css.map ./webroot/vendor/bootstrap/


cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js ./webroot/vendor/bootstrap/
cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map ./webroot/vendor/bootstrap/
