    </div> <!-- Close pt-20 div from header -->
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <!-- Main Footer Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-hospital text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold">EdlivkyHospital</span>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md leading-relaxed">
                        Where sophisticated medical expertise meets personalized patient care. 
                        Our commitment to excellence has made us a trusted partner in your health journey.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-cyan-600 transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="about.php" class="text-gray-300 hover:text-cyan-400 transition duration-300">About Us</a></li>
                        <li><a href="services.php" class="text-gray-300 hover:text-cyan-400 transition duration-300">Our Services</a></li>
                        <li><a href="doctors.php" class="text-gray-300 hover:text-cyan-400 transition duration-300">Our Doctors</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-cyan-400 transition duration-300">Contact Us</a></li>
                        <li><a href="../public/auth/login.php" class="text-gray-300 hover:text-cyan-400 transition duration-300">Staff Portal</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Contact Info</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-cyan-400 mt-1 mr-3"></i>
                            <div>
                                <p class="text-gray-300">123 Healthcare Avenue</p>
                                <p class="text-gray-300">Medical District, MD 12345</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-cyan-400 mr-3"></i>
                            <a href="tel:+1234567890" class="text-gray-300 hover:text-cyan-400 transition duration-300">
                                +1 (234) 567-890
                            </a>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-cyan-400 mr-3"></i>
                            <a href="mailto:info@edlivkyhospital.com" class="text-gray-300 hover:text-cyan-400 transition duration-300">
                                info@edlivkyhospital.com
                            </a>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-cyan-400 mr-3"></i>
                            <div>
                                <p class="text-gray-300">24/7 Emergency</p>
                                <p class="text-gray-300">Mon-Fri: 8AM-8PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © <?php echo date('Y'); ?> EdlivkyHospital. All rights reserved.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="privacy.php" class="text-gray-400 hover:text-cyan-400 text-sm transition duration-300">Privacy Policy</a>
                        <a href="terms.php" class="text-gray-400 hover:text-cyan-400 text-sm transition duration-300">Terms of Service</a>
                        <a href="sitemap.php" class="text-gray-400 hover:text-cyan-400 text-sm transition duration-300">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-cyan-500 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script>
        // Back to top button functionality
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible');
                backToTopButton.classList.remove('opacity-100', 'visible');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Contact Form Validation and Submission
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const firstName = formData.get('firstName');
                const lastName = formData.get('lastName');
                const email = formData.get('email');
                const phone = formData.get('phone');
                const subject = formData.get('subject');
                const message = formData.get('message');

                // Basic validation
                if (!firstName || !lastName || !email || !message) {
                    showNotification('Please fill in all required fields.', 'error');
                    return;
                }

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showNotification('Please enter a valid email address.', 'error');
                    return;
                }

                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                submitButton.disabled = true;

                // Simulate form submission (replace with actual submission logic)
                setTimeout(() => {
                    showNotification('Thank you! Your message has been sent successfully. We will get back to you soon.', 'success');
                    contactForm.reset();
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 2000);
            });
        }

        // Notification system
        function showNotification(message, type) {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification
            const notification = document.createElement('div');
            notification.className = `notification fixed top-24 right-4 z-50 p-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-3"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Show notification
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
