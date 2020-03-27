# oxid-template-compiler
Compile SCSS and Javascript files from Oxid Backend

## Installation

### Using composer

Run the following command in your shop base folder (this is where composer.json is located)

```
composer require aggrosoft/oxid-template-compiler
```

## Prepare Template

You will need the development version of your the template to be able to build the assets. Wave and Flow themes
do not include their build folder by default. The module will show a button to do these steps for you, but this
will only work if you have git and nodejs. Also the server needs to allow the php exec function. If this does not work
follow the manual steps below.

### Automatic prepare

Click the "Initialize Development Version" Button in the template screen in oxid backend. This will try to pull a copy
of the assets from github and will run npm install in the template folder.

### Manual prepare

Execute the following steps, adjust according to your template. Below is for wave 1.2.0

```bash
git clone -b v1.2.0 https://github.com/OXID-eSales/wave-theme.git _theme
cp -r _theme/build/ Application/views/wave/
cp _theme/package.json Application/views/wave/
rm -rf _theme
cd Application/views/wave
npm install
```