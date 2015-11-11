*This project is not yet completed and as such should not be used in a production environment.*

## Description

A GUI based form building package. Allows users to create forms with an easy and intuitive interface. Generates all the files to self host the form online. Creates/includes all dependencies such as jQuery, bootstrap, and multiple other plugins all of which can be viewed below.

## Motivation

Creating forms by hand can be a hassle even when you have a nice code catalog of previous projects to pull from. I wanted a simple way to create forms in minutes both for work and personal projects. 

## To-Do

- [x] Automate generation of HTML
- [x] Automate generation of PHP
- [x] Automate generation of JS
- [x] Automate generation of CSS
- [x] Functionality to Create and Insert to DB
- [x] Functionality to Send Confirmation Email
- [x] Functionality to Send Notification Email
- [ ] Automatically include Submit Button on Bottom of Form (Remove it from the Elements Allowed to Add)
- [x] Fix the Text Element
- [x] Implement PDO over mysqli
- [ ] Generate ID on server side for elements
- [ ] Restructure what information users provide for elements
- [ ] Add editing functionality to elements on client side
- [ ] Add editing functionality to form information on client side
- [ ] Create Zip of Generated Files and Serve as a Download
- [ ] Add more element functionality such as tables, anchors, images, and file input
- [ ] Move modal html to server-side
- [ ] Standardize commenting
- [x] Minimize dependencies
- [ ] Refactor code
- [ ] Finalize installation process


## Installation

Installation is as simple as downloading and hosting the provided files.
PHP is a requirement as most of the work is done server-side for security reasons.

Once files are generated some portions will need to be manually filled in.
Ex: If a DB is used to store form data then login credentials will need to be supplied.

Required User Editing :

root/php/temp/php/SendMail.php - Must provide the SMTP Host information to send emails.

root/php/temp/php/databaseQuery.php - Must provide the Database Username/Password. Recommended that you utilize a config file outside of the root directory.

## Dependencies

The following list are the various plugins that are utilized and included in this project.

A large shout-out to them and the great work that they have done!

- **[PHPMailer](https://github.com/PHPMailer/PHPMailer)**
- **[Bootstrap](https://github.com/twbs/bootstrap)**
- **[jQuery](https://github.com/jquery/jquery)**
- **[jQuery ui](https://github.com/jquery/jquery-ui)**
- **[jQuery Form](https://github.com/malsup/form)**
- **[json3](https://github.com/bestiejs/json3)**

## License

This project is available under the **GNU GENERAL PUBLIC LICENSE Version 2**. A copy of which is provided in the root of this repository.
