    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-8">
        <div class="p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                © <?php echo date('Y'); ?> EdlivkyHospital. All rights reserved. 
                <span class="hidden sm:inline">| Built with ❤️ for better healthcare management</span>
            </p>
        </div>
    </footer>

    <!-- Custom JavaScript -->
    <script>
        // Update last updated time every minute
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
            const lastUpdatedElement = document.getElementById('lastUpdated');
            if (lastUpdatedElement) {
                lastUpdatedElement.textContent = timeString;
            }
        }
        
        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);
        
        // Initialize Flatpickr for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all date inputs
            flatpickr('.date-picker', {
                dateFormat: 'Y-m-d',
                allowInput: true
            });
            
            // Initialize all datetime inputs
            flatpickr('.datetime-picker', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                allowInput: true
            });
            
            // Initialize all time inputs
            flatpickr('.time-picker', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                allowInput: true
            });
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
