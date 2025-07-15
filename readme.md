# Ecoride

Ecoride is my exam project! It's a carpooling website focused on ecology. You can become a driver, a passenger, or even both!  
**The website is only available in French, but most of my comments are in English.**

---

## What can you do?

### Visitor
- Can view informative pages, search for carpools, and see details (with maps), but cannot make a reservation.

### User
- After registration, you receive 20 credits to make reservations (your reservation history appears in your account).
- Receive an email at each step of the reservation process.
- After a carpool, users can leave a review for the driver (leaving a review is mandatory if the user reports that the driver was not good).
- Users can register as drivers in their account.

### Driver
- To become a driver, users need to fill in mandatory information such as their preferences and add a first car (they can add more cars after registering as a driver).
- When creating a carpool, the driver specifies the day, starting time, start and end addresses, and selects a car or adds a new one (the driver does not need to set an end time; JavaScript will calculate it).
- The driver can view the reviews left by users directly on their account.
- The driver receives credits after the user confirms the end of the reservation, and 2 credits are allocated to the platform.

### Administrator
- The administrator can edit all upcoming carpools.
- User and employee lists are accessible from the admin dashboard.
- New employee accounts can be created by the administrator.
- The admin dashboard includes statistics on reservations per day and accumulated credits.
- The administrator can manage user reviews.

### Staff
- Can manage user reviews.
- Can contact drivers and passengers by email.

---

## Technologies Used

### Front-end
- HTML
- CSS
- SCSS
- JavaScript (vanilla)

### Back-end
- PHP
- Symfony
- SQL (MariaDB)
- NoSQL (MongoDB)

### Tools
- Google Maps API
- Docker (to containerize the application, making it easy to deploy and run in any environment)

### Installatiion
---

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
3. **Configure the Database**

     ```
   - Edit `.env.local` and set your database credentials, e.g.:
     ```
     DATABASE_URL="mysql://name:pass@localhost:3306/yourdb"
     ```

4. **Create the Database**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Run the Migrations**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
---

## Work in Progress

- More refactoring needed.
- Map to show search results near you with geolocation (currently only a list is shown).
- More advantages if the driver uses an electric car.

---

## Project Resources

Here are all the documents and tools used throughout the development of Ecoride:
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

https://ecoride.japaneedsyou.fr/