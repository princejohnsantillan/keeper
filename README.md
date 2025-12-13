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


## Todos
- In the Keeper dashboard registration, ask for `Keeper` details and create a Keeper before creating a user and attach the keeper to the user
- When creating a child ask for a relationship and create a `Relationship` entry with the child and the user's keeper id
- All show children should only be children of the logged-in user's keeper id
- When creating a Keeper, ask for relationships with the kids existing in the user's dashboard. Create a relationship with the Keeper and the children.
- Only show Keepers who have a relationship with the children of the logged-in user.
- Only show services that have not yet ended.

- When checking in, select the child and the keeper who will check the child in. A unique auth code/QR should be generated per service+child+keeper combination. 
