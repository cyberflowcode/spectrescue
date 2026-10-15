# SPECTRESCUE System Flowcharts & Architecture Guide

> **Project ALIEN 2026 — Paranormal Emergency Response & Dispatch Management System**  
> *Prepared for Project Defense & Technical Presentation*

---

## 1. End-to-End Emergency Lifecycle Flowchart

This flowchart illustrates how an emergency incident moves from initial citizen reporting all the way through dispatch, containment, and final administrative audit.

```mermaid
flowchart TD
    subgraph S1["1. Citizen Reporting Phase"]
        A["Citizen Reports Incident via Form"] --> B["System Validates Input & Uploads Evidence"]
        B --> C["Generate Unique Tracking Code (e.g. PAR-2026-0004)"]
        C --> D["Save Incident with Status: 'Pending'"]
        D --> E["Notify Active Dispatchers"]
    end

    subgraph S2["2. Dispatcher Evaluation & Assignment"]
        E --> F["Dispatcher Reviews Pending Queue"]
        F --> G{"Assess Threat Level & Filter Responders"}
        G --> H["Select Verified & Available Responder"]
        H --> I["Create Assignment Record & Set Incident to 'Dispatched'"]
        I --> J["Notify Assigned Responder & Citizen"]
    end

    subgraph S3["3. Responder Action Phase"]
        J --> K["Responder Views Assignment Details"]
        K --> L{"Responder Decision"}
        L -- "Decline" --> M["Set Assignment to 'declined'<br/>Revert Incident to 'Pending'"]
        M --> F
        L -- "Accept" --> N["Set Assignment to 'accepted'<br/>Update Incident to 'Responding'"]
        N --> O["Responder Deploys On-Site & Executes Containment"]
    end

    subgraph S4["4. Resolution & Reporting Phase"]
        O --> P["Responder Fills Resolution Dossier"]
        P --> Q["Save Response Record & Update Incident to 'Resolved'"]
        Q --> R["Notify Citizen & Dispatcher"]
    end

    subgraph S5["5. Administration & Auditing"]
        R --> S["Admin Reviews Incident Dossier & Global Analytics"]
    end

    style S1 fill:#0f172a,stroke:#3b82f6,stroke-width:2px,color:#fff
    style S2 fill:#0f172a,stroke:#eab308,stroke-width:2px,color:#fff
    style S3 fill:#0f172a,stroke:#10b981,stroke-width:2px,color:#fff
    style S4 fill:#0f172a,stroke:#06b6d4,stroke-width:2px,color:#fff
    style S5 fill:#0f172a,stroke:#8b5cf6,stroke-width:2px,color:#fff
```

---

## 2. Multi-Role Swimlane Flowchart

This swimlane demonstrates exactly what each of the **four user roles** does, how they interact with each other, and where the system automated actions occur.

```mermaid
sequenceDiagram
    autonumber
    actor Citizen as 👤 Citizen
    actor Dispatcher as 🛰️ Dispatcher
    actor Responder as ⚡ Responder
    actor Admin as 🛡️ Admin

    Note over Citizen,Admin: System Setup & Responder Approval
    Responder->>Admin: Registers with Badge & Specialization
    Admin->>Admin: Verifies and Approves Responder Profile
    Admin-->>Responder: In-App Notification: "Account Approved"

    Note over Citizen,Admin: Incident Ingestion & Dispatch
    Citizen->>Citizen: Submits Emergency Report + Photo Evidence
    Note right of Citizen: Creates Incident [Status: Pending]
    Citizen-->>Dispatcher: In-App Notification: "New Emergency"
    
    Dispatcher->>Dispatcher: Evaluates Location, Incident Type, & Threat Level
    Dispatcher->>Responder: Assigns Mission [Status: Dispatched]
    Dispatcher-->>Citizen: In-App Notification: "Responder Dispatched"
    Dispatcher-->>Responder: In-App Notification: "New Assignment"

    Note over Citizen,Admin: Field Response & Containment
    alt Responder Declines
        Responder->>Dispatcher: Declines Assignment (Reason provided)
        Note right of Responder: Incident reverted to [Pending]
        Dispatcher->>Dispatcher: Re-assigns another Available Responder
    else Responder Accepts
        Responder->>Dispatcher: Accepts Mission [Status: Responding]
        Responder-->>Citizen: In-App Notification: "Responder En Route"
        Responder->>Responder: Performs Containment On-Site
        Responder->>Citizen: Submits Resolution Report [Status: Resolved]
        Responder-->>Dispatcher: In-App Notification: "Mission Resolved"
        Responder-->>Citizen: In-App Notification: "Threat Neutralized"
    end

    Note over Citizen,Admin: Archival & Supervision
    Admin->>Admin: Monitors System Threat Map & Analytics Dashboard
```

---

## 3. Incident State Machine (Status Transitions)

The table and state diagram below show every possible state for an incident, the triggering action, the acting role, and the database changes.

```mermaid
stateDiagram-v2
    [*] --> Pending: Citizen submits emergency report
    
    Pending --> Dispatched: Dispatcher assigns a verified Responder
    
    Dispatched --> Responding: Responder clicks "Accept Assignment"
    Dispatched --> Pending: Responder clicks "Decline" (Requires Re-dispatch)
    
    Responding --> Resolved: Responder submits Resolution Report
    
    Resolved --> [*]: Archived in Admin Dossiers
```

### State Transition Summary Table

| From State | To State | Triggering Action | Acting Role | Controller & Action |
| :--- | :--- | :--- | :--- | :--- |
| **None** | `Pending` | Submits report form with evidence | Citizen | `CitizenController@storeIncident` |
| `Pending` | `Pending` | Updates report details, address, or map coordinates | Citizen | `CitizenController@updateIncident` |
| `Pending` | `Dispatched` | Selects responder & confirms assignment | Dispatcher | `DispatcherController@assignResponder` |
| `Dispatched` | `Responding` | Clicks "Accept Assignment" | Responder | `ResponderController@respondAssignment` |
| `Dispatched` | `Pending` | Clicks "Decline Assignment" | Responder | `ResponderController@respondAssignment` |
| `Responding` | `Resolved` | Submits containment & findings report | Responder | `ResponderController@storeReport` |

---

## 4. Responder Verification Sub-Flowchart

To ensure safety and authenticity, Responders must undergo administrative verification before receiving any emergency assignments.

```mermaid
flowchart TD
    A["Responder fills Registration Form<br/>(Badge #, Specialization, Bio)"] --> B["System creates User (role: responder)<br/>and Responder (status: 'pending')"]
    B --> C["System notifies all Administrators"]
    C --> D["Admin navigates to /admin/responders"]
    D --> E{"Admin Verification"}
    E -- "Approve" --> F["Update status: 'approved'<br/>Set verified_at: now()"]
    E -- "Reject" --> G["Update status: 'rejected'"]
    F --> H["Notify Responder: 'Application Approved'"]
    G --> I["Notify Responder: 'Application Rejected'"]
    H --> J["Responder is now eligible for live dispatch assignments"]

    style F fill:#065f46,stroke:#34d399,color:#fff
    style G fill:#7f1d1d,stroke:#f87171,color:#fff
```

---

## 5. Defense Quick-Reference: Database Tables & Role Access

When presenting this flowchart to panel members or instructors, use this mapping to explain the system architecture:

| Table | Associated Model | Primary User Role | Purpose in Flowchart |
| :--- | :--- | :--- | :--- |
| `users` | [User](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/User.php) | All (`citizen`, `dispatcher`, `responder`, `admin`) | Central authentication & role access control. |
| `incident_types` | [IncidentType](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/IncidentType.php) | System / Admin | Classification (e.g. Poltergeist, Demonic Possession, Cryptid). |
| `responders` | [Responder](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Responder.php) | Responder / Admin | Professional profile, badge number, specialization, availability. |
| `incidents` | [Incident](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Incident.php) | Citizen / Dispatcher | Main emergency tracking entity (`Pending` -> `Dispatched` -> `Responding` -> `Resolved`). |
| `evidence` | [Evidence](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Evidence.php) | Citizen | Photos or documents uploaded to support the incident report. |
| `assignments` | [Assignment](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Assignment.php) | Dispatcher / Responder | Connects Incident, Dispatcher, and Responder (`assigned`, `accepted`, `declined`). |
| `responses` | [Response](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Response.php) | Responder | Resolution report (containment method, findings summary, neutralized flag). |
| `notifications` | [Notification](file:///j:/SystemFilesNew/wamp64/www/ProjectALIEN2026/app/Models/Notification.php) | All Users | Real-time in-app alerts triggered at every step of the workflow. |
