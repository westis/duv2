# Product Requirements Document (PRD): `/api/v1/` Migration for DUV Ultramarathon Statistics

**Version:** 1.4 │ **Date:** May 21, 2025

---

## 1. Overview

We will build a modern, consistent, and secure REST API under `/api/v1/` that runs alongside the current `/json/*.php` scripts. This API:

- Standardizes JSON field names, formats, and error handling based on the SQL schema.
- Uses API Key authentication for restricted data.
- Provides interactive, self-documenting documentation via Swagger UI.
- Enables gradual endpoint-by-endpoint migration without disrupting the live site.
- Follows comprehensive RESTful API design principles based on domain analysis.
- Implements a phased approach, focusing first on core endpoints (races, results, runners, records).

---

## 2. Background & Rationale

The existing PHP scripts exhibit:

- **Inconsistent data**: mixed date formats, varied field names, missing or extra fields.
- **No centralized docs**: each endpoint must be inspected individually.
- **High maintenance burden**: duplicative logic across scripts increases error risk.

Moving to a single, well-designed API spec ensures:

- **Uniform JSON structure**: consistent fields and types.
- **Discoverability**: one Swagger UI page lists all endpoints, parameters, and schemas.
- **Security**: API Keys control access to sensitive data.
- **Maintainability**: changes in specification propagate to all endpoints.
- **Scalability**: proper resource design facilitates future expansion.

---

## 3. Goals

1. **Standardization:** Uniform JSON structure and naming conventions.
2. **Discoverability:** Interactive Swagger UI documentation.
3. **Security:** API Key-based auth for protected endpoints.
4. **Incremental Migration:** Allow new `/api/v1/` endpoints to replace old scripts one at a time.
5. **RESTful Design:** Comprehensive API that follows REST principles and best practices.

---

## 4. Scope

### In-Scope (Phase 1)

- **API Design:**

  - Comprehensive analysis of domain model from `duv.sql`
  - Design of RESTful resources with proper naming and relationships
  - Definition of standard CRUD operations for core resources
  - Design of specialized non-CRUD endpoints for complex operations
  - Detailed parameter specifications and response schemas
  - Authentication tiers with proper permission control

- **OpenAPI Spec:** Create and refine `duv-api-public.yaml` through domain analysis rather than automatic generation.

- **API Keys:** MySQL `api_keys` table + PHP header validation.

- **Core Endpoints (Read-only):**

  - `/api/v1/races` → races list with filtering options
  - `/api/v1/races/{raceId}` → details for one race
  - `/api/v1/results` → performance results filtered by race or runner
  - `/api/v1/runners/{runnerId}` → runner profile by ID
  - `/api/v1/records` → records for various distances and categories

- **Admin Endpoints:**

  - `/api/v1/admin/submitted-races` → user-submitted races pending approval
  - `/api/v1/admin/submitted-races/{submissionId}` → details for a specific submission
  - `/api/v1/admin/users` → user management endpoints (for admins only)

- **Swagger UI Organization:**

  - Primary section for core endpoints (races, results, runners, records)
  - Admin section for endpoints requiring administrative access (submitted races, approvals)
  - Secondary sections for planned future endpoints (stats, regional-specific endpoints)
  - Comprehensive documentation of all parameters and schemas

- **Documentation:** Swagger UI served via GitHub Pages.

- **Frontend Tasks:** Update fetch calls to use new endpoints and include API Key headers.

### Out-of-Scope (Phase 1)

- Create, Update, Delete operations (planned for future phases).
- OAuth2/JWT, rate limiting, caching or load balancing.
- Event-Race hierarchy restructuring (under consideration for Phase 2).

---

## 5. Implementation Approach

The implementation will follow two parallel tracks:

### 5.1. Local Development Environment

A local development environment will be set up to demonstrate both the new API and frontend. This will:

- Serve as a proof of concept
- Allow rapid prototyping and testing
- Provide a reference implementation for the production deployment
- Demonstrate the integration between frontend and API

### 5.2. Gradual Production Implementation

Jürgen Schoch at DUV will implement the necessary PHP code to gradually introduce the new API to the production environment. This approach will:

- Minimize disruption to the existing site
- Allow endpoint-by-endpoint migration
- Enable the frontend to switch from the local implementation to the real backend
- Ensure seamless transition for users

---

## 6. Project Setup

### 6.1. GitHub Repository Setup

1. **Create Repository**

   - Create a new GitHub repository named `duv-api-migration`
   - Initialize with README.md and .gitignore for PHP
   - Add license file (MIT recommended for open-source)

2. **Configure Branch Protection**

   - Set up branch protection rules for `main` branch
   - Require pull request reviews before merging
   - Require status checks to pass before merging

3. **Set Up GitHub Pages**
   - Enable GitHub Pages from the `docs` directory
   - Configure custom domain if applicable
   - Add CNAME file for domain settings

### 6.2. Development Environment Setup

1. **Local Prerequisites**

   - Install XAMPP (v8.2+) or equivalent for PHP and MySQL
   - Install Node.js LTS (v20+) and npm
   - Install uv package manager: `pip install uv`

2. **Clone Repository**

   ```bash
   git clone https://github.com/your-username/duv-api-migration.git
   cd duv-api-migration
   ```

3. **Database Configuration**
   - Import database schema from `duv.sql`
   - Create `.env` file with database credentials
   - Configure local database connection

### 6.3. Package Management

1. **PHP Dependencies**

   - Initialize Composer for PHP dependency management

   ```bash
   composer init
   ```

   - Add required PHP libraries

   ```bash
   composer require swagger-php/swagger-php
   composer require guzzlehttp/guzzle
   ```

2. **JavaScript Dependencies**

   - Initialize npm project

   ```bash
   npm init -y
   ```

   - Install Swagger UI

   ```bash
   npm install swagger-ui-dist
   ```

3. **Development Tools**
   - Install ESLint and PHP_CodeSniffer for code quality
   ```bash
   npm install eslint --save-dev
   composer require squizlabs/php_codesniffer --dev
   ```

### 6.4. CI/CD Pipeline Setup

1. **GitHub Actions Workflow**

   - Create `.github/workflows/ci.yml` for continuous integration
   - Configure actions for PHP linting and testing
   - Set up automatic Swagger UI generation

2. **Environment Configuration**

   - Set up development, staging, and production environments
   - Configure environment-specific variables
   - Create deployment scripts

3. **Deployment Process**
   - Configure staging server for testing new endpoints
   - Set up production deployment workflow
   - Create rollback procedures

### 6.5. Testing Approach

For a solo developer project that will be handed off to Jürgen, a simplified testing approach is recommended:

1. **Manual Testing**

   - Create a checklist of endpoint tests to run after changes
   - Document expected responses for different parameter combinations
   - Test with edge cases and error conditions

2. **API Documentation**

   - Create a Postman collection as documentation for Jürgen
   - Include example requests for each endpoint
   - Document expected responses and error handling

3. **Handoff Material**
   - Prepare demo scripts that show how to use each endpoint
   - Document common troubleshooting scenarios
   - Include examples of how the frontend will integrate with the API

### 6.6. Version Control Workflow

1. **Branching Strategy**

   - Use Gitflow workflow with main, develop, and feature branches
   - Create branches for each endpoint implementation
   - Use semantic versioning for releases

2. **Code Review Process**

   - Require pull requests for all changes
   - Set up code review checklist
   - Configure automatic code quality checks

3. **Release Management**
   - Define release process and schedule
   - Create release notes template
   - Set up version tagging

---

## 7. Implementation Steps

### A. API Design and Documentation

1. **Domain Analysis**

   - Analyze the `duv.sql` schema to identify all tables, relationships, and fields
   - Document primary and foreign key relationships
   - Identify data types and constraints for all fields
   - Map database tables to logical API resources

2. **Design RESTful API Structure**

   - Define resource naming conventions (plural nouns, consistent patterns)
   - Design standard CRUD endpoints for core resources
   - Identify specialized endpoints for complex operations
   - Document query parameters for filtering, sorting, pagination

3. **Develop OpenAPI Specification**

   - Create `duv-api-public.yaml` based on the domain analysis
   - Define detailed schemas for request/response objects
   - Document authentication requirements
   - Create standardized error response formats
   - Ensure all parameters are properly documented

4. **Set Up Swagger UI Locally**

   - Download Swagger UI ZIP from GitHub
   - Configure to use `duv-api-public.yaml` specification
   - Test UI functionality locally

5. **Publish Documentation via GitHub Pages**
   - Create repo `duv-api-docs`
   - Push Swagger UI and specification
   - Configure GitHub Pages to serve documentation

### B. Backend Implementation

1. **Create `api_keys` Table**

   ```sql
   CREATE TABLE api_keys (
     `key` VARCHAR(64) PRIMARY KEY,
     user_id INT NOT NULL,
     expires DATETIME,
     active BOOL DEFAULT TRUE,
     tier VARCHAR(10) NOT NULL DEFAULT 'basic',
     rate_limit INT UNSIGNED DEFAULT 1000
   );
   ```

2. **Create `users` Table (for web UI authentication)**

   ```sql
   CREATE TABLE users (
     user_id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) NOT NULL UNIQUE,
     password_hash VARCHAR(255) NOT NULL,
     email VARCHAR(100) NOT NULL UNIQUE,
     role VARCHAR(20) NOT NULL DEFAULT 'user',
     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
     last_login DATETIME NULL,
     active BOOL DEFAULT TRUE
   );
   ```

3. **Admin UI for API Keys and User Management**

   - PHP page for generating and managing API keys
   - User administration interface for admin accounts

4. **Common Helper File: `/api/v1/helpers.php`**

   ```php
   <?php
   // helpers.php
   function connectToDatabase() {
     return new mysqli('localhost','user','pass','duv');
   }

   function validateApiKey($db, $required = true, $minTier = 'basic') {
     $key = $_SERVER['HTTP_X_API_KEY'] ?? '';
     if (!$key && !$required) return ['authenticated' => false, 'tier' => 'public'];

     $stmt = $db->prepare(
       'SELECT user_id, tier FROM api_keys WHERE `key`=? AND active=1'
     );
     $stmt->bind_param('s', $key);
     $stmt->execute();
     $result = $stmt->get_result();

     if ($result->num_rows === 0) {
       if ($required) {
         http_response_code(401);
         echo json_encode(['error'=>'Invalid API key']);
         exit;
       }
       return ['authenticated' => false, 'tier' => 'public'];
     }

     $data = $result->fetch_assoc();
     $tierLevels = ['public' => 0, 'basic' => 1, 'premium' => 2, 'admin' => 3];

     if ($tierLevels[$data['tier']] < $tierLevels[$minTier]) {
       http_response_code(403);
       echo json_encode(['error'=>'Insufficient permissions']);
       exit;
     }

     return [
       'authenticated' => true,
       'tier' => $data['tier'],
       'user_id' => $data['user_id']
     ];
   }

   function validateSession($required = true, $minRole = 'user') {
     session_start();
     if (!isset($_SESSION['user_id'])) {
       if ($required) {
         http_response_code(401);
         echo json_encode(['error'=>'Authentication required']);
         exit;
       }
       return ['authenticated' => false, 'role' => 'guest'];
     }

     $roleLevels = ['guest' => 0, 'user' => 1, 'editor' => 2, 'admin' => 3];

     if ($roleLevels[$_SESSION['role']] < $roleLevels[$minRole]) {
       http_response_code(403);
       echo json_encode(['error'=>'Insufficient permissions']);
       exit;
     }

     return [
       'authenticated' => true,
       'role' => $_SESSION['role'],
       'user_id' => $_SESSION['user_id']
     ];
   }

   function authenticate($requiredLevel = 'public') {
     // Try API key first
     $apiAuth = validateApiKey($GLOBALS['db'], false);
     if ($apiAuth['authenticated']) return $apiAuth;

     // Then try session
     $sessionAuth = validateSession(false);
     if ($sessionAuth['authenticated']) {
       // Map user roles to API tiers
       $roleToTier = [
         'user' => 'basic',
         'editor' => 'premium',
         'admin' => 'admin'
       ];
       return [
         'authenticated' => true,
         'tier' => $roleToTier[$sessionAuth['role']],
         'user_id' => $sessionAuth['user_id'],
         'via' => 'session'
       ];
     }

     // If authentication is required but neither method succeeded
     if ($requiredLevel !== 'public') {
       http_response_code(401);
       echo json_encode(['error'=>'Authentication required']);
       exit;
     }

     return ['authenticated' => false, 'tier' => 'public'];
   }

   function outputJson($data) {
     header('Content-Type: application/json');
     echo json_encode($data);
     exit;
   }
   ```

5. **Endpoint Implementation**

   - Implement PHP scripts for each endpoint defined in the OpenAPI spec
   - Ensure consistency with the specification regarding field names and formats
   - Validate inputs according to the parameter definitions
   - Add proper error handling according to specified error responses

6. **Frontend Integration**

   - Create central configuration for API access
   - Update fetch calls to use new endpoints
   - Adapt to new field naming conventions

7. **Testing and Verification**
   - Test each endpoint manually against the OpenAPI specification
   - Verify authentication works correctly
   - Test with frontend components
   - Confirm backward compatibility where needed

---

## 8. API Structure and Design

### Core Resources

The API is structured around the following primary resources:

1. **Races** (formerly events)

   - Ultramarathon races with details about dates, locations, distances
   - Filterable by year, country, distance range, etc.
   - Supports searching and pagination

2. **Results**

   - Performance records linking runners to races
   - Can be filtered by race ID, runner ID, ranking, etc.
   - Different time calculation methods supported

3. **Runners**

   - Athlete profiles with personal information and statistics
   - Privacy levels control data visibility
   - Can include performance history

4. **Records**
   - Records for various distances, surfaces, and categories
   - Filterable by distance, gender, age group, etc.
   - National and global record tracking

### Future Resources

The following resources will be documented in the API specification but implemented in later phases:

1. **Statistics**

   - Participation trends and historical analyses
   - Performance breakdowns by country, gender, age group

2. **Regional-specific Data**

   - German-specific record tracking (trecordger)
   - Country-specific statistics and rankings

3. **Extended Search**
   - Advanced search capabilities for races, runners, and results
   - Cross-resource search functionality

### Admin Resources

These resources require administrative access and will be available both through programmatic API keys and through the web UI for logged-in admin users:

1. **Submitted Races**

   - User-submitted race information (from tevent_submit table)
   - Approval workflow for adding to official races
   - Edit and review capabilities

2. **User Management**
   - Admin user accounts and permissions
   - API key generation and management
   - Activity logs for administrative actions

### Authentication Tiers

The API supports multiple authentication mechanisms and authorization tiers:

#### API Key Authentication (Programmatic Access)

- **Public**: Limited access without authentication
- **Basic**: Standard access with API key
- **Premium**: Enhanced access with premium API key
- **Admin**: Full access with administrative API key

API keys are passed in the X-API-Key header for all requests requiring authentication.

#### User Session Authentication (Web UI)

- Initially implemented for admin users who manage content
- Cookie-based sessions after successful login
- Mapped to appropriate API authorization levels
- Future expansion to regular users planned for later phases

#### Integration Approach

The system will implement a hybrid authentication approach:

- API keys for programmatic access
- Session-based authentication for web UI users
- Session permissions mapped to equivalent API authorization levels
- Admin users can access the same endpoints as admin API key holders

Each endpoint specifies which tier is required for access. Some endpoints allow public access but provide additional fields with authentication.

### Parameters and Filtering

The API implements consistent parameter patterns:

- Collection endpoints support filtering, sorting, and pagination
- Standard query parameters include `limit`, `offset`, `sortBy`, and `order`
- Resource-specific filters follow predictable naming patterns
- All parameters are documented with proper types, validation rules, and examples

### Error Handling

Standardized error responses provide consistent feedback:

- Validation errors (400)
- Authentication errors (401)
- Authorization errors (403)
- Resource not found errors (404)
- Server errors (500)

Each error includes a status code, error code, message, and optional details.

---

## 9. Implementation Plan and Phasing

The API will be implemented in multiple phases to ensure a controlled migration without disrupting existing services.

### Phase 1: Core Functionality

The first phase focuses on implementing the most critical endpoints needed for the new frontend:

1. **Core Resources:**

   - Races (formerly events) - list, detail, filtering
   - Results - performance data with runner information
   - Runners - athlete profiles and statistics
   - Records - for various distances and categories

2. **Admin Resources:**

   - Submitted races - user-submitted race information
   - User management - for administrative users only
   - Approval workflows - for content moderation

3. **Implementation Strategy:**

   - Create the API specification for all planned endpoints
   - Fully implement core and admin endpoints
   - Include stubs for future endpoints in the Swagger UI documentation
   - Deploy authentication for both API keys and user sessions

4. **Documentation:**
   - Organize Swagger UI with separate sections for core, admin, and future endpoints
   - Provide detailed documentation for all parameters and response schemas
   - Mark future endpoints as "Planned for Phase 2/3" in the documentation

### Phase 2: Extended Functionality

The second phase will implement additional endpoints for comprehensive functionality:

1. **Extended Resources:**

   - Statistics - event participation, performance trends
   - Regional-specific endpoints - German records, country-specific data
   - Summary data - aggregated statistics and rankings
   - Advanced search endpoints

2. **Implementation Strategy:**

   - Complete implementation of all endpoints documented in Phase 1
   - Enhance error handling and validation
   - Optimize database queries for performance

3. **Frontend Integration:**
   - Complete migration of all frontend components to the new API
   - Begin deprecation process for old API endpoints

### Phase 3: Optimization and Completion

The final phase will focus on optimization and completing the migration:

1. **Optimization:**

   - Implement caching mechanisms
   - Add rate limiting
   - Performance tuning

2. **Completion:**
   - Implement write operations (POST, PUT, DELETE)
   - Event-Race hierarchy restructuring
   - Full decommissioning of legacy API

By following this phased approach, we can deliver value incrementally while maintaining stability for existing users.

---

## 10. Event-Race Hierarchy Considerations

A current limitation in the DUV database model is the lack of a proper event-race hierarchy. Currently, everything is stored in a single `tevent` table, which doesn't adequately represent that an event could contain multiple races (e.g., different distances within the same event).

### 10.1. Current Structure Analysis

- The `tevent` table has a `ParentID` field that is used to connect annual editions of the same event series (e.g., connecting "Berlin Marathon 2022" with "Berlin Marathon 2023")
- There is no explicit modeling of a parent event containing multiple races that occur during the same weekend
- This limits our ability to properly represent relationships between races that are part of the same event but with different distances

### 10.2. Potential Solutions

1. **Create New Relationship Structure**

   - Introduce a new concept of "Event Weekend" or "Event Container"
   - Define a new way to group races that occur during the same event weekend
   - Create specialized API endpoints that present these groupings

2. **Introduce New Relationship Table**

   - Create a new `event_race_mapping` table without modifying existing data
   - Use this table to define which races belong to the same event weekend
   - Use it to build proper hierarchical responses in the API

3. **Extended Metadata Approach**

   - Add a new field to identify races that are part of the same event weekend
   - Maintain backward compatibility with existing records
   - Use this field to create logical groupings in API responses

### 10.3. Implementation Approach

For Phase 1:

- Document the current limitations in the API specification
- Ensure the API correctly represents the existing relationship between annual editions of events
- Consider adding query parameters to help clients filter related races

For Phase 2/3:

- Evaluate the feasibility of introducing a proper event-race hierarchy
- Develop a solution that preserves existing annual edition relationships
- Implement enhanced endpoints that represent both hierarchies (annual editions and same-weekend races)

This approach allows us to address immediate needs while planning for a more robust long-term solution.

## 11. Runner Identity Management

The current DUV database has a challenge related to runner identity management in the `tperson` table, where the same runner may have multiple entries.

### 11.1. Current Structure Analysis

- A runner may have multiple entries in the `tperson` table (e.g., due to name variations, nationality changes)
- The `ParentID` field is used to connect these multiple entries:
  - When a runner has multiple entries, one is considered the "main" entry
  - The PersonID of this main entry is stored as the ParentID in all other entries for the same runner
  - If a runner has only one entry, their ParentID is 0
- The `tperformance` table links to specific PersonID entries, not to the unified runner identity

### 11.2. API Design Challenges

1. **Inconsistent Referencing**: The same runner may be referenced by different PersonIDs in different results
2. **Identity Resolution**: Need to determine the canonical identity for a runner with multiple entries
3. **Profile Aggregation**: Runner statistics need to be aggregated across all linked entries
4. **Update Management**: Changes to runner information may need to be propagated across entries

### 11.3. Proposed Solutions

1. **Virtual Unified Runner Resource**

   - Expose a "unified runner" concept in the API that aggregates all linked entries
   - Use the entry with matching PersonID and ParentID as the canonical source
   - Include metadata about alternative identities

2. **Identity Consolidation**

   - In API responses, always normalize references to use the main PersonID
   - Provide details of identity variations only when explicitly requested
   - Handle special cases like name changes over time

3. **Query Parameter Options**

   - Add query parameters to control whether to return unified or specific identity data
   - Allow filtering based on any identity aspects across all linked entries
   - Provide options for how to handle merged runner data

### 11.4. Implementation Approach

For Phase 1:

- Implement basic unified runner profiles that aggregate data from all linked entries
- Ensure performance data correctly links to the appropriate runner identity
- Document the identity management approach in the API specification

For Phase 2:

- Implement more sophisticated identity resolution and merging
- Add endpoints to view and manage identity variations
- Provide better support for searching across all identity variations

By addressing this challenge properly, the API can provide a more coherent and accurate representation of runners despite the underlying data complexity.

_End of PRD._
