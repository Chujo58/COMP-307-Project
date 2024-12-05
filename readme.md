# McGill COMP 307 (Principles of Web Development) Final Project

## Folder structure

<table>
    <tbody>
        <tr>
            <th>Feature</th>
            <th>File type</th>
            <th>File path</th>
            <th>Notes</th>
        </tr>
        <tr>
            <th colspan=4>Main pages</th>
        </tr>
        <tr>
            <td>Main page</td>
            <td>PHP</td>
            <td>index.php</td>
            <td>Does the routing using PHP GET method</td>
        </tr>
        <tr>
            <td>Popup Loader</td>
            <td>PHP</td>
            <td>load_form.php</td>
            <td>Loads the data into the popup (only available on main page)</td>
        </tr>
        <tr>
            <th colspan=4>Dashboard</th>
        </tr>
        <tr>
            <td rowspan=1>User dashboard</td>
            <td>HTML</td>
            <td>matter/dashboard.htm</td>
            <td><b>This is temporary and should be changed.</b></td>
        </tr>
        <tr>
            <td rowspan=1>Staff dashboard</td>
            <td>HTML</td>
            <td>matter/dashboard.htm</td>
            <td><b>This is temporary and should be changed.</b></td>
        </tr>
        <tr>
            <th colspan=4>Popups</th>
        </tr>
        <tr>
            <td rowspan=2>Login popup</td>
            <td>PHP</td>
            <td>php/login.php</td>
            <td>Handles the SQL queries to DB for login</td>
        </tr>
        <tr>
            <td>HTML</td>
            <td>matter/login.htm</td>
            <td>HTML code used to populate the login popup</td>
        </tr>
        <tr>
            <td rowspan=2>Sign Up popup</td>
            <td>PHP</td>
            <td>php/signup.php</td>
            <td>Handles the SQL queries to DB for signup</td>
        </tr>
        <tr>
            <td>HTML</td>
            <td>matter/signup.htm</td>
            <td>HTML code used to populate the signup popup</td>
        </tr>
    </tbody>
</table>

## To Dos

* [ ] Encryption of the passwords
* [ ] Dashboard pages
* [ ] Availibility pages (+editing page for staff)
* [ ] Course pages
* [ ] Booking & Confirmation page

>  The course can probably be merged into the dashboard, same for the availability page. We might want to add some sort of phonebook to search through staff members with office hours (maybe allow multi selection to preview multiple OH in one calendar view). This would require a selection box to choose the staff with whom we want a booking.

### Code specific to dos

* [ ] Line 49 in `php/login.php` might not require the extra SQL query to revalidate the user.

## Repository Analytics

![Alt](https://repobeats.axiom.co/api/embed/cc50e34da53c299fd9a5fc0523f897c5b004073c.svg "Repobeats analytics image")
