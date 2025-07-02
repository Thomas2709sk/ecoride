# Ecoride

**Ecoride** is my exam project ! It's a carpooling website focused on ecology. You can become a driver, be a passenger or why not both ? "The website is only available in French but most of my commentary are in English ".

---

## What can you do ?

### Visitor

- Can check informative pages, search for carpools and see the details (with maps ) but cannot make a reservation

### User

- After registration, earn 20 ccredits to make a reservation.( all your reservations history appears in your account)
- Receive an email at each step of the reservation process.
- After the carpool, the user can leave a review for the driver (mandatory if the user say the driver was not good ).
- Users can register as a driver in their User account.

### Driver

- To become a driver, users need to fill mandatory informations like their preferences and a a first car ( they can add more cars after registration in their driver account ).
- When creating a carpool, the guide provides the day, starting time , address start and end , choose a car or add a new one ( driver don't need to put end time , JS will do the calculation )
- The driver can view the reviews left by users directly on their account.
- The driver receives their credits after the user confirms the end of the reservation, and 2 credits are allocated to the platform.

### Administrator

- The administrator can edit all upcoming carpools.
- User and employee lists are accessible from the admin dashboard.
- New employee accounts can be created by the administrator.
- The admin dashboard includes statistics on reservations by day and accumulated credits.
- He can manage user reviews.

### Staff

- Can manage user reviews.
- Can contact driver and passenger with their emails.

---

## Technologies Used

### Front-end

- **HTML**
- **CSS**
- **SCSS**
- **Javascript (vanilla)**

### Back-end

- **PHP**
- **Symfony**
- **SQL (MariaDB)**
- **NoSQL (MongoDB)**

### Tools

- Google maps API
- **Docker**(  to containerize the application, making it easy to deploy and run in any environment. )

---

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Thomas2709sk/ecoride.git
   cd ecoride
   ```

2. **Using Docker**
   - Make sure you have [Docker Desktop](https://www.docker.com/products/docker-desktop) installed.
   - Start the containers:
     ```bash
     docker-compose up --build -d
     ```

---

## Work in Progress

- More Refactor needed.
- Map to show search results near you with geolocation ( only show a list at the moment ).
- More advantage if driver use electric car

---

## Project Resources

Here are all the documents and tools used throughout the development of Ecoride :

- [Trello board](https://trello.com/b/OZKjROh0/ecoride)
- [Style guide (PDF)](documents/charte_graphique_ecoride.pdf)
- [Mobile Wireframe (PDF)](documents/wireframe_mobile.pdf)
- [Desktop Wireframe (PDF)](documents/Wireframe_Desktop.pdf)
- [Mobile Mockup (PDF)](documents/mockup_mobile.pdf)
- [Desktop Mockup (PDF)](documents/mockup_desktop.pdf)
- [Use Case Diagram (PDF)](documents/diagramme_utilisation.pdf)
- [Sequence Diagram Carpool (PDF)](documents/diagramme_sequence_covoit.pdf)
- [Sequence Diagram Review (PDF)](documents/diagramme-sequence-avis.pdf)
- [Conceptual Data Model (PDF)](documents/MCD.pdf)