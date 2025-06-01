# Shopping List exercise

## Description
A Shopping List application, with both fronted and backend.
Currently only a single "global" shopping list is implemented, that is common to all the registered users.
Application has mobile responsive design, allows for data import and export either via application or command line,
and notifies other users, when shopping list data changes, via Server sent events.

## Tech stack used

Project has two directories, frontend and backend for both parts of the project.
Fronted was done with NodeJs, Vue 3, Vite and Tailwind.
Backend was done with Laravel 12, Php 8.2, MySql 8.
Project is containerized with Docker.

## How to setup 
Docker is needed to run the containers. Move to the root folder of project and run command:

```
docker-compose up -d
```
For me build took some time, around 700 seconds to build the containers, especialy npm install part in frontend container takes long. 

After containers are running, several other steps are needed in two containers.

For the frontend container:

```sh
# you can also run commands outside container, just use the bottom command
# and replace sh, with the command needed
docker exec -it exercise-node sh
# create .evn file
cp .env.sample .env
# check that file was created
ls -hals
# leave the container
exit
```

For the backend container

```sh
# you can also run commands outside container, just use the bottom command
# and replace sh, with the command needed
# enter inside container
# command is run as the root user
docker exec  -u 0  -it exercise-laravel-app sh
# create both env files
cp .env.sample .env
cp .env.sample.testing .env.testing
# check that files were created
ls -hals
# install dependencies in laravel
composer install
# key needs to be regenerated in .env file
php artisan key:generate
# run the database migrations
php artisan migrate
# if you wish to run the tests, these use a separate database, so another migration is needed
php artisan migrate --env=testing
# set permissions back to the correct user
chown -R www-data /var/www
# leave the container
exit
```

You can now access the frontend at url
http://localhost:3000/
Frontend runs in developer mode with Vue developer tools.

## How to run tests 

Test are implemented on backend part and are run inside the backend container. Separate container for database is used, to reduce the number of steps in setup. If you don't play to run the tests, you don't need to run the db_test service, that is test database container.


```sh
docker exec -it exercise-laravel-app sh
# command that runs all the tests, no data is left in test database after tests finish
php artisan test
# leave the container
exit
```

## How to run import commands in command line

Location of files is assumed to be inside project folder:
backend/storage/app/private

```sh
docker exec -it exercise-laravel-app sh
# export command, mandatory parameter is output file
php artisan app:export-shopping-list output.json
# import command, mandatory parameter is the input file
php artisan app:import-shopping-list import.json
# leave the container
exit
```

## How to check database state

If you wish to check the database state, you can use some external client.
I used DBeaver client. If database settings in .env file were not changed, the following settings
should be used in DBeaver. 

Url: jdbc:mysql://localhost:3306/laravel_local
Server: localhost
Database: laravel_local
Port: 3306
Username: username
Password: password

I using Dbeaver, two additional properties need to be set on connection:
Right-click your connection, choose "Edit Connection"
On the "Connection settings" screen (main screen), click on "Edit Driver Settings"
Click on "Driver properties" Set these two properties: "allowPublicKeyRetrieval" to true and "useSSL" to false. This step is also described here: https://stackoverflow.com/a/59778108


## Application access

Application runs in url http://localhost:3000/, if not logged in you will be redirected to login page.
User needs to be created to with password to use the application. There are no existing users by default, mail needs to be a valid email string,
but it is not a requirement for it to exist, there is no confirmation email. Based on browser language settings, 
either Slovenian or English language will be shown, but this can be changed.

## Specified rules about application logic

There are 3 separate statuses for each item, based on the step during purchase.
Unchecked, In Shopping and Checked, each with each own tab/display in table,
At the start only unchecked status is shown, other 2 tabs are only visible, when items are present in such a state. In addition to item name, requested item quantity is also inserted.
If you have a plural item, you wish would be bought, like 'bananas', just leave the quantity as 1.

Unchecked item is inserted into shopping list and can be edited or removed. When items are in shopping item names/quantity are not editable and items can't be deleted. 
Only one user can have items in shopping status and change their checked quantities.
Items in shopping can be checked with full requested quantity or partial quantity.
Other users still see what happens with items in shopping, but have no controls available.
During shopping item can be inserted with same name, as the one in shopping, in unchecked items.
While items are in shopping status, checked quantity can be modified at will.
After shopping is completed, items with checked quantity will be stored in the History tab.
Items that were partially checked, or not checked will be added back with remaining quantity to unchecked list.
If item on unchecked list already exists with same name, this quantity will be added, else new unchecked item will be created.


## Data update rules
For json input data to be valid, item name should be unique for unchecked items and items in_shopping.
When searching if data exists, so that new record is not re-created/duplicated, only refreshed,
different rules are used based on item status. 
Id fields are ignored in comparison because records could be imported, 
with the same data, but id fields could be totally different, because of the autoincrement property.

For unchecked and items in_shopping, match is when the same item name matches.
For checked item, this is not enough, because item with same name could be checked out multiple times.
So for a record to match, it should be checked out at the same time, by the same user.

When inserting/updating with data from json file only data that could be set for that status is written.
So for example unchecked items will not have checked quantity set, even if value will be present in json file.



## Possible improvements to application

### Multiple shopping List
Currently for simplicity only one global shopping list is implemented.
Separate shopping list should be implemented, user should have the access 
only to certain list and option to create a new shopping list, where 
he could invite other users.


### Improvements when list of data grows larger
Currently all data is listed with an infinite table, this approach is not scalable when data will grow much larger. Pagination should be used, and only reading data with needed status.
Search field could also be added to search for item name. 
Input text field would be used for search, which should trigger search query, on change, 
but this is done debounced, so that request will be triggered, only sometime after last change event, like 500ms. 
This is done to avoid triggering a request, after each value change, each key press.
Certain table columns could have sort options, like date purchased on Checked/History tab,

### Hide quantity fields by default
If quantity is left as 1, the same behavior happens if quantity was not implemented.
The quantity field might be confusing to users unfamiliar with application.
A checkbox/profile setting could be added to display the quantity field in the interface only if specifically requested.

### Items in shopping improvements
Two improvements can be implemented, without even changing the current data model.
You could select which items, will be taken with you into shopping, others will be left in unchecked list. Another user could also start a different shopping, with remaining unchecked items, while another user has items in shopping.

### Confirmation actions
It makes sense for certain features, that have irreversible effect on data, to have confirmation in case user clicks them by mistake.
Such a feature, for example is "Clear List", which removes all data, or "Finish Shopping". 
Confirmation of such action would be done in a separate popup.

### Authentication
Only the very basic email/password authentication is implemented for demonstration purposes.
Many other features should be added like password reset, email confirmation and authentication via Gmail account by integrating Google Sign in. Password fields could have the display password option. There is currently no option to save user preferences, currently this would make sense for the interface language.


## Possible technical improvements

### Single docker database container

Currenlty there are 2 docker containers in application, one is used for application tests.
A single database container could be used, with different databases, one for Application
and one for testing.


### Reduce the number of reads in fronted, when SSE events are received from backend

In many cases, when receiving a SSE event from backend, it is not necessary,
to reload the whole data, only to update the changes. Currenlty this is implemented,
only for delete events, but could be expanded to other events,
with the exception of import event.