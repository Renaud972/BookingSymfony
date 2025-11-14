# AGENTS.md - AI Agent Development Log

## Project Overview
**BookingSymfony** is a rental booking platform built with Symfony 4.2, similar to Airbnb. The application allows users to create rental advertisements, manage bookings, write reviews, and includes a full admin dashboard.

---

## Initial Project Setup (June 20, 2019)

### Commit: `1c3f960` - "Requete DQL - tableau de bord et page d'acceuil"

This commit represents the complete initial implementation of the BookingSymfony application with DQL queries for the dashboard and homepage.

#### Core Features Implemented

##### 1. **Entity Model**
Complete relational data model with the following entities:
- **User**: User accounts with roles, profile information, and authentication
- **Ad**: Rental property advertisements with pricing, images, and content
- **Booking**: Reservation system linking users to properties
- **Comment**: Rating and review system for properties
- **Image**: Image gallery for advertisements
- **Role**: User permission system
- **PasswordUpdate**: Password management functionality

##### 2. **Controllers**

**Public Controllers:**
- `HomeController`: Homepage displaying best-rated ads and top users
- `AdController`: CRUD operations for rental advertisements
- `AccountController`: User registration, login, profile management
- `BookingController`: Property booking and reservation management
- `UserController`: User profile display

**Admin Controllers:**
- `AdminDashboardController`: Statistics dashboard with DQL queries
- `AdminAdController`: Advertisement moderation
- `AdminBookingController`: Booking management
- `AdminCommentController`: Comment moderation
- `AdminAccountController`: Admin authentication

##### 3. **Custom Services**

**Statistics Service (`src/Services/Statistics.php`)**
Provides aggregated statistics using DQL queries:
- `getUsersCount()`: Total user count
- `getAdsCount()`: Total advertisement count
- `getBookingsCount()`: Total booking count
- `getCommentsCount()`: Total comment count
- `getAdsStats($direction)`: Best/worst rated properties with aggregated ratings

**Pagination Service (`src/Services/Pagination.php`)**
Custom pagination system for admin panels and listing pages

##### 4. **Repository Custom Queries**

**AdRepository:**
- `findBestAds($limit)`: Retrieves top-rated properties using QueryBuilder
  - Joins with comments
  - Groups by advertisement
  - Orders by average rating (DESC)
  - Returns ads with their average ratings

**UserRepository:**
- `findBestUsers($limit)`: Finds top-rated property owners
  - Joins users → ads → comments
  - Groups by user
  - Filters users with more than 3 comments
  - Orders by average rating
  - Returns users with average ratings and comment counts

##### 5. **Forms**
Complete form system with validation:
- `AnnonceType`: Advertisement creation/editing
- `BookingType`: Booking reservation form with date validation
- `CommentType`: Review submission
- `RegistrationType`: User registration
- `AccountType`: Profile update
- `PasswordUpdateType`: Password change
- `ImageType`: Image upload
- `FrToDatetimeTransformer`: Custom date transformer for French format

##### 6. **Security & Authentication**
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- Separate admin authentication system
- Password encoding and validation
- Form-based login for both public and admin interfaces
- Access control annotations on controllers

##### 7. **Frontend**
- Responsive templates using Twig
- Bootstrap 4 integration
- Custom CSS for public (`style.css`) and admin (`admin.css`) interfaces
- JavaScript for dynamic ad image management (`ad.js`)
- Custom error pages (403, 404)

##### 8. **Database Migrations**
Eight migration files tracking the database schema evolution:
- Initial user and ad tables
- Booking system
- Comment and rating system
- Role management
- Image galleries

##### 9. **Data Fixtures**
`AppFixtures.php` includes seed data using Faker for:
- Sample users with roles
- Rental properties with realistic data
- Bookings and reservations
- Comments and ratings
- Images

##### 10. **Key DQL/QueryBuilder Implementations**

**Homepage Query (AdRepository::findBestAds):**
```php
SELECT a as annonce, AVG(c.rating) as avgRatings
FROM Ad a
JOIN a.comments c
GROUP BY a
ORDER BY avgRatings DESC
LIMIT $limit
```

**Best Users Query (UserRepository::findBestUsers):**
```php
SELECT u as user, AVG(c.rating) as avgRatings, COUNT(c) as sumComments
FROM User u
JOIN u.ads a
JOIN a.comments c
GROUP BY u
HAVING sumComments > 3
ORDER BY avgRatings DESC
LIMIT $limit
```

**Admin Dashboard Statistics (Statistics Service):**
- Uses DQL `COUNT()` aggregation for entity counts
- Calculates average ratings with `AVG(c.rating)`
- Joins across Ad → Comment → User relationships
- Supports sorting for best/worst performing ads

##### 11. **Business Logic Features**

**Ad Entity Methods:**
- `initializeSlug()`: Auto-generates URL-friendly slugs using Cocur/Slugify
- `getAverageRatings()`: Calculates average rating from comments
- `getNotAvailableDays()`: Returns array of unavailable booking dates
- `getCommentFromAuthor()`: Retrieves specific user's comment

**Booking Validation:**
- Date range validation
- Availability checking against existing bookings
- Price calculation based on duration

##### 12. **Third-Party Packages**
- **Doctrine ORM**: Database abstraction and queries
- **Cocur/Slugify**: URL slug generation
- **Faker**: Test data generation
- **SwiftMailer**: Email functionality
- **Monolog**: Logging system

---

## Technical Highlights

### Advanced Doctrine Usage
- Custom repository methods with QueryBuilder
- DQL queries for complex aggregations
- Entity lifecycle callbacks (`@ORM\PrePersist`, `@ORM\PreUpdate`)
- Bidirectional relationships with proper cascade options
- Orphan removal for dependent entities

### Form Validation
- Symfony validation constraints
- Custom error messages in French
- UniqueEntity validation
- Length and format validation
- Date transformer for localized input

### Architecture Patterns
- Service layer for business logic (Statistics, Pagination)
- Repository pattern for data access
- Form type classes for reusable forms
- Abstract ApplicationType for form inheritance
- Separation of public and admin interfaces

---

## Development Environment
- **PHP**: ^7.1.3
- **Symfony**: 4.2.*
- **Database**: Doctrine ORM with migrations
- **Frontend**: Twig templates, Bootstrap 4, vanilla JavaScript
- **Dev Tools**: Symfony Web Server, Profiler, Maker Bundle, Debug Pack

---

## Current State
The application is a fully functional rental booking platform with:
- ✅ User authentication and authorization
- ✅ Advertisement CRUD with image galleries
- ✅ Booking system with availability tracking
- ✅ Rating and review system
- ✅ Admin dashboard with statistics (DQL-powered)
- ✅ Custom pagination service
- ✅ Responsive frontend
- ✅ Data fixtures for testing

---

## Notes for Future AI Agents

### Key Implementation Details
1. **DQL Focus**: This commit specifically emphasized DQL queries for the dashboard and homepage, implementing aggregation functions (AVG, COUNT) and complex joins.

2. **French Language**: The application is built for French-speaking users with French validation messages and UI text.

3. **Custom Services**: The Statistics service is central to the admin dashboard, using direct DQL queries via the EntityManager.

4. **Date Handling**: Bookings use a custom date transformer to support French date formats (dd/mm/yyyy).

5. **Slug System**: All ads automatically generate URL-friendly slugs using lifecycle callbacks.

### Architecture Decisions
- **Repository Methods**: Custom queries are isolated in repositories for reusability
- **Service Layer**: Business logic is extracted into services (Statistics, Pagination)
- **Admin Separation**: Admin functionality is completely separated with its own templates and routing
- **Entity Methods**: Business logic related to entities (like `getAverageRatings()`) lives in the entity classes

### Testing Approach
- Fixtures provide realistic sample data
- Symfony's test pack is configured but no tests are written yet
- Web profiler available for development debugging

---

## Metadata
- **Last Updated**: June 20, 2019
- **Total Files**: 133 files added
- **Total Lines**: ~13,500 lines of code
- **Primary Author**: Renaud (gsxr00@gmail.com)
