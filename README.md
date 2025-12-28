# Kids Keeper

## Terminologies
- Child: A minor who is registered to attend an activity.
- Guardian: A parent, legal guardian, or authorized individual who is permitted to check a child in or out of an 
  activity.
- Activity: A scheduled program or event for children, such as a Sunday school session, school program, camp, or 
  playhouse activity.
- Attendance: A time-stamped record that shows when a child is checked in to or checked out of an activity, and by whom.
- Gatepass: A secure, time-bound code or digital pass issued to a guardian that authorizes the check-in or check-out of a child.
- Organization: The school, church, group, or institution responsible for organizing and managing children’s activities.
- Keeper: An authorized staff member or volunteer of an organization responsible for verifying gatepasses and ensuring the safe check-in and check-out of children.


# Notes
- Label names: by default the `nickname` is used but if none is provided then the first word of the `first name` will be used.

## Todos
- Add avatar for guardians and children
- Add QR scanning for check in/out
- Add Printing of attendance info
- Add team management section for organization admins
- Add decent homepage with links to login/register
- figure out how to manage age group/classification
- Add functionality to set another primary guardian, an invite must be sent and accepted to make it official

```php
enum AgeGroup
{
    case Toddler;
    case Preschool;
    case Junior;
    case Preteens;
}
```
