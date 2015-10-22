*This project is not yet completed and as such should not be used in a production environment.*

## Description

A GUI based form building package. Allows users to create forms with an easy and intuitive interface. Generates all the files to self host the form online. Creates/includes all dependencies such as jQuery, bootstrap, and multiple other plugins all of which can be viewed below.

## Motivation

Creating forms by hand can be a hassle even when you have a nice code catalog of previous projects to pull from. I wanted a simple way to create forms in minutes both for work and personal projects. 

## To-Do

Currently on submission only the HTML is generated. The PHP, JS, and CSS still need to be automatically generated. The files then need to be zipped together and served to the client.

- [x] Automate generation of HTML
- [ ] Automate generation of PHP
- [ ] Automate generation of JS
- [ ] Automate generation of CSS
- [ ] Move modal html to server-side
- [ ] Standardize commenting
- [ ] Minimize dependencies
- [ ] Refactor code
- [ ] Finalize installation process


## Installation

Installation is as simple as downloading and hosting the provided files.
PHP is a requirement as most of the work is done server-side for security reasons.

Once files are generated some portions will need to be manually filled in.
Ex: If a DB is used to store form data then login credentials will need to be supplied.

## Dependencies

The following list are the various plugins that are utilized and included in this project.

A large shout-out to them and the great work that they have done!

- **[PHPMailer](https://github.com/PHPMailer/PHPMailer)**
- **[Bootstrap](https://github.com/twbs/bootstrap)**
- **[jQuery](https://github.com/jquery/jquery)**
- **[jQuery ui](https://github.com/jquery/jquery-ui)**
- **[jQuery Form](https://github.com/malsup/form)**
- **[jQuery Masked Input](https://github.com/digitalBush/jquery.maskedinput)**
- **[jQuery Numeric](https://github.com/SamWM/jQuery-Plugins/tree/master/numeric/)**
- **[json3](https://github.com/bestiejs/json3)**

## License

This project is available under the **GNU GENERAL PUBLIC LICENSE Version 2**. A copy of which is provided in the root of this repository.
