<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'EdlivkyHospital - Excellence in Healthcare'; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Leading healthcare provider offering comprehensive medical services with state-of-the-art facilities and experienced medical professionals.'; ?>">
    <meta name="keywords" content="<?php echo isset($pageKeywords) ? $pageKeywords : 'hospital, healthcare, medical services, emergency care, doctors, nurses, medical treatment'; ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .logo-gradient {
            background: linear-gradient(135deg, #06b6d4 0%, #ec4899 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-blur {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Modal Animation Styles */
        #signInModal {
            backdrop-filter: blur(4px);
        }

        #modalContent {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Form Focus Styles */
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        /* Gradient Button Hover Effects */
        .gradient-btn {
            background: linear-gradient(135deg, #06b6d4 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }

        .gradient-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(6, 182, 212, 0.3);
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="nav-blur fixed w-full top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-hospital text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">EdlivkyHospital</span>
                    </div>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-cyan-600 font-medium transition duration-300">About</a>
                    <a href="#services" class="text-gray-700 hover:text-cyan-600 font-medium transition duration-300">Expertise</a>
                    <a href="services.php" class="text-gray-700 hover:text-cyan-600 font-medium transition duration-300">Our Team</a>
                    <a href="contact.php" class="text-gray-700 hover:text-cyan-600 font-medium transition duration-300">Contact</a>

                    <!-- Sign In Button -->
                    <button id="signInButton" class="border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:border-cyan-500 hover:text-cyan-600 transition duration-300 flex items-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>

                    <!-- Call Now Button -->
                    <a href="tel:+1234567890" class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        Call Now
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-cyan-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="#home" class="block px-3 py-2 text-gray-700 hover:text-cyan-600 font-medium">About</a>
                <a href="#services" class="block px-3 py-2 text-gray-700 hover:text-cyan-600 font-medium">Expertise</a>
                <a href="services.php" class="block px-3 py-2 text-gray-700 hover:text-cyan-600 font-medium">Our Team</a>
                <a href="contact.php" class="block px-3 py-2 text-gray-700 hover:text-cyan-600 font-medium">Contact</a>
                <button id="mobileSignInButton" class="block w-full px-3 py-2 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold text-center mt-4 hover:border-cyan-500 hover:text-cyan-600 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
                <a href="tel:+1234567890" class="block px-3 py-2 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white rounded-lg font-semibold text-center mt-4">
                    <i class="fas fa-phone mr-2"></i>
                    Call Now
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Sign In Modal -->
    <div id="signInModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Modal Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition duration-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-gray-600 mt-2">Sign in to access your account</p>
            </div>

            <!-- Tab Navigation -->
            <div class="px-6 pt-6">
                <div class="flex bg-gray-100 rounded-xl p-1">
                    <button id="publicTab" class="flex-1 py-3 px-4 text-sm font-medium rounded-lg transition duration-300 text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        Public Sign In
                    </button>
                    <button id="staffTab" class="flex-1 py-3 px-4 text-sm font-medium rounded-lg transition duration-300 bg-white text-gray-900 shadow-sm">
                        <i class="fas fa-user-md mr-2"></i>
                        Staff Sign In
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <!-- Public Sign In Content -->
                <div id="publicContent" class="hidden">
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Patient Portal</h3>
                        <p class="text-gray-600 mb-6">Access your medical records, appointments, and more.</p>

                        <div class="space-y-3">
                            <button class="w-full bg-gradient-to-r from-cyan-500 to-cyan-600 text-white py-3 px-6 rounded-xl font-semibold hover:shadow-lg transition duration-300">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In to Patient Portal
                            </button>
                            <button class="w-full border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:border-cyan-500 hover:text-cyan-600 transition duration-300">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Patient Account
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 mt-4">
                            New to our patient portal? Contact us at
                            <a href="tel:+1234567890" class="text-cyan-600 hover:text-cyan-700">+1 (234) 567-890</a>
                        </p>
                    </div>
                </div>

                <!-- Staff Sign In Content -->
                <div id="staffContent">
                    <form id="staffLoginForm" class="space-y-6">
                        <div>
                            <label for="staffEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <input type="email" id="staffEmail" name="email" required
                                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                                       placeholder="Enter your staff email">
                                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label for="staffPassword" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" id="staffPassword" name="password" required
                                       class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition duration-300"
                                       placeholder="Enter your password">
                                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" id="rememberMe" name="remember" class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" id="staffLoginButton"
                                class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In to Staff Portal
                        </button>

                        <div class="text-center">
                            <p class="text-sm text-gray-500">
                                Need help? Contact IT Support at
                                <a href="tel:+1234567890" class="text-cyan-600 hover:text-cyan-700">ext. 1234</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add top padding to account for fixed navigation -->
    <div class="pt-20">

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Sign In Modal functionality
        const signInModal = document.getElementById('signInModal');
        const modalContent = document.getElementById('modalContent');
        const signInButton = document.getElementById('signInButton');
        const mobileSignInButton = document.getElementById('mobileSignInButton');
        const closeModal = document.getElementById('closeModal');
        const publicTab = document.getElementById('publicTab');
        const staffTab = document.getElementById('staffTab');
        const publicContent = document.getElementById('publicContent');
        const staffContent = document.getElementById('staffContent');

        // Open modal
        function openSignInModal() {
            signInModal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Close modal
        function closeSignInModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                signInModal.classList.add('hidden');
            }, 300);
        }

        // Event listeners for opening modal
        signInButton.addEventListener('click', openSignInModal);
        mobileSignInButton.addEventListener('click', openSignInModal);

        // Event listeners for closing modal
        closeModal.addEventListener('click', closeSignInModal);
        signInModal.addEventListener('click', function(e) {
            if (e.target === signInModal) {
                closeSignInModal();
            }
        });

        // Tab switching
        function switchToPublicTab() {
            publicTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            publicTab.classList.remove('text-gray-600');
            staffTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            staffTab.classList.add('text-gray-600');

            publicContent.classList.remove('hidden');
            staffContent.classList.add('hidden');
        }

        function switchToStaffTab() {
            staffTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            staffTab.classList.remove('text-gray-600');
            publicTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            publicTab.classList.add('text-gray-600');

            staffContent.classList.remove('hidden');
            publicContent.classList.add('hidden');
        }

        publicTab.addEventListener('click', switchToPublicTab);
        staffTab.addEventListener('click', switchToStaffTab);

        // Password toggle
        const togglePassword = document.getElementById('togglePassword');
        const staffPassword = document.getElementById('staffPassword');

        togglePassword.addEventListener('click', function() {
            const type = staffPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            staffPassword.setAttribute('type', type);

            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });

        // Staff Login Form Submission
        const staffLoginForm = document.getElementById('staffLoginForm');
        const staffLoginButton = document.getElementById('staffLoginButton');

        staffLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const email = document.getElementById('staffEmail').value;
            const password = document.getElementById('staffPassword').value;
            const remember = document.getElementById('rememberMe').checked;

            // Basic validation
            if (!email || !password) {
                showModalNotification('Please fill in all required fields.', 'error');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showModalNotification('Please enter a valid email address.', 'error');
                return;
            }

            // Show loading state
            const originalText = staffLoginButton.innerHTML;
            staffLoginButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing In...';
            staffLoginButton.disabled = true;

            // Create form data for submission
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('remember', remember ? '1' : '0');
            formData.append('ajax_login', '1');

            // Submit to existing staff login system
            fetch('../public/auth/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModalNotification('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        // Redirect to staff dashboard
                        window.location.href = data.redirect || '../public/dashboard.php';
                    }, 1500);
                } else {
                    showModalNotification(data.message || 'Invalid credentials. Please try again.', 'error');
                    staffLoginButton.innerHTML = originalText;
                    staffLoginButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                showModalNotification('Connection error. Please try again.', 'error');
                staffLoginButton.innerHTML = originalText;
                staffLoginButton.disabled = false;
            });
        });

        // Modal notification system
        function showModalNotification(message, type) {
            // Remove existing modal notifications
            const existingNotifications = document.querySelectorAll('.modal-notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification
            const notification = document.createElement('div');
            notification.className = `modal-notification absolute top-4 left-4 right-4 p-3 rounded-lg shadow-lg z-10 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center text-sm">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            modalContent.style.position = 'relative';
            modalContent.appendChild(notification);

            // Auto hide after 4 seconds
            setTimeout(() => {
                notification.remove();
            }, 4000);
        }
        
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
        
        // Navigation background on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }
        });
    </script>
