# EdlivkyHospital Admin Dashboard

A modern, responsive hospital administration dashboard built with PHP, Tailwind CSS, and Chart.js.

## Features

- **Responsive Design**: Fully mobile-responsive layout that works on all devices
- **Dark Mode**: Complete dark/light mode toggle functionality
- **Interactive Charts**: Monthly hospital activity bar chart and patient distribution pie chart
- **Quick Actions**: Easy access to common tasks (Patient Registration, Appointment Scheduling, etc.)
- **Statistics Dashboard**: Real-time statistics cards showing key metrics
- **Modern UI**: Clean, professional interface using Tailwind CSS and Flowbite components

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Tailwind CSS v3.4.0
- **UI Components**: Flowbite v2.2.0
- **Interactivity**: Alpine.js v3.13.0
- **Charts**: Chart.js v4.4.0
- **Date Handling**: Flatpickr v4.6.13
- **Backend**: PHP & MYssql
- **Icons**: Font Awesome 6.4.0

## Project Structure

```
edlivkyhospital/
├── public/
│   ├── assets/
│   │   ├── js/
│   │   │   └── dashboard-charts.js
│   │   ├── css/
│   │   │   └── style.css
│   │   └── images/
│   ├── includes/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── patients/
│   │   └── add.php
│   ├── staff/
│   │   └── add.php
│   ├── appointments/
│   │   └── add.php
│   ├── medical-records/
│   │   └── add.php
│   ├── beds/
│   │   └── add.php
│   └── dashboard.php
├── src/
│   └── input.css
├── node_modules/
├── package.json
├── tailwind.config.js
└── README.md
```

## Installation

1. **Prerequisites**:
   - XAMPP or similar PHP server environment
   - Node.js and npm installed

2. **Setup**:
   ```bash
   # Clone or download the project to your XAMPP htdocs directory
   cd c:\xampp\htdocs\edlivkyhospital
   
   # Install dependencies
   npm install
   
   # Build CSS
   npm run build
   ```

3. **Start XAMPP** and ensure Apache is running

4. **Access the dashboard**:
   - Open your browser and navigate to: `http://localhost/edlivkyhospital/public/dashboard.php`

## Dashboard Features

### Statistics Cards
- **Total Patients**: 1,247 (+12% from last month)
- **Active Staff**: 89 (+5% from last month)
- **Today's Appointments**: 23 (+8% from last month)
- **Available Beds**: 42 (-3% from last month)

### Quick Actions
- Register Patient (Blue)
- Schedule Appointment (Green)
- Add Medical Record (Purple)
- Assign Bed (Orange)

### Charts
1. **Monthly Hospital Activity**: Bar chart showing patients, appointments, and procedures over 6 months
2. **Patient Distribution by Department**: Pie chart showing:
   - Emergency: 35%
   - Cardiology: 25%
   - Surgery: 15%
   - Pediatrics: 20%
   - Others: 5%

### Recent Activity
Real-time feed showing recent system activities with timestamps

## Dark Mode
Toggle between light and dark themes using the moon/sun icon in the top navigation. The preference is saved in localStorage.

## Mobile Responsiveness
- Collapsible sidebar for mobile devices
- Responsive grid layouts
- Touch-friendly interface
- Optimized chart displays for small screens

## Development

### Building CSS
```bash
# Watch for changes (development)
npm run build-css

# Build once (production)
npm run build
```

### Customization
- Modify `src/input.css` for custom styles
- Update `tailwind.config.js` for theme customization
- Edit chart configurations in `public/assets/js/dashboard-charts.js`

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Future Enhancements
- Database integration for dynamic data
- User authentication system
- Advanced reporting features
- Real-time notifications
- API integration for external systems

## License
This project is licensed under the MIT License.

## Support
For support and questions, please contact the development team.
