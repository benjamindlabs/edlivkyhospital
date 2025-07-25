Frontend Development Prompt for EdlivkyHospital Admin Dashboard
Project Overview
Build a  very mobile responsive hospital administration dashboard with:

Tailwind CSS for styling

Flowbite for pre-built UI components (modals, dropdowns)

Alpine.js for reactive frontend logic

Chart.js for data visualization

Flatpickr for date/time inputs

PHP/MySQL backend integration

Dashboard Structure Requirements
dark mode toggle (toggle via JS)
1. Sidebar Navigation
Collapsible sidebar with these menu items (each linking to their respective forms):

Dashboard (Overview with stats/charts)

Patients:

Registration Form

Patient record (with edit/delete)

Staff:

Staff Registration Form

Staff record

Appointments:

Scheduling Form

Calendar View/records

Medical Records:

Record Entry Form

Patient History Viewer/records

Bed Management:

Bed Assignment Form

Ward Status Overview/records



2. Form Requirements for Each Section
A. Patient Registration Form
Fields:

Personal Info

First Name (text, required)

Last Name (text, required)

Date of Birth (date picker)

Gender (dropdown: Male/Female/Other)

Contact Info

Phone (tel input)

Email (email validation)

Address (textarea)

Medical Info

Blood Type (dropdown: A+, B+, etc.)

Emergency Contact (name/phone)

B. Staff Registration Form
Fields:

Basic Info

Full Name (text)

Role (dropdown: Doctor/Nurse/Admin)

Department (dynamic dropdown based on role)

Professional Details

License Number (for medical staff)

Hire Date (date picker)

Status (Active/On Leave)

C. Appointment Scheduling Form
Fields:

Patient (searchable dropdown)

Staff (role-filtered dropdown)

Date/Time (Flatpickr datetime)

Reason (textarea)

Status (default: "Scheduled")

D. Medical Record Form
Fields:

Patient (auto-filled when accessed via patient profile)

Diagnosis (textarea with autosuggest)

Treatment Plan (richtext editor)

Prescriptions (linked to medications table)

Follow-up Date (optional)

E. Bed Assignment Form
Fields:

Patient (searchable dropdown)

Bed (auto-filtered by availability)

Admission Date/Time (default: current)

Discharge Date/Time (optional)

Reason (textarea)

3. Technical Specifications
Frontend Tech Stack
Component	Technology	Purpose
Styling	Tailwind CSS	Utility-first CSS framework
UI Components	Flowbite	Pre-built modals/tables
Interactivity	Alpine.js	Reactive frontend logic
Charts	Chart.js	Visualization for dashboard
Date Handling	Flatpickr	User-friendly date/time inputs
Backend Integration
PHP scripts to handle form submissions

MySQL queries aligned with your schema

AJAX for dynamic data loading

4. Deliverables
Responsive Sidebar with all menu links

5 Complete Forms (Patients, Staff, Appointments, Records, Beds)

Dashboard Analytics with:

Patient admission charts

Appointment status pie chart

Bed occupancy heatmap




. Example Code Structure
Folder Structure
text
edlivkyhospital/  
├── public/  
│   ├── assets/  
│   │   ├── js/ (Chart.js, Alpine.js, custom scripts)  
│   │   ├── css/ (Tailwind compiled)  
│   │   └── images/  
│   ├── includes/  
│   │   ├── sidebar.php  
│   │   ├── header.php  
│   │   └── footer.php  
│   ├── dashboard.php  
│   ├── patients/  
│   │   ├── list.php  
│   │   └── add.php  
│   └── ... (other sections)  
├── tailwind.config.js  
└── package.json  
