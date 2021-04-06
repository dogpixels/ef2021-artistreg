# Eurofurence Online 2021 Artist Registration Web Application

## Requirements
docker >= 20.10.5

## development notes

#### to get this thing up and running, do the following
* `git clone https://github.com/dogpixels/ef2021-artistreg`
* copy __app_local.php__ into __config/__
* build and run the docker container by running __reload.ps1__
* go to localhost/users/renew/1/firstrunsetup and set a password for the initial administrator account
* now you're good to log in with this administrator account

#### todo
* build frontend form for the data field

# Installation
`git clone https://github.com/dogpixels/ef2021-artistreg.git`

## Running
`docker build -t efo2021-artistreg . && docker run -dp 80:80 --name efo2021-artistreg-instance efo2021-artistreg`
