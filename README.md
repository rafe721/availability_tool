# Availability Tool

Web application to enquire about availability of rooms from a specific date.

## Assumptions and considerations
1. At all times the user will eb able to browse up to Six weeks ahead of the chosed arrival date
2. Once a room is selected, the same room can be selected for different dates. All other rooms cannot be seleccted.
3. A user can clear the selection OR unselect the room should they wish to select a different room.

---

## Platform
1. Windows 7 Home Premium Service Pack 1
2. PHP aritsan developmental web application server.

## Application Stack
1. Laravel 5.6.38 & dependancies
2. PHP7.2.0
4. MySQL 5.7.11

## Resources & tools
1. jquery 3.3.1
2. Bootstrap 4.1.3 (for UI styles)
3. fontawesome-free 5.3.1
6. Jetbrains PhpStorm 2018.2.4 (IDE)
7. Composer 1.7.2

---

## Environment Setup
Please ensure that you have all the components of the application stack in your computer. If any of the application stack or the tools mentioned in the below points are missing, please install the versions mentioned in the application stack or greater for your Operating system.

1. Ensure you have **php 7.20**; See [PHP 7.2 installation](http://php.net/manual/en/install.php).
2. Install **MySQL** in your system if not already available. See [Getting Started with MySQL](https://dev.mysql.com/doc/mysql-getting-started/en/).
3. Please follow the [Laravel Installation guide](https://laravel.com/docs/5.6/installation).
4. Start the services for both **MySQL**.
5. Install **Composer** in your system. See [Composer - Getting Started](https://getcomposer.org/doc/00-intro.md).

Once you have the application stack in place:

1. **Clone the contents of this repository** to desired location of your computer.
2. To install all the **required dependencies**, run ``composer install``.
3. Ensure that the **Mysql database tables** are created. Follow [Database: Migrations](https://laravel.com/docs/5.7/migrations#creating-tables) for instructions.
4. **Start** your application server. If it is the Laravel Artisan server, run the command ``sh php artisan serve`` from your project directory.

---

## Using the App

### User management and security.
The application does not have any login layer. There is only one endpoing for web pages ('/availability').

### On Page load
The app is single page. On page load, there is a datepicker (set to current date), a number spinner (representing number of guests; initially Zero; 0) and a an empty weekly calendar for the next 6 weeks.

### Looking for available rooms
1. To look for rooms, a user must select select a date for arrival and select the number of guests in their party.
2. Once the data is entered and the search button is clicked, relevant information about available rooms is retrieved from the web application.
3. Information about room availability is shown for six weeks from the chosen arrival date.

### Selecting rooms & viewing the summary
1. Once a user looks for rooms matching their preference (date of arrival and nomber of guests), they can travel upto six weeks ahead to look for rooms they like.
2. Once they choose a room for a day, the can choose the same room for different days as well. Once a room is selected, another room cannot be selected.
3. When (a room is/rooms are) selected, a summary appears to the right of the calendar showing the selected booking information (booking total - using tax settings, currency and tax values; dates picked; number of guests. If tax not included in the price of the room, a sum of taxes for rooms selected is also displayed).
4. At the bottom of the summary form will be an 'Enquiry' button and a 'clear' button.
5. The summary block is visible only when there are rooms selected. If all selected rooms are unselected (OR) if the selection was cleared, the summary block will disappear.

### Clearing a selection
1. Clicking on the clear button on the summary block will clear all the selected rooms and make the summary block disappear.
2. Hitting the search button will also achieve the effect of clearing the selection.

### Making an enquiry
1. The other button at the bottom of the summary block is the 'Enquire' button.
2. Presing it will open a form to get information about the first name, last name and email of the user.
3. Presing the 'Enquire now' button makes an enquiry. The result of the operation is in a JSON format.
4. If all the information is present, we will get a correct output response.
``
{
	"room_id" : 1234,
	"arrival_date": "2018-12-22",
	"nights": 3,
	"total-rate": 750,
	"guest_firstname": "John",
	"guest_lastname": "Smith",
	"guest_email": "john@test.com"
}
``
5. If the information is not right, the errors involved will be returned aws output.
``
{
	{
    "email": [
        "The email field is required."
    ]
}
``
### Output of the enquiry
1. The output of the enquiry will be shown as a JSON block in a <pre> tag below the summary.
2. If there are any changes to the selection (another enquiry, clearing the enquiry etc), the enquiry and output get cleared.
---

## The way forward if there was more time
1. Delegated responsibility to smaller dedicated VueJS components and managed communication between them.
2. Implemented better date handling in both the front end and backend.
3. Implemented better styling (this is something I take time get just right).
4. Refactor and modularise code to make it easier to test code.
5. Add PHPUnit tests.
