# TODO

* [ ] Landing page
* [ ] Log in page
* [ ] Sign up page
* [ ] Dashboards (student and staff)
* [ ] Availability page
* [ ] Course page
* [ ] Booking page & Confirmation page
* [ ] Modify availability page for staff

## Paths to different pages

| Page              | Current path location |
| ----------------- | --------------------- |
| Landing (Main)    | matter/main.htm       |
| Log in            | matter/login.htm      |
| Sign up           | matter/signup.htm     |
| Dashboard         | matter/dashboard.htm  |
| Availability      |                       |
| Course            |                       |
| Booking & Confirm |                       |

## Required forms
> Fix the injection of data directly from `$_POST` in the login and signup scripts.
The above is also known as `SECURITY` or pain in the ass.
* [ ] Login form
* [ ] Sign up form
* [ ] Booking form

## Required database tables
* [ ] Login database with username, id and passwords to confirm login.
* [ ] Availability database with user id and available time slots.

# THINGS TO FIX (ASAP IF POSSIBLE)
* [ ] Line 49 in `php/login.php` might not need the extra SQL query to "revalidate" the user.
* [ ] Fix the encryption... one day.